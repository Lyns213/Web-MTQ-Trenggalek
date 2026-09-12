<div>
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="refresh" content="20">
        <title>MTQ KABUPATEN TRENGGALEK {{ date('Y') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">
        <link rel="icon" href="{{ url(asset('logotgxmini.png')) }}">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f0f0;
        }

        .bg-mtq-blue {
            background-color: #3B64CA;
        }

        .text-mtq-blue {
            color: #3B64CA;
        }

        .border-mtq-blue {
            border-color: #3B64CA;
        }

        .custom-shadow {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* New Donut Style */
        .donut {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(#3B64CA 0%, #3B64CA var(--percentage), #e0e0e0 var(--percentage), #e0e0e0 100%);
        }

        .donut .timer-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            font-weight: 600;
            color: #3B64CA;
        }

        .timer-controls button {
            padding: 10px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            margin: 0 5px;
            transition: background-color 0.3s ease;
        }

        .timer-controls button:hover {
            opacity: 0.9;
        }

        #startBtn {
            background-color: #34d399;
        }

        #pauseBtn {
            background-color: #fbbf24;
        }

        #restartBtn {
            background-color: #ef4444;
        }

        .pukimak {
            border: 1px solid #3B64CA;
        }

        .my-slider {
            position: relative;
        }

        .my-slider .item {
            width: 200px;
            margin: 0 auto;
            text-align: center;
            padding: 10px; /* Padding for each item */
        }
    </style>
</head>

<body class="p-4">
    @if(isset($emptyState) && $emptyState)
    <div class="max-w-3xl mx-auto overflow-hidden bg-white rounded-lg custom-shadow">
        <div class="text-center py-20 px-4">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Peserta</h3>
            <p class="mt-2 text-sm text-gray-500">Belum ada peserta yang terdaftar untuk tahun ini.</p>
        </div>
    </div>
