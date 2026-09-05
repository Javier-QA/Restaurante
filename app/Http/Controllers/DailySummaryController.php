<?php

namespace App\Http\Controllers;

use App\Models\DailySummary;
use App\Services\Sunat\DailySummaryBuilder;
use App\Services\Sunat\SunatConfig;
use App\Services\Sunat\SunatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Resumen Diario de Boletas (RC) - SUNAT.
 *
 * Las boletas (tipo 03) se comunican a SUNAT en bloque diariamente.
 * El envío es asíncrono: SUNAT responde con un TICKET, y luego hay
 * que consultar el estado con ese ticket para obtener el CDR.
 */
class DailySummaryController extends Controller
{
    public function index()
    {
        $summaries = DailySummary::with('user')
            ->orderByDesc('reference_date')
            ->orderByDesc('correlativo')
            ->paginate(25);

        return view('daily_summaries.index', compact('summaries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_date' => 'required|date|before_or_equal:today',
        ]);

        $refDate = Carbon::parse($request->input('reference_date'));

        try {
            $built = (new DailySummaryBuilder(new SunatConfig()))
                ->build($refDate, Auth::id());

            (new SunatService())->sendSummary($built['model'], $built['summary']);
            $built['model']->refresh();
        } catch (\Throwable $e) {
            Log::error('DailySummary store', ['date' => $refDate->toDateString(), 'msg' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('daily_summaries.index')
            ->with('success', "Resumen {$built['model']->identifier}: {$built['model']->sunat_status}");
    }

    public function check(DailySummary $summary)
    {
        if (!$summary->ticket) {
            return back()->with('error', 'El resumen no tiene ticket asignado.');
        }

        try {
            (new SunatService())->checkSummaryTicket($summary);
            $summary->refresh();
        } catch (\Throwable $e) {
            return back()->with('error', 'Excepción: ' . $e->getMessage());
        }

        $msg = "Resumen {$summary->identifier}: {$summary->sunat_status}";
        return back()->with($summary->sunat_status === 'ACCEPTED' ? 'success' : 'error', $msg);
    }

    public function downloadXml(DailySummary $summary)
    {
        return $this->stream($summary->xml_path, 'application/xml');
    }

    public function downloadCdr(DailySummary $summary)
    {
        return $this->stream($summary->cdr_path, 'application/zip');
    }

    private function stream(?string $relPath, string $mime)
    {
        if (!$relPath || !Storage::disk('local')->exists($relPath)) {
            abort(404, 'Archivo no encontrado.');
        }
        return response()->stream(function () use ($relPath) {
            echo Storage::disk('local')->get($relPath);
        }, 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'attachment; filename="' . basename($relPath) . '"',
        ]);
    }
}
