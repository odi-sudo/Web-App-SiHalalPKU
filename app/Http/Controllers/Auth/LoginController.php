<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AkunService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class LoginController extends Controller
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
     * Display login page.
     *
     * @return View
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Verify login credentials.
     * Sesuai Sequence Diagram: verifikasiLogin(email, pass)
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function verifikasiLogin(Request $request): RedirectResponse
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            // Get account by email
            $akun = $this->akunService->getAccount($request->email);

            // A1: Login Tidak Valid - Account not found
            if (!$akun) {
                return back()
                    ->withInput(['email' => $request->email])
                    ->withErrors(['login' => 'Username atau password Salah.']);
            }

            // A1: Login Tidak Valid - Password incorrect
            if (!$this->akunService->cekPassword($request->password, $akun->password)) {
                return back()
                    ->withInput(['email' => $request->email])
                    ->withErrors(['login' => 'Username atau password Salah.']);
            }

            // Create session
            $this->akunService->createSession($akun);

            // Redirect based on role
            if ($akun->isAdministrator()) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('success', 'Selamat datang, ' . $akun->nama);
            }

            return redirect()
                ->route('customer.dashboard')
                ->with('success', 'Selamat datang, ' . $akun->nama);

        } catch (Exception $e) {
            // E1: Gagal Terhubung ke Basis Data
            return back()
                ->withInput(['email' => $request->email])
                ->withErrors(['login' => 'Gangguan Server']);
        }
    }

    /**
     * Logout user.
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        $this->akunService->destroySession();

        return redirect()
            ->route('home')
            ->with('success', 'Anda telah berhasil logout.');
    }
}