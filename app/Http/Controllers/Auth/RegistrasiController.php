<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AkunService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class RegistrasiController extends Controller
{
    /**
     * @var AkunService
     */
    protected AkunService $akunService;

    /**
     * Constructor
     *
     * @param AkunService $akunService
     */
    public function __construct(AkunService $akunService)
    {
        $this->akunService = $akunService;
    }

    /**
     * Display registration page.
     * Sesuai Sequence Diagram: bukaHalamanRegistrasi()
     *
     * @return View
     */
    public function bukaHalamanRegistrasi(): View
    {
        return view('auth.registrasi');
    }

    /**
     * Process registration.
     * Sesuai Sequence Diagram: prosesRegistrasi(data)
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function prosesRegistrasi(Request $request): RedirectResponse
    {
        // Validate request
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string',
        ]);

        try {
            // Check if email already exists (A1: Email Sudah Terdaftar)
            if ($this->akunService->cekEmail($request->email)) {
                return back()
                    ->withInput()
                    ->withErrors(['email' => 'Email sudah terdaftar']);
            }

            // Validate password confirmation (A2: Password Tidak Cocok)
            if (!$this->akunService->validasiPassword($request->password, $request->password_confirmation)) {
                return back()
                    ->withInput()
                    ->withErrors(['password_confirmation' => 'Konfirmasi password tidak cocok.']);
            }

            // Save user (E1: Gagal Menyimpan handled by exception)
            $this->akunService->simpanUser([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'Pengguna',
            ]);

            // Redirect to login with success message
            return redirect()
                ->route('login')
                ->with('success', 'Registrasi Berhasil');

        } catch (Exception $e) {
            // E1: Gagal Menyimpan ke Basis Data
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}