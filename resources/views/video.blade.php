<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tonton Video - SmartEmoCare</title>
  @vite(['resources/css/app.css'])
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.16.0/dist/tf.min.js"></script>
  <script src="{{ asset('tf_model/model_script.js') }}"></script>

  <style>
    body {
      background: #f9fafb;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      font-family: "Poppins", sans-serif;
    }
    .container {
      background: white;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
      width: 400px;
    }
    video {
      width: 100%;
      border-radius: 10px;
      margin-top: 12px;
    }
    .btn {
      margin-top: 15px;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      color: white;
    }
    .btn-start { background: #22c55e; }
    .btn-stop { background: #ef4444; }

    /* ⚙️ Tambahan: Modal */
    #resultModal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      align-items: center;
      justify-content: center;
      z-index: 50;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2 class="font-semibold text-gray-700">Tonton Video</h2>
    <p class="text-gray-500 text-sm mb-3">
      Pilih video yang ingin kamu tonton, lalu klik <b>Mulai</b>. Deteksi wajah akan berjalan otomatis.
    </p>

    <select id="videoSelect" class="border rounded-md p-2 w-full mb-2">
      <option value="/videos/video1.mp4">Video 1</option>
      <option value="/videos/video2.mp4">Video 2</option>
    </select>

    <video id="playVideo" controls></video>
    <video id="webcam" autoplay muted class="hidden"></video>

    <div class="mt-3">
      <button id="startBtn" class="btn btn-start">Mulai</button>
      <button id="stopBtn" class="btn btn-stop" disabled>Selesai</button>
    </div>
  </div>

  <!-- ⚙️ Modal pop up hasil -->
  <div id="resultModal" class="flex">
    <div class="p-6 rounded-2xl shadow-lg text-center max-w-md bg-white">
      <h2 class="text-xl font-bold mb-4 text-gray-800">✨ Hasil Gabungan Mood ✨</h2>
      <p class="text-gray-700 mb-3">Gabungan dari HSCL-25 dan Ekspresi Wajah:</p>
      <p id="finalScore" class="text-3xl font-extrabold text-sky-500 mb-4">-</p>
      <p class="text-sm text-gray-600 mb-6">(Rentang skala: 0–100)</p>

      <a id="praAksiBtn"
         href="#"
         class="bg-sky-500 hover:bg-sky-600 text-white px-5 py-2 rounded-md transition">
         Lanjut ke Pra-Aksi
      </a>

      <button id="closeModal"
         class="ml-2 bg-gray-300 hover:bg-gray-400 text-black px-5 py-2 rounded-md transition">
         Tutup
      </button>
    </div>
  </div>

  <script>
    const userId = {{ $user->id }};
    const hsclScore = {{ $score ?? 0 }} * 25; // tetap skala 0–100, tapi sudah dibalik dari controller

    const playVideo = document.getElementById("playVideo");
    const webcam = document.getElementById("webcam");
    const startBtn = document.getElementById("startBtn");
    const stopBtn = document.getElementById("stopBtn");
    const videoSelect = document.getElementById("videoSelect");
    const modal = document.getElementById("resultModal");
    const finalScoreElem = document.getElementById("finalScore");
    const praAksiBtn = document.getElementById("praAksiBtn");
    const closeModal = document.getElementById("closeModal");

    // === Tombol Mulai ===
    startBtn.addEventListener("click", async () => {
  playVideo.src = videoSelect.value;
  playVideo.play();
  webcam.classList.remove("hidden"); // <— ini penting
  startBtn.disabled = true;
  stopBtn.disabled = false;
  await startDetection(userId);
  playVideo.onended = () => stopBtn.click();
});

    {{--  startBtn.addEventListener("click", async () => {
      playVideo.src = videoSelect.value;
      playVideo.play();
      startBtn.disabled = true;
      stopBtn.disabled = false;
      await startDetection(userId);
      playVideo.onended = () => stopBtn.click();
    });  --}}

    // === Tombol Selesai ===
    stopBtn.addEventListener("click", async () => {
      stopBtn.disabled = true;

      const avgScore = await stopDetection(userId);

      const response = await fetch(`/api/mood-initial/${userId}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
          hscl_score: hsclScore,
          video_score: avgScore
        }),
      });

      const result = await response.json();

      if (result.status === "success") {
        // ⚙️ Tampilkan modal hasil gabungan
        const totalMood = Math.round(result.mood_awal);
        finalScoreElem.textContent = totalMood;
        praAksiBtn.href = `/pra_aksi/${userId}`;
        modal.style.display = "flex";
      } else {
        alert("Terjadi kesalahan saat menyimpan mood awal.");
      }
    });

    // ⚙️ Tutup modal
    closeModal.addEventListener("click", () => {
      modal.style.display = "none";
      startBtn.disabled = false;
    });
  </script>
</body>
</html>
