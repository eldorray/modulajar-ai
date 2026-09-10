<x-dynamic-component :component="$fromPwa ? 'pwa-layout' : 'app-layout'" title="Edit RPP" active="rpp">
    <x-slot name="header">Edit RPP &amp; Tema</x-slot>
    <div class="max-w-4xl mx-auto space-y-5 p-3 sm:p-6">
        <h1 class="text-xl font-bold">Edit RPP &amp; Tema</h1>
        <p class="text-sm">Perubahan disimpan pada dokumen ini tanpa memanggil AI. Kurikulum tetap {{ $rpp->kurikulum }}. Tema berlaku pada PDF, cetak, dan warna Word; variasi desain berlaku pada PDF/cetak.</p>
        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-400 bg-red-50 text-red-800 p-4">
                <p class="font-bold">Perubahan belum disimpan:</p>
                <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form method="POST" action="{{ route('rpp.update', $rpp) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @if ($fromPwa)<input type="hidden" name="from" value="pwa">@endif
            <input type="hidden" name="revision" value="{{ old('revision', hash('sha256', $rpp->toJson())) }}">
            <section class="rounded-xl border p-4 space-y-4">
                <h2 class="font-bold">Identitas dokumen</h2>
                <p class="text-sm">Identitas digunakan pada sampul dan tanda tangan. Bila identitas juga tertulis di isi hasil AI, sesuaikan bagian isi tersebut di bawah.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach (['nama_guru' => 'Nama guru', 'mata_pelajaran' => 'Mata pelajaran', 'topik' => 'Topik', 'kelas' => 'Kelas', 'alokasi_waktu' => 'Alokasi waktu', 'kepala_sekolah' => 'Kepala sekolah', 'nip_kepala_sekolah' => 'NIP kepala sekolah', 'kota' => 'Kota'] as $key => $label)
                        <label class="block min-w-0 text-sm" for="{{ $key }}">{{ $label }}
                            <input id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $rpp->$key) }}" class="mt-1 block w-full rounded-lg border-gray-300 text-gray-900" @required(in_array($key, ['nama_guru', 'mata_pelajaran', 'topik', 'alokasi_waktu']))>
                        </label>
                    @endforeach
                    <label class="block text-sm" for="tanggal">Tanggal
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $rpp->tanggal?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 text-gray-900">
                    </label>
                    <label class="block text-sm" for="semester">Semester
                        <select id="semester" name="semester" class="mt-1 block w-full rounded-lg border-gray-300 text-gray-900">
                            <option value="">Tidak ditentukan</option>
                            @foreach (['Ganjil', 'Genap'] as $semester)<option value="{{ $semester }}" @selected(old('semester', $rpp->semester) === $semester)>{{ $semester }}</option>@endforeach
                        </select>
                    </label>
                </div>
            </section>
            <section class="rounded-xl border p-4 space-y-4">
                <h2 class="font-bold">Tema &amp; desain tersimpan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach (['tema' => ['Tema warna', config('rpp_themes'), \App\Support\RppDocumentStyle::theme(null, $rpp->tema)], 'desain' => ['Desain PDF / cetak', config('rpp_designs'), \App\Support\RppDocumentStyle::design(null, $rpp->desain)]] as $key => [$label, $options, $value])
                        <label class="block text-sm" for="{{ $key }}">{{ $label }}
                            <select id="{{ $key }}" name="{{ $key }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-gray-900">
                                @foreach ($options as $option => $settings)<option value="{{ $option }}" @selected(old($key, $value) === $option)>{{ $settings['label'] }}</option>@endforeach
                            </select>
                        </label>
                    @endforeach
                </div>
            </section>
            <section class="rounded-xl border p-4 space-y-4">
                <h2 class="font-bold">Isi RPP</h2>
                <p class="text-sm">Edit teks per bagian. Urutan, daftar, tabel, dan struktur data tetap dipertahankan. Angka dan pengaturan nonteks tidak diubah. Tidak perlu menulis JSON.</p>
                @foreach ($fields as $index => $field)
                    <label class="block text-sm break-words" for="content-{{ $index }}">{{ $field['label'] }}
                        <textarea id="content-{{ $index }}" name="content_fields[{{ $index }}]" rows="{{ strlen($field['value']) > 200 ? 6 : 3 }}" maxlength="50000" class="mt-1 block w-full min-w-0 rounded-lg border-gray-300 text-gray-900">{{ old('content_fields.'.$index, $field['value']) }}</textarea>
                    </label>
                @endforeach
            </section>
            <div class="flex flex-wrap gap-3 pb-6">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a class="btn btn-outline" href="{{ route($fromPwa ? 'pwa.rpp.show' : 'rpp.show', $rpp) }}">Batal / kembali</a>
            </div>
        </form>
    </div>
</x-dynamic-component>
