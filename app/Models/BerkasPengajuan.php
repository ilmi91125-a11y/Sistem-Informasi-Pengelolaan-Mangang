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

    protected $fillable = [
        'id_pengajuan',
        'jenis_berkas',
        'nama_berkas',
        'url_berkas',
        'tanggal_unggah'
    ];

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

    // Relasi ke model PengajuanTempat
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanTempat::class, 'id_pengajuan');
    }

    // Mengecek apakah berkas memiliki URL
    public function hasFile(): bool
    {
        return !empty($this->url_berkas);
    }

    // Mengecek apakah berkas sudah diunggah
    public function isUploaded(): bool
    {
        return !empty($this->tanggal_unggah);
    }

    // Mengubah jenis berkas menjadi format label
    public function getJenisBerkasLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->jenis_berkas ?? ''));
    }

    // Mengambil berkas berdasarkan jenisnya
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis_berkas', $jenis);
    }
}
