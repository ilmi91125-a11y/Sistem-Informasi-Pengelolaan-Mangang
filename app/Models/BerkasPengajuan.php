<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BerkasPengajuan extends Model
{
    use HasUuids;

    protected $table = 'berkas_pengajuan';
    protected $primaryKey = 'id_berkas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_pengajuan', 'jenis_berkas', 'nama_berkas', 'url_berkas', 'tanggal_unggah'];

    protected function casts(): array
    {
        return [
            'tanggal_unggah' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_berkas'];
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanTempat::class, 'id_pengajuan');
    }
}
