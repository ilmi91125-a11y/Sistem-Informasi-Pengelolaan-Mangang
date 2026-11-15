<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasUuids;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_pengguna', 'jenis_notifikasi', 'judul', 'pesan', 'id_referensi', 'sudah_dibaca'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'sudah_dibaca' => 'boolean',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_notifikasi'];
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}
