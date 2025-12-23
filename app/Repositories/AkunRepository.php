<?php

namespace App\Repositories;

use App\Models\Akun;
use Illuminate\Database\Eloquent\Collection;

class AkunRepository
{
    /**
     * @var Akun
     */
    protected Akun $model;

    /**
     * Constructor
     *
     * @param Akun $model
     */
    public function __construct(Akun $model)
    {
        $this->model = $model;
    }

    /**
     * Find account by email.
     *
     * @param string $email
     * @return Akun|null
     */
    public function findByEmail(string $email): ?Akun
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find account by ID.
     *
     * @param int $id
     * @return Akun|null
     */
    public function findById(int $id): ?Akun
    {
        return $this->model->find($id);
    }

    /**
     * Check if email exists.
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return $this->model->where('email', $email)->exists();
    }

    /**
     * Create new account.
     *
     * @param array $data
     * @return Akun
     */
    public function create(array $data): Akun
    {
        return $this->model->create($data);
    }

    /**
     * Update account.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $akun = $this->findById($id);
        if (!$akun) {
            return false;
        }
        return $akun->update($data);
    }

    /**
     * Delete account.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $akun = $this->findById($id);
        if (!$akun) {
            return false;
        }
        return $akun->delete();
    }

    /**
     * Get all accounts.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get accounts by role.
     *
     * @param string $role
     * @return Collection
     */
    public function getByRole(string $role): Collection
    {
        return $this->model->where('role', $role)->get();
    }
}