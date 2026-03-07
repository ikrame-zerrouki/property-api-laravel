<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'pieces' => $this->pieces,                     // number of rooms
            'surface' => $this->surface,                    // surface area in m²
            'prix' => $this->prix,                          // price
            'prix_formate' => number_format($this->prix, 0, ',', ' ') . ' DA', // formatted price
            'ville' => $this->ville,                        // city
            'description' => $this->description,
            'statut' => $this->statut,                      // status (disponible, vendu, location)
            'statut_label' => $this->getStatusLabel(),      // French status label
            'is_published' => $this->is_published,
            'surface_formate' => $this->surface . ' m²',    // formatted surface
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,

            // relationships
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'agent' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email
                ];
            }),
        ];
    }

    /**
     * Get status label in French.
     */
    private function getStatusLabel(): string
    {
        return match($this->statut) {
            'disponible' => 'Disponible',
            'vendu' => 'Vendu',
            'location' => 'À louer',
            default => $this->statut
        };
    }

    /**
     * Add additional data to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'success' => true,
        ];
    }
}
