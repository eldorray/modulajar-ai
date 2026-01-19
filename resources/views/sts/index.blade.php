<x-app-layout>
    <x-slot name="header">Soal STS Saya</x-slot>

    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Daftar Soal STS</h2>
            <a href="{{ route('sts.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Soal STS Baru
            </a>
        </div>

        @if ($stsList->count() > 0)
            <x-ui.card>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-[hsl(var(--border))]">
                                <th class="text-left py-3 px-4 text-sm font-medium text-[hsl(var(--muted-foreground))]">
                                    Mata Pelajaran</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-[hsl(var(--muted-foreground))]">
                                    Kelas/Fase</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-[hsl(var(--muted-foreground))]">
                                    Topik</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-[hsl(var(--muted-foreground))]">
                                    Jumlah Soal</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-[hsl(var(--muted-foreground))]">
                                    Status</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-[hsl(var(--muted-foreground))]">
                                    Tanggal</th>
                                <th
                                    class="text-right py-3 px-4 text-sm font-medium text-[hsl(var(--muted-foreground))]">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stsList as $sts)
                                <tr class="border-b border-[hsl(var(--border))] hover:bg-[hsl(var(--accent))]">
                                    <td class="py-3 px-4 text-sm text-[hsl(var(--foreground))]">
                                        {{ $sts->mata_pelajaran }}</td>
                                    <td class="py-3 px-4 text-sm text-[hsl(var(--foreground))]">{{ $sts->kelas }} /
                                        {{ $sts->fase }}</td>
                                    <td class="py-3 px-4 text-sm text-[hsl(var(--foreground))]">
                                        {{ Str::limit($sts->topik, 40) }}</td>
                                    <td class="py-3 px-4 text-sm text-[hsl(var(--foreground))]">{{ $sts->jumlah_soal }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if ($sts->status === 'completed')
                                            <x-ui.badge variant="success">Selesai</x-ui.badge>
                                        @elseif($sts->status === 'processing')
                                            <x-ui.badge variant="warning">Proses</x-ui.badge>
                                        @elseif($sts->status === 'failed')
                                            <x-ui.badge variant="destructive">Gagal</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="secondary">Draft</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-[hsl(var(--muted-foreground))]">
                                        {{ $sts->created_at->format('d M Y') }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($sts->status === 'completed' || !empty($sts->content_result))
                                                <a href="{{ route('sts.show', $sts) }}"
                                                    class="btn btn-outline btn-sm">Lihat</a>
                                                <a href="{{ route('sts.pdf', $sts) }}"
                                                    class="btn btn-destructive btn-sm">PDF</a>
                                                <a href="{{ route('sts.word', $sts) }}"
                                                    class="btn btn-blue btn-sm">Word</a>
                                            @endif
                                            <form action="{{ route('sts.destroy', $sts) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus STS ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-ghost btn-sm text-[hsl(var(--destructive))]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <div class="mt-4">
                {{ $stsList->links() }}
            </div>
        @else
            <x-ui.card class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-[hsl(var(--muted-foreground))] mb-4" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <h3 class="text-lg font-medium text-[hsl(var(--foreground))] mb-2">Belum ada soal STS</h3>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mb-6">Mulai buat soal STS pertama Anda dengan
                    bantuan AI.</p>
                <a href="{{ route('sts.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Soal STS Pertama
                </a>
            </x-ui.card>
        @endif
    </div>
</x-app-layout>
