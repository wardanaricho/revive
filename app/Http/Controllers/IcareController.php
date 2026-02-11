<?php

namespace App\Http\Controllers;

use App\Services\Bpjs\IcareService;
use Illuminate\Http\Request;

class IcareController extends Controller
{
    protected $service;

    public function __construct(IcareService $service)
    {
        $this->service = $service;
    }

    public function getIcare(string $noKartu, string $kodeDokter)
    {
        $result = $this->service->getIcare(
            $noKartu,
            $kodeDokter
        );

        return response()->json($result);
    }
}
