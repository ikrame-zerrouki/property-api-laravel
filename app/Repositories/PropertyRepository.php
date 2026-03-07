<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\Image;
use App\DTOs\CreatePropertyDTO;
use App\DTOs\UpdatePropertyDTO;
use App\DTOs\PropertyFilterDTO;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PropertyRepository
{
    /**
     * Create a new property.
     */
    public function create(CreatePropertyDTO $dto): Property
    {
        // Get authenticated user ID safely
        $userId = auth()->id();

        // Fallback to ID 1 for testing
        if (!$userId) {
            $userId = 1;
        }

        // Create property
        $property = Property::create([
            'user_id' => $userId,
            'type' => $dto->type,
            'pieces' => $dto->pieces,
            'surface' => $dto->surface,
            'prix' => $dto->prix,
            'ville' => $dto->ville,
            'description' => $dto->description,
            'statut' => $dto->statut,
            'is_published' => $dto->is_published,
            'title' => $this->generateTitle($dto)
        ]);

        // Upload images if provided
        if (!empty($dto->images)) {
            $this->uploadImages($property->id, $dto->images);
        }

        return $property->load('images');
    }

    /**
     * Update an existing property.
     */
    public function update(int $id, UpdatePropertyDTO $dto): Property
    {
        $property = $this->find($id);

        if (!$property) {
            throw new \Exception('Property not found');
        }

        $updateData = array_filter([
            'type' => $dto->type,
            'pieces' => $dto->pieces,
            'surface' => $dto->surface,
            'prix' => $dto->prix,
            'ville' => $dto->ville,
            'description' => $dto->description,
            'statut' => $dto->statut,
            'is_published' => $dto->is_published
        ], fn($value) => !is_null($value));

        if (!empty($updateData)) {
            $property->update($updateData);
        }

        // Delete requested images
        if ($dto->hasImagesToDelete()) {
            foreach ($dto->images_to_delete as $imageId) {
                $image = Image::find($imageId);
                if ($image && $image->property_id == $id) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        // Upload new images
        if ($dto->hasNewImages()) {
            $this->uploadImages($property->id, $dto->new_images);
        }

        return $property->load('images');
    }

    /**
     * Soft delete a property.
     */
    public function delete(int $id): bool
    {
        $property = $this->find($id);
        return $property ? $property->delete() : false;
    }

    /**
     * Permanently delete a property and its images.
     */
    public function forceDelete(int $id): bool
    {
        $property = Property::withTrashed()->find($id);

        if (!$property) {
            return false;
        }

        // Delete associated images from storage
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->forceDelete();
        }

        return $property->forceDelete();
    }

    /**
     * Find a property by ID.
     */
    public function find(int $id): ?Property
    {
        return Property::with('images')->find($id);
    }

    /**
     * Find a property by ID including trashed.
     */
    public function findWithTrashed(int $id): ?Property
    {
        return Property::withTrashed()->with('images')->find($id);
    }

    /**
     * Get all properties with filters.
     */
    public function getAllProperties(PropertyFilterDTO $filters): LengthAwarePaginator
    {
        $query = Property::with('images');

        if ($filters->search) {
            try {
                $query->selectRaw('*, MATCH(title, description) AGAINST(?) as relevance', [$filters->search])
                      ->orderBy('relevance', 'desc');
            } catch (\Exception $e) {
                Log::warning('Full-text search failed, using basic search', ['error' => $e->getMessage()]);
            }
        }

        // Comment this line during testing to see all properties
        // $query->where('is_published', true);

        $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters->perPage);
    }

    /**
     * Get properties for a specific user.
     */
    public function getUserProperties(int $userId, PropertyFilterDTO $filters): LengthAwarePaginator
    {
        $query = Property::with('images')
            ->where('user_id', $userId)
            ->latest();

        $this->applyFilters($query, $filters);

        return $query->paginate($filters->perPage);
    }

    /**
     * Apply filters to query.
     */
    private function applyFilters($query, PropertyFilterDTO $filters): void
    {
        if ($filters->ville) {
            $query->where('ville', 'like', '%' . $filters->ville . '%');
        }

        if ($filters->type) {
            $query->where('type', $filters->type);
        }

        if ($filters->prix_min) {
            $query->where('prix', '>=', $filters->prix_min);
        }

        if ($filters->prix_max) {
            $query->where('prix', '<=', $filters->prix_max);
        }

        if ($filters->statut) {
            $query->where('statut', $filters->statut);
        }

        if ($filters->search) {
            try {
                $query->whereRaw('MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)', [$filters->search]);
            } catch (\Exception $e) {
                Log::info('Falling back to LIKE search', ['search' => $filters->search]);
                $query->where(function($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters->search . '%')
                      ->orWhere('description', 'like', '%' . $filters->search . '%');
                });
            }
        }
    }

    /**
     * Upload images for a property.
     */
    private function uploadImages(int $propertyId, array $images): void
    {
        foreach ($images as $image) {
            if ($image && $image->isValid()) {
                $path = $image->store('properties', 'public');
                Image::create([
                    'property_id' => $propertyId,
                    'path' => $path
                ]);
            }
        }
    }

    /**
     * Prepare image URLs for response.
     */
    public function prepareImageUrls($properties): void
    {
        if ($properties instanceof Property) {
            $properties->images->each(function($image) {
                $image->url = Storage::url($image->path);
            });
        } else {
            collect($properties->items())->each(function($property) {
                $property->images->each(function($image) {
                    $image->url = Storage::url($image->path);
                });
            });
        }
    }

    /**
     * Generate title for property.
     */
    private function generateTitle(CreatePropertyDTO $dto): string
    {
        $typeNames = [
            'appartement' => 'Appartement',
            'villa' => 'Villa',
            'terrain' => 'Terrain',
            'magasin' => 'Magasin',
            'bureau' => 'Bureau'
        ];

        $typeName = $typeNames[$dto->type] ?? $dto->type;
        return $typeName . ' ' . $dto->pieces . ' pièces à ' . $dto->ville;
    }

    /**
     * Restore a soft-deleted property.
     */
    public function restore(int $id): bool
    {
        $property = Property::withTrashed()->find($id);
        return $property ? $property->restore() : false;
    }

    /**
     * Count properties by user.
     */
    public function countByUser(int $userId): int
    {
        return Property::where('user_id', $userId)->count();
    }

    /**
     * Get property statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => Property::count(),
            'published' => Property::where('is_published', true)->count(),
            'available' => Property::where('statut', 'disponible')->count(),
            'sold' => Property::where('statut', 'vendu')->count(),
            'rental' => Property::where('statut', 'location')->count()
        ];
    }
}
