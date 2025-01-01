<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkItemRequest extends FormRequest
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
            'type' => 'required|string',
            'diamond' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'work_id' => 'required|exists:works,id',
            'is_active' => 'nullable|in:0,1'
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'The type is required',
            'type.string' => 'The type must be a string',
            'diamond.integer' => 'The diamond must be an integer',
            'price.numeric' => 'The price must be a number',
            'work_id.required' => 'The work ID is required',
            'work_id.exists' => 'The selected work does not exist',
            'is_active.in' => 'The is_active must be 0 or 1',
        ];
    }
}
