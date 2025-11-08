<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload & Preview Video</title>
    @vite(['resources/css/app.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.16.0/dist/tf.min.js"></script>
</head>
<body class="relative flex flex-col items-center justify-center min-h-screen bg-[#FFFFF0] p-4">

    <!-- simpan id user yang baru dibuat -->
    <input type="hidden" id="user-id" value="{{ $userId }}">

    <script>
        // buat variabel global agar bisa diakses model_script.js
        window.USER_ID = document.getElementById('user-id').value;
    </script>


    <!-- Webcam kecil di pojok kanan atas -->
    <video id="webcam"
           autoplay
           playsinline
           muted
           class="absolute top-4 right-4 w-32 h-32 border-2 border-gray-300 rounded-lg shadow-lg">
    </video>

    <!-- Dekorasi Emoji -->
    <img src="/img/emoji-bubble.png"   alt="Bubble Emoji"   class="absolute top-16 left-10  w-32 md:w-48 h-32 md:h-48 z-10">
    {{--  <img src="/img/emoji-game.png"     alt="Game Emoji"     class="absolute top-10 right-10 w-32 md:w-48 h-32 md:h-48 z-10">  --}}
    <img src="/img/emoji-party.png"    alt="Party Emoji"    class="absolute bottom-10 left-8  w-32 md:w-48 h-32 md:h-48 z-10">
    <img src="/img/emoji-boardgame.png"alt="Boardgame Emoji"class="absolute bottom-12 right-12 w-32 md:w-48 h-32 md:h-48 z-10">

    <!-- Box Video Preview -->
    <div class="border border-black rounded-xl w-[800px] h-[550px] bg-white shadow-xl flex items-center justify-center">
        <video id="preview" class="w-full h-full object-cover rounded-xl" controls>
            Browser Anda tidak mendukung pemutar video.
        </video>
    </div>

    <!-- Tombol Aksi -->
    <div class="flex flex-row gap-4 mt-6">
        <!-- Pilih Video -->
        <label for="videoInput" 
               class="cursor-pointer bg-[#009879] text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition">
            Pilih Video
        </label>
        <input type="file" id="videoInput" accept="video/*" class="hidden" onchange="loadVideo(event)">

        <!-- Tombol mulai deteksi -->
        <a id="startBtn"
           href="#"
           class="bg-[#FF7F50] text-white px-6 py-2 rounded-lg hover:bg-[#e0673d] transition">
           Camera
        </a>

        <a id="stopBtn"
           href="#"
           class="bg-[#FF7F50] text-white px-6 py-2 rounded-lg hover:bg-[#e0673d] transition">
           Stop
        </a>

        <!-- Lanjut -->
        <a href="/" 
           class="bg-[#FF7F50] text-white px-6 py-2 rounded-lg hover:bg-[#e0673d] transition">
           ➡️ Lanjut
        </a>
    </div>

    <!-- Script Preview Video -->
    <script>
        function loadVideo(event) {
            const file = event.target.files[0];
            if (file) {
                const videoElement = document.getElementById('preview');
                videoElement.src = URL.createObjectURL(file);
                videoElement.play();
            }
        }
    </script>

    <!-- Model script -->
    

    <script>
const userId = @json(session('user_id'));   // <- ambil id user yg baru dibuat

function sendEmotion(emotion, confidence) {
    fetch('/api/emotions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            user_id: userId,
            emotion: emotion,
            confidence: confidence,
            frame_time: new Date().toISOString()
        })
    }).then(r => r.json()).then(console.log);
}
</script>


<script src="{{ asset('tf_model/model_script.js') }}"></script>
</body>
</html>
