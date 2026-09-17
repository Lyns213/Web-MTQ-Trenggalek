@php
    $role = $item['role'] ?? 'peserta';
    $bg = $bgTemplates[$role] ?? $bgTemplates['peserta'];
@endphp

<div class="card-box">
    <!-- Template background image -->
    <img src="{{ $bg }}" class="bg-img">

    <!-- Subrole (e.g. TARTIL PUTRA) -->
    @if(!empty($item['subrole']))
        <div class="subrole-wrap">
            <span class="subrole-text">{{ $item['subrole'] }}</span>
        </div>
    @endif

    <!-- Pasfoto inside the photo frame -->
    <div class="photo-wrap">
        @if(!empty($item['photo_base64']))
            <img src="{{ $item['photo_base64'] }}" class="photo-img">
        @else
            <div class="photo-ph">&#128100;</div>
        @endif
    </div>

    @if($role === 'peserta')
        <!-- Nomor Peserta -->
        <div class="number-wrap">
            <span class="number-text">{{ $item['nomor'] ?? '-' }}</span>
        </div>

        <!-- Nama Peserta -->
        <div class="name-wrap-peserta">
            <span class="name-text">{{ $item['nama'] ?? '-' }}</span>
        </div>
    @else
        <!-- Nama for Dewan Hakim / Panitera / Panitia -->
        <div class="name-wrap-official">
            <span class="name-text">{{ $item['nama'] ?? '-' }}</span>
        </div>
    @endif
</div>
