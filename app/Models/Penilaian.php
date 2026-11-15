<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasUuids;

    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_penempatan', 'nilai_dosen', 'nilai_instansi', 'nilai_akhir', 'catatan', 'tanggal_input'];

    protected function casts(): array
    {
        return [
            'nilai_dosen' => 'decimal:2',
            'nilai_instansi' => 'decimal:2',
            'nilai_akhir' => 'decimal:2',
            'tanggal_input' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_penilaian'];
    }

    public function penempatan()
    {
        return $this->belongsTo(PenempatanMagang::class, 'id_penempatan');
    }
}
