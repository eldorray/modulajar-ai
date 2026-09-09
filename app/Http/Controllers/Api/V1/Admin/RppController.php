<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RppResource;
use App\Models\Rpp;
use App\Models\SchoolSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class RppController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Rpp::with('user')->whereHas('user', fn ($owner) => $owner->where('role', 'guru'));
        $query->when($request->filled('search'), fn ($q) => $q->where(fn ($nested) => $nested
            ->where('mata_pelajaran', 'like', '%'.$request->string('search').'%')
            ->orWhere('topik', 'like', '%'.$request->string('search').'%')
            ->orWhere('nama_guru', 'like', '%'.$request->string('search').'%')));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $paginator = $query->latest()->paginate(15);

        return api_response()->paginated($paginator, RppResource::collection($paginator));
    }

    public function show(Rpp $rpp)
    {
        return api_response()->success(RppResource::make($rpp));
    }

    public function pdf(Rpp $rpp)
    {
        abort_if($rpp->status !== 'completed' || ! $rpp->content_result, 409, 'RPP belum siap diunduh.');
        $deepLearning = $rpp->kurikulum === 'Kurikulum Merdeka Deep Learning';
        $pdf = Pdf::loadView($deepLearning ? 'rpp.pdf_deep_learning' : 'rpp.pdf', [
            'rpp' => $rpp,
            'schoolSettings' => SchoolSetting::getSettings(),
        ])->setPaper('A4', 'portrait');

        return $pdf->download(($deepLearning ? 'RPPM_' : 'ModulAjar_').str_replace(' ', '_', $rpp->mata_pelajaran)."_{$rpp->id}.pdf");
    }
}
