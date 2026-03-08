<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     // Temporarily disabled for testing
    //     return true;

    //     // Original code (commented)
    //     // return auth()->check() && in_array(auth()->user()->role, ['agent', 'admin']);
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
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
            'images.required' => 'Please select at least one image',
            'images.array' => 'Invalid image format',
            'images.*.required' => 'The uploaded file is invalid',
            'images.*.image' => 'The file must be an image',
            'images.*.mimes' => 'The image must be of type: jpeg, png, jpg, gif',
            'images.*.max' => 'The image must not exceed 2MB'
        ];
    }
}
