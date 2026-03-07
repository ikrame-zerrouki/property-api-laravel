<?php

namespace App\Services;

use App\Models\Image;
use App\Repositories\ImageRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageService
{
    public function __construct(
        private readonly ImageRepository $imageRepository
    ) {}

    /**
     * Upload a single image.
     */
    public function uploadImage(int $propertyId, UploadedFile $file): Image
    {
        try {
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store image in public disk
            $path = $file->storeAs('properties/' . $propertyId, $filename, 'public');

            if (!$path) {
                throw new \Exception('Failed to store image');
            }

            // Create image record in database
            $image = $this->imageRepository->create([
                'property_id' => $propertyId,
                'path' => $path
            ]);

            return $image;

        } catch (\Exception $e) {
            Log::error('Error uploading image: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an image.
     */
    public function deleteImage(Image $image): bool
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            // Delete directory if empty
            $directory = dirname($image->path);
            if (Storage::disk('public')->files($directory) === []) {
                Storage::disk('public')->deleteDirectory($directory);
            }

            // Delete database record
            return $this->imageRepository->delete($image->id);

        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get image URL.
     */
    public function getImageUrl(string $path): string
    {
        return Storage::url($path);
    }
}
