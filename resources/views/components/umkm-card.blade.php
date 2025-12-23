@props(['umkm'])

<div class="bg-[#f2f2f2] border-2 border-[#2d7e37] rounded-xl shadow-md overflow-hidden w-full md:w-[227px] flex-shrink-0">
    <!-- Image -->
    <div class="h-[125px] md:h-[162px] w-full overflow-hidden rounded-t-[11px] p-[5px] md:p-[7px]">
        @if($umkm->foto_usaha)
            <img src="{{ $umkm->foto_usaha_url }}" 
                 alt="{{ $umkm->nama_usaha }}" 
                 class="w-full h-full object-cover rounded-[11px]">
        @else
            <img src="{{ asset('images/placeholder_umkm.webp') }}" 
                 alt="{{ $umkm->nama_usaha }}" 
                 class="w-full h-full object-cover rounded-[11px]">
        @endif
    </div>

    <!-- Content -->
    <div class="p-2 md:p-3">
        <!-- Title -->
        <h3 class="font-bold text-[8px] md:text-base text-black mb-1 truncate">
            {{ $umkm->nama_usaha }}
        </h3>

        <!-- Halal Badge -->
        @if($umkm->status_halal)
            <div class="bg-[#bbe6c0] border border-[#2d7e37] rounded px-1 md:px-2 py-0.5 inline-block mb-1">
                <span class="text-[#0a440d] text-[4px] md:text-[10px] font-semibold">Tersertifikasi Halal</span>
            </div>
        @endif

        <!-- Rating -->
        <div class="flex items-center gap-0.5 md:gap-1 mb-1">
            <svg class="w-[8px] h-[8px] md:w-[15px] md:h-[15px] text-[#fbbf24]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            <span class="text-[5px] md:text-xs font-medium text-black">{{ number_format($umkm->average_rating ?? 0, 1) }}</span>
        </div>

        <!-- Address -->
        <div class="flex items-start gap-0.5 md:gap-1 mb-1 md:mb-2">
            <svg class="w-[8px] h-[8px] md:w-[15px] md:h-[15px] text-[#2d7e37] flex-shrink-0 mt-0.5" fill="#F24822" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            <span class="text-[5px] md:text-xs font-medium text-black line-clamp-2">{{ $umkm->alamat }}</span>
        </div>

        <!-- Detail Button -->
        <a href="{{ route('profil.detail', $umkm->id) }}" 
           class="block w-[41px] md:w-[86px] h-[10px] md:h-[17px] bg-gradient-to-b from-[#2d7e37] to-[#0a440d] rounded shadow-md ml-auto">
            <span class="flex items-center justify-center h-full text-white text-[5px] md:text-xs font-bold">Lihat Detail</span>
        </a>
    </div>
</div>
