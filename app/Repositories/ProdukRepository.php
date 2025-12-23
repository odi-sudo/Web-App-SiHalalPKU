<?php

namespace App\Repositories;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Collection;

class ProdukRepository
{
    /**
     * @var Produk
     */
    protected Produk $model;

    /**
     * Constructor
     *
     * @param Produk $model
     */
    public function __construct(Produk $model)
    {
        $this->model = $model;
    }

    /**
     * Get all products.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with('umkm')->get();
    }

    /**
     * Find product by ID.
     *
     * @param int $id
     * @return Produk|null
     */
    public function findById(int $id): ?Produk
    {
        return $this->model->with('umkm')->find($id);
    }

    /**
     * Get products by UMKM ID.
     *
     * @param int $umkmId
     * @return Collection
     */
    public function getByUmkmId(int $umkmId): Collection
    {
        return $this->model->where('umkm_id', $umkmId)->get();
    }

    /**
     * Create new product.
     *
     * @param array $data
     * @return Produk
     */
    public function create(array $data): Produk
    {
        return $this->model->create($data);
    }

    /**
     * Create multiple products.
     *
     * @param array $products
     * @return bool
     */
    public function createMany(array $products): bool
    {
        return $this->model->insert($products);
    }

    /**
     * Update product.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $produk = $this->findById($id);
        if (!$produk) {
            return false;
        }
        return $produk->update($data);
    }

    /**
     * Delete product.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $produk = $this->model->find($id);
        if (!$produk) {
            return false;
        }
        return $produk->delete();
    }

    /**
     * Delete all products by UMKM ID.
     *
     * @param int $umkmId
     * @return int
     */
    public function deleteByUmkmId(int $umkmId): int
    {
        return $this->model->where('umkm_id', $umkmId)->delete();
    }

    /**
     * Search products by name.
     *
     * @param string $keyword
     * @return Collection
     */
    public function searchByName(string $keyword): Collection
    {
        return $this->model->with('umkm')
            ->where('nama_produk', 'ILIKE', "%{$keyword}%")
            ->get();
    }

    /**
     * Get products sorted by price.
     *
     * @param string $direction
     * @return Collection
     */
    public function sortByPrice(string $direction = 'asc'): Collection
    {
        return $this->model->with('umkm')
            ->orderBy('harga', $direction)
            ->get();
    }
}