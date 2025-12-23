<?php

namespace App\Http\Controllers;

use App\Services\UmkmService;
use App\Services\ProdukService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Exception;

class SearchController extends Controller
{
    /**
     * @var UmkmService
     */
    protected UmkmService $umkmService;

    /**
     * @var ProdukService
     */
    protected ProdukService $produkService;

    /**
     * Constructor
     *
     * @param UmkmService $umkmService
     * @param ProdukService $produkService
     */
    public function __construct(
        UmkmService $umkmService,
        ProdukService $produkService
    ) {
        $this->umkmService = $umkmService;
        $this->produkService = $produkService;
    }

    /**
     * Search UMKM and Products by keyword (like Grab search).
     * Sesuai Sequence Diagram: cariUmkm(keyword)
     *
     * @param Request $request
     * @return View
     */
    public function cariUmkm(Request $request): View
    {
        try {
            $keyword = $request->input('q', $request->input('keyword', ''));
            $sortBy = $request->input('sort', '');
            $userLat = $request->input('lat');
            $userLng = $request->input('lng');

            // If keyword is empty, show all UMKMs
            if (empty(trim($keyword))) {
                $allUmkms = $this->umkmService->getAll();
                
                // Apply sorting if specified
                if ($sortBy) {
                    $allUmkms = $this->umkmService->sortHasil(
                        collect($allUmkms),
                        $sortBy,
                        $userLat ? (float) $userLat : null,
                        $userLng ? (float) $userLng : null
                    );
                }
                
                return view('customer.hasil-pencarian', [
                    'results' => $allUmkms,
                    'keyword' => $keyword,
                    'type' => 'umkm',
                    'sortBy' => $sortBy
                ]);
            }

            // Search UMKM by name, address, and description
            $umkmResults = $this->umkmService->searchKeyword($keyword);
            
            // Search Products by name and get their UMKMs with matching products
            $produkResults = $this->produkService->searchByNameWithProducts($keyword);
            
            // Merge results: combine UMKM results with product search results
            // For UMKMs found via product search, attach matching products
            $mergedResults = collect();
            
            // Add UMKMs found directly
            foreach ($umkmResults as $umkm) {
                $umkm->matched_produks = collect();
                $mergedResults->push($umkm);
            }
            
            // Add UMKMs found via product search (or update existing with matching products)
            foreach ($produkResults as $umkmWithProducts) {
                $existing = $mergedResults->firstWhere('id', $umkmWithProducts->id);
                if ($existing) {
                    // Update existing UMKM with matched products
                    $existing->matched_produks = $umkmWithProducts->matched_produks;
                } else {
                    // Add new UMKM from product search
                    $mergedResults->push($umkmWithProducts);
                }
            }

            // A1: Pencarian Tidak Ditemukan
            if ($mergedResults->isEmpty()) {
                return view('customer.hasil-pencarian', [
                    'results' => collect(),
                    'keyword' => $keyword,
                    'type' => 'umkm',
                    'message' => 'Tidak Ditemukan'
                ]);
            }

            // A2: Pengguna Melakukan Sorting
            if ($sortBy) {
                $mergedResults = $this->umkmService->sortHasil(
                    $mergedResults,
                    $sortBy,
                    $userLat ? (float) $userLat : null,
                    $userLng ? (float) $userLng : null
                );
            }

            return view('customer.hasil-pencarian', [
                'results' => $mergedResults,
                'keyword' => $keyword,
                'type' => 'umkm',
                'sortBy' => $sortBy
            ]);

        } catch (Exception $e) {
            // E1: Gagal Terhubung
            return view('customer.hasil-pencarian', [
                'results' => collect(),
                'keyword' => $request->input('keyword', ''),
                'type' => 'umkm',
                'error' => 'Pencarian Gagal'
            ]);
        }
    }

    /**
     * Search products by name.
     * Sesuai Sequence Diagram: cariProduk(keyword)
     *
     * @param Request $request
     * @return View
     */
    public function cariProduk(Request $request): View
    {
        try {
            $keyword = $request->input('keyword', '');
            $sortBy = $request->input('sort', '');
            $userLat = $request->input('lat');
            $userLng = $request->input('lng');

            // Search products and get associated UMKMs
            $results = $this->produkService->searchByName($keyword);

            // A1: Produk Tidak Ditemukan
            if ($results->isEmpty()) {
                return view('customer.hasil-pencarian', [
                    'results' => collect(),
                    'keyword' => $keyword,
                    'type' => 'produk',
                    'message' => 'Produk tidak ditemukan'
                ]);
            }

            // A2: Pengguna Melakukan Sorting
            if ($sortBy) {
                $results = $this->umkmService->sortHasil(
                    $results,
                    $sortBy,
                    $userLat ? (float) $userLat : null,
                    $userLng ? (float) $userLng : null
                );
            }

            return view('customer.hasil-pencarian', [
                'results' => $results,
                'keyword' => $keyword,
                'type' => 'produk',
                'sortBy' => $sortBy
            ]);

        } catch (Exception $e) {
            // E1: Gagal Terhubung
            return view('customer.hasil-pencarian', [
                'results' => collect(),
                'keyword' => $request->input('keyword', ''),
                'type' => 'produk',
                'error' => 'Pencarian Gagal'
            ]);
        }
    }

    /**
     * AJAX search for autocomplete.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxSearch(Request $request): JsonResponse
    {
        try {
            $keyword = $request->input('keyword', '');
            $type = $request->input('type', 'umkm');

            if (strlen($keyword) < 2) {
                return response()->json(['success' => true, 'data' => []]);
            }

            if ($type === 'produk') {
                $results = $this->produkService->searchByName($keyword);
            } else {
                $results = $this->umkmService->searchKeyword($keyword);
            }

            return response()->json([
                'success' => true,
                'data' => $results->take(10)->map(function ($umkm) {
                    return [
                        'id' => $umkm->id,
                        'nama_usaha' => $umkm->nama_usaha,
                        'alamat_singkat' => $umkm->alamat_singkat,
                        'foto_usaha' => $umkm->foto_usaha ? asset($umkm->foto_usaha) : asset('images/default-umkm.jpg'),
                        'rating' => number_format($umkm->average_rating, 1),
                    ];
                })
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pencarian Gagal',
                'data' => []
            ], 500);
        }
    }
}