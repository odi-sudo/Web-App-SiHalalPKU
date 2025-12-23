<?php

namespace App\Http\Controllers;

use App\Services\UmkmService;
use App\Services\ProdukService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Exception;

class UmkmController extends Controller
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
     * Display list of UMKM (Admin).
     * Sesuai Sequence Diagram: requestDaftarUmkm()
     *
     * @return View
     */
    public function requestDaftarUmkm(): View
    {
        try {
            $umkms = $this->umkmService->getAllPaginated(10);
            return view('admin.umkm.index', compact('umkms'));
        } catch (Exception $e) {
            return view('admin.umkm.index', [
                'umkms' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'error' => 'Gagal Memproses Data'
            ]);
        }
    }

    /**
     * Display empty form for creating UMKM.
     * Sesuai Sequence Diagram: tampilkanFormKosong()
     *
     * @return View
     */
    public function tampilkanFormKosong(): View
    {
        return view('admin.umkm.create');
    }

    /**
     * Store new UMKM data.
     * Sesuai Sequence Diagram: simpanDataBaru(data)
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function simpanDataBaru(Request $request): RedirectResponse
    {
        // Validate request
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'nullable|string|max:50',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'deskripsi' => 'nullable|string',
            'foto_usaha' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status_halal' => 'boolean',
            'produks' => 'nullable|array',
            'produks.*.nama_produk' => 'required_with:produks|string|max:255',
            'produks.*.harga' => 'required_with:produks|numeric|min:0',
            'produks.*.foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Prepare UMKM data
            $umkmData = [
                'nama_usaha' => $request->nama_usaha,
                'alamat' => $request->alamat,
                'kontak' => $request->kontak,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'deskripsi' => $request->deskripsi,
                'status_halal' => $request->input('status_halal', '1') == '1',
                'foto_usaha' => $request->file('foto_usaha'),
            ];

            // Prepare products data
            $produks = [];
            if ($request->has('produks')) {
                foreach ($request->produks as $index => $produk) {
                    $produks[] = [
                        'nama_produk' => $produk['nama_produk'],
                        'harga' => $produk['harga'],
                        'foto_produk' => $request->file("produks.{$index}.foto_produk"),
                    ];
                }
            }

            // Insert UMKM with products
            $this->umkmService->insert($umkmData, $produks);

            return redirect()
                ->route('admin.umkm.index')
                ->with('success', 'Berhasil disimpan');

        } catch (Exception $e) {
            // E1: Gagal Terhubung
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display confirmation dialog for delete.
     * Sesuai Sequence Diagram: tampilkanKonfirmasi()
     *
     * @param int $id
     * @return JsonResponse
     */
    public function tampilkanKonfirmasi(int $id): JsonResponse
    {
        try {
            $umkm = $this->umkmService->getDetail($id);
            
            if (!$umkm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Apakah Anda yakin ingin menghapus data ini?',
                'data' => [
                    'id' => $umkm->id,
                    'nama_usaha' => $umkm->nama_usaha
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Memproses Data'
            ], 500);
        }
    }

    /**
     * Delete UMKM data.
     * Sesuai Sequence Diagram: hapusData(id)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function hapusData(int $id): RedirectResponse
    {
        try {
            $this->umkmService->delete($id);

            return redirect()
                ->route('admin.umkm.index')
                ->with('success', 'Berhasil Dihapus.');

        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display form with existing data for editing.
     * Sesuai Sequence Diagram: tampilkanFormIsi(id)
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function tampilkanFormIsi(int $id): View|RedirectResponse
    {
        try {
            $umkm = $this->umkmService->getDetail($id);

            if (!$umkm) {
                return redirect()
                    ->route('admin.umkm.index')
                    ->withErrors(['error' => 'Data tidak ditemukan']);
            }

            return view('admin.umkm.edit', compact('umkm'));

        } catch (Exception $e) {
            return redirect()
                ->route('admin.umkm.index')
                ->withErrors(['error' => 'Gagal Memproses Data']);
        }
    }

    /**
     * Update UMKM data.
     * Sesuai Sequence Diagram: updateData(id, data)
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateData(Request $request, int $id): RedirectResponse
    {
        // Validate request
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'nullable|string|max:50',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'deskripsi' => 'nullable|string',
            'foto_usaha' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status_halal' => 'boolean',
            'produks' => 'nullable|array',
            'produks.*.nama_produk' => 'required_with:produks|string|max:255',
            'produks.*.harga' => 'required_with:produks|numeric|min:0',
            'produks.*.foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Prepare UMKM data
            $umkmData = [
                'nama_usaha' => $request->nama_usaha,
                'alamat' => $request->alamat,
                'kontak' => $request->kontak,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'deskripsi' => $request->deskripsi,
                'status_halal' => $request->input('status_halal', '1') == '1',
            ];

            // Add foto_usaha only if new file uploaded
            if ($request->hasFile('foto_usaha')) {
                $umkmData['foto_usaha'] = $request->file('foto_usaha');
            }

            // Prepare products data
            $produks = [];
            if ($request->has('produks')) {
                foreach ($request->produks as $index => $produk) {
                    $produks[] = [
                        'nama_produk' => $produk['nama_produk'],
                        'harga' => $produk['harga'],
                        'foto_produk' => $request->file("produks.{$index}.foto_produk"),
                    ];
                }
            }

            // Update UMKM with products
            $this->umkmService->update($id, $umkmData, $produks);

            return redirect()
                ->route('admin.umkm.index')
                ->with('success', 'Berhasil diperbarui.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}