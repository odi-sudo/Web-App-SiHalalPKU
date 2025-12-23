@extends('layouts.app')

@section('title', 'SiHalalPKU - Peta UMKM Halal Pekanbaru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 401px; width: 100%; }
    .leaflet-popup-content-wrapper { border-radius: 18px; }
</style>
@endpush

@section('content')
<!-- Navbar -->
@include('components.navbar')

<div class="pt-[85px]">
    <!-- Hero Section -->
    <div class="bg-gradient-to-b from-[#2d7e37] to-[#18471d] rounded-b-[25px] px-6 py-8 relative -mt-[36px] pt-[60px]">
        <h1 class="text-3xl font-bold text-white text-center mb-6 drop-shadow-lg">
            Jelajahi UMKM di Pekanbaru
        </h1>

        <!-- Search Bar -->
        <div class="w-full px-6 flex gap-4">
            <form action="{{ route('search.umkm') }}" method="GET" class="flex-1 flex gap-4">
                <div class="flex-1 relative">
                    <input type="text" 
                           name="q" 
                           placeholder="Cari Restoran atau makanan di Pekanbaru"
                           class="w-full h-[46px] bg-white border-[3px] border-[#3c3c3c] rounded-full shadow-md px-6 text-sm text-[#787878] focus:outline-none focus:border-[#2d7e37]">
                </div>
                <button type="submit" 
                        class="h-[33px] w-[145px] bg-gradient-to-b from-[#ffcd29] to-[#997500] rounded-full shadow-md flex items-center justify-center gap-2 hover:opacity-90 transition-opacity self-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-xl font-semibold text-white">Cari</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-full px-6 py-8">
        <!-- Map Section -->
        <h2 class="text-2xl font-bold text-black mb-4 drop-shadow-md">
            Peta Interaktif UMKM
        </h2>
        
        <div class="border-4 border-[#2d7e37] shadow-md mb-8 relative">
            <div id="map"></div>
        </div>

        <!-- Divider -->
        <div class="bg-[#d9d9d9] h-[19px] -mx-6 mb-8"></div>

        <!-- UMKM Pilihan Section -->
        <h2 class="text-2xl font-bold text-black mb-6 drop-shadow-md">
            UMKM Pilihan
        </h2>

        <div class="flex gap-6 overflow-x-auto pb-4">
            @forelse($umkms as $umkm)
                @include('components.umkm-card', ['umkm' => $umkm])
            @empty
                <div class="text-center text-gray-500 py-8 w-full">
                    <p>Belum ada UMKM yang terdaftar</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Pekanbaru
    const map = L.map('map').setView([0.5071, 101.4478], 13);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Custom marker icon
    const customIcon = L.divIcon({
        html: `<svg class="w-[35px] h-[35px] text-[#2d7e37]" fill="#F24822" viewBox="0 0 24 24">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
        </svg>`,
        className: 'custom-marker',
        iconSize: [35, 35],
        iconAnchor: [17, 35],
        popupAnchor: [0, -35]
    });

    // UMKM data from Laravel
    const umkmData = @json($coordinates);
    const placeholderUrl = "{{ asset('images/placeholder_umkm.webp') }}";

    // Add markers
    umkmData.forEach(function(umkm) {
        const marker = L.marker([umkm.latitude, umkm.longitude], { icon: customIcon }).addTo(map);
        
        const popupContent = `
            <div class="bg-[#f6f5f5] rounded-[18px] p-3 min-w-[280px]">
                <div class="flex gap-3">
                    <div class="w-[86px] h-[74px] border-[3px] border-[#2d7e37] rounded-xl overflow-hidden flex-shrink-0">
                        <img src="${umkm.foto_usaha ? '/storage/' + umkm.foto_usaha : placeholderUrl}" 
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
});
</script>
@endpush
