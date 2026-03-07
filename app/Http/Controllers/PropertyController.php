<?php

namespace App\Http\Controllers;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\UpdatePropertyDTO;
use App\DTOs\PropertyFilterDTO;
use App\Repositories\PropertyRepository;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function __construct(
        private PropertyRepository $propertyRepository
    ) {}

    /**
     * Display a listing of properties.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = PropertyFilterDTO::fromRequest($request);
            $properties = $this->propertyRepository->getAllProperties($filters);
            $this->propertyRepository->prepareImageUrls($properties);

            return response()->json([
                'success' => true,
                'data' => $properties
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching properties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created property.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check authorization
            $this->authorize('create', Property::class);

            $validated = $request->validate(CreatePropertyDTO::validationRules());

            $dto = CreatePropertyDTO::fromRequest($request);
            $property = $this->propertyRepository->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully',
                'data' => $property
            ], 201);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to create properties'
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified property.
     */
    public function show($id): JsonResponse
    {
        try {
            $property = $this->propertyRepository->find($id);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            $this->propertyRepository->prepareImageUrls($property);

            return response()->json([
                'success' => true,
                'data' => $property
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified property.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $property = $this->propertyRepository->find($id);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('update', $property);

            $dto = UpdatePropertyDTO::fromRequest($request);
            $updatedProperty = $this->propertyRepository->update($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'data' => $updatedProperty
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this property'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified property.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $property = $this->propertyRepository->find($id);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('delete', $property);

            $deleted = $this->propertyRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully'
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this property'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle property publication status.
     */
    public function togglePublish($id): JsonResponse
    {
        try {
            $property = $this->propertyRepository->find($id);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('publish', $property);

            $property->update([
                'is_published' => !$property->is_published
            ]);

            return response()->json([
                'success' => true,
                'message' => $property->is_published ? 'Property published' : 'Property unpublished',
                'data' => $property
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to change publication status'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling publication status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
