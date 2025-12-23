<?php

namespace App\Repositories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository
{
    /**
     * @var Review
     */
    protected Review $model;

    /**
     * Constructor
     *
     * @param Review $model
     */
    public function __construct(Review $model)
    {
        $this->model = $model;
    }

    /**
     * Get all reviews.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with(['akun', 'umkm'])->get();
    }

    /**
     * Find review by ID.
     *
     * @param int $id
     * @return Review|null
     */
    public function findById(int $id): ?Review
    {
        return $this->model->with(['akun', 'umkm'])->find($id);
    }

    /**
     * Get reviews by UMKM ID.
     *
     * @param int $umkmId
     * @return Collection
     */
    public function getByUmkmId(int $umkmId): Collection
    {
        return $this->model->with('akun')
            ->where('umkm_id', $umkmId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get reviews by account ID.
     *
     * @param int $akunId
     * @return Collection
     */
    public function getByAkunId(int $akunId): Collection
    {
        return $this->model->with('umkm')
            ->where('akun_id', $akunId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create new review.
     *
     * @param array $data
     * @return Review
     */
    public function create(array $data): Review
    {
        return $this->model->create($data);
    }

    /**
     * Update review.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $review = $this->findById($id);
        if (!$review) {
            return false;
        }
        return $review->update($data);
    }

    /**
     * Delete review.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $review = $this->model->find($id);
        if (!$review) {
            return false;
        }
        return $review->delete();
    }

    /**
     * Get average rating for UMKM.
     *
     * @param int $umkmId
     * @return float
     */
    public function getAverageRating(int $umkmId): float
    {
        return $this->model->where('umkm_id', $umkmId)->avg('rating') ?? 0;
    }

    /**
     * Get review count for UMKM.
     *
     * @param int $umkmId
     * @return int
     */
    public function getReviewCount(int $umkmId): int
    {
        return $this->model->where('umkm_id', $umkmId)->count();
    }

    /**
     * Check if user has reviewed UMKM.
     *
     * @param int $akunId
     * @param int $umkmId
     * @return bool
     */
    public function hasUserReviewed(int $akunId, int $umkmId): bool
    {
        return $this->model->where('akun_id', $akunId)
            ->where('umkm_id', $umkmId)
            ->exists();
    }
}