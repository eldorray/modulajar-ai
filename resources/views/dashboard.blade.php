<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="space-y-6">
        <!-- Welcome Card -->
        <x-ui.card>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[hsl(var(--primary))] flex items-center justify-center">
                    <span class="text-xl font-semibold text-[hsl(var(--primary-foreground))]">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">
                        Selamat datang, {{ auth()->user()->name }}!
                    </h2>
                    <p class="text-[hsl(var(--muted-foreground))]">
                        Buat RPP/Modul Ajar dengan bantuan AI dalam hitungan detik.
                    </p>
                </div>
            </div>
        </x-ui.card>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.card>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[hsl(var(--foreground))]">{{ $stats['total_rpp'] }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Total RPP</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[hsl(var(--foreground))]">{{ $stats['completed_rpp'] }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Selesai</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[hsl(var(--foreground))]">{{ $stats['processing_rpp'] }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Diproses</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[hsl(var(--foreground))]">{{ $stats['failed_rpp'] }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Gagal</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        @if (auth()->user()->isAdmin() && $adminStats)
            <!-- Admin Stats -->
            <div class="pt-4 border-t border-[hsl(var(--border))]">
                <h3 class="text-lg font-semibold mb-4 text-[hsl(var(--foreground))]">Statistik Admin</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-ui.card>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-[hsl(var(--foreground))]">{{ $adminStats['total_users'] }}
                            </p>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">Total Users</p>
                        </div>
                    </x-ui.card>
                    <x-ui.card>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-[hsl(var(--foreground))]">{{ $adminStats['total_rpps'] }}
                            </p>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">Total RPP</p>
                        </div>
                    </x-ui.card>
                    <x-ui.card>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-[hsl(var(--foreground))]">
                                {{ number_format($adminStats['deepseek']['total_tokens'] + $adminStats['gemini']['total_tokens']) }}
                            </p>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">Total Token AI</p>
                        </div>
                    </x-ui.card>
                    <x-ui.card class="bg-gradient-to-br from-green-50 to-emerald-50 border-green-200">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-700">Rp
                                {{ number_format($adminStats['total_cost_idr'], 0, ',', '.') }}</p>
                            <p class="text-sm text-green-600">Estimasi Biaya AI</p>
                        </div>
                    </x-ui.card>
                </div>

                <!-- Per-Provider Token Breakdown -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- DeepSeek -->
                    <x-ui.card class="border-blue-200">
                        <x-slot name="header">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                <h4 class="font-semibold text-blue-700">DeepSeek Chat</h4>
                            </div>
                        </x-slot>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-xl font-bold text-blue-700">
                                    {{ number_format($adminStats['deepseek']['total_tokens']) }}</p>
                                <p class="text-xs text-[hsl(var(--muted-foreground))]">Total Tokens</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">
                                    {{ number_format($adminStats['deepseek']['input_tokens']) }} in</p>
                                <p class="text-sm text-gray-600">
                                    {{ number_format($adminStats['deepseek']['output_tokens']) }} out</p>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-green-600">Rp
                                    {{ number_format($adminStats['deepseek']['cost']['total_cost_idr'], 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-[hsl(var(--muted-foreground))]">Biaya</p>
                            </div>
                        </div>
                    </x-ui.card>

                    <!-- Gemini -->
                    <x-ui.card class="border-purple-200">
                        <x-slot name="header">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                                <h4 class="font-semibold text-purple-700">Gemini 2.5 Flash</h4>
                            </div>
                        </x-slot>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-xl font-bold text-purple-700">
                                    {{ number_format($adminStats['gemini']['total_tokens']) }}</p>
                                <p class="text-xs text-[hsl(var(--muted-foreground))]">Total Tokens</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">
                                    {{ number_format($adminStats['gemini']['input_tokens']) }} in</p>
                                <p class="text-sm text-gray-600">
                                    {{ number_format($adminStats['gemini']['output_tokens']) }} out</p>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-green-600">Rp
                                    {{ number_format($adminStats['gemini']['cost']['total_cost_idr'], 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-[hsl(var(--muted-foreground))]">Biaya</p>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        @endif

        <!-- User Token Usage (Admin Only) -->
        @if (auth()->user()->isAdmin() && $userTokens && $userTokens->total_tokens > 0)
            <x-ui.card>
                <x-slot name="header">
                    <h4 class="font-semibold text-[hsl(var(--foreground))]">Penggunaan Token Anda</h4>
                </x-slot>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Input</p>
                        <p class="text-lg font-semibold">{{ number_format($userTokens->input_tokens) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Output</p>
                        <p class="text-lg font-semibold">{{ number_format($userTokens->output_tokens) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Total Token</p>
                        <p class="text-lg font-semibold">{{ number_format($userTokens->total_tokens) }}</p>
                    </div>
                    <div class="text-center bg-green-50 rounded-lg py-2">
                        <p class="text-sm text-green-600">Est. Biaya</p>
                        <p class="text-lg font-bold text-green-700">Rp
                            {{ number_format($userCost['total_cost_idr'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </x-ui.card>
        @endif

        <!-- Recent RPPs -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">RPP Terbaru</h3>
                <a href="{{ route('rpp.create') }}" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    Buat RPP
                </a>
            </div>

            @if ($recentRpps->count() > 0)
                <x-ui.card class="p-0">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Topik</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Fase</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentRpps as $rpp)
                                    <tr>
                                        <td class="font-medium">{{ Str::limit($rpp->topik, 40) }}</td>
                                        <td>{{ $rpp->mata_pelajaran }}</td>
                                        <td>{{ $rpp->fase }}</td>
                                        <td>
                                            @if ($rpp->status === 'completed')
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
                                        <td>
                                            <a href="{{ route('rpp.show', $rpp) }}" class="btn btn-ghost btn-sm">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            @else
                <x-ui.card>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-[hsl(var(--muted-foreground))] mb-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-medium text-[hsl(var(--foreground))] mb-2">Belum ada RPP</h3>
                        <p class="text-[hsl(var(--muted-foreground))] mb-4">Mulai buat RPP pertama Anda dengan bantuan
                            AI.</p>
                        <a href="{{ route('rpp.create') }}" class="btn btn-primary">
                            Buat RPP Sekarang
                        </a>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-app-layout>
