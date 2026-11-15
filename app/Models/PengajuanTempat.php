<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PengajuanTempat extends Model
{
    use HasUuids;

    protected $table = 'pengajuan_tempat';
    protected $primaryKey = 'id_pengajuan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_pendaftaran', 'id_tempat', 'rencana_kegiatan', 'rencana_mulai', 'rencana_selesai', 'tanggal_pengajuan', 'status_pengajuan'];

    protected function casts(): array
    {
        return [
            'rencana_mulai' => 'date',
            'rencana_selesai' => 'date',
            'tanggal_pengajuan' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_pengajuan'];
    }

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranMagang::class, 'id_pendaftaran');
    }

    public function tempat()
    {
        return $this->belongsTo(TempatMagang::class, 'id_tempat');
    }

    public function berkas()
    {
        return $this->hasMany(BerkasPengajuan::class, 'id_pengajuan');
    }

    public function verifikasi()
    {
        return $this->hasMany(VerifikasiPengajuan::class, 'id_pengajuan');
    }

    public function surat()
    {
        return $this->hasOne(SuratMagang::class, 'id_pengajuan');
    }
}
