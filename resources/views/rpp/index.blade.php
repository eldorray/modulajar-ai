<x-app-layout>
    <x-slot name="header">RPP Saya</x-slot>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">Daftar RPP</h2>
                <p class="text-[hsl(var(--muted-foreground))]">Kelola semua RPP yang telah Anda buat.</p>
            </div>
            <a href="{{ route('rpp.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat RPP Baru
            </a>
        </div>

        <!-- RPP List -->
        @if($rpps->count() > 0)
        <x-ui.card class="p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Topik</th>
                            <th>Mata Pelajaran</th>
                            <th>Fase</th>
                            <th>Nama Guru</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rpps as $rpp)
                        <tr>
                            <td class="font-medium">{{ Str::limit($rpp->topik, 40) }}</td>
                            <td>{{ $rpp->mata_pelajaran }}</td>
                            <td>{{ $rpp->fase }}</td>
                            <td>{{ $rpp->nama_guru }}</td>
                            <td>
                                @if($rpp->status === 'completed')
                                    <x-ui.badge variant="success">Selesai</x-ui.badge>
                                @elseif($rpp->status === 'processing')
                                    <x-ui.badge variant="warning">Diproses</x-ui.badge>
                                @elseif($rpp->status === 'failed')
                                    <x-ui.badge variant="destructive">Gagal</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary">Draft</x-ui.badge>
                                @endif
                            </td>
                            <td>{{ $rpp->created_at->format('d M Y') }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('rpp.show', $rpp) }}" class="btn btn-ghost btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if($rpp->status === 'completed')
                                    <a href="{{ route('rpp.pdf', $rpp) }}" class="btn btn-ghost btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                    @endif
                                    <form action="{{ route('rpp.destroy', $rpp) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus RPP ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm text-[hsl(var(--destructive))]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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

        <!-- Pagination -->
        <div class="mt-4">
            {{ $rpps->links() }}
        </div>
        @else
        <x-ui.card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-[hsl(var(--muted-foreground))] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-[hsl(var(--foreground))] mb-2">Belum ada RPP</h3>
                <p class="text-[hsl(var(--muted-foreground))] mb-6">Mulai buat RPP pertama Anda dengan bantuan AI.</p>
                <a href="{{ route('rpp.create') }}" class="btn btn-primary btn-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat RPP Sekarang
                </a>
            </div>
        </x-ui.card>
        @endif
    </div>
</x-app-layout>
