<?php

namespace App\Http\Controllers;

use App\Services\UmkmService;
use App\Services\ReviewService;
use App\Services\AkunService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Exception;

class ProfilController extends Controller
{
    /**
     * @var UmkmService
     */
    protected UmkmService $umkmService;

    /**
     * @var ReviewService
     */
    protected ReviewService $reviewService;

    /**
     * @var AkunService
     */
    protected AkunService $akunService;

    /**
     * Constructor
     *
     * @param UmkmService $umkmService
     * @param ReviewService $reviewService
     * @param AkunService $akunService
     */
    public function __construct(
        UmkmService $umkmService,
        ReviewService $reviewService,
        AkunService $akunService
    ) {
        $this->umkmService = $umkmService;
        $this->reviewService = $reviewService;
        $this->akunService = $akunService;
    }

    /**
     * Display UMKM detail profile.
     * Sesuai Sequence Diagram: requestDetail(id)
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function requestDetail(int $id): View|RedirectResponse
    {
        try {
            // Get UMKM detail with relations
            $umkm = $this->umkmService->getDetail($id);

            // A1: Data Tidak Ditemukan
            if (!$umkm) {
                return redirect()
                    ->route('customer.dashboard')
                    ->withErrors(['error' => 'Data tidak ditemukan']);
            }

            // Get reviews for this UMKM
            $reviews = $this->reviewService->getReviews($id);
            $averageRating = $this->reviewService->getAverageRating($id);
            $reviewCount = $this->reviewService->getReviewCount($id);

            // Check if current user has reviewed
            $hasReviewed = false;
            $currentUser = $this->akunService->getCurrentUser();
            if ($currentUser) {
                $hasReviewed = $this->reviewService->hasUserReviewed($currentUser->id, $id);
            }

            // Store the referrer URL for the back button
            // Only update if coming from a different page (not the same profil-umkm page)
            $previousUrl = url()->previous();
            $currentUrl = url()->current();
            
            if ($previousUrl !== $currentUrl && !str_contains($previousUrl, '/umkm/' . $id)) {
                session(['profil_umkm_back_url' => $previousUrl]);
            }
            
            // Get the stored back URL or default to home
            $backUrl = session('profil_umkm_back_url', route('home'));

            return view('customer.profil-umkm', compact(
                'umkm',
                'reviews',
                'averageRating',
                'reviewCount',
                'hasReviewed',
                'currentUser',
                'backUrl'
            ));

        } catch (Exception $e) {
            // E1: Gagal Memuat Profil
            return redirect()
                ->route('customer.dashboard')
                ->withErrors(['error' => 'Gagal memuat Data']);
        }
    }

    /**
     * Store new review.
     * Sesuai Sequence Diagram: simpanUlasan(data)
     *
     * @param Request $request
     * @param int $umkmId
     * @return RedirectResponse
     */
    public function simpanUlasan(Request $request, int $umkmId): RedirectResponse
    {
        // A3: Gagal Rating karena Belum Login
        if (!$this->akunService->isAuthenticated()) {
            return redirect()
                ->route('login')
                ->with('warning', 'Harap login terlebih dahulu');
        }

        try {
            // Validate request
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'ulasan' => 'nullable|string|max:1000',
            ]);

            $currentUser = $this->akunService->getCurrentUser();

            // A4: Peringkat Kosong - handled by validation above
            if (!$request->rating) {
                return back()->withErrors(['rating' => 'Peringkat wajib diisi.']);
            }

            // Create review
            $this->reviewService->create([
                'akun_id' => $currentUser->id,
                'umkm_id' => $umkmId,
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
            ]);

            return redirect()
                ->route('profil.detail', $umkmId)
                ->with('success', 'Terima kasih');

        } catch (Exception $e) {
            // E2: Gagal Menyimpan Ulasan
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get reviews as JSON for AJAX.
     *
     * @param int $umkmId
     * @return JsonResponse
     */
    public function getReviewsJson(int $umkmId): JsonResponse
    {
        try {
            $reviews = $this->reviewService->getReviews($umkmId);

            return response()->json([
                'success' => true,
                'data' => $reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'nama' => $review->akun->nama,
                        'rating' => $review->rating,
                        'ulasan' => $review->ulasan,
                        'created_at' => $review->created_at->format('d M Y'),
                        'stars_html' => $review->stars_html,
                    ];
                }),
                'average_rating' => $this->reviewService->getAverageRating($umkmId),
                'review_count' => $this->reviewService->getReviewCount($umkmId),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat ulasan',
                'data' => []
            ], 500);
        }
    }
}