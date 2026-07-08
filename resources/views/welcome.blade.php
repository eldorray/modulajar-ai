<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RPP Generator AI - Buat Modul Ajar dan Soal STS</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; }
        .hero-gradient {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 800px;
            background: radial-gradient(ellipse at top, rgba(255, 192, 116, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }
        .hero-glow-1 {
            position: absolute;
            top: -150px;
            left: 10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 166, 77, 0.2);
            filter: blur(100px);
            z-index: -1;
            border-radius: 50%;
        }
        .hero-glow-2 {
            position: absolute;
            top: -50px;
            right: 15%;
            width: 500px;
            height: 500px;
            background: rgba(255, 138, 51, 0.15);
            filter: blur(120px);
            z-index: -1;
            border-radius: 50%;
        }
    </style>
</head>
<body class="text-gray-900 antialiased min-h-screen flex flex-col relative overflow-x-hidden selection:bg-orange-100 selection:text-orange-900">

    <!-- Background Gradients -->
    <div class="hero-gradient"></div>
    <div class="hero-glow-1"></div>
    <div class="hero-glow-2"></div>

    <!-- Navigation -->
    <nav class="relative z-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight">RPP Gen.</span>
                </a>

                <!-- Centered Links (Desktop) -->
                <div class="hidden md:flex items-center space-x-8 text-sm font-medium text-gray-600">
                    <a href="#fitur" class="hover:text-black transition-colors">Fitur</a>
                </div>

                <!-- Right Action -->
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-5 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold transition-all shadow-sm">
                                Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold transition-all shadow-sm">
                                Mulai Sekarang &rarr;
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-grow pt-16 sm:pt-24 pb-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm mb-8 animate-fade-up">
                <span class="text-xs font-bold bg-gradient-to-r from-orange-500 to-red-500 text-transparent bg-clip-text">New</span>
                <span class="text-xs font-medium text-gray-600 border-l border-gray-200 pl-2">✨ Fitur AI Pintar</span>
            </div>

            <!-- Heading -->
            <h1 class="text-5xl sm:text-6xl md:text-7xl font-[800] tracking-tight text-gray-900 mb-6 max-w-4xl mx-auto leading-[1.05] animate-fade-up delay-100">
                Buat Modul Ajar dan Soal Sempurna. Dengan Strategi AI Cerdas.
            </h1>
            
            <!-- Subtitle -->
            <p class="text-lg text-gray-500 mb-10 max-w-2xl mx-auto animate-fade-up delay-200 font-medium">
                Tingkatkan alur kerja Anda untuk hasil mengajar yang superior dengan alat terpadu dan cerdas.
            </p>

            <!-- Mini Checks -->
            <div class="flex flex-wrap justify-center gap-6 text-sm font-semibold text-gray-600 mb-10 animate-fade-up delay-300">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-orange-100 flex items-center justify-center text-orange-500"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                    Dapat Disesuaikan
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-orange-100 flex items-center justify-center text-orange-500"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                    Performa Maksimal
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-orange-100 flex items-center justify-center text-orange-500"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                    Keamanan Tinggi
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-20 animate-fade-up delay-400">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-8 py-3.5 rounded-full bg-[#121212] text-white font-semibold text-sm hover:bg-black transition-colors shadow-lg flex items-center gap-2 w-full sm:w-auto">
                        Buka Dashboard
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @else
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-8 py-3.5 rounded-full bg-[#121212] text-white font-semibold text-sm hover:bg-black transition-colors shadow-lg flex items-center gap-2 w-full sm:w-auto">
                        Mulai Gratis
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    @endif
                    <a href="{{ route('login') }}" class="px-8 py-3.5 rounded-full bg-white text-gray-900 border border-gray-200 font-semibold text-sm hover:bg-gray-50 transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto">
                        Masuk Akun
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                @endauth
            </div>

            <!-- Social Proof -->
            <div class="animate-fade-up delay-500">
                <p class="text-xs font-semibold text-gray-400 mb-6 uppercase tracking-widest">Bergabung dengan 4.000+ guru yang telah berkembang</p>
                <div class="flex flex-wrap justify-center items-center gap-8 text-gray-400">
                    <!-- Fictional Logos (using generic shapes/text for now) -->
                    <div class="flex items-center gap-2 text-lg font-bold"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg> Grapho</div>
                    <div class="flex items-center gap-2 text-lg font-bold"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle></svg> Signum</div>
                    <div class="flex items-center gap-2 text-lg font-bold"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2z"></path></svg> Vectra</div>
                    <div class="flex items-center gap-2 text-lg font-bold"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg> Optimal</div>
                </div>
            </div>
            
            <!-- Decorative Floating Elements (Left) -->
            <div class="hidden xl:block absolute top-28 -left-16 w-64 p-5 bg-white/70 backdrop-blur-xl border border-white/40 rounded-2xl shadow-xl shadow-orange-900/5 transform -rotate-6 animate-float z-0">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-xs font-bold text-gray-800">Waktu Terhemat</div>
                        <div class="text-[10px] text-gray-400">Minggu Ini</div>
                    </div>
                    <div class="w-6 h-6 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center shadow-inner">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="w-full h-1 bg-gray-100 rounded-full mb-2"></div>
                <div class="w-4/5 h-1 bg-gray-100 rounded-full mb-6"></div>
                <div class="w-full bg-white rounded-xl border border-gray-100 p-3 shadow-sm">
                    <div class="flex justify-between items-end mb-2">
                        <div class="text-xl font-black text-gray-900">45<span class="text-xs text-gray-500 font-medium"> Jam</span></div>
                        <div class="text-xs font-bold text-green-500">Efisiensi +85%</div>
                    </div>
                    <div class="flex items-end gap-1.5 h-12">
                        <div class="flex-1 bg-gray-100 rounded-t-sm h-1/3"></div>
                        <div class="flex-1 bg-orange-100 rounded-t-sm h-2/3"></div>
                        <div class="flex-1 bg-gray-100 rounded-t-sm h-1/2"></div>
                        <div class="flex-1 bg-orange-400 rounded-t-sm h-full shadow-sm"></div>
                        <div class="flex-1 bg-gray-100 rounded-t-sm h-1/4"></div>
                        <div class="flex-1 bg-gray-100 rounded-t-sm h-3/4"></div>
                    </div>
                </div>
                <div class="absolute -bottom-3 -right-3 w-8 h-8 rounded-full bg-orange-500 border-4 border-white shadow-md flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>

            <!-- Decorative Floating Elements (Right) -->
            <div class="hidden xl:block absolute top-40 -right-16 w-72 p-5 bg-white/70 backdrop-blur-xl border border-white/40 rounded-2xl shadow-xl shadow-orange-900/5 transform rotate-3 animate-float delay-200 z-0">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-6 h-6 rounded bg-orange-500 text-white flex items-center justify-center"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                    <div class="text-xs font-bold text-gray-800">RPP Berhasil Dibuat</div>
                </div>
                <div class="text-3xl font-black text-gray-900 mb-4">15.4K+</div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-full bg-gray-200 border-2 border-white relative z-30"></div>
                    <div class="w-6 h-6 rounded-full bg-gray-300 border-2 border-white -ml-4 relative z-20"></div>
                    <div class="w-6 h-6 rounded-full bg-orange-200 border-2 border-white -ml-4 relative z-10"></div>
                    <div class="text-[10px] text-gray-500 font-semibold ml-2">Modul Ajar & STS</div>
                </div>
                <div class="flex gap-4 p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div>
                        <div class="text-[9px] text-gray-400 font-semibold mb-1">Kurikulum Merdeka</div>
                        <div class="text-sm font-bold text-gray-800"><span class="text-green-500 mr-1">&uarr;</span>12,500</div>
                    </div>
                    <div class="w-px bg-gray-100"></div>
                    <div>
                        <div class="text-[9px] text-gray-400 font-semibold mb-1">Soal STS</div>
                        <div class="text-sm font-bold text-gray-800"><span class="text-green-500 mr-1">&uarr;</span>2,900</div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Features Section -->
    <section id="fitur" class="py-24 bg-white relative">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Valuable Features</p>
                <h2 class="text-3xl md:text-[40px] font-[800] text-gray-900 mb-6 tracking-tight">Solusi Modul Ajar yang Disesuaikan</h2>
                <p class="text-gray-500 max-w-2xl mx-auto font-medium text-sm leading-relaxed">Sesuaikan platform kami dengan kebutuhan mengajar unik Anda dengan solusi yang fleksibel dan terstruktur untuk Kurikulum Merdeka.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Card 1 -->
                <div class="bg-[#F8F9FA] rounded-3xl p-8 relative overflow-hidden group hover:bg-gray-100 transition-colors">
                    <div class="absolute top-6 right-6 w-8 h-8 bg-white shadow-sm rounded-full flex items-center justify-center text-orange-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="font-bold text-[17px] text-gray-900 mb-2">Modul Ajar Otomatis</h3>
                    <p class="text-gray-500 text-[13px] mb-8 pr-12 leading-relaxed">Hasilkan RPP berstandar secara akurat.</p>
                    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 transform group-hover:-translate-y-2 transition-transform duration-300">
                        <div class="text-[10px] font-bold text-gray-400 mb-1">Average Daily Output</div>
                        <div class="w-full h-1 bg-gray-100 rounded-full mb-3"></div>
                        <div class="flex justify-between items-end mb-4">
                            <div class="w-16 h-3 bg-orange-100 rounded-full"></div>
                            <div class="w-20 h-4 bg-gray-100 rounded-md"></div>
                        </div>
                        <div class="flex items-end gap-1.5 h-12">
                            <div class="flex-1 bg-gray-100 rounded h-1/2"></div>
                            <div class="flex-1 bg-orange-100 rounded h-3/4"></div>
                            <div class="flex-1 bg-orange-400 rounded h-full"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-[#F8F9FA] rounded-3xl p-8 relative overflow-hidden group hover:bg-gray-100 transition-colors">
                    <div class="absolute top-6 right-6 w-8 h-8 bg-white shadow-sm rounded-full flex items-center justify-center text-orange-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-[17px] text-gray-900 mb-2">Soal STS AI</h3>
                    <p class="text-gray-500 text-[13px] mb-8 pr-12 leading-relaxed">Buat Soal Sumatif Tengah Semester beserta kuncinya.</p>
                    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 transform group-hover:-translate-y-2 transition-transform duration-300">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex gap-2">
                                <div class="w-4 h-4 rounded-full bg-orange-100"></div>
                                <div class="w-12 h-4 bg-gray-100 rounded"></div>
                            </div>
                            <div class="w-10 h-4 bg-gray-100 rounded-full"></div>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="w-full h-8 bg-gray-50 rounded-lg flex items-center px-3 border border-gray-100"><div class="w-1/2 h-2 bg-gray-200 rounded"></div></div>
                            <div class="w-full h-8 bg-gray-50 rounded-lg flex items-center px-3 border border-gray-100"><div class="w-2/3 h-2 bg-gray-200 rounded"></div></div>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-[#F8F9FA] rounded-3xl p-8 relative overflow-hidden group hover:bg-gray-100 transition-colors">
                    <div class="absolute top-6 right-6 w-8 h-8 bg-white shadow-sm rounded-full flex items-center justify-center text-orange-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-[17px] text-gray-900 mb-2">Kolaborasi Tim</h3>
                    <p class="text-gray-500 text-[13px] mb-8 pr-12 leading-relaxed">Kelola profil guru dengan sangat efisien.</p>
                    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 transform group-hover:-translate-y-2 transition-transform duration-300">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-gray-200 relative">
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div>
                                <div class="w-20 h-2.5 bg-gray-800 rounded-full mb-2"></div>
                                <div class="w-12 h-2 bg-gray-300 rounded-full"></div>
                            </div>
                        </div>
                        <div class="w-full h-10 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center">
                            <div class="w-24 h-2 bg-gray-200 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Wide Card 1 -->
                <div class="bg-[#F8F9FA] rounded-3xl p-8 relative overflow-hidden group hover:bg-gray-100 transition-colors">
                    <div class="absolute top-6 right-6 w-8 h-8 bg-white shadow-sm rounded-full flex items-center justify-center text-orange-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    </div>
                    <h3 class="font-bold text-[17px] text-gray-900 mb-2">Format Ekspor PDF Terstruktur</h3>
                    <p class="text-gray-500 text-[13px] mb-8">Format siap cetak dengan tata letak yang bersih dan terstruktur untuk diserahkan ke Kepala Sekolah.</p>
                    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 flex items-center justify-center transform group-hover:-translate-y-2 transition-transform duration-300 mx-8">
                        <div class="flex gap-5 w-full">
                            <div class="w-16 h-20 bg-gray-50 rounded shadow-sm border border-gray-200 flex flex-col p-2">
                                <div class="w-full h-1 bg-gray-300 rounded-full mb-1"></div>
                                <div class="w-1/2 h-1 bg-gray-300 rounded-full mb-3"></div>
                                <div class="w-full h-0.5 bg-gray-200 mb-1"></div>
                                <div class="w-3/4 h-0.5 bg-gray-200 mb-1"></div>
                            </div>
                            <div class="flex-1 py-1">
                                <div class="w-1/2 h-2 bg-gray-300 rounded-full mb-3"></div>
                                <div class="w-full h-1.5 bg-gray-100 rounded-full mb-2"></div>
                                <div class="w-4/5 h-1.5 bg-gray-100 rounded-full mb-4"></div>
                                <div class="w-1/3 h-6 bg-orange-50 text-orange-500 rounded flex items-center justify-center text-[9px] font-bold">Download PDF</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wide Card 2 -->
                <div class="bg-[#F8F9FA] rounded-3xl p-8 relative overflow-hidden group hover:bg-gray-100 transition-colors">
                    <div class="absolute top-6 right-6 w-8 h-8 bg-white shadow-sm rounded-full flex items-center justify-center text-orange-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    </div>
                    <h3 class="font-bold text-[17px] text-gray-900 mb-2">Dashboard Terpusat</h3>
                    <p class="text-gray-500 text-[13px] mb-8">Pantau sisa token AI Anda dan riwayat pembuatan dokumen dalam satu layar yang sangat informatif.</p>
                    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 transform group-hover:-translate-y-2 transition-transform duration-300 mx-8">
                        <div class="flex gap-3 mb-4">
                            <div class="flex-1 bg-gray-50 rounded-xl p-3 border border-gray-100">
                                <div class="w-6 h-6 rounded-full bg-blue-100 mb-2"></div>
                                <div class="w-12 h-2.5 bg-gray-800 rounded-full mb-1"></div>
                                <div class="w-8 h-1.5 bg-gray-300 rounded-full"></div>
                            </div>
                            <div class="flex-1 bg-gray-50 rounded-xl p-3 border border-gray-100">
                                <div class="w-6 h-6 rounded-full bg-orange-100 mb-2"></div>
                                <div class="w-12 h-2.5 bg-gray-800 rounded-full mb-1"></div>
                                <div class="w-8 h-1.5 bg-gray-300 rounded-full"></div>
                            </div>
                        </div>
                        <div class="w-full h-1 bg-gray-100 rounded-full mb-2"></div>
                        <div class="w-1/2 h-1 bg-gray-100 rounded-full mb-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-24 bg-white border-t border-gray-50">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-20 items-center">
                
                <!-- Left Side -->
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Benefits</p>
                    <h2 class="text-3xl md:text-[42px] font-[800] text-gray-900 mb-6 tracking-tight leading-[1.1]">
                        Buka Era Baru Operasional Unggul dan Inovasi
                    </h2>
                    <p class="text-gray-500 mb-8 text-[15px] font-medium leading-relaxed">Bebaskan diri Anda dari administrasi yang menyita waktu dengan alat canggih dan proses yang disederhanakan.</p>
                    
                    <div class="flex flex-wrap gap-2.5">
                        <span class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-700 text-[11px] font-bold tracking-wide shadow-sm">Robust Security</span>
                        <span class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-700 text-[11px] font-bold tracking-wide shadow-sm">Customizable</span>
                        <span class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-700 text-[11px] font-bold tracking-wide shadow-sm">Accessibility</span>
                        <span class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-700 text-[11px] font-bold tracking-wide shadow-sm">Automated Efficiency</span>
                    </div>
                </div>

                <!-- Right Side (List) -->
                <div class="space-y-10 relative">
                    <!-- Line connector -->
                    <div class="absolute left-[11px] top-6 bottom-6 w-0.5 bg-orange-100 -z-10 rounded-full"></div>
                    
                    <div class="flex gap-6">
                        <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center shrink-0 mt-0.5 z-10 border-4 border-white">
                            <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-base mb-1.5">Expert Team</h4>
                            <p class="text-gray-500 text-[13px] leading-relaxed font-medium">Tim pengembang berdedikasi tinggi siap membantu menyelesaikan rintangan dalam mengajar.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-6">
                        <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center shrink-0 mt-0.5 z-10 border-4 border-white">
                            <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-base mb-1.5">Fast and Scalable</h4>
                            <p class="text-gray-500 text-[13px] leading-relaxed font-medium">Buat RPP tanpa batas secara instan untuk kebutuhan sekolah Anda.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-6">
                        <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center shrink-0 mt-0.5 z-10 border-4 border-white">
                            <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-base mb-1.5">Customizable for You</h4>
                            <p class="text-gray-500 text-[13px] leading-relaxed font-medium">Sesuaikan AI dengan tujuan materi dari Kurikulum Merdeka yang spesifik.</p>
                        </div>
                    </div>

                    <div class="flex gap-6">
                        <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center shrink-0 mt-0.5 z-10 border-4 border-white">
                            <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-base mb-1.5">Maximum Efficiency</h4>
                            <p class="text-gray-500 text-[13px] leading-relaxed font-medium">Efisiensi operasional dengan sistem integrasi terpusat.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#fafafa] py-16 border-t border-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <a href="/" class="flex items-center gap-2">
                <div class="w-6 h-6 rounded bg-gray-900 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="font-bold text-lg tracking-tight">RPP Gen.</span>
            </a>
            
            <div class="text-sm font-medium text-gray-400 text-center">
                &copy; {{ date('Y') }} RPP Generator AI. Developed by Fahmie.
            </div>
            
            <div class="flex gap-6 text-sm font-medium text-gray-500">
                <a href="#" class="hover:text-gray-900 transition-colors">Privacy</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Terms</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Contact</a>
            </div>
        </div>
    </footer>

</body>
</html>
