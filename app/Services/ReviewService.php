<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\ReviewRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class ReviewService
{
    /**
     * @var ReviewRepository
     */
    protected ReviewRepository $reviewRepository;

    /**
     * Constructor
     *
     * @param ReviewRepository $reviewRepository
     */
    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * Get all reviews for a UMKM.
     * Sesuai Sequence Diagram: getReviews(id) -> return list review
     *
     * @param int $umkmId
     * @return Collection
     */
    public function getReviews(int $umkmId): Collection
    {
        return $this->reviewRepository->getByUmkmId($umkmId);
    }

    /**
     * Get review by ID.
     *
     * @param int $id
     * @return Review|null
     */
    public function getById(int $id): ?Review
    {
        return $this->reviewRepository->findById($id);
    }

    /**
     * Get reviews by account ID.
     *
     * @param int $akunId
     * @return Collection
     */
    public function getByAkunId(int $akunId): Collection
    {
        return $this->reviewRepository->getByAkunId($akunId);
    }

    /**
     * Create new review.
     *
     * @param array $data
     * @return Review
     * @throws Exception
     */
    public function create(array $data): Review
    {
        try {
            DB::beginTransaction();

            // Validate rating
            if (!isset($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
                throw new Exception('Peringkat wajib diisi.');
            }

            $review = $this->reviewRepository->create([
                'akun_id' => $data['akun_id'],
                'umkm_id' => $data['umkm_id'],
                'rating' => $data['rating'],
                'ulasan' => $data['ulasan'] ?? null,
            ]);

            DB::commit();
            return $review;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage() ?: 'Gagal Menyimpan');
        }
    }

    /**
     * Update review.
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update(int $id, array $data): bool
    {
        try {
            DB::beginTransaction();

            // Validate rating if provided
            if (isset($data['rating']) && ($data['rating'] < 1 || $data['rating'] > 5)) {
                throw new Exception('Peringkat harus antara 1-5.');
            }

            $result = $this->reviewRepository->update($id, $data);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage() ?: 'Gagal mengupdate ulasan');
        }
    }

    /**
     * Delete review.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            return $this->reviewRepository->delete($id);
        } catch (Exception $e) {
            throw new Exception('Gagal menghapus ulasan');
        }
    }

    /**
     * Get average rating for UMKM.
     *
     * @param int $umkmId
     * @return float
     */
    public function getAverageRating(int $umkmId): float
    {
        return $this->reviewRepository->getAverageRating($umkmId);
    }

    /**
     * Get review count for UMKM.
     *
     * @param int $umkmId
     * @return int
     */
    public function getReviewCount(int $umkmId): int
    {
        return $this->reviewRepository->getReviewCount($umkmId);
    }

    /**
     * Check if user has already reviewed UMKM.
     *
     * @param int $akunId
     * @param int $umkmId
     * @return bool
     */
    public function hasUserReviewed(int $akunId, int $umkmId): bool
    {
        return $this->reviewRepository->hasUserReviewed($akunId, $umkmId);
    }

    /**
     * Validate review data.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validateReviewData(array $data): array
    {
        $errors = [];

        if (!isset($data['rating']) || empty($data['rating'])) {
            $errors['rating'] = 'Peringkat wajib diisi.';
        } elseif ($data['rating'] < 1 || $data['rating'] > 5) {
            $errors['rating'] = 'Peringkat harus antara 1-5.';
        }

        if (!empty($errors)) {
            throw new Exception(implode(' ', $errors));
        }

        return $data;
    }
}