@else
<div class="max-w-3xl mx-auto overflow-hidden bg-white rounded-lg custom-shadow">
        <!-- Header -->
        <div class="flex items-center p-3 text-white bg-mtq-blue">
            <img src="{{ asset('images/logotgxmini.png') }}" alt="Logo" class="w-10 h-10 mr-3">
            <h1 class="text-xl font-bold">MTQ KABUPATEN TRENGGALEK 2024</h1>
        </div>

        <!-- Kolom atas -->
        <div class="flex">
            <!-- Informasi Mahasiswa -->
            <div class="flex-1 p-4 bg-white rounded-lg shadow-md">
                <div class="flex h-full">
                    <img src="{{ asset('storage/' . $currentRecord->peserta->pasfoto) }}" alt="Contestant" class="object-cover w-1/3 mr-4 rounded">
                    <div class="flex-1">
                        <div class="p-1 mb-6">
                            <h2 class="text-xs font-semibold text-gray-600">Nama</h2>
                            <p class="pl-2 text-lg font-bold pukimak">{{ $currentRecord->peserta->no_peserta }}</p>
                        </div>
                        <!--<div class="p-1 mb-6">-->
                        <!--    <h2 class="text-xs font-semibold text-gray-600">Asal</h2>-->
                        <!--    <p class="pl-2 text-lg font-bold pukimak">{{ $currentRecord->peserta->utusan->kecamatan }}</p>-->
                        <!--</div>-->
                        <div class="p-1 ">
                            <h2 class="text-xs font-semibold text-gray-600">Cabang</h2>
                            <p class="pl-2 text-lg font-bold pukimak">{{ $currentRecord->peserta->cabang->nama_cabang }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Countdown Timer -->
            <!--<div class="p-6 bg-white rounded-lg shadow-md" id="timer-container">-->
            <!--    <div id="timer-background" class="relative flex items-center justify-center mb-6 transition-colors duration-300">-->
            <!--        <canvas id="timerChart" width="150" height="150"></canvas>-->
            <!--        <div class="absolute inset-0 flex items-center justify-center">-->
            <!--            <p id="timer" class="text-4xl font-bold text-black">60</p>-->
            <!--        </div>-->
            <!--    </div>-->

                <!-- Tombol untuk kontrol timer -->
            <!--    <div class="flex justify-center space-x-4">-->
            <!--        <button id="startBtn" class="p-2 text-white bg-green-500 rounded-lg hover:bg-green-600">Start</button>-->
            <!--        <button id="pauseBtn" class="p-2 text-white bg-yellow-500 rounded-lg hover:bg-yellow-600">Pause</button>-->
            <!--        <button id="restartBtn" class="p-2 text-white bg-red-500 rounded-lg hover:bg-red-600">Restart</button>-->
            <!--    </div>-->
            <!--</div>-->
        </div>

        <!-- Kolom bawah -->
        <div class="flex p-4">
            <!-- Carousel Nilai -->
            <div class="pr-2" style="width: 65%;">
                <div class="my-slider">
                    @foreach ($records as $record)
                        <div class="p-2 mx-3 text-center text-white rounded bg-mtq-blue">
                            <h3 class="mb-1 text-xs">Tahfizh</h3>
                            <p class="text-3xl font-bold">{{ $currentRecord->tahfizh }}</p>
                        </div>
                        <div class="p-2 mx-3 text-center text-white rounded bg-mtq-blue">
                            <h3 class="mb-1 text-xs">Tajwid</h3>
                            <p class="text-3xl font-bold">{{ $currentRecord->tajwid }}</p>
                        </div>
                        <div class="p-2 mx-3 text-center text-white rounded bg-mtq-blue">
                            <h3 class="mb-1 text-xs">Fashahah</h3>
                            <p class="text-3xl font-bold">{{ $currentRecord->fashahah }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Total Nilai -->
            <div class="pl-2" style="width: 35%;">
                <div class="flex flex-col justify-center h-full p-2 text-center bg-gray-100 rounded">
                    <h3 class="mb-1 text-xs">Total Nilai</h3>
                    <p class="text-4xl font-bold text-mtq-blue">{{ $currentRecord->total }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-end justify-between px-4 pb-4">
            @if ($previousRecord)
                <a href="{{ route('nilai-tigapuluhjuz.index', $previousRecord->id) }}" class="px-6 py-2 text-sm font-semibold text-white bg-gray-800 rounded">Back</a>
            @else
                <button class="px-6 py-2 text-sm font-semibold text-white bg-gray-400 rounded" disabled>Back</button>
            @endif

            @if ($nextRecord)
                <a href="{{ route('nilai-tigapuluhjuz.index', $nextRecord->id) }}" class="px-6 py-2 text-sm font-semibold text-white bg-gray-800 rounded">Next</a>
            @else
                <button class="px-6 py-2 text-sm font-semibold text-white bg-gray-400 rounded" disabled>Next</button>
            @endif
        </div>
@endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Ambil elemen yang diperlukan dari DOM
        // let timerDisplay = document.getElementById('timer');
        // let startBtn = document.getElementById('startBtn');
        // let pauseBtn = document.getElementById('pauseBtn');
        // let restartBtn = document.getElementById('restartBtn');

        // // Mengambil waktu tersisa dari localStorage atau atur ke 60
        // let timeLeft = localStorage.getItem('timeLeft') ? parseInt(localStorage.getItem('timeLeft')) : 60;
        // let countdown;

        // // Variabel untuk menandakan apakah timer sedang berjalan
        // let isRunning = false;

        // // Inisialisasi Doughnut Chart untuk Timer
        // let ctx = document.getElementById('timerChart').getContext('2d');
        // let timerChart = new Chart(ctx, {
        //     type: 'doughnut',
        //     data: {
        //         datasets: [{
        //             data: [timeLeft, 60 - timeLeft], // [Time Left, Time Elapsed]
        //             backgroundColor: ['#3498db', '#e0e0e0'], // Blue and grey colors
        //             borderWidth: 0
        //         }]
        //     },
        //     options: {
        //         cutout: '80%',
        //         rotation: -90, // Mulai dari atas
        //         circumference: 360, // Lingkaran penuh
        //         responsive: false
        //     }
        // });

        // // Fungsi untuk memperbarui tampilan timer
        // function updateTimerDisplay() {
        //     timerDisplay.textContent = timeLeft;
        //     timerChart.data.datasets[0].data = [timeLeft, 60 - timeLeft];
        //     timerChart.update();

        //     // Ubah warna latar belakang berdasarkan waktu yang tersisa
        //     const timerContainer = document.getElementById('timer-container');
        //     if (timeLeft <= 15) {
        //         timerContainer.classList.remove('bg-yellow-500', 'bg-white');
        //         timerContainer.classList.add('bg-red-500'); // Merah
        //     } else if (timeLeft <= 30) {
        //         timerContainer.classList.remove('bg-white', 'bg-red-500');
        //         timerContainer.classList.add('bg-yellow-500'); // Kuning
        //     } else {
        //         timerContainer.classList.remove('bg-red-500', 'bg-yellow-500');
        //         timerContainer.classList.add('bg-white'); // Putih
        //     }
        // }

        // // Fungsi untuk memulai timer
        // function startTimer() {
        //     if (!isRunning) {
        //         isRunning = true;
        //         countdown = setInterval(function () {
        //             if (timeLeft > 0) {
        //                 timeLeft--;
        //                 localStorage.setItem('timeLeft', timeLeft); // Simpan ke localStorage
        //                 updateTimerDisplay();
        //             } else {
        //                 clearInterval(countdown);
        //                 isRunning = false;
        //                 alert('Waktu habis!');
        //                 localStorage.removeItem('timeLeft'); // Hapus waktu dari localStorage saat selesai
        //             }
        //         }, 1000);
        //     }
        // }

        // // Fungsi untuk menghentikan sementara (pause) timer
        // function pauseTimer() {
        //     clearInterval(countdown);
        //     isRunning = false;
        // }

        // // Fungsi untuk mereset timer
        // function restartTimer() {
        //     clearInterval(countdown);
        //     timeLeft = 60; // Setel kembali ke 60 detik
        //     localStorage.removeItem('timeLeft'); // Hapus dari localStorage
        //     updateTimerDisplay();
        //     isRunning = false;
        // }

        // // Event Listeners untuk tombol kontrol
        // startBtn.addEventListener('click', startTimer);
        // pauseBtn.addEventListener('click', pauseTimer);
        // restartBtn.addEventListener('click', restartTimer);

        // // Inisialisasi tampilan timer pertama kali
        // updateTimerDisplay();

        // // Menjalankan timer otomatis jika halaman di-refresh
        // if (localStorage.getItem('timeLeft') > 0) {
        //     startTimer();
        // }

        // Inisialisasi carousel
        var slider = tns({
            container: '.my-slider',
            items: 2, // Number of items to show at once
            slideBy: 1, // Slide one item at a time
            autoplay: true, // Enable auto sliding
            autoplayTimeout: 2000, // 2 seconds per slide
            speed: 1500, // Slow down the sliding speed
            autoplayButtonOutput: false, // Hide autoplay buttons
            controls: false, // Hide controls
            nav: false, // Hide navigation dots
            mouseDrag: true, // Allow dragging with mouse or touch
            gutter: 20, // Space between each item
            loop: true // Infinite loop
        });
    </script>
</body>

</html>
</div>
