<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkRequest extends FormRequest
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
            'description' => 'nullable|string',
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'is_active' => 'nullable|in:0,1'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The work name is required',
            'name.max' => 'The work name cannot exceed 255 characters',
            'date.required' => 'The date is required',
            'date.date' => 'Please provide a valid date',
            'user_id.required' => 'The user ID is required',
            'user_id.exists' => 'The selected user does not exist'
        ];
    }
}
