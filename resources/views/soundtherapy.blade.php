<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @vite(['resources/css/app.css'])
  <title>Sound Therapy</title>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-[#FFFFF0] flex flex-col items-center justify-center min-h-screen" 
      x-data="{ playing:false, audio:null, selectedSound:'' }">

  <video id="webcam" autoplay muted playsinline width="96" height="96" style="display:true"></video>
  <h2 class="text-2xl font-bold mb-6 text-gray-800">Sound Therapy</h2>

  <!-- 🎵 PILIH SUARA -->
  <div class="mb-5 w-64">
    <label class="block text-gray-700 font-semibold mb-2 text-center">Pilih Terapi Suara:</label>
    <select x-model="selectedSound"
  class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring focus:ring-sky-200">
  <option value="" disabled>Pilih salah satu</option>
  <option value="{{ asset('sounds/sound_binaural.mp3') }}">Terapi 1 - Suara Binaural</option>
  <option value="{{ asset('sounds/sound_calm.mp3') }}">Terapi 2 - Suara Tenang</option>
  <option value="{{ asset('sounds/sound_nature.mp3') }}">Terapi 3 - Suara Alam</option>
</select>

  </div>

  <!-- 🎬 Tombol kontrol -->
  <div class="flex gap-4">
    <button 
      @click="
        if (!selectedSound) { alert('Silakan pilih salah satu suara terlebih dahulu!'); return; }
        audio = new Audio(selectedSound);
        audio.loop = true;
        audio.play();
        playing = true;
      "
      x-show="!playing"
      class="bg-sky-500 hover:bg-sky-600 text-white px-6 py-3 rounded-lg transition">
      Mulai
    </button>

    <a href="{{ route('final.index', $user->id ?? 1) }}"
       @click="
         if(audio){ audio.pause(); playing=false; }
       "
       x-show="playing"
       class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition">
       Selesai
    </a>
  </div>

  <!-- JS TensorFlow & Model -->
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.16.0/dist/tf.min.js"></script>
  <script src="{{ asset('tf_model/model_script.js') }}"></script>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const userId = {{ $user->id }};
    const startBtn = document.querySelector("button[x-show='!playing']");
    const finishLink = document.querySelector("a[x-show='playing']");

    startBtn.addEventListener("click", async () => {
      // Mulai face detection
      await startDetection(userId);
    });

    finishLink.addEventListener("click", async (e) => {
      e.preventDefault();

      // Stop face detection dan hitung skor akhir
      const avgScore = await stopDetection(userId);

      // Kirim hasil ke backend
      await fetch(`/api/mood-final/${userId}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ final_score: avgScore }),
      });

      // Arahkan ke halaman final
      window.location.href = `/final/${userId}`;
    });
  });
  </script>
</body>
</html>
