<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LaporanMagang extends Model
{
    use HasUuids;

    protected $table = 'laporan_magang';
    protected $primaryKey = 'id_laporan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_penempatan', 'judul_laporan', 'url_berkas', 'tanggal_unggah', 'status_laporan', 'catatan_dosen'];

    protected function casts(): array
    {
        return [
            'tanggal_unggah' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_laporan'];
    }

    public function penempatan()
    {
        return $this->belongsTo(PenempatanMagang::class, 'id_penempatan');
    }
}
