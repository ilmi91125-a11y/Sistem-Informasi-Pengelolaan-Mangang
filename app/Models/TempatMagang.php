<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TempatMagang extends Model
{
    use HasUuids;

    protected $table = 'tempat_magang';
    protected $primaryKey = 'id_tempat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['nama_instansi', 'alamat', 'bidang_usaha', 'kontak', 'deskripsi', 'sumber_data', 'status_aktif'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'status_aktif' => 'boolean',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_tempat'];
    }

    public function pengajuan()
    {
        return $this->hasMany(PengajuanTempat::class, 'id_tempat');
    }

    public function penempatan()
    {
        return $this->hasMany(PenempatanMagang::class, 'id_tempat');
    }
}
