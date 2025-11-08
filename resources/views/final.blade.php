<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @vite(['resources/css/app.css'])
  <title>Hasil Akhir - SmartEmoCare</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
</head>

<body class="bg-[#FFFFF0] min-h-screen flex flex-col items-center justify-center px-4 py-10">

  <div class="bg-white border border-gray-300 rounded-2xl shadow-lg max-w-2xl w-full p-8 space-y-6 text-center">

    <h1 class="text-2xl font-bold text-gray-800 mb-4">✨ Hasil Akhir Deteksi Mood ✨</h1>

    <p class="text-gray-600">
      Halo, <strong>{{ $user->name }}</strong>! Berikut hasil perbandingan mood kamu setelah sesi bermain.
    </p>

    {{-- === tampilkan pesan error jika data belum tersedia === --}}
    @if(isset($error) && $error)
      <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-xl mt-4">
        {{ $error }}
      </div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-sky-50 p-4 rounded-xl">
          <h3 class="font-semibold text-gray-700 mb-2">Mood Awal (HSCL-25)</h3>
          <p class="text-3xl font-bold text-sky-500">{{ $mood_awal ?? 'N/A' }}</p>
          <p class="text-sm text-gray-600 mt-1">Skala 0–100</p>
        </div>

        <div class="bg-green-50 p-4 rounded-xl">
          <h3 class="font-semibold text-gray-700 mb-2">Mood Akhir (Deteksi Ekspresi)</h3>
          <p class="text-3xl font-bold text-green-500">{{ $mood_akhir ?? 'N/A' }}</p>
          <p class="text-sm text-gray-600 mt-1">Dihitung dari rata-rata ekspresi wajah</p>
        </div>
      </div>

      @if($perubahan !== null)
        <div class="mt-6">
          <p class="text-gray-700 font-medium">
            Perubahan Mood: 
            <span class="{{ $perubahan >= 0 ? 'text-green-600' : 'text-red-600' }}">
              {{ $perubahan >= 0 ? '+' : '' }}{{ $perubahan }}%
            </span>
          </p>
        </div>
      @endif

      {{-- === Chart perbandingan mood === --}}
      <canvas id="moodChart" width="400" height="200" class="mt-6"></canvas>

      <div class="mt-8 flex justify-center gap-4">
        <a href="/" class="bg-sky-400 hover:bg-sky-500 text-white px-6 py-2 rounded-lg">Kembali ke Beranda</a>
      
      </div>
    @endif
  </div>

  <script>
    const ctx = document.getElementById('moodChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Mood Awal', 'Mood Akhir'],
          datasets: [{
            label: 'Skor Mood',
            data: [{{ $mood_awal ?? 0 }}, {{ $mood_akhir ?? 0 }}],
            backgroundColor: ['#38bdf8', '#4ade80']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
          },
          scales: {
            y: {
              beginAtZero: true,
              max: 100,
              ticks: { stepSize: 20 }
            }
          }
        }
      });
    }
  </script>

</body>
</html>
