<?php

namespace App\Services\Bpjs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SuratKontrolBpjsService
{
    protected BpjsService $bpjs;
    protected $service;

    public function __construct(BpjsService $bpjs)
    {
        $this->bpjs = $bpjs;
        $this->service = 'vclaim-rest';
    }

    public function getBySep(string $noSep): array
    {
        $url = $this->bpjs->getBaseUrl() . $this->service . "/RencanaKontrol/nosep/" . $noSep;

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
        $url = $this->bpjs->getBaseUrl() . $this->service . "/RencanaKontrol/noSuratKontrol/" . $noSurat;

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
        $url = $this->bpjs->getBaseUrl() . $this->service . "/RencanaKontrol/insert";
        // dd($url);
        $payload = [
            "request" => [
                "noSEP" => $request->noSEP,
                "kodeDokter" => $request->kodeDokter,
                "poliKontrol" => $request->poliKontrol,
                "tglRencanaKontrol" => $request->tglRencanaKontrol,
                "user" => $request->user
            ]
        ];

        // dd($payload);

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

    public function updateSuratKontrol(Request $request): array
    {
        $url = $this->bpjs->getBaseUrl() . $this->service . "/RencanaKontrol/Update";

        $payload = [
            "request" => [
                "noSuratKontrol" => $request->noSuratKontrol,
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
            ->send('PUT', $url);

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


    public function deleteSuratKontrol($noSurat, $user): array
    {
        // dd($noSurat, $user);
        $url = $this->bpjs->getBaseUrl() . $this->service . "/RencanaKontrol/Delete";

        $payload = [
            "request" => [
                "t_suratkontrol" => [
                    "noSuratKontrol" => $noSurat,
                    "user" => $user
                ]
            ]
        ];

        $response = Http::withHeaders(
            $this->bpjs->getHeaders()
        )
            ->withBody(
                json_encode($payload),
                'Application/x-www-form-urlencoded'
            )
            ->send('DELETE', $url);

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
