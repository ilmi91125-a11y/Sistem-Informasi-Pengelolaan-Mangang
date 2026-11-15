<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasUuids;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_pengguna', 'id_dosen_pa', 'nim', 'program_studi', 'angkatan'];

    public function uniqueIds(): array
    {
        return ['id_mahasiswa'];
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function dosenPa()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen_pa');
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranMagang::class, 'id_mahasiswa');
    }

    public function penempatan()
    {
        return $this->hasMany(PenempatanMagang::class, 'id_mahasiswa');
    }
}
