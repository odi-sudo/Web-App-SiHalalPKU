<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Umkm extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Umkm';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_usaha',
        'alamat',
        'kontak',
        'status_halal',
        'latitude',
        'longitude',
        'deskripsi',
        'foto_usaha',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status_halal' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'foto_usaha_url',
    ];

    /**
     * Get all products for this UMKM.
     *
     * @return HasMany
     */
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'umkm_id');
    }

    /**
     * Get all reviews for this UMKM.
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'umkm_id');
    }

    /**
     * Calculate average rating for this UMKM.
     *
     * @return float
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total review count.
     *
     * @return int
     */
    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get formatted address (short version).
     *
     * @return string
     */
    public function getAlamatSingkatAttribute(): string
    {
        $parts = explode(',', $this->alamat);
        return trim($parts[0] ?? $this->alamat);
    }

    /**
     * Get foto usaha URL (supports both Cloudinary and local storage).
     *
     * @return string|null
     */
    public function getFotoUsahaUrlAttribute(): ?string
    {
        if (empty($this->foto_usaha)) {
            return null;
        }

        // If it's already a full URL (Cloudinary), return as is
        if (str_starts_with($this->foto_usaha, 'http://') || str_starts_with($this->foto_usaha, 'https://')) {
            return $this->foto_usaha;
        }

        // Local storage path - remove 'storage/' prefix if exists
        $path = str_replace('storage/', '', $this->foto_usaha);
        return asset('storage/' . $path);
    }
}