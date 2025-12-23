@extends('layouts.app')

@section('title', $umkm->nama_usaha . ' - SiHalalPKU')

@section('content')
<!-- Navbar -->
@include('components.navbar')

<div class="pt-[47px] md:pt-[85px]">
    <!-- Hero Image with Back Button -->
    <div class="relative h-[150px] md:h-[278px] overflow-hidden">
        <!-- Back Button Bar -->
        <div class="absolute top-0 left-0 right-0 h-[30px] md:h-[50px] bg-gradient-to-b from-[#2d7e37] to-[#18471d] z-10 flex items-center px-2 md:px-4 rounded-b-[15px] md:rounded-b-[30px]">
            <a href="{{ $backUrl ?? route('home') }}" class="flex items-center gap-1 md:gap-2 text-white hover:opacity-80 transition-opacity">
                <svg class="w-[18px] h-[18px] md:w-[30px] md:h-[30px]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
                <span class="text-xs md:text-base font-bold">Kembali</span>
            </a>
        </div>
        
        <!-- Background Image -->
        @if($umkm->foto_usaha)
            <img src="{{ asset('storage/' . $umkm->foto_usaha) }}" 
                 alt="{{ $umkm->nama_usaha }}" 
                 class="w-full h-full object-cover">
        @else
            <img src="{{ asset('images/placeholder_umkm.webp') }}" 
                 alt="{{ $umkm->nama_usaha }}" 
                 class="w-full h-full object-cover">
        @endif
    </div>

    <!-- Content -->
    <div class="w-full px-3 md:px-6 py-4 md:py-6 relative">
        <!-- UMKM Info -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-4 md:mb-6">
            <div class="flex-1 mb-4 md:mb-0">
                <!-- Title -->
                <h1 class="text-xl md:text-5xl font-bold text-black drop-shadow-md mb-1 md:mb-2">
                    {{ $umkm->nama_usaha }}
                </h1>

                <!-- Halal Badge -->
                @if($umkm->status_halal)
                    <div class="bg-[#bbe6c0] border border-[#0a440d] rounded px-2 md:px-3 py-0.5 md:py-1 inline-block mb-2 md:mb-3">
                        <span class="text-[#300938] text-[8px] md:text-sm font-semibold">Tersertifikasi</span>
                    </div>
                @endif

                <!-- Rating -->
                <div class="flex items-center gap-1 md:gap-2 mb-1 md:mb-2">
                    <svg class="w-[15px] h-[15px] md:w-[25px] md:h-[25px] text-[#fbbf24]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="text-[8px] md:text-sm font-semibold text-black">{{ number_format($averageRating, 1) }} ({{ $reviewCount }} Ulasan)</span>
                </div>

                <!-- Address -->
                <div class="flex items-center gap-1 md:gap-2 mb-1 md:mb-2">
                    <svg class="w-[15px] h-[15px] md:w-[25px] md:h-[25px] text-[#2d7e37]" fill="#F24822" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span class="text-[8px] md:text-sm font-semibold text-black">{{ $umkm->alamat }}</span>
                </div>

                <!-- Phone -->
                @if($umkm->kontak)
                    <div class="flex items-center gap-1 md:gap-2">
                        <svg class="w-[15px] h-[15px] md:w-[25px] md:h-[25px] text-[#2d7e37]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 00-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                        </svg>
                        <span class="text-[8px] md:text-sm font-semibold text-black">{{ $umkm->kontak }}</span>
                    </div>
                @endif
            </div>

            <!-- Mini Map -->
            <div class="w-full md:w-[244px] h-[100px] md:h-[124px] border-[3px] border-[#2d7e37] overflow-hidden relative rounded-lg">
                <div id="mini-map" class="w-full h-full"></div>
                <div class="absolute bottom-2 right-2">
                    <svg class="w-[15px] h-[15px] md:w-[25px] md:h-[25px] text-[#2d7e37]" fill="#F24822" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="bg-[#d9d9d9] h-[10px] md:h-[19px] -mx-3 md:-mx-6 mb-4 md:mb-6"></div>

        <!-- Menu Section -->
        <h2 class="text-base md:text-2xl font-semibold text-[#2b2b2b] mb-3 md:mb-4">
            Daftar Menu
        </h2>

        <div class="flex gap-3 md:gap-6 overflow-x-auto pb-4 mb-4 md:mb-6">
            @forelse($umkm->produks as $produk)
                <div class="bg-[#f2f2f2] border-2 border-[#2d7e37] rounded-xl shadow-md overflow-hidden w-[120px] md:w-[227px] flex-shrink-0">
                    <!-- Image -->
                    <div class="h-[90px] md:h-[162px] w-full overflow-hidden p-1 md:p-2">
                        @if($produk->foto_produk)
                            <img src="{{ asset('storage/' . $produk->foto_produk) }}" 
                                 alt="{{ $produk->nama_produk }}" 
                                 class="w-full h-full object-cover rounded-[11px]">
                        @else
                            <img src="{{ asset('images/placeholder_produk.webp') }}" 
                                 alt="{{ $produk->nama_produk }}" 
                                 class="w-full h-full object-cover rounded-[11px]">
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="px-2 md:px-3 pb-2 md:pb-3">
                        <h3 class="font-bold text-[8px] md:text-base text-black mb-1 truncate">
                            {{ $produk->nama_produk }}
                        </h3>
                        <p class="text-[8px] md:text-base font-semibold text-[#2d7e37] text-right">
                            {{ $produk->harga_formatted }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4 md:py-8 w-full">
                    <p class="text-xs md:text-base">Belum ada menu yang tersedia</p>
                </div>
            @endforelse
        </div>

        <!-- Divider -->
        <div class="bg-[#d9d9d9] h-[10px] md:h-[19px] -mx-3 md:-mx-6 mb-4 md:mb-6"></div>

        <!-- Reviews Section -->
        <h2 class="text-base md:text-2xl font-semibold text-black mb-3 md:mb-4">
            Ulasan
        </h2>

        <!-- Reviews List -->
        <div class="space-y-3 md:space-y-4 mb-4 md:mb-6">
            @forelse($reviews as $review)
                <div class="flex items-start gap-2 md:gap-4">
                    <!-- Avatar -->
                    <div class="w-[30px] h-[30px] md:w-[47px] md:h-[47px] rounded-full overflow-hidden flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($review->akun->nama ?? 'User') }}&background=2d7e37&color=fff" 
                             alt="{{ $review->akun->nama ?? 'User' }}" 
                             class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1">
                        <!-- User Name and Rating -->
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[10px] md:text-base font-semibold text-black">{{ $review->akun->nama ?? 'Anonim' }}</span>
                            <div class="flex gap-0.5 md:gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-[12px] h-[12px] md:w-[25px] md:h-[25px] {{ $i <= $review->rating ? 'text-[#fbbf24]' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>

                        <!-- Review Text -->
                        <div class="bg-[#ececec] rounded-lg shadow-md px-2 md:px-4 py-1 md:py-2">
                            <p class="text-[8px] md:text-xs text-black">{{ $review->ulasan }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-3 md:py-4">
                    <p class="text-xs md:text-base">Belum ada ulasan</p>
                </div>
            @endforelse
        </div>

        <!-- Add Review Button -->
        <div class="flex justify-center">
            @auth
                <button onclick="openReviewModal()" 
                        class="h-[28px] md:h-[39px] px-6 md:px-12 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] rounded-full shadow-md text-white text-sm md:text-2xl font-semibold hover:opacity-90 transition-opacity">
                    Beri Ulasan
                </button>
            @else
                <a href="{{ route('login') }}" 
                   class="h-[28px] md:h-[39px] px-6 md:px-12 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] rounded-full shadow-md text-white text-sm md:text-2xl font-semibold hover:opacity-90 transition-opacity flex items-center">
                    Beri Ulasan
                </a>
            @endauth
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 z-50 hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/background_food.webp') }}" alt="Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-[#171616] opacity-50"></div>
    </div>
    
    <!-- Modal Content -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[90%] md:w-[751px] bg-[#f6f6f6] border border-[#2d7e37] rounded-2xl md:rounded-3xl shadow-md p-4 md:p-8">
        <h2 class="text-xl md:text-4xl font-bold text-black drop-shadow-md mb-4 md:mb-6">
            Beri Ulasan
        </h2>

        <div class="flex gap-2 md:gap-4 mb-4 md:mb-6">
            <!-- UMKM Image -->
            <div class="w-[60px] h-[60px] md:w-[103px] md:h-[103px] rounded overflow-hidden flex-shrink-0">
                @if($umkm->foto_usaha)
                    <img src="{{ asset('storage/' . $umkm->foto_usaha) }}" 
                         alt="{{ $umkm->nama_usaha }}" 
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gray-200"></div>
                @endif
            </div>

            <div>
                <h3 class="text-sm md:text-xl font-bold text-black drop-shadow-md mb-1">
                    {{ $umkm->nama_usaha }}
                </h3>
                <p class="text-[8px] md:text-xs font-semibold text-[#6b6b6b] mb-1 md:mb-2">
                    Peringkat Bintang *
                </p>

                <!-- Star Rating -->
                <div class="flex gap-0.5 md:gap-1" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" 
                                onclick="setRating({{ $i }})" 
                                class="star-btn w-[24px] h-[24px] md:w-[40px] md:h-[40px] text-gray-300 hover:text-[#fbbf24] transition-colors"
                                data-rating="{{ $i }}">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </button>
                    @endfor
                </div>
            </div>
        </div>

        <form action="{{ route('review.store', $umkm->id) }}" method="POST">
            @csrf
            <input type="hidden" name="rating" id="ratingInput" value="">

            <div class="mb-3 md:mb-4">
                <label class="block text-xs md:text-base font-semibold text-[#444] mb-1 md:mb-2">
                    Teks Ulasan
                </label>
                <div class="relative">
                    <textarea name="ulasan" 
                              id="ulasanInput"
                              rows="3" 
                              maxlength="500"
                              placeholder="Masukkan Ulasan"
                              class="w-full bg-[#f6f6f6] border border-[#2d7e37] rounded-2xl md:rounded-3xl shadow-md px-4 md:px-6 py-3 md:py-4 text-xs md:text-base resize-none focus:outline-none focus:ring-2 focus:ring-[#2d7e37]"></textarea>
                    <span class="absolute bottom-3 md:bottom-4 right-4 md:right-6 text-[10px] md:text-sm text-[#4e4d4d]" id="charCount">0/500</span>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="h-[24px] md:h-[30px] px-6 md:px-8 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] rounded-full shadow-md text-white text-xs md:text-sm font-bold hover:opacity-90 transition-opacity">
                    Kirim
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mini-map { z-index: 1; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize mini map
    const lat = {{ $umkm->latitude }};
    const lng = {{ $umkm->longitude }};
    
    const miniMap = L.map('mini-map', {
        zoomControl: false,
        dragging: false,
        scrollWheelZoom: false
    }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);

    // Add marker
    L.marker([lat, lng]).addTo(miniMap);

    // Character counter
    const ulasanInput = document.getElementById('ulasanInput');
    const charCount = document.getElementById('charCount');
    
    if (ulasanInput && charCount) {
        ulasanInput.addEventListener('input', function() {
            charCount.textContent = this.value.length + '/500';
        });
    }
});

let selectedRating = 0;

function openReviewModal() {
    document.getElementById('reviewModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function setRating(rating) {
    selectedRating = rating;
    document.getElementById('ratingInput').value = rating;
    
    const buttons = document.querySelectorAll('.star-btn');
    buttons.forEach((btn, index) => {
        if (index < rating) {
            btn.classList.remove('text-gray-300');
            btn.classList.add('text-[#fbbf24]');
        } else {
            btn.classList.remove('text-[#fbbf24]');
            btn.classList.add('text-gray-300');
        }
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReviewModal();
    }
});
</script>
@endpush
