<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleAuthController extends Controller
{
    /**
     * Allowed roles for registration
     */
    private $allowedRoles = ['student', 'mentor', 'corporate'];

    /**
     * Redirect the user to the Google authentication page.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function redirectToGoogle(Request $request)
    {
        try {
            // Validate Google OAuth configuration
            $this->validateGoogleConfig();
            
            // Get and validate role from request
            $role = $request->query('role', 'student');
            
            if (!in_array($role, $this->allowedRoles)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid role specified',
                    'error' => 'Role must be one of: ' . implode(', ', $this->allowedRoles),
                    'hint' => 'Add ?role=student or ?role=mentor or ?role=corporate to the URL'
                ], 400);
            }
            
            /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
            $driver = Socialite::driver('google');
            
            // Store role in state parameter (will be returned by Google)
            $state = bin2hex(random_bytes(16));
            Cache::put('google_oauth_role_' . $state, $role, now()->addMinutes(10));
            
            return $driver->stateless()
                ->with(['state' => $state])
                ->redirect();
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Validate Google OAuth configuration
            $this->validateGoogleConfig();
            
            // Get role from state parameter
            $state = $request->query('state');
            $role = 'student'; // Default role
            
            if ($state) {
                $cachedRole = Cache::pull('google_oauth_role_' . $state);
                if ($cachedRole && in_array($cachedRole, $this->allowedRoles)) {
                    $role = $cachedRole;
                }
            }
            
            // Get user from Google
            /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
            $driver = Socialite::driver('google');
            
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = $driver->stateless()->user();
            
            // Validate email domain if configured
            if (!$this->isEmailDomainAllowed($googleUser->getEmail())) {
                Log::warning('Google login attempt with unauthorized domain: ' . $googleUser->getEmail());
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email domain not allowed',
                    'error' => 'Your email domain is not authorized to use this application'
                ], 403);
            }
            
            // Find or create user with selected role
            $user = $this->findOrCreateUser($googleUser, $role);
            
            // Generate JWT token
            $token = JWTAuth::fromUser($user);
            
            Log::info('Google login successful for user: ' . $user->email . ' with role: ' . $user->role);
            
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
     * @param \Laravel\Socialite\Two\User $googleUser
     * @param string $role
     * @return User
     */
    private function findOrCreateUser($googleUser, $role = 'student')
    {
        // Try to find user by google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // Check if user exists with the same email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update existing user with google_id (keep existing role)
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                
                Log::info('Linked Google account to existing user: ' . $user->email);
            } else {
                // Create new user with selected role
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(bin2hex(random_bytes(16))), // Random secure password
                    'role' => $role, // Use selected role
                ]);
                
                Log::info('Created new user from Google: ' . $user->email . ' with role: ' . $role);
            }
        } else {
            // Update avatar if changed
            if ($user->avatar !== $googleUser->getAvatar()) {
                $user->update([
                    'avatar' => $googleUser->getAvatar(),
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
