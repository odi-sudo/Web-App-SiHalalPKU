@extends('layouts.app')

@section('title', 'Kelola Data UMKM - SiHalalPKU')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Green Header Banner -->
    <div class="absolute top-0 left-0 right-0 h-20 md:h-36 bg-gradient-to-br from-[#2d7e37] to-[#18471d] rounded-b-[20px] md:rounded-b-[30px] -z-10"></div>

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="w-full mx-auto px-4 md:px-8">
            <div class="flex items-center justify-between h-14 md:h-20">
                <!-- Logo -->
                <h1 class="text-lg md:text-[32px] font-semibold text-[#1e1e1e]">SiHalalPKU</h1>

                <!-- User Info -->
                <div class="flex items-center gap-2 md:gap-4">
                    <span class="text-xs md:text-2xl font-semibold text-black text-shadow hidden sm:block">
                        {{ Auth::user()->nama ?? 'Admin' }}
                    </span>
                    <div class="w-[35px] h-[35px] md:w-[70px] md:h-[70px] rounded-full overflow-hidden bg-gray-200">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama ?? 'Admin') }}&background=2d7e37&color=fff&size=70" 
                             alt="Profile" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="h-[30px] md:h-[66px] w-px bg-gray-300 mx-1 md:mx-2"></div>
                    <form action="{{ route('logout') }}" method="POST" class="flex items-center">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 md:gap-2 text-black hover:text-red-600 transition-colors">
                            <svg class="w-[18px] h-[18px] md:w-[30px] md:h-[30px]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 7L15.59 8.41L18.17 11H8V13H18.17L15.59 15.58L17 17L22 12L17 7ZM4 5H12V3H4C2.9 3 2 3.9 2 5V19C2 20.1 2.9 21 4 21H12V19H4V5Z"/>
                            </svg>
                            <span class="text-xs md:text-2xl font-semibold">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Back Button -->
    <div class="w-full px-2 md:px-4 mt-2 md:mt-4 bg-gradient-to-b from-[#2d7e37] to-[#18471d] z-10 flex items-center rounded-b-[15px] md:rounded-b-[30px]">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 md:gap-2 text-white font-bold hover:opacity-80 transition-opacity py-2">
            <svg class="w-[18px] h-[18px] md:w-[30px] md:h-[30px]" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z"/>
            </svg>
            <span class="text-xs md:text-base">Kembali</span>
        </a>
    </div>

    <!-- Main Content -->
    <main class="w-full mx-auto px-3 md:px-8 pt-4 md:pt-8">
        <!-- Page Title & Add Button -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-8 gap-3 md:gap-0">
            <div>
                <h1 class="text-xl md:text-5xl font-bold text-black text-shadow mb-1 md:mb-2">Kelola Data UMKM</h1>
                <p class="text-xs md:text-2xl font-semibold text-[#252525]">Kelola data UMKM di Pekanbaru</p>
            </div>
            <a href="{{ route('admin.umkm.create') }}" 
               class="h-[36px] md:h-[53px] px-4 md:px-8 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] rounded-[25px] md:rounded-[45px] shadow-md flex items-center gap-1 md:gap-2 text-white text-xs md:text-xl font-bold hover:opacity-90 transition-opacity w-fit">
                <svg class="w-4 h-4 md:w-6 md:h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                </svg>
                Tambah Data
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-xs md:text-base">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-xs md:text-base">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Mobile Cards View -->
        <div class="md:hidden space-y-3">
            @forelse($umkms as $umkm)
                <div class="bg-[#f4f4f4] rounded-xl shadow-md p-3 border border-gray-200">
                    <div class="flex gap-3">
                        <!-- Image -->
                        <div class="w-[70px] h-[65px] rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                            @if($umkm->foto_usaha)
                                <img src="{{ $umkm->foto_usaha_url }}" 
                                     alt="{{ $umkm->nama_usaha }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/placeholder_umkm.webp') }}" 
                                     alt="{{ $umkm->nama_usaha }}" 
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-[#3a3a3a] truncate">{{ $umkm->nama_usaha }}</h3>
                            <p class="text-[10px] text-gray-600 truncate">{{ $umkm->alamat }}</p>
                            <p class="text-[10px] text-gray-600">{{ $umkm->kontak ?? '-' }}</p>
                            @if($umkm->status_halal)
                                <span class="inline-block mt-1 px-2 py-0.5 bg-[#bbe6c0] border border-[#2d7e37] rounded text-[#0a440d] text-[8px] font-semibold">
                                    Tersertifikasi Halal
                                </span>
                            @else
                                <span class="inline-block mt-1 px-2 py-0.5 bg-gray-100 border border-gray-400 rounded text-gray-600 text-[8px] font-semibold">
                                    Belum Tersertifikasi
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('admin.umkm.edit', $umkm->id) }}" 
                           class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-[#fff2c8] border-2 border-[#ffcd29] rounded-full text-[#ffa629] text-[10px] font-medium hover:opacity-80 transition-opacity">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"/>
                            </svg>
                            Ubah
                        </a>
                        <form action="{{ route('admin.umkm.destroy', $umkm->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus UMKM ini?')"
                              class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-[#ffe3e3] border-2 border-[#f24822] rounded-full text-[#f24822] text-[10px] font-medium hover:opacity-80 transition-opacity">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 19C6 20.1 6.9 21 8 21H16C17.1 21 18 20.1 18 19V7H6V19ZM19 4H15.5L14.5 3H9.5L8.5 4H5V6H19V4Z"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p class="text-sm text-gray-500 mb-3">Belum ada UMKM yang terdaftar</p>
                    <a href="{{ route('admin.umkm.create') }}" 
                       class="inline-flex items-center gap-1 px-4 py-2 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] text-white text-xs font-bold rounded-full hover:opacity-90 transition-opacity">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                        </svg>
                        Tambah UMKM Pertama
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block bg-[#f4f4f4] rounded-[20px] shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="text-left py-4 px-6 text-[#3a3a3a] font-medium text-xl">Gambar Usaha</th>
                            <th class="text-left py-4 px-6 text-[#3a3a3a] font-medium text-xl">Nama Usaha</th>
                            <th class="text-left py-4 px-6 text-[#3a3a3a] font-medium text-xl">Alamat</th>
                            <th class="text-left py-4 px-6 text-[#3a3a3a] font-medium text-xl">Kontak</th>
                            <th class="text-left py-4 px-6 text-[#3a3a3a] font-medium text-xl">Produk</th>
                            <th class="text-left py-4 px-6 text-[#3a3a3a] font-medium text-xl">Status Halal</th>
                            <th class="text-left py-4 px-6 text-[#3a3a3a] font-medium text-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse($umkms as $umkm)
                            <tr class="hover:bg-gray-100 transition-colors">
                                <!-- Gambar Usaha -->
                                <td class="py-4 px-6">
                                    <div class="w-[104px] h-[97px] rounded-lg overflow-hidden bg-gray-200">
                                        @if($umkm->foto_usaha)
                                            <img src="{{ $umkm->foto_usaha_url }}" 
                                                 alt="{{ $umkm->nama_usaha }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/placeholder_umkm.webp') }}" 
                                                 alt="{{ $umkm->nama_usaha }}" 
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                </td>

                                <!-- Nama Usaha -->
                                <td class="py-4 px-6">
                                    <p class="text-xl font-medium text-[#3a3a3a]">{{ $umkm->nama_usaha }}</p>
                                </td>

                                <!-- Alamat -->
                                <td class="py-4 px-6">
                                    <p class="text-xl font-medium text-[#3a3a3a] max-w-[150px]">{{ Str::limit($umkm->alamat, 40) }}</p>
                                </td>

                                <!-- Kontak -->
                                <td class="py-4 px-6">
                                    <p class="text-xl font-medium text-[#3a3a3a]">{{ $umkm->kontak ?? '-' }}</p>
                                </td>

                                <!-- Produk -->
                                <td class="py-4 px-6">
                                    <div class="text-xl font-medium text-[#3a3a3a] max-w-[180px]">
                                        @if($umkm->produks && $umkm->produks->count() > 0)
                                            @foreach($umkm->produks->take(2) as $produk)
                                                <p>{{ $produk->nama_produk }} - Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                            @endforeach
                                            @if($umkm->produks->count() > 2)
                                                <p class="text-gray-500 text-sm">+{{ $umkm->produks->count() - 2 }} lainnya</p>
                                            @endif
                                        @else
                                            <p class="text-gray-400">-</p>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status Halal -->
                                <td class="py-4 px-6">
                                    @if($umkm->status_halal)
                                        <span class="inline-block px-4 py-1 bg-[#bbe6c0] border border-[#2d7e37] rounded text-[#0a440d] text-sm font-semibold">
                                            Tersertifikasi Halal
                                        </span>
                                    @else
                                        <span class="inline-block px-4 py-1 bg-gray-100 border border-gray-400 rounded text-gray-600 text-sm font-semibold">
                                            Belum Tersertifikasi
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-2">
                                        <!-- Ubah Data -->
                                        <a href="{{ route('admin.umkm.edit', $umkm->id) }}" 
                                           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#fff2c8] border-[3px] border-[#ffcd29] rounded-full text-[#ffa629] font-medium hover:opacity-80 transition-opacity">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"/>
                                            </svg>
                                            Ubah Data
                                        </a>
                                        <!-- Hapus Data -->
                                        <form action="{{ route('admin.umkm.destroy', $umkm->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus UMKM ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 bg-[#ffe3e3] border-[3px] border-[#f24822] rounded-full text-[#f24822] font-medium hover:opacity-80 transition-opacity">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M6 19C6 20.1 6.9 21 8 21H16C17.1 21 18 20.1 18 19V7H6V19ZM19 4H15.5L14.5 3H9.5L8.5 4H5V6H19V4Z"/>
                                                </svg>
                                                Hapus Data
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-xl text-gray-500 mb-4">Belum ada UMKM yang terdaftar</p>
                                    <a href="{{ route('admin.umkm.create') }}" 
                                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] text-white font-bold rounded-full hover:opacity-90 transition-opacity">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                                        </svg>
                                        Tambah UMKM Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($umkms->hasPages())
                <div class="px-6 py-4 border-t border-gray-300 bg-white">
                    {{ $umkms->links() }}
                </div>
            @endif
        </div>

        <!-- Mobile Pagination -->
        @if($umkms->hasPages())
            <div class="md:hidden px-3 py-3 bg-white rounded-xl shadow-sm mt-3">
                {{ $umkms->links() }}
            </div>
        @endif
    </main>
</div>
@endsection
