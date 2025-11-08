<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;

class CardsSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            // Category 1 (Merah - Sangat Buruk)
            ['category' => 1, 'instruction' => "Ambil napas dalam selama 1 menit penuh. Fokus pada pernapasanmu.", 'source' => "Harvard Health"],
            ['category' => 1, 'instruction' => "Tulis 3 hal kecil yang kamu syukuri saat ini.", 'source' => "Emmons & McCullough"],
            ['category' => 1, 'instruction' => "Letakkan tangan di dada dan katakan: 'Aku cukup, aku sedang berproses.'", 'source' => "Kristin Neff"],
            ['category' => 1, 'instruction' => "Putar lagu menenangkan atau instrumen alam selama 2 menit.", 'source' => "The Lancet, 2015"],
            ['category' => 1, 'instruction' => "Gambarlah bebas selama 3 menit dengan warna favoritmu.", 'source' => "Art Therapy Journal"],
            ['category' => 1, 'instruction' => "Lakukan gerakan ringan: angkat tangan ke atas dan tarik napas. Ulangi 3x.", 'source' => "Mayo Clinic"],
            ['category' => 1, 'instruction' => "Ucapkan kalimat positif: 'Aku boleh merasa seperti ini, dan aku akan melewati ini.'", 'source' => "CBT Technique"],
            // Category 2 (Kuning - Mulai Stres)
            ['category' => 2, 'instruction' => "Berjalan di tempat sambil tersenyum selama 1 menit.", 'source' => "Psych Science"],
            ['category' => 2, 'instruction' => "Tulis pesan motivasi untuk dirimu 1 jam dari sekarang.", 'source' => "Future Self Journaling"],
            ['category' => 2, 'instruction' => "Mainkan game 'Cari 5 benda berwarna kuning' di sekitarmu.", 'source' => "Mindfulness-Based Stress Reduction"],
            ['category' => 2, 'instruction' => "Ulangi kalimat ini 5 kali: 'Aku sedang belajar menjadi lebih tenang.'", 'source' => "Behavioral Therapy Practices"],
            ['category' => 2, 'instruction' => "Gambar wajah kartun dengan ekspresi lucu dan beri nama.", 'source' => "Humor and mental health research"],
            ['category' => 2, 'instruction' => "Buat bentuk hati dengan tanganmu lalu tutup mata dan bayangkan seseorang yang mendukungmu.", 'source' => "Harvard Health"],
            // Category 3 (Biru - Perlu Ditingkatkan)
            ['category' => 3, 'instruction' => "Tuliskan satu pencapaian kecil hari ini. Apresiasi dirimu.", 'source' => "Positive Psychology"],
            ['category' => 3, 'instruction' => "Lakukan peregangan ringan dengan musik favorit selama 2 menit.", 'source' => "APA"],
            ['category' => 3, 'instruction' => "Mainkan 'Tebak ekspresi': tirukan 3 ekspresi wajah dan beri nama emosinya.", 'source' => "Emotional intelligence exercises"],
            ['category' => 3, 'instruction' => "Ceritakan cerita lucu yang pernah kamu alami (atau karang bebas!).", 'source' => "Humor therapy"],
            ['category' => 3, 'instruction' => "Tulis pesan dukungan untuk pemain berikutnya.", 'source' => "Oxford study"],
            ['category' => 3, 'instruction' => "Buat daftar 3 aktivitas yang membuatmu bahagia akhir-akhir ini.", 'source' => "Behavioral activation"],
            // Category 4 (Hijau - Baik)
            ['category' => 4, 'instruction' => "Tuliskan 1 hal positif tentang orang di sekitarmu saat ini.", 'source' => "Kindness journaling"],
            ['category' => 4, 'instruction' => "Buat pose 'pahlawan' selama 30 detik dan rasakan kekuatanmu.", 'source' => "Amy Cuddy"],
            ['category' => 4, 'instruction' => "Tulis cita-citamu dan satu langkah kecil ke arah itu.", 'source' => "Goal setting theory"],
            ['category' => 4, 'instruction' => "Tertawalah pura-pura selama 20 detik. Biarkan berubah jadi sungguhan!", 'source' => "Laughter yoga"],
            ['category' => 4, 'instruction' => "Beri pujian untuk dirimu atas hal kecil hari ini.", 'source' => "Self-affirmation studies"],
            ['category' => 4, 'instruction' => "Mainkan 'Ucap 5 kata positif acak dengan cepat!'", 'source' => "Positive word priming"]
        ];

        foreach ($cards as $c) Card::create($c);
    }
}