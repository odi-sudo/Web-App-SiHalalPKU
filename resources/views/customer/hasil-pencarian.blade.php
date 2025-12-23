@extends('layouts.app')

@section('title', 'Hasil Pencarian - SiHalalPKU')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 200px; width: 100%; }
    @media (min-width: 768px) {
        #map { height: 300px; }
    }
    .leaflet-popup-content-wrapper { border-radius: 18px; }
</style>
@endpush

@section('content')
<!-- Navbar -->
@include('components.navbar')

<div class="pt-[47px] md:pt-[85px]">
    <!-- Hero Section -->
    <div class="bg-gradient-to-b from-[#2d7e37] to-[#18471d] rounded-b-[15px] md:rounded-b-[25px] px-3 md:px-6 py-4 md:py-8 relative -mt-[20px] md:-mt-[36px] pt-[30px] md:pt-[60px]">
        <h1 class="text-lg md:text-3xl font-bold text-white text-center mb-3 md:mb-6 drop-shadow-lg">
            Jelajahi UMKM di Pekanbaru
        </h1>

        <!-- Search Bar -->
        <div class="w-full px-2 md:px-6 flex gap-2 md:gap-4">
            <form action="{{ route('search.umkm') }}" method="GET" class="flex-1 flex gap-2 md:gap-4">
                <div class="flex-1 relative">
                    <input type="text" 
                           name="q" 
                           value="{{ $keyword ?? '' }}"
                           placeholder="Cari Restoran atau makanan"
                           class="w-full h-[32px] md:h-[46px] bg-white border-2 md:border-[3px] border-[#3c3c3c] rounded-full shadow-md px-3 md:px-6 text-[10px] md:text-sm text-[#787878] focus:outline-none focus:border-[#2d7e37]">
                </div>
                <button type="submit" 
                        class="h-[24px] md:h-[33px] w-[60px] md:w-[145px] bg-gradient-to-b from-[#ffcd29] to-[#997500] rounded-full shadow-md flex items-center justify-center gap-1 md:gap-2 hover:opacity-90 transition-opacity self-center">
                    <svg class="w-3 h-3 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-[10px] md:text-xl font-semibold text-white">Cari</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-full px-3 md:px-6 py-4 md:py-8">
        <!-- Results Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6 gap-2 md:gap-0">
            <div>
                <h2 class="text-base md:text-2xl font-bold text-black drop-shadow-md">
                    Hasil Pencarian
                </h2>
                @if(isset($keyword) && $keyword)
                    <p class="text-[10px] md:text-base text-gray-600 mt-0.5 md:mt-1">
                        Menampilkan hasil untuk "{{ $keyword }}"
                        @if(isset($results))
                            ({{ $results->count() }} hasil)
                        @endif
                    </p>
                @endif
            </div>

            <!-- Sort Options -->
            <div class="flex items-center gap-2 md:gap-4">
                <span class="text-[10px] md:text-sm text-gray-600">Urutkan:</span>
                <select id="sortSelect" class="px-2 md:px-4 py-1 md:py-2 text-[10px] md:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2d7e37]">
                    <option value="" {{ !isset($sortBy) || $sortBy == '' ? 'selected' : '' }}>
                        Default
                    </option>
                    <option value="jarak_terdekat" {{ isset($sortBy) && $sortBy == 'jarak_terdekat' ? 'selected' : '' }}>
                        Jarak Terdekat
                    </option>
                    <option value="harga_terendah" {{ isset($sortBy) && $sortBy == 'harga_terendah' ? 'selected' : '' }}>
                        Harga Terendah
                    </option>
                    <option value="harga_tertinggi" {{ isset($sortBy) && $sortBy == 'harga_tertinggi' ? 'selected' : '' }}>
                        Harga Tertinggi
                    </option>
                    <option value="rating_tertinggi" {{ isset($sortBy) && $sortBy == 'rating_tertinggi' ? 'selected' : '' }}>
                        Rating Tertinggi
                    </option>
                </select>
            </div>
        </div>

        <!-- Message if no results -->
        @if(isset($message))
            <div class="text-center py-8 md:py-16">
                <svg class="w-16 h-16 md:w-24 md:h-24 mx-auto mb-3 md:mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-base md:text-xl font-bold text-gray-600 mb-2">{{ $message }}</h3>
                <p class="text-xs md:text-base text-gray-500">Coba gunakan kata kunci yang berbeda</p>
                <a href="{{ route('home') }}" class="inline-block mt-3 md:mt-4 px-4 md:px-6 py-1.5 md:py-2 bg-[#2d7e37] text-white text-xs md:text-base font-semibold rounded-lg hover:opacity-90 transition-opacity">
                    Kembali ke Beranda
                </a>
            </div>
        @else
            <!-- Results Grid -->
            <div class="grid grid-cols-3 gap-2 md:flex md:flex-wrap md:gap-6">
                @forelse($results as $umkm)
                    <div class="flex flex-col">
                        @include('components.umkm-card', ['umkm' => $umkm])
                        
                        <!-- Display matched products if available -->
                        @if(isset($umkm->matched_produks) && $umkm->matched_produks->isNotEmpty())
                            <div class="mt-1 md:mt-2 bg-[#f9f9f9] border border-[#2d7e37] rounded-lg p-1 md:p-2 w-full md:w-[227px]">
                                <p class="text-[6px] md:text-xs font-semibold text-[#2d7e37] mb-0.5 md:mb-1">Produk yang cocok:</p>
                                @foreach($umkm->matched_produks->take(3) as $produk)
                                    <div class="flex items-center gap-1 md:gap-2 py-0.5 md:py-1 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                        <div class="w-5 h-5 md:w-8 md:h-8 rounded overflow-hidden flex-shrink-0 bg-gray-100">
                                            @if($produk->foto_produk)
                                                <img src="{{ $produk->foto_produk_url }}" 
                                                     alt="{{ $produk->nama_produk }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <img src="{{ asset('images/placeholder_produk.webp') }}" 
                                                     alt="{{ $produk->nama_produk }}" 
                                                     class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[6px] md:text-xs font-medium text-gray-800 truncate">{{ $produk->nama_produk }}</p>
                                            <p class="text-[5px] md:text-[10px] text-gray-600">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                @if($umkm->matched_produks->count() > 3)
                                    <p class="text-[5px] md:text-[10px] text-gray-500 mt-0.5 md:mt-1">+{{ $umkm->matched_produks->count() - 3 }} produk lainnya</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="w-full text-center py-8 md:py-16 col-span-3">
                        <svg class="w-16 h-16 md:w-24 md:h-24 mx-auto mb-3 md:mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-base md:text-xl font-bold text-gray-600 mb-2">Tidak ada hasil</h3>
                        <p class="text-xs md:text-base text-gray-500">Coba gunakan kata kunci yang berbeda</p>
                    </div>
                @endforelse
            </div>

            <!-- Map Section -->
            @if(isset($results) && $results->isNotEmpty())
                <div class="mt-4 md:mt-8">
                    <h3 class="text-base md:text-xl font-bold text-black mb-2 md:mb-4">Lokasi di Peta</h3>
                    <div class="border-2 md:border-4 border-[#2d7e37] rounded-lg shadow-md overflow-hidden">
                        <div id="map"></div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
