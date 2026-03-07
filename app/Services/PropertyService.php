<?php

namespace App\Services;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\UpdatePropertyDTO;
use App\DTOs\PropertyFilterDTO;
use App\Repositories\PropertyRepository;
use App\Repositories\ImageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Property;

class PropertyService
{
    public function __construct(
        private readonly PropertyRepository $propertyRepository,
        private readonly ImageRepository $imageRepository,
        private readonly ImageService $imageService
    ) {}

    /**
     * Create a new property.
     */
    public function createProperty(CreatePropertyDTO $dto): Property
    {
        try {
            DB::beginTransaction();

            // Create property using repository
            $property = $this->propertyRepository->create($dto);

            DB::commit();

            // Load relations
            return $property->load('user', 'images');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating property: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing property.
     */
    public function updateProperty(int $id, UpdatePropertyDTO $dto): Property
    {
        try {
            DB::beginTransaction();

            // Update property using repository
            $property = $this->propertyRepository->update($id, $dto);

            DB::commit();

            // Load relations
            return $property->load('user', 'images');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating property: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a property (soft delete).
     */
    public function deleteProperty(int $id): bool
    {
        try {
            DB::beginTransaction();

            // Get property to delete its images
            $property = $this->propertyRepository->find($id);

            if ($property) {
                // Delete associated images from storage
                foreach ($property->images as $image) {
                    $this->imageService->deleteImage($image);
                }
            }

            // Delete property (soft delete)
            $result = $this->propertyRepository->delete($id);

            DB::commit();

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting property: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get a single property by ID.
     */
    public function getProperty(int $id): ?Property
    {
        $property = $this->propertyRepository->find($id);

        if ($property) {
            // Prepare image URLs
            $this->prepareImageUrlsForProperty($property);
        }

        return $property;
    }

    /**
     * Get filtered properties with pagination.
     */
    public function getFilteredProperties(PropertyFilterDTO $filters): array
    {
        // Get paginated properties from repository
        $paginator = $this->propertyRepository->getAllProperties($filters);

        // Prepare image URLs for each property
        foreach ($paginator->items() as $property) {
            $this->prepareImageUrlsForProperty($property);
        }

        // Return formatted response
        return [
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem()
            ]
        ];
    }

    /**
     * Get properties for a specific user.
     */
    public function getUserProperties(int $userId, PropertyFilterDTO $filters): array
    {
        $paginator = $this->propertyRepository->getUserProperties($userId, $filters);

        foreach ($paginator->items() as $property) {
            $this->prepareImageUrlsForProperty($property);
        }

        return [
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage()
            ]
        ];
    }

    /**
     * Prepare image URLs for a single property.
     */
    private function prepareImageUrlsForProperty(Property $property): void
    {
        foreach ($property->images as $image) {
            $image->url = $this->imageService->getImageUrl($image->path);
        }
    }

    /**
     * Check if user can modify property.
     */
    public function canUserModifyProperty(int $userId, Property $property): bool
    {
        // Admin can modify any property
        if (auth()->user() && auth()->user()->role === 'admin') {
            return true;
        }

        // Agent can only modify their own properties
        return $property->user_id === $userId;
    }

    /**
     * Toggle property publication status.
     */
    public function togglePublication(int $id): Property
    {
        $property = $this->propertyRepository->find($id);

        if ($property) {
            $property->is_published = !$property->is_published;
            $property->save();
        }

        return $property;
    }

    /**
     * Restore a soft-deleted property.
     */
    public function restoreProperty(int $id): bool
    {
        return $this->propertyRepository->restore($id);
    }

    /**
     * Get property statistics.
     */
    public function getStatistics(): array
    {
        return $this->propertyRepository->getStatistics();
    }
}
