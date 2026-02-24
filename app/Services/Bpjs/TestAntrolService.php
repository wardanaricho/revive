<?php

namespace App\Services\Bpjs;

use Illuminate\Support\Facades\Http;

class TestAntrolService
{
    protected BpjsService $bpjs;
    protected $service;

    public function __construct(BpjsService $bpjs)
    {
        $this->bpjs = $bpjs;
        $this->service = 'antreanrs';
    }

    public function getListAntrianByTanggal(string $tgl): array
    {
        $url = $this->bpjs->getBaseUrl() . $this->service . "/antrean/pendaftaran/tanggal/" . $tgl;
        // dd($url);
        $response = Http::withHeaders(
            $this->bpjs->getHeaders()
        )->get($url);
        // dd($response);
        $result = $response->json();

        if (isset($result['response'])) {

            $decrypted = $this->bpjs->decrypt(
                $result['response']
            );

            $result['response'] = json_decode($decrypted, true);
        }

        return $result;
    }
}
