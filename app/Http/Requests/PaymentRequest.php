<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        return [
            'name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'from' => 'nullable|string',
            'source_id' => 'required|exists:payment_sources,id',
            'date' => 'required|date',
            'work_id' => 'nullable|exists:works,id',
            'user_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|in:0,1'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The payment name is required',
            'name.max' => 'The payment name cannot exceed 255 characters',
            'amount.required' => 'The payment amount is required',
            'amount.numeric' => 'The payment amount must be a number',
            'category.required' => 'The payment category is required',
            'category.string' => 'The payment category must be a string',
            'description.string' => 'The payment description must be a string',
            'from.string' => 'The payment from must be a string',
            'source_id.required' => 'The payment source is required',
            'source_id.exists' => 'The selected payment source does not exist',
            'date.required' => 'The date is required',
            'date.date' => 'Please provide a valid date',
            'work_id.required' => 'The work ID is required',
            'work_id.exists' => 'The selected work does not exist',
            'user_id.required' => 'The user ID is required',
            'user_id.exists' => 'The selected user does not exist'
        ];
    }
}