@if(isset($results) && $results->isNotEmpty())
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([0.5071, 101.4478], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const customIcon = L.divIcon({
        html: `<svg class="w-[35px] h-[35px] text-[#2d7e37]" fill="#F24822" viewBox="0 0 24 24">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
        </svg>`,
        className: 'custom-marker',
        iconSize: [35, 35],
        iconAnchor: [17, 35],
        popupAnchor: [0, -35]
    });

    const bounds = [];
    const umkmData = @json($results);
    const placeholderUrl = "{{ asset('images/placeholder_umkm.webp') }}";

    umkmData.forEach(function(umkm) {
        const marker = L.marker([umkm.latitude, umkm.longitude], { icon: customIcon }).addTo(map);
        bounds.push([umkm.latitude, umkm.longitude]);
        
        const popupContent = `
            <div class="bg-[#f6f5f5] rounded-[18px] p-3 min-w-[280px]">
                <div class="flex gap-3">
                    <div class="w-[86px] h-[74px] border-[3px] border-[#2d7e37] rounded-xl overflow-hidden flex-shrink-0">
                        <img src="${umkm.foto_usaha_url || placeholderUrl}" 
                             alt="${umkm.nama_usaha}" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-base text-black">${umkm.nama_usaha}</h3>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4 text-[#2d7e37]" fill="#F24822" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                            </svg>
                            <span class="text-xs text-black">Alamat: ${umkm.alamat.substring(0, 20)}...</span>
                        </div>
                        <a href="/umkm/${umkm.id}" 
                           class="inline-block mt-2 px-3 py-1 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] rounded-md text-white text-xs font-bold">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        marker.bindPopup(popupContent, {
            maxWidth: 350,
            className: 'custom-popup'
        });
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50] });
    }
});
@endif

// Handle sorting with location for distance-based sorting
document.getElementById('sortSelect').addEventListener('change', function() {
    const sortValue = this.value;
    const keyword = "{{ $keyword ?? '' }}";
    
    if (sortValue === 'jarak_terdekat') {
        // Request location permission for distance sorting
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    window.location.href = `{{ route('search.umkm') }}?q=${encodeURIComponent(keyword)}&sort=${sortValue}&lat=${lat}&lng=${lng}`;
                },
                function(error) {
                    alert('Untuk mengurutkan berdasarkan jarak, izinkan akses lokasi Anda.');
                    // Reset to default
                    document.getElementById('sortSelect').value = "{{ $sortBy ?? '' }}";
                }
            );
        } else {
            alert('Browser Anda tidak mendukung geolokasi.');
            document.getElementById('sortSelect').value = "{{ $sortBy ?? '' }}";
        }
    } else {
        // For other sorting options, just redirect
        window.location.href = `{{ route('search.umkm') }}?q=${encodeURIComponent(keyword)}&sort=${sortValue}`;
    }
});
</script>
@endpush
