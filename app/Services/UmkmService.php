<?php

namespace App\Services;

use App\Models\Umkm;
use App\Repositories\UmkmRepository;
use App\Repositories\ProdukRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class UmkmService
{
    /**
     * @var UmkmRepository
     */
    protected UmkmRepository $umkmRepository;

    /**
     * @var ProdukRepository
     */
    protected ProdukRepository $produkRepository;

    /**
     * @var CloudinaryService
     */
    protected CloudinaryService $cloudinaryService;

    /**
     * Constructor
     *
     * @param UmkmRepository $umkmRepository
     * @param ProdukRepository $produkRepository
     * @param CloudinaryService $cloudinaryService
     */
    public function __construct(
        UmkmRepository $umkmRepository,
        ProdukRepository $produkRepository,
        CloudinaryService $cloudinaryService
    ) {
        $this->umkmRepository = $umkmRepository;
        $this->produkRepository = $produkRepository;
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Get all UMKM.
     * Sesuai Sequence Diagram: getAll() -> return collection
     *
     * @return EloquentCollection
     */
    public function getAll(): EloquentCollection
    {
        return $this->umkmRepository->getAll();
    }

    /**
     * Get all UMKM with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->umkmRepository->getAllPaginated($perPage);
    }

    /**
     * Get UMKM detail by ID.
     * Sesuai Sequence Diagram: getDetail(id) -> return UMKM + relasi
     *
     * @param int $id
     * @return Umkm|null
     */
    public function getDetail(int $id): ?Umkm
    {
        return $this->umkmRepository->findById($id);
    }

    /**
     * Insert new UMKM with products (transactional).
     * Sesuai Sequence Diagram: insert(data) -> Transactional insert (Umkm & Produk)
     *
     * @param array $data
     * @param array $produks
     * @return Umkm
     * @throws Exception
     */
    public function insert(array $data, array $produks = []): Umkm
    {
        try {
            DB::beginTransaction();

            // Handle foto_usaha upload
            if (isset($data['foto_usaha']) && $data['foto_usaha']) {
                $data['foto_usaha'] = $this->uploadImage($data['foto_usaha'], 'umkm');
            }

            // Create UMKM
            $umkm = $this->umkmRepository->create($data);

            // Create products if provided
            if (!empty($produks)) {
                foreach ($produks as $produk) {
                    // Handle foto_produk upload
                    if (isset($produk['foto_produk']) && $produk['foto_produk']) {
                        $produk['foto_produk'] = $this->uploadImage($produk['foto_produk'], 'produk');
                    }
                    
                    $produk['umkm_id'] = $umkm->id;
                    $this->produkRepository->create($produk);
                }
            }

            DB::commit();
            return $umkm;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Gagal Memproses Data: ' . $e->getMessage());
        }
    }

    /**
     * Update UMKM data.
     * Sesuai Sequence Diagram: update(id, data)
     *
     * @param int $id
     * @param array $data
     * @param array $produks
     * @return bool
     * @throws Exception
     */
    public function update(int $id, array $data, array $produks = []): bool
    {
        try {
            DB::beginTransaction();

            $umkm = $this->umkmRepository->findById($id);
            if (!$umkm) {
                throw new Exception('Data tidak ditemukan');
            }

            // Handle foto_usaha upload
            if (isset($data['foto_usaha']) && $data['foto_usaha']) {
                // Delete old image
                if ($umkm->foto_usaha) {
                    $this->deleteImage($umkm->foto_usaha);
                }
                $data['foto_usaha'] = $this->uploadImage($data['foto_usaha'], 'umkm');
            }

            // Update UMKM
            $this->umkmRepository->update($id, $data);

            // Add new products if provided (without deleting existing ones)
            if (!empty($produks)) {
                // Only add new products, don't delete existing ones
                foreach ($produks as $produk) {
                    if (isset($produk['foto_produk']) && $produk['foto_produk']) {
                        $produk['foto_produk'] = $this->uploadImage($produk['foto_produk'], 'produk');
                    }
                    
                    $produk['umkm_id'] = $id;
                    $this->produkRepository->create($produk);
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Gagal Memproses Data: ' . $e->getMessage());
        }
    }

    /**
     * Delete UMKM.
     * Sesuai Sequence Diagram: delete(id)
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $umkm = $this->umkmRepository->findById($id);
            if (!$umkm) {
                throw new Exception('Data tidak ditemukan');
            }

            // Delete images
            if ($umkm->foto_usaha) {
                $this->deleteImage($umkm->foto_usaha);
            }

            foreach ($umkm->produks as $produk) {
                if ($produk->foto_produk) {
                    $this->deleteImage($produk->foto_produk);
                }
            }

            // Delete UMKM (cascade will delete products and reviews)
            $result = $this->umkmRepository->delete($id);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Gagal Memproses Data: ' . $e->getMessage());
        }
    }

    /**
     * Get all coordinates for map.
     * Sesuai Sequence Diagram: getCoordinates() -> return JSON lat/long
     *
     * @return EloquentCollection
     */
    public function getCoordinates(): EloquentCollection
    {
        return $this->umkmRepository->getCoordinates();
    }

    /**
     * Search UMKM by keyword.
     * Sesuai Sequence Diagram: searchKeyword(keyword) -> Cari nama_usaha (LIKE query)
     *
     * @param string $keyword
     * @return EloquentCollection
     */
    public function searchKeyword(string $keyword): EloquentCollection
    {
        return $this->umkmRepository->searchByKeyword($keyword);
    }

    /**
     * Sort search results.
     * Sesuai Sequence Diagram: sortHasil(parameter) -> Logic sorting
     *
     * @param Collection $results
     * @param string $parameter
     * @param float|null $userLat
     * @param float|null $userLng
     * @return Collection
     */
    public function sortHasil(Collection $results, string $parameter, ?float $userLat = null, ?float $userLng = null): Collection
    {
        switch ($parameter) {
            case 'rating_tertinggi':
                return $results->sortByDesc(function ($umkm) {
                    return $umkm->average_rating;
                })->values();

            case 'rating_terendah':
                return $results->sortBy(function ($umkm) {
                    return $umkm->average_rating;
                })->values();

            case 'jarak_terdekat':
                if ($userLat && $userLng) {
                    return $results->sortBy(function ($umkm) use ($userLat, $userLng) {
                        return $this->calculateDistance(
                            $userLat,
                            $userLng,
                            $umkm->latitude,
                            $umkm->longitude
                        );
                    })->values();
                }
                return $results;


            case 'harga_terendah':
                return $results->sortBy(function ($umkm) {
                    $minPrice = $umkm->produks->min('harga');
                    return $minPrice ?? PHP_INT_MAX;
                })->values();

            case 'harga_tertinggi':
                return $results->sortByDesc(function ($umkm) {
                    $maxPrice = $umkm->produks->max('harga');
                    return $maxPrice ?? 0;
                })->values();

            default:
                return $results;
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Distance in kilometers
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Upload image to Cloudinary.
     *
     * @param mixed $file
     * @param string $folder
     * @return string
     */
    private function uploadImage($file, string $folder): string
    {
        return $this->cloudinaryService->uploadImage($file, $folder);
    }

    /**
     * Delete image from Cloudinary.
     *
     * @param string $url
     * @return bool
     */
    private function deleteImage(string $url): bool
    {
        return $this->cloudinaryService->deleteImage($url);
    }

    /**
     * Get UMKM statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $all = $this->getAll();
        
        return [
            'total' => $all->count(),
            'total_umkm' => $all->count(),
            'tersertifikasi' => $all->where('status_halal', true)->count(),
            'total_produk' => $all->sum(function ($umkm) {
                return $umkm->produks->count();
            }),
            'rata_rata_rating' => $all->avg('average_rating'),
        ];
    }
}