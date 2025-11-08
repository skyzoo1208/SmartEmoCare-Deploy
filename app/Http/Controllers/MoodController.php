<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mood;
use App\Models\User;

class MoodController extends Controller
{
    // 🔹 Method untuk menyimpan hasil mood akhir dari deteksi wajah
    public function storeFinalMood(Request $request, User $user)
{
    $validated = $request->validate([
        'final_score' => 'nullable|numeric|min:0|max:100',
        'mood_akhir'  => 'nullable|numeric|min:0|max:100',
    ]);

    // ambil nilai mood akhir dari salah satu key
    $finalScore = $validated['final_score'] ?? $validated['mood_akhir'] ?? null;

    if (is_null($finalScore)) {
        return response()->json(['success' => false, 'message' => 'Nilai mood akhir tidak ditemukan'], 400);
    }

    $mood = Mood::where('user_id', $user->id)->latest()->first();

    if ($mood) {
        $mood->update(['mood_akhir' => $finalScore]);
    } else {
        $mood = Mood::create([
            'user_id' => $user->id,
            'mood_awal' => null,
            'mood_akhir' => $finalScore,
            'stress_category' => 0,
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Mood akhir berhasil disimpan',
        'data' => $mood,
    ]);
}

    public function storeInitialMood(Request $request, User $user)
{
    $request->validate([
        'hscl_score' => 'required|numeric',
        'video_score' => 'required|numeric',
    ]);

    
    // 🧠 Gabungkan HSCL (60%) + rata-rata ekspresi wajah (40%)
$combined = round(($request->hscl_score * 0.6) + ($request->video_score * 0.4), 1);


    Mood::updateOrCreate(
        ['user_id' => $user->id],
        [
            'mood_awal' => $combined,
            'stress_category' => 2, // opsional: default kategori sedang
        ]
    );

    return response()->json(['status' => 'success', 'mood_awal' => $combined]);
}


}
