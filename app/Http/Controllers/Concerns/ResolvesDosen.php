<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Dosen;
use Illuminate\Http\Request;

trait ResolvesDosen
{
    /** Ambil profil dosen milik pengguna yang login. */
    protected function dosen(Request $request): Dosen
    {
        $dosen = $request->user()->dosen;
        abort_if(! $dosen, 403, 'Profil dosen Anda belum dibuat oleh Tim Akademik.');

        return $dosen;
    }
}
