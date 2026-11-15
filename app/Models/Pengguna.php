<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasUuids, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['email', 'password', 'nama_lengkap', 'no_telepon', 'role', 'status_aktif'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'status_aktif' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function uniqueIds(): array
    {
        return ['id_pengguna'];
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'id_pengguna');
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'id_pengguna');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_pengguna');
    }
}
