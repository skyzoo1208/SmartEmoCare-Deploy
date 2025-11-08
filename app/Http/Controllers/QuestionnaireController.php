<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mood;
use App\Models\QuestionnaireAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionnaireController extends Controller
{
   public function show(User $user)
{
    // HSCL-25 (10 aitem kecemasan + 15 depresi)
    $questions = [
        // Bagian A: Kecemasan
        "Tiba-tiba merasa ketakutan tanpa sebab yang jelas",
        "Merasa ketakutan",
        "Limbang, pening, atau lemas",
        "Kegelisahan atau gemetar di dalam diri",
        "Jantung berdebar kuat atau amat cepat",
        "Gemetaran",
        "Merasa tegang atau terhimpit",
        "Sakit kepala",
        "Saat merasa amat ketakutan atau panik",
        "Merasa resah, tidak dapat diam tenang",

        // Bagian B: Depresi
        "Merasa kurang bertenaga, melamban",
        "Menyalahkan diri sendiri untuk berbagai hal",
        "Mudah menangis",
        "Kehilangan minat atau kesenangan seksual",
        "Selera makan terganggu (berkurang)",
        "Sulit tidur, mudah terbangun di malam hari",
        "Merasa tidak punya harapan mengenai masa depan",
        "Merasa sedih",
        "Merasa kesepian",
        "Berpikir untuk mengakhiri hidup",
        "Merasa terperangkap atau terjebak dalam situasi",
        "Terlalu banyak pikiran / terlalu mengkhawatirkan banyak hal",
        "Merasa tidak tertarik terhadap segala hal",
        "Merasa segala sesuatu memerlukan usaha keras",
        "Merasa tidak berharga",
    ];

    $showResult = false;
    return view('questions', compact('user', 'questions', 'showResult'));
}


public function store(User $user, Request $request)
{
    $request->validate([
        'answers'   => 'required|array|size:25',
        'answers.*' => 'required|integer|min:1|max:4',
        'questions' => 'sometimes|array',
    ]);

    $answers = $request->input('answers');
    $totalScore = array_sum($answers);
    $meanScore  = round($totalScore / count($answers), 2);

// 🔁 Balik skala HSCL agar 1.00 = buruk dan 4.00 = baik
$invertedScore = round(5 - $meanScore, 2);

// Simpan skor HSCL yang sudah dibalik ke session
session(['hscl_score_' . $user->id => $invertedScore]);
    // langsung arahkan ke halaman video
    return redirect()->route('video.show', ['user' => $user->id]);
}



}