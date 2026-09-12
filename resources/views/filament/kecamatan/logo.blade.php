<div class="flex mx-auto">
    <img src="images/logotgx.png" alt="">
    <p class="my-auto font-sans font-semibold text-center">
        {{ Auth::check() ? Auth::user()->name : 'Musabaqah Tilawatil Quran Tingkat Kabupaten Trenggalek Tahun '. date('Y') }}
    </p>
</div>
