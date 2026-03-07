<?php

namespace App\Repositories;

use App\Models\Image;

class ImageRepository
{
    /**
     * Create a new image.
     */
    public function create(array $data): Image
    {
        return Image::create($data);
    }

    /**
     * Find an image by ID.
     */
    public function find(int $id): ?Image
    {
        return Image::find($id);
    }

    /**
     * Delete an image by ID.
     */
    public function delete(int $id): bool
    {
        $image = $this->find($id);

        if (!$image) {
            return false;
        }

        return $image->delete();
    }

    /**
     * Get all images for a specific property.
     */
    public function getByProperty(int $propertyId)
    {
        return Image::where('property_id', $propertyId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Delete all images for a specific property.
     */
    public function deleteByProperty(int $propertyId): bool
    {
        return Image::where('property_id', $propertyId)->delete();
    }

    /**
     * Count images for a specific property.
     */
    public function countByProperty(int $propertyId): int
    {
        return Image::where('property_id', $propertyId)->count();
    }
}
