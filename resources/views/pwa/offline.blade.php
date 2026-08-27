<x-pwa-layout title="Offline" active="home">
    <div class="pwa-card pop-in mt-10 p-7 text-center">
        <img src="{{ asset('logo.png') }}" alt="" class="float mx-auto mb-4 h-16 w-16 object-contain">
        <p class="text-[15px] font-bold">Tidak ada koneksi</p>
        <p class="mt-1 text-[12.5px] leading-5 text-[#7C90AF]">Generate modul ajar butuh internet. Sambungkan kembali, lalu muat ulang.</p>
        <button onclick="location.reload()" class="press mt-5 rounded-full bg-[#0B4FD9] px-6 py-3 text-[13px] font-bold text-white">Muat ulang</button>
    </div>
</x-pwa-layout>
