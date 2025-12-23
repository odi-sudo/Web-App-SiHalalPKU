@extends('layouts.app')

@section('title', 'Dashboard Administrasi - SiHalalPKU')

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
                        {{ $currentUser->nama ?? 'Admin' }}
                    </span>
                    <div class="w-[35px] h-[35px] md:w-[70px] md:h-[70px] rounded-full overflow-hidden bg-gray-200">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($currentUser->nama ?? 'Admin') }}&background=2d7e37&color=fff&size=70" 
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

    <!-- Main Content -->
    <main class="w-full mx-auto px-4 md:px-8 pt-8 md:pt-16">
        <!-- Page Title -->
        <div class="mb-6 md:mb-12">
            <h1 class="text-2xl md:text-5xl font-bold text-black text-shadow mb-1 md:mb-2">Dashboard</h1>
            <p class="text-sm md:text-2xl font-semibold text-[#252525]">
                Selamat Datang, {{ $currentUser->nama ?? 'Admin' }}!
            </p>
        </div>

        <!-- Error Alert -->
        @if(isset($error))
            <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-xs md:text-base">
                {{ $error }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="flex flex-col md:flex-row flex-wrap justify-center gap-4 md:gap-12 mb-8 md:mb-16">
            <!-- Total UMKM -->
            <div class="bg-white border-[3px] md:border-[5px] border-[#2d7e37] rounded-[15px] md:rounded-[25px] shadow-md w-full md:w-[405px] h-[60px] md:h-[88px] flex items-center px-4 md:px-6">
                <div class="flex items-center gap-2 md:gap-4">
                    <svg class="w-[25px] h-[25px] md:w-[45px] md:h-[45px] text-[#2d7e37]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.36 9L18.96 12H5.04L5.64 9H18.36ZM20 4H4V6H20V4ZM20 7H4L3 12V14H4V20H14V14H18V20H20V14H21V12L20 7ZM6 18V14H12V18H6Z"/>
                    </svg>
                    <span class="text-sm md:text-2xl font-semibold text-[#252525]">Total UMKM</span>
                </div>
                <span class="ml-auto text-sm md:text-2xl font-semibold text-[#252525]">{{ $statistics['total_umkm'] ?? 0 }}</span>
            </div>

            <!-- Total Produk -->
            <div class="bg-white border-[3px] md:border-[5px] border-[#2d7e37] rounded-[15px] md:rounded-[25px] shadow-md w-full md:w-[405px] h-[60px] md:h-[88px] flex items-center px-4 md:px-6">
                <div class="flex items-center gap-2 md:gap-4">
                    <svg class="w-[25px] h-[25px] md:w-[45px] md:h-[45px] text-[#2d7e37]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11 9H9V2H7V9H5V2H3V9C3 11.12 4.66 12.84 6.75 12.97V22H9.25V12.97C11.34 12.84 13 11.12 13 9V2H11V9ZM16 6V14H18.5V22H21V2C18.24 2 16 4.24 16 6Z"/>
                    </svg>
                    <span class="text-sm md:text-2xl font-semibold text-[#252525]">Total Produk</span>
                </div>
                <span class="ml-auto text-sm md:text-2xl font-semibold text-[#252525]">{{ $statistics['total_produk'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Kelola UMKM Button -->
        <div class="flex justify-center">
            <a href="{{ route('admin.umkm.index') }}" 
               class="w-full md:w-[584px] h-[45px] md:h-[70px] bg-gradient-to-b from-[#2d7e37] to-[#18471d] rounded-[15px] md:rounded-[24px] shadow-md flex items-center justify-center text-white text-sm md:text-2xl font-semibold hover:opacity-90 transition-opacity">
                Kelola UMKM
            </a>
        </div>
    </main>
</div>
@endsection
