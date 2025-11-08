<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class GameController extends Controller
{
    public function index(User $user, Request $request)
{
    $mode = $request->query('mode', 'board'); // default: board
    return view('game.board', compact('user', 'mode'));
}


    public function drawCard(Request $request, User $user)
    {
        // List semua kartu aksi terapeutik (tanpa kategori)
        $cards = [
            "Tersenyum selama 20 detik",
            "Ceritakan satu memori bahagia 30 detik + tunjukkan senyum",
            "Dengar cuplikan musik ceria 30 detik & gerak ringan",
            "Tertawalah palsu selama 30 detik (laughter challenge)",
            "Napas 4-4-4: tarik 4s — tahan 4s — hembus 4s (3 kali)",
            "Buat wajah bahagia: angkat alis + senyum (pose bahagia) selama 15s",
            "Bernyanyi atau menggumam satu baris lagu favorit",
            "Visualisasi ‘tempat aman’ 30 detik (buka mata, tatap kamera, lalu relaks)",
            "Mimikri (mirror): tirukan ekspresi di kartu selama 10s",
            "Tantangan kebaikan: sebutkan 3 hal yang kamu syukuri (10s tiap item)",
            "Role-play mikro: pura-pura menerima pujian selama 20s",
            "Pose kekuatan (power pose) selama 30 detik",
            "Self-hug (peluk diri sendiri) selama 20 detik",
            "Bayangkan wajah seseorang yang kamu sayangi & tersenyum padanya 15 detik",
            "Gerakan 30s: lompat kecil / tepuk tangan ritmis"
        ];

        // Ambil indeks kartu terakhir dari request (biar berurutan)
        $lastIndex = $request->input('last_index', 0);

        if ($lastIndex >= count($cards)) {
            return response()->json([
                'finished' => true,
                'message' => 'Semua aksi telah diselesaikan 🎉'
            ]);
        }

        $nextCard = [
            'index' => $lastIndex + 1,
            'title' => "Kartu #".($lastIndex + 1),
            'description' => $cards[$lastIndex],
        ];

        return response()->json($nextCard);
    }
}
