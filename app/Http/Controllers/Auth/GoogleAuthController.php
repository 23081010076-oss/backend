<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        try {
            // Validate Google OAuth configuration
            $this->validateGoogleConfig();
            
            return Socialite::driver('google')->stateless()->redirect();
        } catch (Exception $e) {
            Log::error('Google OAuth Redirect Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Google OAuth configuration error',
                'error' => $e->getMessage(),
                'hint' => 'Please check your GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, and GOOGLE_REDIRECT_URI in .env file'
            ], 500);
        }
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback()
    {
        try {
            // Validate Google OAuth configuration
            $this->validateGoogleConfig();
            
            // Get user from Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Validate email domain if configured
            if (!$this->isEmailDomainAllowed($googleUser->email)) {
                Log::warning('Google login attempt with unauthorized domain: ' . $googleUser->email);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email domain not allowed',
                    'error' => 'Your email domain is not authorized to use this application'
                ], 403);
            }
            
            // Find or create user
            $user = $this->findOrCreateUser($googleUser);
            
            // Generate JWT token
            $token = JWTAuth::fromUser($user);
            
            Log::info('Google login successful for user: ' . $user->email);
            
            // Return response based on configuration
            return $this->handleResponse($token, $user);
            
        } catch (Exception $e) {
            Log::error('Google OAuth Callback Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to login with Google',
                'error' => $e->getMessage(),
                'hint' => 'Please check your Google OAuth configuration and try again'
            ], 500);
        }
    }

    /**
     * Validate Google OAuth configuration
     *
     * @throws Exception
     */
    private function validateGoogleConfig()
    {
        if (!config('services.google.client_id')) {
            throw new Exception('GOOGLE_CLIENT_ID is not configured');
        }
        
        if (!config('services.google.client_secret')) {
            throw new Exception('GOOGLE_CLIENT_SECRET is not configured');
        }
        
        if (!config('services.google.redirect')) {
            throw new Exception('GOOGLE_REDIRECT_URI is not configured');
        }
    }

    /**
     * Check if email domain is allowed
     *
     * @param string $email
     * @return bool
     */
    private function isEmailDomainAllowed($email)
    {
        $allowedDomains = config('services.google.allowed_domains', '');
        
        // If no domains configured, allow all
        if (empty($allowedDomains)) {
            return true;
        }
        
        // Parse allowed domains
        $domains = array_map('trim', explode(',', $allowedDomains));
        
        // Get email domain
        $emailDomain = substr(strrchr($email, "@"), 1);
        
        return in_array($emailDomain, $domains);
    }

    /**
     * Find or create user from Google data
     *
     * @param object $googleUser
     * @return User
     */
    private function findOrCreateUser($googleUser)
    {
        // Try to find user by google_id
        $user = User::where('google_id', $googleUser->id)->first();

        if (!$user) {
            // Check if user exists with the same email
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Update existing user with google_id
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);
                
                Log::info('Linked Google account to existing user: ' . $user->email);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => bcrypt(bin2hex(random_bytes(16))), // Random secure password
                    'role' => 'student', // Default role
                ]);
                
                Log::info('Created new user from Google: ' . $user->email);
            }
        } else {
            // Update avatar if changed
            if ($user->avatar !== $googleUser->avatar) {
                $user->update([
                    'avatar' => $googleUser->avatar,
                ]);
            }
        }

        return $user;
    }

    /**
     * Handle response based on configuration
     *
     * @param string $token
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function handleResponse($token, $user)
    {
        $responseType = config('services.google.response_type', 'json');
        
        if ($responseType === 'redirect') {
            // Redirect to frontend with token
            $frontendUrl = config('services.google.frontend_url', config('app.frontend_url', 'http://localhost:3000'));
            $redirectUrl = $frontendUrl . '/auth/callback?token=' . $token;
            
            return redirect()->away($redirectUrl);
        }
        
        // Default: Return JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'User logged in successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }
}
