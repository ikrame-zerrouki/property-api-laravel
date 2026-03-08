<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class CreatePropertyDTO
{
    public function __construct(
        public readonly string $type,
        public readonly int $pieces,
        public readonly float $surface,
        public readonly float $prix,
        public readonly string $ville,
        public readonly string $description,
        public readonly string $statut,
        public readonly bool $is_published,
        public readonly array $images = []
    ) {}

    /**
     * Create DTO from request.
     */
    public static function fromRequest($request): self
    {
        return new self(
            type: $request->type,
            pieces: (int) $request->pieces,
            surface: (float) $request->surface,
            prix: (float) $request->prix,
            ville: $request->ville,
            description: $request->description,
            statut: $request->statut ?? 'disponible',
            is_published: $request->boolean('is_published', false),
            images: $request->file('images', [])
        );
    }

    /**
     * Create DTO from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            pieces: (int) ($data['pieces'] ?? 0),
            surface: (float) ($data['surface'] ?? 0),
            prix: (float) ($data['prix'] ?? 0),
            ville: $data['ville'] ?? '',
            description: $data['description'] ?? '',
            statut: $data['statut'] ?? 'disponible',
            is_published: $data['is_published'] ?? false,
            images: $data['images'] ?? []
        );
    }

    /**
     * Check if DTO has images.
     */
    public function hasImages(): bool
    {
        return !empty($this->images);
    }

    /**
     * Get validation rules.
     */
    public static function validationRules(): array
    {
        return [
            'type' => 'required|in:appartement,villa,terrain,magasin,bureau',
            'pieces' => 'required|integer|min:1',
            'surface' => 'required|numeric|min:1',
            'prix' => 'required|numeric|min:0',
            'ville' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'statut' => 'sometimes|in:disponible,vendu,location',
            'is_published' => 'sometimes|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    /**
     * Convert DTO to array for database.
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'pieces' => $this->pieces,
            'surface' => $this->surface,
            'prix' => $this->prix,
            'ville' => $this->ville,
            'description' => $this->description,
            'statut' => $this->statut,
            'is_published' => $this->is_published,
        ];
    }
}
