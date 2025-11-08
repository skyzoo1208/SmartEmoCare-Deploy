<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.16.0/dist/tf.min.js"></script>
    <title>Let’s Chat</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#FFFFF0]">
<video id="webcam" autoplay playsinline muted width="96" height="96" style="display:True;"></video>
<div class="min-h-screen flex flex-col items-center px-4 py-6 relative">

    <div class="w-full flex justify-between items-center mb-4">
        {{--  <a href="/final/{{ $user->id }}" class="absolute right-4 text-sm text-gray-600 flex items-center gap-2" id="stopBtn" >
            Akhiri Obrolan/Lihat Hasil
            <span class="text-lg">→</span>
        </a>  --}}
    </div>


    {{-- Header --}}
    <div class="w-full flex justify-between items-center mb-4 border-t border-gray-700">
        <h1 class="text-center text-2xl font-semibold w-full text-green-700">Let’s Chat</h1>
    </div>

    {{-- Chat Area --}}
    <div id="chat-area" class="w-full max-w-2xl bg-white rounded-xl shadow p-4 flex flex-col space-y-6 overflow-y-auto" style="height: 400px;">
    {{-- Sambutan Awal AI --}}
    <div class="bg-[#f6f6e9] p-3 rounded-lg w-fit max-w-[80%]">
        <p class="text-gray-700 text-sm">Halo, ada yang bisa saya bantu?</p>
    </div>

    {{-- Pesan dari Database --}}
    @foreach ($messages as $m)
        @if($m->role === 'user')
            {{-- Pesan user --}}
            <div class="bg-[#f6f6e9] p-3 rounded-lg self-end w-fit max-w-[80%] ml-auto">
                <p class="text-gray-700 text-sm">{{ $m->content }}</p>
            </div>
        @else
            {{-- Pesan AI --}}
            <div class="bg-[#f6f6e9] p-3 rounded-lg w-fit max-w-[80%]">
                <p class="text-gray-700 text-sm whitespace-pre-line">{{ $m->content }}</p>
            </div>
        @endif
    @endforeach
</div>

{{-- Form Input --}}
<form id="chat-form" action="/curhat/{{ $user->id }}" method="POST" class="mt-4 w-full max-w-2xl flex items-center gap-2">
    @csrf
    <textarea id="message" name="message" placeholder="Isi pesan di sini..."
        class="flex-1 rounded-full border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none min-h-[40px]"></textarea>
    <button type="submit"
        class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-full text-sm">
        Kirim
    </button>
</form>

</div>
    <!-- Load Script Model -->
    <script src="{{ asset('tf_model/model_script.js') }}"></script>

</body>

<script>
    const textarea = document.getElementById('message');
    const form = document.getElementById('chat-form');
    const chatArea = document.getElementById('chat-area');

    // Fungsi auto-scroll ke bawah
    function scrollToBottom() {
        chatArea.scrollTop = chatArea.scrollHeight;
    }

    // Scroll otomatis saat halaman dibuka
    scrollToBottom();

    // Enter untuk kirim pesan (Shift+Enter = baris baru)
    textarea.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.submit();
        }
    });
</script>


</html>

