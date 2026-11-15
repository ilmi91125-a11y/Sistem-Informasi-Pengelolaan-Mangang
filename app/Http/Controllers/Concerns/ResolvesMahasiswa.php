<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;

trait ResolvesMahasiswa
{
    /** Ambil profil mahasiswa milik pengguna yang login. */
    protected function mhs(Request $request): Mahasiswa
    {
        $mhs = $request->user()->mahasiswa;
        abort_if(! $mhs, 403, 'Profil mahasiswa Anda belum dibuat oleh Tim Akademik.');

        return $mhs;
    }
}
