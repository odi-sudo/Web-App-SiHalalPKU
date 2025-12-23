<?php

namespace App\Services;

use App\Models\Produk;
use App\Repositories\ProdukRepository;
use App\Repositories\UmkmRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProdukService
{
    /**
     * @var ProdukRepository
     */
    protected ProdukRepository $produkRepository;

    /**
     * @var UmkmRepository
     */
    protected UmkmRepository $umkmRepository;

    /**
     * Constructor
     *
     * @param ProdukRepository $produkRepository
     * @param UmkmRepository $umkmRepository
     */
    public function __construct(
        ProdukRepository $produkRepository,
        UmkmRepository $umkmRepository
    ) {
        $this->produkRepository = $produkRepository;
        $this->umkmRepository = $umkmRepository;
    }

    /**
     * Get all products.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->produkRepository->getAll();
    }

    /**
     * Get product by ID.
     *
     * @param int $id
     * @return Produk|null
     */
    public function getById(int $id): ?Produk
    {
        return $this->produkRepository->findById($id);
    }

    /**
     * Get products by UMKM ID.
     *
     * @param int $umkmId
     * @return Collection
     */
    public function getByUmkmId(int $umkmId): Collection
    {
        return $this->produkRepository->getByUmkmId($umkmId);
    }

    /**
     * Search products by name.
     * Sesuai Sequence Diagram: searchByName(keyword) -> Cari nama_produk (Join query)
     *
     * @param string $keyword
     * @return Collection
     */
    public function searchByName(string $keyword): Collection
    {
        $produks = $this->produkRepository->searchByName($keyword);
        
        // Group by UMKM and return unique UMKMs
        $umkmIds = $produks->pluck('umkm_id')->unique();
        
        return $this->umkmRepository->getAll()->whereIn('id', $umkmIds);
    }

    /**
     * Search products by name and return UMKMs with their matching products.
     * This is used for combined search (like Grab search).
     *
     * @param string $keyword
     * @return Collection
     */
    public function searchByNameWithProducts(string $keyword): Collection
    {
        $produks = $this->produkRepository->searchByName($keyword);
        
        // Group products by UMKM
        $groupedProduks = $produks->groupBy('umkm_id');
        
        // Get UMKMs and attach their matching products
        $umkmIds = $groupedProduks->keys();
        $umkms = $this->umkmRepository->getAll()->whereIn('id', $umkmIds);
        
        // Attach matched products to each UMKM
        return $umkms->map(function ($umkm) use ($groupedProduks) {
            $umkm->matched_produks = $groupedProduks->get($umkm->id, collect());
            return $umkm;
        });
    }

    /**
     * Create new product.
     *
     * @param array $data
     * @return Produk
     * @throws Exception
     */
    public function create(array $data): Produk
    {
        try {
            // Handle foto_produk upload
            if (isset($data['foto_produk']) && $data['foto_produk']) {
                $data['foto_produk'] = $this->uploadImage($data['foto_produk']);
            }

            return $this->produkRepository->create($data);
        } catch (Exception $e) {
            throw new Exception('Gagal menyimpan produk: ' . $e->getMessage());
        }
    }

    /**
     * Update product.
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update(int $id, array $data): bool
    {
        try {
            $produk = $this->produkRepository->findById($id);
            if (!$produk) {
                throw new Exception('Produk tidak ditemukan');
            }

            // Handle foto_produk upload
            if (isset($data['foto_produk']) && $data['foto_produk']) {
                // Delete old image
                if ($produk->foto_produk) {
                    $this->deleteImage($produk->foto_produk);
                }
                $data['foto_produk'] = $this->uploadImage($data['foto_produk']);
            }

            return $this->produkRepository->update($id, $data);
        } catch (Exception $e) {
            throw new Exception('Gagal mengupdate produk: ' . $e->getMessage());
        }
    }

    /**
     * Delete product.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            $produk = $this->produkRepository->findById($id);
            if (!$produk) {
                throw new Exception('Produk tidak ditemukan');
            }

            // Delete image
            if ($produk->foto_produk) {
                $this->deleteImage($produk->foto_produk);
            }

            return $this->produkRepository->delete($id);
        } catch (Exception $e) {
            throw new Exception('Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * Sort products by price.
     *
     * @param Collection $products
     * @param string $direction
     * @return Collection
     */
    public function sortByPrice(Collection $products, string $direction = 'asc'): Collection
    {
        if ($direction === 'asc') {
            return $products->sortBy('harga')->values();
        }
        return $products->sortByDesc('harga')->values();
    }

    /**
     * Upload image to storage.
     *
     * @param mixed $file
     * @return string
     */
    private function uploadImage($file): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/produk', $filename);
        return str_replace('public/', 'storage/', $path);
    }

    /**
     * Delete image from storage.
     *
     * @param string $path
     * @return bool
     */
    private function deleteImage(string $path): bool
    {
        $storagePath = str_replace('storage/', 'public/', $path);
        return Storage::delete($storagePath);
    }
}