<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FinalController;
use App\Http\Controllers\FaceDetectionController;
use App\Http\Controllers\RFIDController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\VideoRecordingController;
use App\Http\Controllers\PraAksiController;
use App\Http\Controllers\SoundTherapyController;
use App\Http\Controllers\MoodController;


// Route::post('/start', [UserController::class, 'store'])->name('user.store');
Route::post('/api/mood-initial/{user}', [MoodController::class, 'storeInitialMood']);

Route::post('/api/mood-final/{user}', [MoodController::class, 'storeFinalMood']);

Route::get('/pra_aksi/{user}', [PraAksiController::class, 'index'])->name('pra_aksi');
Route::get('/soundtherapy/{user}', [SoundTherapyController::class, 'index'])->name('soundtherapy');

Route::get('/questions/{user}', [QuestionnaireController::class, 'show'])->name('questions.show');
Route::post('/questions/{user}', [QuestionnaireController::class, 'store'])->name('questions.store');
Route::get('/questions/{user}/result', [QuestionnaireController::class, 'showResult'])->name('questions.result');

Route::get('/boardgame/{user}', [GameController::class, 'index'])->name('board.index');
Route::get('/boardgame/{user}/draw-card', [GameController::class, 'drawCard'])->name('board.drawCard');

Route::get('/curhat/{user}', [ChatController::class, 'index'])->name('chat.index');
Route::post('/curhat/{user}', [ChatController::class, 'send'])->name('chat.send');

// Route::get('/final/{user}', [FinalController::class, 'index'])->name('final.index');
// Route::post('/final/{user}', [FinalController::class, 'store'])->name('final.store');
Route::get('/curhat/{user}', [ChatController::class, 'index'])->name('chat.index');
Route::post('/curhat/{user}', [ChatController::class, 'send'])->name('chat.send');

// Face Detection Route
Route::get('/prediction/start', function () {
    session(['prediksi' => true]);
    return 'Prediksi dimulai';
});

Route::get('/stop', function () {
    session(['prediksi' => false]);
    return 'Prediksi dihentikan';
});

Route::get('/status', function () {
    return response()->json(['prediksi' => session('prediksi', false)]);
});

Route::get('/final/{user}', [FinalController::class, 'index'])->name('final.index');




Route::get('/facedetection', [FaceDetectionController::class, 'index'])->name('facedetection');

// Route::get('/pra_cerita', function () {
//     return view('pra_cerita ');
// });
Route::get('/pra_cerita/{user}', function (App\Models\User $user) {
    return view('pra_cerita', compact('user'));
})->name('pra_cerita');


Route::get('/video/{user}', function (App\Models\User $user) { $score = session('hscl_score_' . $user->id, 0); 
    return view('video', compact('user', 'score')); })->name('video.show');

Route::post('/video/store', [VideoRecordingController::class, 'store'])
     ->name('video.store');




// Halaman awal dengan middleware RFID
Route::get('/', function () {
    return view('form');
});

Route::get('/welcome', function (Request $request) {
    // misal id user disimpan di session saat POST /start
    $userId = session('user_id');   // atau sesuai cara penyimpananmu
    return view('welcome', compact('userId'));
})->name('welcome');

// Route::post('/start', function (Request $request) {
//     $validated = $request->validate([
//         'name' => 'required|string|max:255',
//         'age'  => 'required|integer',
//     ]);

//     $email = uniqid('guest_') . '@guest.local';

//     $user = User::create([
//         'name'  => $validated['name'],
//         'age'   => $validated['age'],
//         'email' => $email,
//     ]);

//     // simpan ke session supaya bisa diambil di /welcome
//     session(['user_id' => $user->id]);

//     return redirect()->route('welcome');
// })->name('start');   // ← ganti name agar tidak bentrok

Route::post('/start', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'age'  => 'required|integer',
    ]);

    // buat email dummy unik
    $email = uniqid('guest_') . '@guest.local';

    // simpan data user baru
    $user = User::create([
        'name'  => $validated['name'],
        'age'   => $validated['age'],
        'email' => $email,
    ]);

    // simpan ke session (opsional)
    session(['user_id' => $user->id]);

    // langsung arahkan ke halaman questions
    return redirect()->route('questions.show', ['user' => $user->id]);
})->name('start');

