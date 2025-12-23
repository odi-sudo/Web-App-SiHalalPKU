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
}