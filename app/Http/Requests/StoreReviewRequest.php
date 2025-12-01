<?php

namespace App\Http\Requests;

use App\Models\Course;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedTypes = [
            Course::class,
            User::class, // For mentor reviews
            Organization::class,
        ];

        return [
            'reviewable_id' => 'required|integer',
            'reviewable_type' => [
                'required',
                'string',
                Rule::in($allowedTypes),
            ],
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reviewable_id.required' => 'Please select an item to review',
            'reviewable_type.required' => 'Review type is required',
            'reviewable_type.in' => 'Invalid review type',
            'rating.required' => 'Please provide a rating',
            'rating.min' => 'Rating must be at least 1 star',
            'rating.max' => 'Rating cannot exceed 5 stars',
            'comment.required' => 'Please provide a comment',
            'comment.min' => 'Comment must be at least 3 characters',
            'comment.max' => 'Comment cannot exceed 1000 characters',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verify that the reviewable item exists
            $reviewableClass = $this->input('reviewable_type');
            
            if (class_exists($reviewableClass)) {
                $reviewableItem = $reviewableClass::find($this->input('reviewable_id'));
                
                if (!$reviewableItem) {
                    $validator->errors()->add(
                        'reviewable_id',
                        'The item you are trying to review does not exist'
                    );
                }
            }
        });
    }
}
