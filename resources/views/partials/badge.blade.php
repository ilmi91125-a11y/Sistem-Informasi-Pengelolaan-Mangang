@php
    $warna = match ($status ?? '') {
        'Diajukan', 'Diproses', 'Menunggu', 'Diunggah' => 'b-kuning',
        'Disetujui', 'Selesai', 'Ditanggapi', 'Diperiksa', 'Terdaftar' => 'b-hijau',
        'Ditolak', 'Dibatalkan' => 'b-merah',
        'Revisi', 'Perlu Dilengkapi' => 'b-biru',
        default => 'b-abu',
    };
@endphp
<span class="badge {{ $warna }}">{{ $status ?: '-' }}</span>
