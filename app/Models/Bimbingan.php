<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Bimbingan extends Model
{
    use HasUuids;

    protected $table = 'bimbingan';
    protected $primaryKey = 'id_bimbingan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_penempatan', 'tanggal_bimbingan', 'topik', 'uraian_kegiatan', 'tanggapan_dosen', 'tanggal_tanggapan', 'status_bimbingan'];

    protected function casts(): array
    {
        return [
            'tanggal_bimbingan' => 'date',
            'tanggal_tanggapan' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_bimbingan'];
    }

    public function penempatan()
    {
        return $this->belongsTo(PenempatanMagang::class, 'id_penempatan');
    }
}
