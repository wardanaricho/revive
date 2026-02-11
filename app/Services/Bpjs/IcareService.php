<?php

namespace App\Services\Bpjs;

use Illuminate\Support\Facades\Http;

class IcareService
{
    protected BpjsService $bpjs;

    public function __construct(BpjsService $bpjs)
    {
        $this->bpjs = $bpjs;
    }

    public function getIcare(string $noKartu, string $kodeDokter)
    {
        $url = $this->bpjs->getBaseUrl() . "wsihs/api/rs/validate";

        $body = [
            'param' => $noKartu,
            'kodedokter' => intval($kodeDokter)
        ];

        $response = Http::withHeaders($this->bpjs->getHeaders())
            ->post($url, $body);


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
