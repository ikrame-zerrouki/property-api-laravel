<?php

namespace App\DTOs;

class PropertyFilterDTO
{
    public function __construct(
        public readonly ?string $ville = null,
        public readonly ?string $type = null,
        public readonly ?float $prix_min = null,
        public readonly ?float $prix_max = null,
        public readonly ?string $statut = null,
        public readonly ?string $search = null,
        public readonly int $perPage = 15
    ) {}

    /**
     * Create DTO from request.
     */
    public static function fromRequest($request): self
    {
        return new self(
            ville: $request->ville,
            type: $request->type,
            prix_min: $request->prix_min ? (float) $request->prix_min : null,
            prix_max: $request->prix_max ? (float) $request->prix_max : null,
            statut: $request->statut,
            search: $request->search,
            perPage: $request->per_page ?? 15
        );
    }
}
