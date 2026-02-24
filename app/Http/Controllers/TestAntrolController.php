<?php

namespace App\Http\Controllers;

use App\Services\Bpjs\TestAntrolService;

class TestAntrolController extends Controller
{
    protected $service;

    public function __construct(TestAntrolService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $tgl = date('Y-m-d');
        $data = $this->service->getListAntrianByTanggal("2026-02-18");

        return response()->json($data);
        // return view('test-antrol');
    }
}
