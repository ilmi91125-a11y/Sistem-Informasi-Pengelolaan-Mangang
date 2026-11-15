<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PendaftaranMagang extends Model
{
    use HasUuids;

    protected $table = 'pendaftaran_magang';
    protected $primaryKey = 'id_pendaftaran';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_mahasiswa', 'periode_magang', 'tanggal_daftar', 'status_pendaftaran'];

    protected function casts(): array
    {
        return [
            'tanggal_daftar' => 'date',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_pendaftaran'];
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function pengajuan()
    {
        return $this->hasMany(PengajuanTempat::class, 'id_pendaftaran');
    }
}
