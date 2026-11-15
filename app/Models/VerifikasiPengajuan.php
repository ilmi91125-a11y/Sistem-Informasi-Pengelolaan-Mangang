<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VerifikasiPengajuan extends Model
{
    use HasUuids;

    protected $table = 'verifikasi_pengajuan';
    protected $primaryKey = 'id_verifikasi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_pengajuan', 'id_dosen', 'keputusan', 'catatan', 'tanggal_verifikasi'];

    protected function casts(): array
    {
        return [
            'tanggal_verifikasi' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_verifikasi'];
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanTempat::class, 'id_pengajuan');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen');
    }
}
