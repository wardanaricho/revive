<?php

namespace App\Services\Bpjs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SuratKontrolBpjsService
{
    protected BpjsService $bpjs;

    public function __construct(BpjsService $bpjs)
    {
        $this->bpjs = $bpjs;
    }

    public function getBySep(string $noSep): array
    {
        $url = $this->bpjs->getBaseUrl() . "/RencanaKontrol/nosep/" . $noSep;

        $response = Http::withHeaders(
            $this->bpjs->getHeaders()
        )->get($url);

        $result = $response->json();

        if (isset($result['response'])) {

            $decrypted = $this->bpjs->decrypt(
                $result['response']
            );

            $result['response'] = json_decode($decrypted, true);
        }

        return $result;
    }

    public function getByNoSurat(string $noSurat): array
    {
        $url = $this->bpjs->getBaseUrl() . "/RencanaKontrol/noSuratKontrol/" . $noSurat;

        $response = Http::withHeaders(
            $this->bpjs->getHeaders()
        )->get($url);

        $result = $response->json();

        if (isset($result['response'])) {

            $decrypted = $this->bpjs->decrypt(
                $result['response']
            );

            $result['response'] = json_decode($decrypted, true);
        }

        return $result;
    }

    public function storeSuratKontrol(Request $request): array
    {
        $url = $this->bpjs->getBaseUrl() . "/RencanaKontrol/insert";

        $payload = [
            "request" => [
                "noSEP" => $request->noSEP,
                "kodeDokter" => $request->kodeDokter,
                "poliKontrol" => $request->poliKontrol,
                "tglRencanaKontrol" => $request->tglRencanaKontrol,
                "user" => $request->user
            ]
        ];

        $response = Http::withHeaders(
            $this->bpjs->getHeaders()
        )
            ->withBody(
                json_encode($payload),
                'Application/x-www-form-urlencoded'
            )
            ->post($url);

        $result = $response->json();

        // decrypt response BPJS
        if (isset($result['response'])) {

            $decrypted = $this->bpjs->decrypt(
                $result['response']
            );

            $result['response'] = json_decode($decrypted, true);
        }

        return $result;
    }
}
