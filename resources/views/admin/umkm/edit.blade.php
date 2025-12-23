@extends('layouts.app')

@section('title', 'Ubah Data UMKM - SiHalalPKU')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="min-h-screen bg-white">
    <!-- Green Header Banner -->
    <div class="absolute top-0 left-0 right-0 h-20 md:h-36 bg-gradient-to-br from-[#2d7e37] to-[#18471d] rounded-b-[20px] md:rounded-b-[30px] -z-10"></div>

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8">
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
    <div class="max-w-[1440px] mx-auto px-3 md:px-4 mt-2 md:mt-4">
        <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-1 md:gap-2 text-white font-bold hover:opacity-80 transition-opacity">
            <svg class="w-[18px] h-[18px] md:w-[30px] md:h-[30px]" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z"/>
            </svg>
            <span class="text-xs md:text-base">Kembali</span>
        </a>
    </div>

    <!-- Main Content -->
    <main class="max-w-[1440px] mx-auto px-3 md:px-4 pt-4 md:pt-8 pb-8 md:pb-12">
        <!-- Page Title -->
        <div class="mb-4 md:mb-8">
            <h1 class="text-xl md:text-5xl font-bold text-black text-shadow mb-1 md:mb-2">Ubah Data UMKM</h1>
            <p class="text-xs md:text-2xl font-semibold text-[#252525]">Selamat Datang, {{ Auth::user()->nama ?? 'Admin' }}!</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-xs md:text-base">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('admin.umkm.update', $umkm->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="flex flex-col md:flex-row gap-4 md:gap-6">
                <!-- Left Column - UMKM Info -->
                <div class="w-full md:w-[640px] md:flex-shrink-0">
                    <!-- Image Upload & Preview -->
                    <div class="flex items-start gap-3 md:gap-6 mb-4 md:mb-6">
                        <div class="w-[100px] h-[100px] md:w-[187px] md:h-[187px] bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center" id="image-preview">
                            @if($umkm->foto_usaha)
                                <img src="{{ $umkm->foto_usaha_url }}" class="w-full h-full object-cover" alt="{{ $umkm->nama_usaha }}">
                            @else
                                <img src="{{ asset('images/placeholder_umkm.webp') }}" class="w-full h-full object-cover" alt="UMKM Preview">
                            @endif
                        </div>
                        <div class="pt-6 md:pt-20">
                            <label class="inline-block px-4 md:px-8 py-1.5 md:py-2 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] text-white text-[10px] md:text-base font-bold rounded-full shadow-md cursor-pointer hover:opacity-90 transition-opacity">
                                Upload Gambar Usaha
                                <input type="file" 
                                       name="foto_usaha" 
                                       accept="image/*" 
                                       class="hidden" 
                                       id="foto-usaha-input"
                                       onchange="previewImage(this, 'image-preview')">
                            </label>
                        </div>
                    </div>

                    <!-- Nama Usaha -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1 md:mb-2">Nama Usaha</label>
                        <input type="text" 
                               name="nama_usaha" 
                               value="{{ old('nama_usaha', $umkm->nama_usaha) }}"
                               class="w-full h-[38px] md:h-[52px] px-4 md:px-6 bg-[#f6f6f6] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] focus:outline-none focus:ring-2 focus:ring-[#2d7e37]"
                               required>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1 md:mb-2">Alamat</label>
                        <input type="text" 
                               name="alamat" 
                               value="{{ old('alamat', $umkm->alamat) }}"
                               class="w-full h-[38px] md:h-[52px] px-4 md:px-6 bg-[#f6f6f6] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] focus:outline-none focus:ring-2 focus:ring-[#2d7e37]"
                               required>
                    </div>

                    <!-- Kontak -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1 md:mb-2">Kontak</label>
                        <input type="text" 
                               name="kontak" 
                               value="{{ old('kontak', $umkm->kontak) }}"
                               class="w-full h-[38px] md:h-[52px] px-4 md:px-6 bg-[#f6f6f6] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] focus:outline-none focus:ring-2 focus:ring-[#2d7e37]">
                    </div>

                    <!-- Status Sertifikat Halal -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1 md:mb-2">Status Sertifikat Halal</label>
                        <div class="relative">
                            <select name="status_halal" 
                                    class="w-full h-[38px] md:h-[52px] px-4 md:px-6 bg-[#ccc] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] appearance-none focus:outline-none focus:ring-2 focus:ring-[#2d7e37]">
                                <option value="1" {{ $umkm->status_halal ? 'selected' : '' }}>Tersertifikat Halal</option>
                                <option value="0" {{ !$umkm->status_halal ? 'selected' : '' }}>Belum Tersertifikat</option>
                            </select>
                            <svg class="absolute right-4 md:right-6 top-1/2 -translate-y-1/2 w-4 h-4 md:w-6 md:h-6 text-gray-600 pointer-events-none" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7.41 8.59L12 13.17L16.59 8.59L18 10L12 16L6 10L7.41 8.59Z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Latitude -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1 md:mb-2">Latitude</label>
                        <input type="number" 
                               name="latitude" 
                               id="latitude"
                               step="any"
                               value="{{ old('latitude', $umkm->latitude) }}"
                               class="w-full h-[38px] md:h-[52px] px-4 md:px-6 bg-[#f6f6f6] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] focus:outline-none focus:ring-2 focus:ring-[#2d7e37]"
                               required>
                    </div>

                    <!-- Longitude -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1 md:mb-2">Longitude</label>
                        <input type="number" 
                               name="longitude" 
                               id="longitude"
                               step="any"
                               value="{{ old('longitude', $umkm->longitude) }}"
                               class="w-full h-[38px] md:h-[52px] px-4 md:px-6 bg-[#f6f6f6] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] focus:outline-none focus:ring-2 focus:ring-[#2d7e37]"
                               required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1 md:mb-2">Deskripsi</label>
                        <textarea name="deskripsi" 
                                  rows="3"
                                  class="w-full px-4 md:px-6 py-3 md:py-4 bg-[#f6f6f6] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] focus:outline-none focus:ring-2 focus:ring-[#2d7e37] resize-none"
                                  placeholder="Tuliskan deskripsi UMKM di sini...">{{ old('deskripsi', $umkm->deskripsi) }}</textarea>
                    </div>
                </div>

                <!-- Vertical Divider (hidden on mobile) -->
                <div class="hidden md:block w-px bg-gray-300 self-stretch"></div>

                <!-- Right Column - Daftar Produk -->
                <div class="flex-1">
                    <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-2 md:mb-4">Daftar Produk</label>
                    
                    <div class="bg-[#f6f6f6] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md p-3 md:p-6 min-h-[280px] md:min-h-[409px]">
                        <!-- Existing Products -->
                        <div id="products-list" class="space-y-2 md:space-y-3 mb-4 md:mb-6">
                            @foreach($umkm->produks as $index => $produk)
                                <div class="flex items-center gap-2 md:gap-4 p-2 md:p-3 bg-white rounded-lg border border-gray-200" id="existing-product-{{ $produk->id }}">
                                    <div class="w-[40px] h-[40px] md:w-[50px] md:h-[50px] bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($produk->foto_produk)
                                            <img src="{{ $produk->foto_produk_url }}" class="w-full h-full object-cover" alt="{{ $produk->nama_produk }}">
                                        @else
                                            <img src="{{ asset('images/placeholder_produk.webp') }}" class="w-full h-full object-cover" alt="Product">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800 text-xs md:text-base truncate">{{ $produk->nama_produk }}</p>
                                        <p class="text-gray-600 text-[10px] md:text-sm">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                    </div>
                                    <input type="hidden" name="existing_produks[]" value="{{ $produk->id }}">
                                </div>
                            @endforeach
                        </div>

                        <!-- Product Form (hidden by default if products exist) -->
                        <div id="product-form" class="mb-4 md:mb-6 {{ $umkm->produks->count() > 0 ? 'hidden' : '' }}">
                            <!-- Product Image -->
                            <div class="flex items-center gap-3 md:gap-4 mb-3 md:mb-4">
                                <div class="w-[50px] h-[50px] md:w-[61px] md:h-[61px] bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center" id="product-image-preview">
                                    <img src="{{ asset('images/placeholder_produk.webp') }}" class="w-full h-full object-cover" alt="Product Preview">
                                </div>
                                <label class="inline-block px-3 md:px-4 py-1 bg-gradient-to-b from-[#ffcd29] to-[#997b19] text-white text-[10px] md:text-sm font-bold rounded-full shadow-md cursor-pointer hover:opacity-90 transition-opacity">
                                    Upload Gambar Menu
                                    <input type="file" 
                                           accept="image/*" 
                                           class="hidden" 
                                           id="temp-product-image"
                                           onchange="previewProductImage(this)">
                                </label>
                            </div>

                            <!-- Nama Produk -->
                            <div class="mb-2 md:mb-3">
                                <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1">Nama Produk</label>
                                <input type="text" 
                                       id="temp-nama-produk"
                                       class="w-full h-[38px] md:h-[52px] px-4 md:px-6 bg-white border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] focus:outline-none focus:ring-2 focus:ring-[#2d7e37]">
                            </div>

                            <!-- Harga Produk -->
                            <div class="mb-3 md:mb-4">
                                <label class="block text-sm md:text-xl font-semibold text-[#6b6b6b] mb-1">Harga Produk</label>
                                <input type="number" 
                                       id="temp-harga-produk"
                                       class="w-full h-[38px] md:h-[52px] px-4 md:px-6 bg-white border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-sm md:text-xl font-semibold text-[#2e2c2c] focus:outline-none focus:ring-2 focus:ring-[#2d7e37]">
                            </div>

                            <!-- Save Product Button -->
                            <div class="flex justify-end">
                                <button type="button" 
                                        onclick="addProduct()"
                                        class="px-4 md:px-6 py-1 bg-gradient-to-b from-[#2d7e37] to-[#0a440d] text-white text-xs md:text-sm font-bold rounded-full shadow-md hover:opacity-90 transition-opacity">
                                    Simpan
                                </button>
                            </div>
                        </div>

                        <!-- Add More Product Button -->
                        <div class="mt-3 md:mt-4">
                            <button type="button" 
                                    id="add-product-btn"
                                    onclick="showProductForm()"
                                    class="px-3 md:px-4 py-1.5 md:py-2 bg-[#f6f6f6] border border-[#2d7e37] rounded-[16px] md:rounded-[24px] shadow-md text-xs md:text-base text-[#333] font-bold hover:bg-gray-100 transition-colors {{ $umkm->produks->count() === 0 ? 'hidden' : '' }}">
                                + Tambah Produk
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-6 md:mt-8">
                <button type="submit" 
                        class="px-8 md:px-12 py-2 md:py-3 bg-gradient-to-b from-[#ffcd29] to-[#997b19] text-white text-sm md:text-xl font-bold rounded-full shadow-md hover:opacity-90 transition-opacity">
                    Simpan
                </button>
            </div>
        </form>
    </main>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let productIndex = {{ $umkm->produks->count() }};

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover" alt="Preview">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewProductImage(input) {
    const preview = document.getElementById('product-image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover" alt="Preview">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function addProduct() {
    const namaProduk = document.getElementById('temp-nama-produk').value;
    const hargaProduk = document.getElementById('temp-harga-produk').value;
    const imageInput = document.getElementById('temp-product-image');

    if (!namaProduk || !hargaProduk) {
        alert('Mohon isi nama dan harga produk');
        return;
    }

    const productsList = document.getElementById('products-list');
    const placeholderUrl = "{{ asset('images/placeholder_produk.webp') }}";
    const productHtml = `
        <div class="flex items-center gap-4 p-3 bg-white rounded-lg border border-gray-200" id="product-${productIndex}">
            <div class="w-[50px] h-[50px] bg-gray-200 rounded-lg overflow-hidden flex-shrink-0" id="product-img-${productIndex}">
                <img src="${placeholderUrl}" class="w-full h-full object-cover" alt="Product">
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-800">${namaProduk}</p>
                <p class="text-gray-600">Rp ${Number(hargaProduk).toLocaleString('id-ID')}</p>
            </div>
            <button type="button" onclick="removeProduct(${productIndex})" class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 19C6 20.1 6.9 21 8 21H16C17.1 21 18 20.1 18 19V7H6V19ZM19 4H15.5L14.5 3H9.5L8.5 4H5V6H19V4Z"/>
                </svg>
            </button>
            <input type="hidden" name="produks[${productIndex}][nama_produk]" value="${namaProduk}">
            <input type="hidden" name="produks[${productIndex}][harga]" value="${hargaProduk}">
        </div>
    `;

    productsList.insertAdjacentHTML('beforeend', productHtml);

    // Copy image preview if exists
    const previewImg = document.querySelector('#product-image-preview img');
    if (previewImg) {
        document.getElementById(`product-img-${productIndex}`).innerHTML = `<img src="${previewImg.src}" class="w-full h-full object-cover" alt="Product">`;
    }

    // Reset form
    document.getElementById('temp-nama-produk').value = '';
    document.getElementById('temp-harga-produk').value = '';
    document.getElementById('product-image-preview').innerHTML = `
        <img src="{{ asset('images/placeholder_produk.webp') }}" class="w-full h-full object-cover" alt="Product Preview">
    `;
    document.getElementById('temp-product-image').value = '';

    productIndex++;

    // Show add more button
    document.getElementById('add-product-btn').classList.remove('hidden');
    document.getElementById('product-form').classList.add('hidden');
}

function removeProduct(index) {
    document.getElementById(`product-${index}`).remove();
}

function showProductForm() {
    document.getElementById('product-form').classList.remove('hidden');
    document.getElementById('add-product-btn').classList.add('hidden');
}
</script>
@endpush
