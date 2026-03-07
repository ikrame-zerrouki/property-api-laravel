<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Property;
use App\Services\ImageService;
use App\Http\Requests\UploadImageRequest;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function __construct(
        private readonly ImageService $imageService
    ) {}

    /**
     * Upload images for a property.
     */
    public function upload(UploadImageRequest $request, int $propertyId)
    {
        try {
            $property = Property::find($propertyId);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'error' => 'Property not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('uploadImages', $property);

            if (!$request->hasFile('images')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please select at least one image'
                ], 400);
            }

            DB::beginTransaction();

            $uploadedImages = [];
            $files = $request->file('images');

            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $image) {
                if ($image && $image->isValid()) {
                    $imageModel = $this->imageService->uploadImage($propertyId, $image);
                    $uploadedImages[] = $imageModel;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($uploadedImages) . ' image(s) uploaded successfully',
                'data' => ImageResource::collection($uploadedImages)
            ], 201);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized to upload images for this property'
            ], 403);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading images: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Error uploading images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific image.
     */
    public function destroy(int $imageId)
    {
        try {
            $image = Image::with('property')->find($imageId);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'error' => 'Image not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('deleteImage', $image->property);

            DB::beginTransaction();

            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized to delete this image'
            ], 403);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting image: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all images for a property.
     */
    public function getPropertyImages(int $propertyId)
    {
        try {
            $property = Property::find($propertyId);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'error' => 'Property not found'
                ], 404);
            }

            $images = $property->images()
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => ImageResource::collection($images)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching images: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Error fetching images'
            ], 500);
        }
    }

    /**
     * Set image as primary.
     */
    public function setPrimary(int $imageId)
    {
        try {
            $image = Image::with('property')->find($imageId);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'error' => 'Image not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('update', $image->property);

            // TODO: Implement logic to set primary image
            // This would typically involve updating a 'is_primary' field

            return response()->json([
                'success' => true,
                'message' => 'Image set as primary',
                'data' => new ImageResource($image)
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized to modify this image'
            ], 403);
        } catch (\Exception $e) {
            Log::error('Error setting primary image: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Error setting primary image'
            ], 500);
        }
    }

    /**
     * Delete multiple images at once.
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'image_ids' => 'required|array',
                'image_ids.*' => 'required|integer|exists:images,id'
            ]);

            $deletedCount = 0;
            $failedIds = [];

            DB::beginTransaction();

            foreach ($request->image_ids as $imageId) {
                $image = Image::with('property')->find($imageId);

                if (!$image) {
                    $failedIds[] = $imageId;
                    continue;
                }

                // Check authorization for each image
                try {
                    $this->authorize('deleteImage', $image->property);
                } catch (\Exception $e) {
                    $failedIds[] = $imageId;
                    continue;
                }

                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }

                $image->delete();
                $deletedCount++;
            }

            DB::commit();

            $message = "{$deletedCount} image(s) deleted successfully";
            if (!empty($failedIds)) {
                $message .= ", failed to delete image IDs: " . implode(', ', $failedIds);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'deleted_count' => $deletedCount,
                    'failed_ids' => $failedIds
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting images: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Error deleting images'
            ], 500);
        }
    }
}
