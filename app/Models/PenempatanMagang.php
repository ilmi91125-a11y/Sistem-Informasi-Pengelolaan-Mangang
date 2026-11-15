<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PenempatanMagang extends Model
{
    use HasUuids;

    protected $table = 'penempatan_magang';
    protected $primaryKey = 'id_penempatan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_mahasiswa', 'id_tempat', 'id_dosen_pembimbing', 'tanggal_mulai', 'tanggal_selesai', 'status_magang'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_penempatan'];
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function tempat()
    {
        return $this->belongsTo(TempatMagang::class, 'id_tempat');
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen_pembimbing');
    }

    public function bimbingan()
    {
        return $this->hasMany(Bimbingan::class, 'id_penempatan');
    }

    public function laporan()
    {
        return $this->hasMany(LaporanMagang::class, 'id_penempatan');
    }

    public function penilaian()
    {
        return $this->hasOne(Penilaian::class, 'id_penempatan');
    }
}
