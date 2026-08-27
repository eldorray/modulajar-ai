<?php

namespace App\Http\Controllers\Pwa;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Rpp;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Auth;

class GuruAppController extends Controller
{
    /**
     * Beranda: statistik modul ajar milik guru.
     */
    public function home()
    {
        $userId = Auth::id();

        $counts = Rpp::forUser($userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            ")
            ->first();

        $stats = [
            'total' => (int) $counts->total,
            'completed' => (int) $counts->completed,
            'processing' => (int) $counts->processing,
            'failed' => (int) $counts->failed,
        ];

        $tokens = (int) AiUsageLog::where('user_id', $userId)->sum('total_tokens');

        $perMapel = Rpp::forUser($userId)
            ->selectRaw('mata_pelajaran, COUNT(*) as jumlah')
            ->groupBy('mata_pelajaran')
            ->orderByDesc('jumlah')
            ->limit(4)
            ->get();

        return view('pwa.home', [
            'stats' => $stats,
            'tokens' => $tokens,
            'perMapel' => $perMapel,
            'bulanIni' => Rpp::forUser($userId)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'recent' => Rpp::forUser($userId)->latest()->limit(3)->get(),
        ]);
    }

    /**
     * Daftar modul ajar milik guru.
     */
    public function index()
    {
        return view('pwa.rpp-index', [
            'rpps' => Rpp::forUser(Auth::id())->latest()->paginate(10),
        ]);
    }

    /**
     * Form generate modul ajar (versi mobile).
     */
    public function create()
    {
        return view('pwa.rpp-create');
    }

    /**
     * Detail ringkas satu modul ajar.
     */
    public function show(Rpp $rpp)
    {
        if ($rpp->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('pwa.rpp-show', compact('rpp'));
    }

    /**
     * Akun guru.
     */
    public function akun()
    {
        return view('pwa.akun', [
            'user' => Auth::user(),
            'rppCount' => Rpp::forUser(Auth::id())->count(),
            'units' => SchoolSetting::whereIn('jenjang', SchoolSetting::JENJANG)->get(),
        ]);
    }

    /**
     * Halaman offline untuk service worker.
     */
    public function offline()
    {
        return view('pwa.offline');
    }
}
