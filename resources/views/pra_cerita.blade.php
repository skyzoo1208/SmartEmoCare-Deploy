<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pra Cerita</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.16.0/dist/tf.min.js"></script>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#FFFFF0] min-h-screen flex flex-col items-center justify-center relative overflow-x-hidden">

    <!-- Confetti dan Hiasan Kiri Kanan -->
    <img src="/img/kiripink.png" alt="Confetti Kiri" class="absolute left-0 top-1/4 w-28">
    <img src="/img/kanan.png" alt="Confetti Kanan" class="absolute right-0 top-1/4 w-28">

    <!-- Kotak Pesan -->
    <div class="bg-[#f8f8e5] border border-gray-300 rounded-xl shadow-lg px-10 py-6 max-w-[600px] text-center z-10">
        <h2 class="text-xl font-medium mb-2">Horayy🥳</h2>
        <p class="text-gray-800 leading-relaxed mb-6">
            Kamu telah menyelesaikan permainan tersebut dengan baik. Selanjutnya, <br>
            kamu boleh melihat hasil-hasil dari face detection atau bercerita tentang apa yang sedang kamu alami.
        </p>

        <div class="flex justify-center gap-4">
            <a href="/hasil" class="bg-gradient-to-r from-sky-400 to-green-400 text-white px-6 py-2 rounded-md hover:opacity-90 transition">Lihat Hasil</a>
            <a href="/curhat" class="bg-gradient-to-r from-green-400 to-sky-400 text-white px-6 py-2 rounded-md hover:opacity-90 transition">Lanjut Bercerita</a>
        </div>
    </div>

    <!-- Gambar Balon di Tengah -->
    <div class="mt-8 z-0">
        <img src="/img/tengah.png" alt="Balon" class="w-[280px] mx-auto">
    </div>

    <!-- Load Script Model -->
    <script src="{{ asset('tf_model/model_script.js') }}"></script>

</body>
</html>
