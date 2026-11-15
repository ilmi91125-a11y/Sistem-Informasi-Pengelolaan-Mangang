<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SuratMagang extends Model
{
    use HasUuids;

    protected $table = 'surat_magang';
    protected $primaryKey = 'id_surat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_pengajuan', 'id_pemroses', 'nomor_surat', 'tanggal_pengajuan', 'status_surat', 'catatan', 'tanggal_selesai', 'url_berkas'];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'datetime',
            'tanggal_selesai' => 'date',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_surat'];
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanTempat::class, 'id_pengajuan');
    }

    public function pemroses()
    {
        return $this->belongsTo(Pengguna::class, 'id_pemroses');
    }
}
