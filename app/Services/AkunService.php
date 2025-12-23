<?php

namespace App\Services;

use App\Models\Akun;
use App\Repositories\AkunRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class AkunService
{
    /**
     * @var AkunRepository
     */
    protected AkunRepository $akunRepository;

    /**
     * Constructor
     *
     * @param AkunRepository $akunRepository
     */
    public function __construct(AkunRepository $akunRepository)
    {
        $this->akunRepository = $akunRepository;
    }

    /**
     * Check if email already exists.
     * Sesuai Sequence Diagram: cekEmail(email) -> return boolean
     *
     * @param string $email
     * @return bool
     */
    public function cekEmail(string $email): bool
    {
        return $this->akunRepository->emailExists($email);
    }

    /**
     * Validate password confirmation.
     * Sesuai Sequence Diagram: validasiPassword(pass, confirm) -> return boolean
     *
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    public function validasiPassword(string $password, string $confirmPassword): bool
    {
        return $password === $confirmPassword;
    }

    /**
     * Save new user to database.
     * Sesuai Sequence Diagram: simpanUser(data) -> Eloquent create
     *
     * @param array $data
     * @return Akun
     * @throws Exception
     */
    public function simpanUser(array $data): Akun
    {
        try {
            DB::beginTransaction();

            $akun = $this->akunRepository->create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'Pengguna',
            ]);

            DB::commit();
            return $akun;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Terjadi Kesalahan Server');
        }
    }

    /**
     * Get account by email.
     * Sesuai Sequence Diagram: getAccount(email) -> return object akun
     *
     * @param string $email
     * @return Akun|null
     */
    public function getAccount(string $email): ?Akun
    {
        return $this->akunRepository->findByEmail($email);
    }

    /**
     * Check password against hash.
     * Sesuai Sequence Diagram: cekPassword(inputPass, dbHash) -> Hash check
     *
     * @param string $inputPassword
     * @param string $hashedPassword
     * @return bool
     */
    public function cekPassword(string $inputPassword, string $hashedPassword): bool
    {
        return Hash::check($inputPassword, $hashedPassword);
    }

    /**
     * Create authenticated session for user.
     * Sesuai Sequence Diagram: createSession(dataAkun)
     *
     * @param Akun $akun
     * @return void
     */
    public function createSession(Akun $akun): void
    {
        Auth::login($akun);
        session()->regenerate();
    }

    /**
     * Destroy user session (logout).
     *
     * @return void
     */
    public function destroySession(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * Get currently authenticated user.
     *
     * @return Akun|null
     */
    public function getCurrentUser(): ?Akun
    {
        return Auth::user();
    }

    /**
     * Check if user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Get account by ID.
     *
     * @param int $id
     * @return Akun|null
     */
    public function getAccountById(int $id): ?Akun
    {
        return $this->akunRepository->findById($id);
    }
}