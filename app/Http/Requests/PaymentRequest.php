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
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'source' => 'required|string',
            'date' => 'required|date',
            'work_id' => 'required|exists:works,id',
            'user_id' => 'required|exists:users,id',
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
            'source.required' => 'The payment source is required',
            'source.string' => 'The payment source must be a string',
            'date.required' => 'The date is required',
            'date.date' => 'Please provide a valid date',
            'work_id.required' => 'The work ID is required',
            'work_id.exists' => 'The selected work does not exist',
            'user_id.required' => 'The user ID is required',
            'user_id.exists' => 'The selected user does not exist'
        ];
    }
}
