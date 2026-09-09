<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreRppRequest;
use App\Http\Resources\Api\V1\RppResource;
use App\Jobs\GenerateRpp;
use App\Models\Rpp;
use App\Models\SchoolSetting;
use App\Services\RppWordExporter;
use App\Support\ApiErrorCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use PhpOffice\PhpWord\IOFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class RppController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Rpp::query()->where('user_id', $request->user()->id);
        $query->when($request->filled('search'), fn ($q) => $q->where(fn ($nested) => $nested
            ->where('mata_pelajaran', 'like', '%'.$request->string('search').'%')
            ->orWhere('topik', 'like', '%'.$request->string('search').'%')
            ->orWhere('nama_guru', 'like', '%'.$request->string('search').'%')));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $paginator = $query->latest()->paginate(10);

        return api_response()->paginated($paginator, RppResource::collection($paginator));
    }

    public function store(StoreRppRequest $request): JsonResponse
    {
        $input = $request->generationInput();
        $fingerprint = hash('sha256', json_encode(Arr::sortRecursive($input), JSON_THROW_ON_ERROR));
        $existing = Rpp::query()->where('user_id', $request->user()->id)
            ->where('idempotency_key', $request->idempotencyKey())->first();

        if ($existing) {
            return $existing->request_fingerprint === $fingerprint
                ? api_response()->success(RppResource::make($existing))
                : api_response()->error('Idempotency key telah digunakan untuk payload berbeda.', ApiErrorCode::IdempotencyConflict, 409);
        }

        try {
            $rpp = Rpp::query()->create([
                ...Arr::only($input, [
                    'nama_guru', 'kepala_sekolah', 'nip_kepala_sekolah', 'kota', 'tanggal',
                    'mata_pelajaran', 'fase', 'kelas', 'semester', 'target_peserta_didik', 'topik',
                    'alokasi_waktu', 'jumlah_pertemuan', 'kompetensi_awal', 'kata_kunci',
                    'model_pembelajaran', 'jenis_asesmen', 'kurikulum', 'tema',
                ]),
                'user_id' => $request->user()->id,
                'generation_input' => $input,
                'idempotency_key' => $request->idempotencyKey(),
                'request_fingerprint' => $fingerprint,
                'status' => 'processing',
            ]);
        } catch (QueryException $exception) {
            $existing = Rpp::query()->where('user_id', $request->user()->id)
                ->where('idempotency_key', $request->idempotencyKey())->first();
            if (! $existing || $existing->request_fingerprint !== $fingerprint) {
                return api_response()->error('Idempotency key telah digunakan untuk payload berbeda.', ApiErrorCode::IdempotencyConflict, 409);
            }

            return api_response()->success(RppResource::make($existing));
        }

        GenerateRpp::dispatch($rpp->id)->afterCommit();

        return api_response()->success(RppResource::make($rpp), 202);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        return api_response()->success(RppResource::make($this->owned($request, $id)));
    }

    public function status(Request $request, int $id): JsonResponse
    {
        return api_response()->success(RppResource::make($this->owned($request, $id)));
    }

    public function destroy(Request $request, int $id): Response
    {
        $this->owned($request, $id)->delete();

        return response()->noContent();
    }

    public function pdf(Request $request, int $id): Response
    {
        $rpp = $this->completed($request, $id);
        $deepLearning = $rpp->kurikulum === 'Kurikulum Merdeka Deep Learning';
        $pdf = Pdf::loadView($deepLearning ? 'rpp.pdf_deep_learning' : 'rpp.pdf', [
            'rpp' => $rpp,
            'schoolSettings' => SchoolSetting::getSettings(),
        ])->setPaper('A4', 'portrait');
        $filename = ($deepLearning ? 'RPPM_' : 'ModulAjar_').str_replace(' ', '_', $rpp->mata_pelajaran)."_{$rpp->id}.pdf";

        return $pdf->download($filename);
    }

    public function docx(Request $request, int $id, RppWordExporter $exporter): BinaryFileResponse|JsonResponse
    {
        $rpp = $this->completed($request, $id);
        $document = $exporter->export($rpp, SchoolSetting::getSettings());
        $tempFile = tempnam(sys_get_temp_dir(), 'rpp-word-');
        IOFactory::createWriter($document, 'Word2007')->save($tempFile);

        return response()->download($tempFile, 'RPPM_'.str_replace(' ', '_', $rpp->mata_pelajaran)."_{$rpp->id}.docx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    private function owned(Request $request, int $id): Rpp
    {
        return Rpp::query()->where('user_id', $request->user()->id)->findOrFail($id);
    }

    private function completed(Request $request, int $id): Rpp
    {
        $rpp = $this->owned($request, $id);
        if ($rpp->status !== 'completed' || ! $rpp->content_result) {
            throw new HttpResponseException(
                api_response()->error('RPP belum siap diunduh.', ApiErrorCode::ResourceNotReady, 409)
            );
        }

        return $rpp;
    }
}
