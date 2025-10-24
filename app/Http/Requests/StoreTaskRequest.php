<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admins can create tasks
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'client' => 'required|string|regex:/^(contracted|client)_\d+$/',
            'serviceDate' => 'required|date|after_or_equal:today',
            'cabinsList' => 'nullable|array|min:0',
            'cabinsList.*.cabin' => 'required_with:cabinsList|string|max:255',
            'cabinsList.*.serviceType' => 'required_with:cabinsList|string|max:255',
            'cabinsList.*.cabinType' => 'required_with:cabinsList|string|max:255',
            'rateType' => 'nullable|string|in:daily,weekly,arrival,departure',
            'extraTasks' => 'nullable|array|min:0',
            'extraTasks.*.type' => 'required_with:extraTasks|string|max:255',
            'extraTasks.*.price' => 'nullable|numeric|min:0|max:' . config('optimization.pricing.max_extra_task_price', 10000), // Max price validation
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Ensure at least one of cabinsList or extraTasks is provided
            if (empty($this->cabinsList) && empty($this->extraTasks)) {
                $validator->errors()->add(
                    'general',
                    'You must select at least one cabin or add at least one extra task.'
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client.required' => 'Please select a client.',
            'client.regex' => 'Invalid client format.',
            'serviceDate.required' => 'Service date is required.',
            'serviceDate.after_or_equal' => 'Service date must be today or in the future.',
            'extraTasks.*.price.max' => 'Extra task price cannot exceed 10,000 EUR.',
            'extraTasks.*.price.min' => 'Extra task price cannot be negative.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators can create tasks.'
            ], 403)
        );
    }
}
