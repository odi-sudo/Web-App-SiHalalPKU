<?php

namespace App\Repositories;

use App\Models\Umkm;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UmkmRepository
{
    /**
     * @var Umkm
     */
    protected Umkm $model;

    /**
     * Constructor
     *
     * @param Umkm $model
     */
    public function __construct(Umkm $model)
    {
        $this->model = $model;
    }

    /**
     * Get all UMKM.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with(['produks', 'reviews'])->get();
    }

    /**
     * Get all UMKM with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['produks', 'reviews'])->paginate($perPage);
    }

    /**
     * Find UMKM by ID.
     *
     * @param int $id
     * @return Umkm|null
     */
    public function findById(int $id): ?Umkm
    {
        return $this->model->with(['produks', 'reviews.akun'])->find($id);
    }

    /**
     * Create new UMKM.
     *
     * @param array $data
     * @return Umkm
     */
    public function create(array $data): Umkm
    {
        return $this->model->create($data);
    }

    /**
     * Update UMKM.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $umkm = $this->findById($id);
        if (!$umkm) {
            return false;
        }
        return $umkm->update($data);
    }

    /**
     * Delete UMKM.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $umkm = $this->model->find($id);
        if (!$umkm) {
            return false;
        }
        return $umkm->delete();
    }

    /**
     * Get all coordinates for map.
     *
     * @return Collection
     */
    public function getCoordinates(): Collection
    {
        return $this->model->select('id', 'nama_usaha', 'alamat', 'latitude', 'longitude', 'foto_usaha')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
    }

    /**
     * Search UMKM by keyword.
     *
     * @param string $keyword
     * @return Collection
     */
    public function searchByKeyword(string $keyword): Collection
    {
        return $this->model->with(['produks', 'reviews'])
            ->where('nama_usaha', 'ILIKE', "%{$keyword}%")
            ->orWhere('alamat', 'ILIKE', "%{$keyword}%")
            ->orWhere('deskripsi', 'ILIKE', "%{$keyword}%")
            ->get();
    }

    /**
     * Search UMKM by product name.
     *
     * @param string $productName
     * @return Collection
     */
    public function searchByProductName(string $productName): Collection
    {
        return $this->model->with(['produks', 'reviews'])
            ->whereHas('produks', function ($query) use ($productName) {
                $query->where('nama_produk', 'ILIKE', "%{$productName}%");
            })
            ->get();
    }

    /**
     * Get UMKM sorted by average rating.
     *
     * @param string $direction
     * @return Collection
     */
    public function sortByRating(string $direction = 'desc'): Collection
    {
        return $this->model->with(['produks', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', $direction)
            ->get();
    }

    /**
     * Get UMKM sorted by distance from a point.
     *
     * @param float $lat
     * @param float $lng
     * @param string $direction
     * @return Collection
     */
    public function sortByDistance(float $lat, float $lng, string $direction = 'asc'): Collection
    {
        // Haversine formula for distance calculation
        $haversine = "(6371 * acos(cos(radians(?)) 
                     * cos(radians(latitude)) 
                     * cos(radians(longitude) - radians(?)) 
                     + sin(radians(?)) 
                     * sin(radians(latitude))))";

        return $this->model->with(['produks', 'reviews'])
            ->selectRaw("*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->orderBy('distance', $direction)
            ->get();
    }

    /**
     * Get UMKM count.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->model->count();
    }
}