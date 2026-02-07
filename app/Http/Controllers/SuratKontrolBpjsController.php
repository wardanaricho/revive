<?php

namespace App\Http\Controllers;

use App\Services\Bpjs\SuratKontrolBpjsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuratKontrolBpjsController extends Controller
{
    protected SuratKontrolBpjsService $service;

    public function __construct(SuratKontrolBpjsService $service)
    {
        $this->service = $service;
    }

    public function getBySep(string $noSep): JsonResponse
    {
        $result = $this->service->getBySep($noSep);

        return response()->json($result);
    }

    public function getByNoSurat(string $noSurat): JsonResponse
    {
        $result = $this->service->getByNoSurat($noSurat);

        return response()->json($result);
    }

    public function storeSuratKontrol(Request $request)
    {
        $request->validate([
            'noSEP' => 'required',
            'kodeDokter' => 'required',
            'poliKontrol' => 'required',
            'tglRencanaKontrol' => 'required|date',
            'user' => 'required'
        ]);

        $result = $this->service->storeSuratKontrol($request);

        if (
            !isset($result['metaData']) ||
            $result['metaData']['code'] != 200
        ) {

            return response()->json([
                'success' => false,
                'message' => $result['metaData']['message'] ?? 'Gagal insert ke BPJS',
                'bpjs_response' => $result
            ], 400);
        }

        $resp = $result['response'];

        DB::connection('mysql_2')->beginTransaction();
        try {
            $nm_poli_bpjs = DB::connection('mysql_2')
                ->table('maping_poli_bpjs')
                ->where('kd_poli_bpjs', $request->poliKontrol)
                ->value('nm_poli_bpjs');

            DB::connection('mysql_2')
                ->table('bridging_surat_kontrol_bpjs')
                ->insert([
                    'no_sep' => $request->noSEP,
                    'tgl_surat' => now()->toDateString(),
                    'no_surat' => $resp['noSuratKontrol'],
                    'tgl_rencana' => $resp['tglRencanaKontrol'],
                    'kd_dokter_bpjs' => $request->kodeDokter,
                    'nm_dokter_bpjs' => $resp['namaDokter'],
                    'kd_poli_bpjs' => $request->poliKontrol,
                    'nm_poli_bpjs' => $nm_poli_bpjs,
                ]);

            DB::connection('mysql_2')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Surat kontrol berhasil dibuat & disimpan',
                'data' => $resp

            ]);
        } catch (\Exception $e) {
            DB::connection('mysql_2')->rollBack();
            Log::error('Insert Surat Kontrol gagal', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'bpjs_response' => $result
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan ke database',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteSuratKontrol(Request $request): JsonResponse
    {
        $request->validate([
            'noSuratKontrol' => 'required',
            'user' => 'required'
        ]);

        $noSuratKontrol = $request->noSuratKontrol;
        $user = $request->user;

        // hit BPJS
        $result = $this->service->deleteSuratKontrol(
            $noSuratKontrol,
            $user
        );

        // cek response BPJS
        if (
            !isset($result['metaData']) ||
            $result['metaData']['code'] != 200
        ) {

            return response()->json([
                'success' => false,
                'message' => $result['metaData']['message'] ?? 'Gagal delete ke BPJS',
                'bpjs_response' => $result
            ], 400);
        }

        DB::connection('mysql_2')->beginTransaction();

        try {

            // cek apakah ada di database lokal
            $exists = DB::connection('mysql_2')
                ->table('bridging_surat_kontrol_bpjs')
                ->where('no_surat', $noSuratKontrol)
                ->exists();

            if (!$exists) {

                DB::connection('mysql_2')->rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Surat kontrol tidak ditemukan di database lokal'
                ], 404);
            }

            // hapus dari database lokal
            DB::connection('mysql_2')
                ->table('bridging_surat_kontrol_bpjs')
                ->where('no_surat', $noSuratKontrol)
                ->delete();

            DB::connection('mysql_2')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Surat kontrol berhasil dihapus',
                'data' => [
                    'noSuratKontrol' => $noSuratKontrol
                ]
            ]);
        } catch (\Exception $e) {

            DB::connection('mysql_2')->rollBack();

            Log::error('Delete Surat Kontrol gagal', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'bpjs_response' => $result
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dari database lokal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
