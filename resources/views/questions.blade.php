<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @vite(['resources/css/app.css'])
  <title>HSCL-25 – Kuesioner</title>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#FFFFF0] min-h-screen py-10 px-4 md:px-10">

  <div class="flex items-center mb-6">
    <a href="/" class="flex items-center text-gray-800 hover:underline">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Kembali ke Beranda
    </a>
  </div>

  <h2 class="text-center text-gray-800 text-lg md:text-xl font-semibold mb-8">
    Kuesioner HSCL-25<br><span class="text-sm font-normal">(Silakan jawab semua pertanyaan sesuai kondisi Anda minggu ini)</span>
  </h2>

  <div class="bg-[#f9f9e8] rounded-xl border border-gray-300 shadow px-4 md:px-8 py-6 max-w-3xl mx-auto space-y-8">

    <form id="questionForm" action="{{ route('questions.store', $user->id) }}" method="POST" class="space-y-6">
      @csrf
      @foreach ($questions as $index => $question)
        <div class="space-y-3 border-b pb-4">
          <p class="text-gray-800 font-medium">{{ $index+1 }}. {{ $question }}</p>
          <input type="hidden" name="questions[{{ $index }}]" value="{{ $question }}">
          @php
              $options = [1 => 'Tidak sama sekali', 2 => 'Sedikit', 3 => 'Kadang-kadang', 4 => 'Sering'];
          @endphp
          <div class="flex justify-center gap-8">
            @foreach ($options as $value => $label)
              <label class="flex flex-col items-center">
                <input type="radio" name="answers[{{ $index }}]" value="{{ $value }}" required
                       class="appearance-none w-6 h-6 rounded-full border border-gray-400 checked:bg-sky-400 mb-1">
                <span class="text-sm text-gray-700">{{ $label }}</span>
              </label>
            @endforeach
          </div>
        </div>
      @endforeach

      <div class="flex justify-end pt-8">
  <button type="submit"
          class="bg-gradient-to-r from-sky-400 to-green-400 text-white font-medium px-6 py-2 rounded-md hover:opacity-90 transition">
    Selanjutnya
  </button>
</div>

    </form>
  </div>

  {{-- Modal hasil skor --}}
  @if(isset($showResult) && $showResult)
  <div id="resultModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="p-6 rounded-2xl shadow-lg text-center max-w-md bg-white">
      <h2 class="text-xl font-bold mb-4 text-gray-800">Hasil Skor HSCL-25</h2>
      <p class="text-gray-700 mb-3">Skor Rata-rata Anda:</p>
      <p class="text-3xl font-extrabold text-sky-500 mb-4">{{ $score ?? 'N/A' }}</p>
      <p class="text-sm text-gray-600 mb-6">(Rentang skor: 1.00 – 4.00)</p>

      <a href="{{ route('pra_aksi', $user->id) }}"
         class="bg-sky-500 hover:bg-sky-600 text-white px-5 py-2 rounded-md transition">
         Lanjut ke Pra-Aksi
      </a>

      <a href="{{ route('questions.show', $user->id) }}"
         class="ml-2 bg-gray-300 hover:bg-gray-400 text-black px-5 py-2 rounded-md transition">
         Tutup
      </a>
    </div>
  </div>
  @endif

</body>
</html>
