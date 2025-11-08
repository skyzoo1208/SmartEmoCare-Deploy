<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Face Detection</title>
  @vite(['resources/css/app.css'])
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.16.0/dist/tf.min.js"></script>
</head>
<body class="bg-[#FFFFF0] min-h-screen overflow-hidden relative">

  <img src="/img/bg3.png" alt="gambar1"
       class="w-full h-full object-cover absolute top-0 left-0 z-0" />

  <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2
              bg-[#DBE4C94D] border rounded-2xl w-[450px] h-auto shadow-md flex flex-col
              justify-center items-center text-center px-6 py-8 z-10 space-y-5">
              
    <video id="webcam" autoplay playsinline muted width="320" height="240"
           class="rounded-lg border border-gray-300 shadow-sm"></video>

    <div class="flex gap-4">
      <button id="startBtn"
              class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">
        🎥 Mulai Deteksi
      </button>
      <button id="stopBtn"
              class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg">
        ⏹️ Selesai
      </button>
    </div>
  </div>

  <script>
    window.USER_ID = "{{ session('user_id') }}"; // kirim ID user ke JS
  </script>

  <script src="{{ asset('tf_model/model_script.js') }}"></script>
</body>
</html>
