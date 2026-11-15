<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KetentuanMagang;

class InformasiController extends Controller
{
    public function index()
    {
        $ketentuan = KetentuanMagang::latest('created_at')->get();

        return view('mahasiswa.informasi', compact('ketentuan'));
    }
}
