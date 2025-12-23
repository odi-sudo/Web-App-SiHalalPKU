@props(['currentUser' => null])

<nav class="bg-white shadow-md h-[47px] md:h-[85px] w-full fixed top-0 left-0 z-50">
    <div class="h-full w-full mx-auto px-2 md:px-4 flex items-center justify-between">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="text-[12px] md:text-2xl font-semibold text-[#1e1e1e]">
            SiHalalPKU
        </a>

        <!-- Right Side -->
        <div class="flex items-center gap-2 md:gap-4">
            @auth
                <!-- User Info -->
                <span class="text-[12px] md:text-2xl font-semibold text-black hidden sm:inline">
                    {{ auth()->user()->nama }}
                </span>
                
                <!-- Avatar -->
                <div class="w-[30px] h-[30px] md:w-[70px] md:h-[70px] rounded-full bg-gray-200 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&background=2d7e37&color=fff" 
                         alt="Avatar" 
                         class="w-full h-full object-cover">
                </div>

                <!-- Divider -->
                <div class="w-px h-[36px] md:h-[66px] bg-gray-300"></div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="flex items-center gap-1 md:gap-2">
                    @csrf
                    <button type="submit" class="flex items-center gap-1 md:gap-2 text-[12px] md:text-2xl font-semibold text-black hover:text-[#2d7e37] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[20px] h-[20px] md:w-[30px] md:h-[30px]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                        </svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-3 md:px-6 py-1 md:py-2 bg-gradient-to-b from-[#2d7e37] to-[#18471d] text-white text-[10px] md:text-base font-semibold rounded-full shadow-md hover:opacity-90 transition-opacity">
                    Masuk
                </a>
                <a href="{{ route('registrasi') }}" class="px-3 md:px-6 py-1 md:py-2 border-2 border-[#2d7e37] text-[#2d7e37] text-[10px] md:text-base font-semibold rounded-full hover:bg-[#2d7e37] hover:text-white transition-all">
                    Daftar
                </a>
            @endauth
        </div>
    </div>
</nav>
