<x-app-layout>
    <x-slot name="header">RPP Guru</x-slot>

    <div class="space-y-6" x-data="{ deleteOpen: false, deleteAction: '', deleteTopic: '' }">
        <!-- Delete Confirmation Modal -->
        <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="deleteOpen = false">
            <div x-show="deleteOpen" x-transition.opacity class="absolute inset-0 bg-black/50" @click="deleteOpen = false"></div>
            <div x-show="deleteOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="relative w-full max-w-md rounded-lg border border-[hsl(var(--border))] bg-[hsl(var(--card))] p-6 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[hsl(var(--destructive))]/10">
                        <svg class="h-5 w-5 text-[hsl(var(--destructive))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">Hapus RPP</h3>
                        <p class="mt-1 text-sm text-[hsl(var(--muted-foreground))]">
                            Yakin ingin menghapus <span class="font-medium text-[hsl(var(--foreground))]" x-text="deleteTopic"></span>? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn btn-outline" @click="deleteOpen = false">Batal</button>
                    <form :action="deleteAction" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-destructive">Hapus</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">RPP Hasil Generate Guru</h2>
            <p class="text-[hsl(var(--muted-foreground))]">Semua Modul Ajar yang dibuat oleh akun guru.</p>
        </div>

        <!-- RPP List -->
        @if($rpps->count() > 0)
        <x-ui.card class="p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Topik</th>
                            <th>Guru</th>
                            <th>Mata Pelajaran</th>
                            <th>Fase</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rpps as $rpp)
                        <tr>
                            <td class="font-medium">{{ Str::limit($rpp->topik, 40) }}</td>
                            <td>{{ $rpp->user->name ?? $rpp->nama_guru }}</td>
                            <td>{{ $rpp->mata_pelajaran }}</td>
                            <td>{{ $rpp->fase }}</td>
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
                                    <a href="{{ route('rpp.show', $rpp) }}" class="btn btn-ghost btn-sm" title="Lihat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if($rpp->status === 'completed')
                                    <a href="{{ route('rpp.pdf', $rpp) }}" class="btn btn-ghost btn-sm" title="Unduh PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                    @endif
                                    <button type="button" class="btn btn-ghost btn-sm text-[hsl(var(--destructive))]" title="Hapus"
                                            @click="deleteOpen = true; deleteAction = '{{ route('rpp.destroy', $rpp) }}'; deleteTopic = @js(Str::limit($rpp->topik, 40))">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <!-- Pagination -->
        @if($rpps->hasPages())
        <div class="mt-4">
            {{ $rpps->links() }}
        </div>
        @endif
        @else
        <x-ui.card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-[hsl(var(--muted-foreground))] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-[hsl(var(--foreground))] mb-2">Belum ada RPP</h3>
                <p class="text-[hsl(var(--muted-foreground))]">Belum ada guru yang membuat Modul Ajar.</p>
            </div>
        </x-ui.card>
        @endif
    </div>
</x-app-layout>
