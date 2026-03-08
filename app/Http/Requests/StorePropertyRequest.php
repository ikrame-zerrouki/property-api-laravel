<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     // Temporarily allow all requests for testing
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Property information based on test requirements
            'type' => 'required|in:appartement,villa,terrain,magasin,bureau',
            'pieces' => 'required|integer|min:1|max:50',
            'surface' => 'required|numeric|min:1|max:10000',
            'prix' => 'required|numeric|min:0|max:1000000000',
            'ville' => 'required|string|max:255',
            'description' => 'required|string|min:10|max:5000',

            // Status and publication
            'statut' => 'sometimes|in:disponible,vendu,location',
            'is_published' => 'sometimes|boolean',

            // Images
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
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
            // Property type
            'type.required' => 'The property type is required',
            'type.in' => 'Invalid property type',

            // Rooms
            'pieces.required' => 'The number of rooms is required',
            'pieces.integer' => 'The number of rooms must be an integer',
            'pieces.min' => 'The number of rooms must be at least 1',

            // Surface
            'surface.required' => 'The surface area is required',
            'surface.numeric' => 'The surface area must be a number',
            'surface.min' => 'The surface area must be at least 1 m²',

            // Price
            'prix.required' => 'The price is required',
            'prix.numeric' => 'The price must be a number',
            'prix.min' => 'The price must be 0 or more',

            // City
            'ville.required' => 'The city is required',

            // Description
            'description.required' => 'The description is required',
            'description.min' => 'The description must be at least 10 characters',

            // Images
            'images.*.image' => 'The file must be an image',
            'images.*.mimes' => 'The image must be of type: jpeg, png, jpg, gif',
            'images.*.max' => 'The image must not exceed 2MB'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'statut' => $this->statut ?? 'disponible',
            'is_published' => $this->is_published ?? false,
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'type',
            'pieces' => 'number of rooms',
            'surface' => 'surface area',
            'prix' => 'price',
            'ville' => 'city',
            'description' => 'description',
            'statut' => 'status',
        ];
    }
}
