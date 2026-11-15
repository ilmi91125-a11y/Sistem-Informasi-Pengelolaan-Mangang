<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasUuids;

    protected $table = 'dosen';
    protected $primaryKey = 'id_dosen';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_pengguna', 'nidn', 'bidang_keahlian'];

    public function uniqueIds(): array
    {
        return ['id_dosen'];
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function mahasiswaPa()
    {
        return $this->hasMany(Mahasiswa::class, 'id_dosen_pa');
    }

    public function penempatanBimbingan()
    {
        return $this->hasMany(PenempatanMagang::class, 'id_dosen_pembimbing');
    }

    public function verifikasi()
    {
        return $this->hasMany(VerifikasiPengajuan::class, 'id_dosen');
    }
}
