<?php

namespace App\Http\Controllers;

use App\Services\UmkmService;
use App\Services\AkunService;
use App\Services\ReviewService;
use Illuminate\View\View;
use Exception;

class AdminController extends Controller
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
     * @var ReviewService
     */
    protected ReviewService $reviewService;

    /**
     * Constructor
     *
     * @param UmkmService $umkmService
     * @param AkunService $akunService
     * @param ReviewService $reviewService
     */
    public function __construct(
        UmkmService $umkmService,
        AkunService $akunService,
        ReviewService $reviewService
    ) {
        $this->umkmService = $umkmService;
        $this->akunService = $akunService;
        $this->reviewService = $reviewService;
    }

    /**
     * Display admin dashboard.
     *
     * @return View
     */
    public function dashboard(): View
    {
        try {
            $statistics = $this->umkmService->getStatistics();
            $recentUmkms = $this->umkmService->getAll()->sortByDesc('created_at')->take(5);
            $currentUser = $this->akunService->getCurrentUser();

            return view('admin.dashboard', compact(
                'statistics',
                'recentUmkms',
                'currentUser'
            ));
        } catch (Exception $e) {
            return view('admin.dashboard', [
                'statistics' => [],
                'recentUmkms' => collect(),
                'currentUser' => null,
                'error' => 'Gagal memuat data'
            ]);
        }
    }
}