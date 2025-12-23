<?php

namespace App\Http\Controllers;

use App\Services\UmkmService;
use App\Services\AkunService;
use Illuminate\View\View;
use Exception;

class CustomerController extends Controller
{
    /**
     * @var UmkmService
     */
    protected UmkmService $umkmService;

    /**
     * @var AkunService
     */
    protected AkunService $akunService;

    /**
     * Constructor
     *
     * @param UmkmService $umkmService
     * @param AkunService $akunService
     */
    public function __construct(
        UmkmService $umkmService,
        AkunService $akunService
    ) {
        $this->umkmService = $umkmService;
        $this->akunService = $akunService;
    }

    /**
     * Display customer dashboard.
     *
     * @return View
     */
    public function dashboard(): View
    {
        try {
            $coordinates = $this->umkmService->getCoordinates();
            $statistics = $this->umkmService->getStatistics();
            $umkms = $this->umkmService->getAll()->take(6);
            $currentUser = $this->akunService->getCurrentUser();

            return view('customer.dashboard', compact(
                'coordinates',
                'statistics',
                'umkms',
                'currentUser'
            ));
        } catch (Exception $e) {
            return view('customer.dashboard', [
                'coordinates' => collect(),
                'statistics' => [],
                'umkms' => collect(),
                'currentUser' => null,
                'error' => 'Gagal memuat data'
            ]);
        }
    }

    /**
     * Display home page (public).
     *
     * @return View
     */
    public function home(): View
    {
        try {
            $coordinates = $this->umkmService->getCoordinates();
            $statistics = $this->umkmService->getStatistics();
            $umkms = $this->umkmService->getAll()->take(6);

            return view('customer.home', compact(
                'coordinates',
                'statistics',
                'umkms'
            ));
        } catch (Exception $e) {
            return view('customer.home', [
                'coordinates' => collect(),
                'statistics' => [],
                'umkms' => collect(),
                'error' => 'Gagal memuat data'
            ]);
        }
    }
}