<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mood;

class FinalController extends Controller
{
    public function index(User $user)
    {
        // 🔹 Ambil data mood terakhir user
        $mood = Mood::where('user_id', $user->id)->latest()->first();

        // Jika belum ada data, tampilkan pesan error yang aman
        if (!$mood) {
            return view('final', [
                'user'          => $user,
                'mood_awal'     => null,
                'kategori_awal' => null,
                'mood_akhir'    => null,
                'perubahan'     => null,
                'error'         => 'Data mood belum tersedia. Pastikan deteksi wajah sudah selesai.'
            ]);
        }

        // Ambil data awal & akhir
        $mood_awal = $mood->mood_awal;
        $kategori_awal = $mood->stress_category;
        $mood_akhir = $mood->mood_akhir;

        // Hitung perubahan (dalam persentase)
        $perubahan = null;
        if (!is_null($mood_awal) && !is_null($mood_akhir)) {
            $perubahan = round((($mood_akhir - $mood_awal) / max($mood_awal, 1)) * 100, 1);
        }

        // Kirim data ke view tanpa $lastEmotion atau $avgConfidence
        return view('final', [
            'user'          => $user,
            'mood_awal'     => $mood_awal,
            'kategori_awal' => $kategori_awal,
            'mood_akhir'    => $mood_akhir,
            'perubahan'     => $perubahan,
            'error'         => null,
        ]);
    }
}
