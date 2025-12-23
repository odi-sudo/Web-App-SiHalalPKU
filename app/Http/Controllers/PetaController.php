<?php

namespace App\Http\Controllers;

use App\Services\UmkmService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Exception;

class PetaController extends Controller
{
    /**
     * @var UmkmService
     */
    protected UmkmService $umkmService;

    /**
     * Constructor
     *
     * @param UmkmService $umkmService
     */
    public function __construct(UmkmService $umkmService)
    {
        $this->umkmService = $umkmService;
    }

    /**
     * Load interactive map page.
     * Sesuai Sequence Diagram: loadPeta()
     *
     * @return View
     */
    public function loadPeta(): View
    {
        try {
            $coordinates = $this->umkmService->getCoordinates();
            $statistics = $this->umkmService->getStatistics();
            
            return view('customer.peta', compact('coordinates', 'statistics'));
        } catch (Exception $e) {
            // E1: Gagal Memuat API Peta atau Data Lokasi
            return view('customer.peta', [
                'coordinates' => collect(),
                'statistics' => [],
                'error' => 'Gagal memuat peta'
            ]);
        }
    }

    /**
     * Get coordinates as JSON for AJAX requests.
     *
     * @return JsonResponse
     */
    public function getCoordinatesJson(): JsonResponse
    {
        try {
            $coordinates = $this->umkmService->getCoordinates();
            
            return response()->json([
                'success' => true,
                'data' => $coordinates->map(function ($umkm) {
                    return [
                        'id' => $umkm->id,
                        'nama_usaha' => $umkm->nama_usaha,
                        'alamat' => $umkm->alamat,
                        'alamat_singkat' => $umkm->alamat_singkat,
                        'latitude' => (float) $umkm->latitude,
                        'longitude' => (float) $umkm->longitude,
                        'foto_usaha' => $umkm->foto_usaha ? asset($umkm->foto_usaha) : asset('images/default-umkm.jpg'),
                    ];
                })
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat peta',
                'data' => []
            ], 500);
        }
    }
}