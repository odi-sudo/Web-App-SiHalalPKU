@extends('layouts.app')

@section('title', 'Peta UMKM - SiHalalPKU')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: calc(100vh - 85px); width: 100%; }
    .leaflet-popup-content-wrapper { 
        border-radius: 18px; 
        padding: 0;
    }
    .leaflet-popup-content { margin: 0; }
    .custom-marker { background: transparent; border: none; }
</style>
@endpush

@section('content')
<!-- Navbar -->
@include('components.navbar')

<div class="pt-[85px] relative">
    <!-- Full Screen Map -->
    <div id="map"></div>

    <!-- Search Overlay -->
    <div class="absolute top-4 left-4 right-4 z-[1000] max-w-xl">
        <form action="{{ route('search.umkm') }}" method="GET">
            <div class="flex gap-2">
                <input type="text" 
                       name="q" 
                       placeholder="Cari UMKM..."
                       class="flex-1 h-[46px] bg-white border-2 border-[#2d7e37] rounded-full shadow-lg px-6 text-sm focus:outline-none">
                <button type="submit" 
                        class="h-[46px] w-[46px] bg-gradient-to-b from-[#2d7e37] to-[#18471d] rounded-full shadow-lg flex items-center justify-center hover:opacity-90 transition-opacity">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Overlay -->
    <div class="absolute bottom-4 left-4 z-[1000] bg-white rounded-xl shadow-lg p-4">
        <h3 class="text-lg font-bold text-[#2d7e37] mb-2">Statistik</h3>
        <div class="space-y-1 text-sm">
            <p><span class="font-semibold">{{ $statistics['total_umkm'] ?? 0 }}</span> UMKM Terdaftar</p>
            <p><span class="font-semibold">{{ $statistics['umkm_halal'] ?? 0 }}</span> Tersertifikasi Halal</p>
        </div>
    </div>

    <!-- Error Message -->
    @if(isset($error))
        <div class="absolute top-20 left-1/2 transform -translate-x-1/2 z-[1000] bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded-lg shadow-lg">
            {{ $error }}
        </div>
    @endif
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
        html: `<svg class="w-[35px] h-[35px]" style="color: #F24822;" fill="currentColor" viewBox="0 0 24 24">
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
            <div style="padding: 12px; min-width: 280px;">
                <div style="display: flex; gap: 12px;">
                    <div style="width: 86px; height: 74px; border: 3px solid #2d7e37; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                        <img src="${umkm.foto_usaha_url || placeholderUrl}" 
                             alt="${umkm.nama_usaha}" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex: 1;">
                        <h3 style="font-weight: bold; font-size: 16px; color: black; margin: 0 0 4px 0;">${umkm.nama_usaha}</h3>
                        <p style="font-size: 12px; color: #666; margin: 0 0 8px 0;">${umkm.alamat}</p>
                        <a href="/umkm/${umkm.id}" 
                           style="display: inline-block; padding: 4px 12px; background: linear-gradient(to bottom, #2d7e37, #0a440d); border-radius: 6px; color: white; font-size: 12px; font-weight: bold; text-decoration: none;">
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

    // Fit bounds if there are markers
    if (umkmData.length > 0) {
        const bounds = umkmData.map(u => [u.latitude, u.longitude]);
        map.fitBounds(bounds, { padding: [50, 50] });
    }
});
</script>
@endpush
