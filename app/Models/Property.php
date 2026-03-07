<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'pieces',
        'surface',
        'prix',
        'ville',
        'description',
        'statut',
        'is_published',
        'title'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'pieces' => 'integer',
        'surface' => 'float',
        'prix' => 'decimal:2'
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate title when creating a property
        static::creating(function ($property) {
            $property->title = $property->type . ' ' .
                               $property->pieces . ' pièces à ' .
                               $property->ville;
        });

        // Update title only when relevant fields change
        static::updating(function ($property) {
            if ($property->isDirty(['type', 'pieces', 'ville'])) {
                $property->title = $property->type . ' ' .
                                   $property->pieces . ' pièces à ' .
                                   $property->ville;
            }
        });
    }

    /**
     * Get the user (agent) who owns this property.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images associated with this property.
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Scope a query to only include published properties.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include available properties.
     */
    public function scopeAvailable($query)
    {
        return $query->where('statut', 'disponible');
    }
}
