<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class UpdatePropertyDTO
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?int $pieces = null,
        public readonly ?float $surface = null,
        public readonly ?float $prix = null,
        public readonly ?string $ville = null,
        public readonly ?string $description = null,
        public readonly ?string $statut = null,
        public readonly ?bool $is_published = null,
        public readonly array $new_images = [],
        public readonly array $images_to_delete = []
    ) {}

    /**
     * Create DTO from request.
     */
    public static function fromRequest($request): self
    {
        return new self(
            type: $request->type,
            pieces: $request->pieces ? (int) $request->pieces : null,
            surface: $request->surface ? (float) $request->surface : null,
            prix: $request->prix ? (float) $request->prix : null,
            ville: $request->ville,
            description: $request->description,
            statut: $request->statut,
            is_published: $request->has('is_published') ? $request->boolean('is_published') : null,
            new_images: $request->file('new_images', []),
            images_to_delete: $request->input('images_to_delete', [])
        );
    }

    /**
     * Check if there are new images to upload.
     */
    public function hasNewImages(): bool
    {
        return !empty($this->new_images);
    }

    /**
     * Check if there are images to delete.
     */
    public function hasImagesToDelete(): bool
    {
        return !empty($this->images_to_delete);
    }
}
