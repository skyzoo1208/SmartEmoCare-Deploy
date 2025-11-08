<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @vite(['resources/css/app.css'])
  <title>Pra-Aksi</title>
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.16.0/dist/tf.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body class="bg-[#FFFFF0] flex flex-col items-center justify-center min-h-screen px-4" x-data="{ showSelect:false, showTimer:false, seconds:600 }">
    <video id="webcam" autoplay muted playsinline width="96" height="96" style="display:true"></video>

  <h2 class="text-2xl font-bold mb-6 text-gray-800">Pilih Aktivitas Anda</h2>

  <div class="flex flex-col gap-4 w-full max-w-sm">
    
    <a href="{{ route('board.index', ['user' => $user->id, 'mode' => 'sound']) }}" class="block bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded">Boardgame + Sound</a>

    <a href="{{ route('board.index', ['user' => $user->id, 'mode' => 'board']) }}" class="block bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Boardgame</a>

    <a href="{{ route('soundtherapy', $user->id) }}" class="bg-purple-400 hover:bg-purple-500 text-white py-3 rounded-lg text-center">Sound Therapy</a>
  </div>

  <!-- Modal pilih mode boardgame -->
  <template x-if="showSelect">
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
      <div class="bg-white p-6 rounded-xl text-center space-y-4">
        <h3 class="font-bold text-gray-800 text-lg">Pilih Mode Boardgame</h3>
        <a href="{{ route('board.index', $user->id) }}" class="block bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded">Online</a>
        <button @click="showSelect=false; showTimer=true" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">Offline</button>
      </div>
    </div>
  </template>

  <!-- Timer Offline -->
  <template x-if="showTimer">
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
      <div class="bg-white p-6 rounded-xl text-center space-y-4">
        <h3 class="font-bold text-lg text-gray-800">Waktu Bermain Boardgame</h3>
        <p class="text-3xl font-bold text-sky-500" x-text="Math.floor(seconds/60) + ':' + String(seconds%60).padStart(2, '0')"></p>

        <div class="flex justify-center gap-3">
          <button @click="if(seconds>0){let t=setInterval(()=>{if(seconds>0){seconds--}else{clearInterval(t)}},1000)}"
                  class="bg-sky-400 hover:bg-sky-500 text-white px-4 py-2 rounded">Mulai</button>

          <a href="{{ route('final.index', $user->id ?? 1) }}"
             class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Selesai</a>
        </div>
      </div>
    </div>
  </template>

    
  <script src="{{ asset('tf_model/model_script.js') }}"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const userId = {{ $user->id }};
        let timer = null;

        // Saat user pilih "Offline", tampilkan timer + hidupkan kamera
        document.querySelectorAll("button").forEach(btn => {
            if (btn.textContent.trim() === "Offline") {
                btn.addEventListener("click", async () => {
                    await startDetection(userId);
                });
            }

            if (btn.textContent.trim() === "Mulai") {
                btn.addEventListener("click", async () => {
                    console.log("⏱️ Timer dimulai...");
                    timer = setInterval(() => {
                        const el = document.querySelector("[x-text]");
                        if (!el) return;
                        let timeParts = el.textContent.split(":");
                        let totalSeconds = parseInt(timeParts[0]) * 60 + parseInt(timeParts[1]);
                        if (totalSeconds > 0) totalSeconds--;
                        const m = Math.floor(totalSeconds / 60);
                        const s = String(totalSeconds % 60).padStart(2, "0");
                        el.textContent = `${m}:${s}`;
                    }, 1000);
                });
            }
        });

        const finishBtn = document.querySelector("a[href*='final']");
        if (finishBtn) {
            finishBtn.addEventListener("click", async (e) => {
                e.preventDefault();
                await stopDetection(userId);
            });
        }
    });
    </script>



</body>


</html>
