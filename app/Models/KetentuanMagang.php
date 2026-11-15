<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class KetentuanMagang extends Model
{
    use HasUuids;

    protected $table = 'ketentuan_magang';
    protected $primaryKey = 'id_ketentuan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_pengguna', 'judul', 'isi_ketentuan', 'url_berkas'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_ketentuan'];
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}
