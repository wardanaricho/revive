<?php

namespace App\Services\Bpjs;

use LZCompressor\LZString;

class BpjsService
{
    protected string $consId;
    protected string $secretKey;
    protected string $userKey;
    protected string $timestamp;
    protected string $baseUrl;

    public function __construct()
    {
        $this->consId    = config('bpjs.cons_id');
        $this->secretKey = config('bpjs.secret_key');
        $this->userKey   = config('bpjs.user_key');
        $this->baseUrl   = config('bpjs.base_url');

        $this->timestamp = $this->generateTimestamp();
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function generateTimestamp(): string
    {
        date_default_timezone_set('UTC');
        return (string) time();
    }

    public function signature(): string
    {
        $data = $this->consId . "&" . $this->timestamp;

        return base64_encode(
            hash_hmac(
                'sha256',
                $data,
                $this->secretKey,
                true
            )
        );
    }

    public function getHeaders(): array
    {
        return [
            'X-cons-id'  => $this->consId,
            'X-timestamp' => $this->timestamp,
            'X-signature' => $this->signature(),
            'user_key'   => $this->userKey,
            'Content-Type' => 'application/json'
        ];
    }

    public function decrypt(string $string): string
    {
        $key = $this->consId . $this->secretKey . $this->timestamp;

        $key_hash = hex2bin(hash('sha256', $key));

        $iv = substr(
            hex2bin(hash('sha256', $key)),
            0,
            16
        );

        $decrypted = openssl_decrypt(
            base64_decode($string),
            'AES-256-CBC',
            $key_hash,
            OPENSSL_RAW_DATA,
            $iv
        );

        return LZString::decompressFromEncodedURIComponent($decrypted);
    }
}
