<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    /**
     * Determine whether the user can view any properties.
     */
    public function viewAny(User $user = null): bool
    {
        return true; // Everyone can view
    }

    /**
     * Determine whether the user can view a specific property.
     */
    public function view(?User $user, Property $property): bool
    {
        return true; // Everyone can view
    }

    /**
     * Determine whether the user can create a property.
     */
    public function create(User $user): bool
    {
        // Only admin and agent can create
        return in_array($user->role, ['admin', 'agent']);
    }

    /**
     * Determine whether the user can update a property.
     */
    public function update(User $user, Property $property): bool
    {
        // Admin can update any property
        if ($user->role === 'admin') {
            return true;
        }

        // Agent can only update their own properties
        if ($user->role === 'agent') {
            return $user->id === $property->user_id;
        }

        // Other roles cannot update
        return false;
    }

    /**
     * Determine whether the user can delete a property.
     */
    public function delete(User $user, Property $property): bool
    {
        // Admin can delete any property
        if ($user->role === 'admin') {
            return true;
        }

        // Agent can only delete their own properties
        if ($user->role === 'agent') {
            return $user->id === $property->user_id;
        }

        // Other roles cannot delete
        return false;
    }

    /**
     * Determine whether the user can publish/unpublish a property.
     */
    public function publish(User $user, Property $property): bool
    {
        // Admin can publish any property
        if ($user->role === 'admin') {
            return true;
        }

        // Agent can only publish their own properties
        if ($user->role === 'agent') {
            return $user->id === $property->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can upload images to a property.
     */
    public function uploadImages(User $user, Property $property): bool
    {
        // Admin can upload images to any property
        if ($user->role === 'admin') {
            return true;
        }

        // Agent can only upload images to their own properties
        if ($user->role === 'agent') {
            return $user->id === $property->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete an image from a property.
     */
    public function deleteImage(User $user, Property $property): bool
    {
        // Admin can delete images from any property
        if ($user->role === 'admin') {
            return true;
        }

        // Agent can only delete images from their own properties
        if ($user->role === 'agent') {
            return $user->id === $property->user_id;
        }

        return false;
    }
}
