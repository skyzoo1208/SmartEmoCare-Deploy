// let model;
// let predictionInterval = null;
// let lastPredictionResult = null;
// let emotionBuffer = [];

// const webcamElement = document.getElementById("webcam");
// const startBtn = document.getElementById("startBtn");
// const stopBtn = document.getElementById("stopBtn");

// const labels = [
//     ["anger", 1],
//     ["disgust", 1],
//     ["fear", 1],
//     ["sad", 2],
//     ["contempt", 2],
//     ["neutral", 3],
//     ["surprise", 3],
//     ["happy", 4],
// ];

// // Ganti IP ESP32 sesuai yang muncul di Serial Monitor
// const ESP32_IP = "http://192.168.1.123";

// async function setupWebcam() {
//     try {
//         console.log("[DEBUG] Mengakses webcam...");
//         const stream = await navigator.mediaDevices.getUserMedia({
//             video: true,
//         });
//         webcamElement.srcObject = stream;

//         return new Promise((resolve) => {
//             webcamElement.onloadeddata = () => {
//                 console.log("[DEBUG] Webcam berhasil diinisialisasi.");
//                 resolve();
//             };
//         });
//     } catch (err) {
//         console.error(
//             `[ERROR] Gagal mengakses webcam: ${err.name} - ${err.message}`
//         );
//         throw err;
//     }
// }

// async function loadModel() {
//     const baseURL = window.location.origin;
//     console.log(`[DEBUG] Memuat model dari: ${baseURL}/tf_model/model.json`);
//     model = await tf.loadGraphModel(`${baseURL}/tf_model/model.json`);
//     console.log("[DEBUG] Model berhasil dimuat.");
// }

// function preprocessFrame(video) {
//     console.log("[DEBUG] Memproses frame dari webcam...");
//     return tf.tidy(() => {
//         let img = tf.browser.fromPixels(video);
//         img = tf.image.resizeBilinear(img, [96, 96]);
//         img = img.toFloat().div(255);
//         img = img.transpose([2, 0, 1]);
//         return img.expandDims(0);
//     });
// }

// function softmax(logits) {
//     const maxLogit = Math.max(...logits); // stabilisasi numerik
//     const exps = logits.map((l) => Math.exp(l - maxLogit));
//     const sumExps = exps.reduce((a, b) => a + b, 0);
//     return exps.map((e) => e / sumExps);
// }

// async function sendInitialZero() {
//     console.log("[DEBUG] Kirim nilai awal 0 ke ESP32");
//     try {
//         const res = await fetch(`${ESP32_IP}/lamp?nomor=0`);
//         if (!res.ok) throw new Error(`HTTP ${res.status}`);
//         console.log(
//             `[DEBUG] Respon dari ESP32 untuk nilai 0: ${await res.text()}`
//         );
//     } catch (err) {
//         console.error("[ERROR] Gagal kirim nilai 0 ke ESP32:", err);
//     }
// }

// async function sendRecord(record) {
//     // record: { user_id, emotion, confidence, frame_time }
//     try {
//         const res = await fetch("/api/emotion-records", {
//             method: "POST",
//             headers: {
//                 "Content-Type": "application/json",
//                 Accept: "application/json",
//             },
//             body: JSON.stringify(record),
//         });

//         if (!res.ok) {
//             const text = await res.text();
//             throw new Error(`HTTP ${res.status} - ${text}`);
//         }
//         return await res.json();
//     } catch (err) {
//         console.error("[ERROR] Gagal kirim record:", err, record);
//         throw err;
//     }
// }

// async function sendAllRecords() {
//     if (!Array.isArray(emotionBuffer) || emotionBuffer.length === 0) {
//         return [];
//     }

//     // kirim berurutan (lebih aman untuk DB), kumpulkan respon
//     const results = [];
//     for (const rec of emotionBuffer) {
//         // safety: pastikan rec.user_id valid
//         if (!rec.user_id) {
//             console.warn("[SKIP] user_id kosong, record dilewati:", rec);
//             continue;
//         }
//         // kirim dan tunggu
//         try {
//             const r = await sendRecord(rec);
//             results.push(r);
//         } catch (e) {
//             // jika 1 record gagal, kita lanjutkan ke record berikutnya
//             // kamu bisa memilih untuk stop dan retry jika perlu
//             console.error(
//                 "[ERROR] Gagal mengirim record, lanjut ke record berikutnya.",
//                 e
//             );
//         }
//     }

//     return results;
// }

// async function predict() {
//     if (!webcamElement) return;
//     if (!model) {
//         console.error("[ERROR] Model belum dimuat");
//         return;
//     }

//     const inputTensor = preprocessFrame(webcamElement);
//     const prediction = model.execute({ input: inputTensor }, "Identity:0");
//     const logits = await prediction.data();

//     const probs = softmax(Array.from(logits));
//     const topClass = probs.indexOf(Math.max(...probs));
//     const percentage = probs[topClass] * 100;

//     let category;
//     if (percentage >= 75) category = 1;
//     else if (percentage >= 50) category = 2;
//     else if (percentage >= 25) category = 3;
//     else category = 4;

//     const emosi = labels[topClass][0];
//     const lampu = labels[topClass][1];

//     // ✅ Tambahan log prediction
//     console.log(`[DEBUG] Emosi terdeteksi: ${emosi}`);
//     console.log(`[DEBUG] Confidence: ${percentage.toFixed(2)}%`);

//     lastPredictionResult = {
//         emosi,
//         lampu,
//         percentage: Number(percentage.toFixed(2)),
//         category,
//         timestamp: new Date().toISOString(),
//     };

//     const userId = window.USER_ID ?? null;
//     const record = {
//         user_id: userId,
//         emotion: emosi,
//         confidence: lastPredictionResult.percentage,
//         frame_time: lastPredictionResult.timestamp,
//     };

//     if (userId) {
//         emotionBuffer.push(record);
//     }

//     const stressCategoryEl = document.getElementById("stressCategory");
//     const percentageEl = document.getElementById("percentage");
//     if (stressCategoryEl) stressCategoryEl.textContent = category;
//     if (percentageEl)
//         percentageEl.textContent = lastPredictionResult.percentage + "%";

//     tf.dispose([inputTensor, prediction]);
// }

// //  fungsi untuk kirim data ke server
// function sendEmotion(emotion, confidence) {
//     fetch("/api/emotion-records", {
//         method: "POST",
//         headers: {
//             "Content-Type": "application/json",
//         },
//         body: JSON.stringify({
//             user_id: window.USER_ID, // ambil dari welcome.blade
//             emotion: emotion,
//             confidence: confidence,
//             frame_time: new Date().toISOString(),
//         }),
//     })
//         .then((res) => res.json())
//         .then((data) => console.log("Saved:", data))
//         .catch((err) => console.error("Error:", err));
// }

// async function init() {
//     await loadModel();
//     // kamera langsung hidup
//     await setupWebcam();

//     // kirim nilai awal ke ESP32 bila perlu
//     await sendInitialZero();

//     // prediksi hanya dimulai saat tombol Camera ditekan
//     if (startBtn) {
//         startBtn.addEventListener("click", (e) => {
//             e.preventDefault();
//             if (!predictionInterval) {
//                 predictionInterval = setInterval(predict, 1000);
//                 console.log("[DEBUG] Prediksi dimulai");
//             }
//         });
//     }

//     if (stopBtn) {
//         stopBtn.addEventListener("click", async (e) => {
//             e.preventDefault();

//             // hentikan interval prediksi jika ada
//             if (predictionInterval) {
//                 clearInterval(predictionInterval);
//                 predictionInterval = null;
//             }
//             console.log("[DEBUG] Prediksi dihentikan (interval dibersihkan)");

//             // hentikan webcam (stop semua tracks)
//             try {
//                 if (webcamElement && webcamElement.srcObject) {
//                     webcamElement.srcObject
//                         .getTracks()
//                         .forEach((t) => t.stop());
//                     webcamElement.srcObject = null;
//                 }
//                 console.log("[DEBUG] Webcam dimatikan");
//             } catch (err) {
//                 console.warn("[WARN] Gagal matikan webcam:", err);
//             }

//             if (!lastPredictionResult || emotionBuffer.length === 0) {
//                 alert(
//                     "Prediksi belum dilakukan, tidak ada data untuk disimpan."
//                 );
//                 return;
//             }

//             // disable tombol supaya user tidak menekan berkali-kali
//             stopBtn.disabled = true;
//             if (startBtn) startBtn.disabled = false;

//             // pastikan ada user id
//             const userId =
//                 window.USER_ID ??
//                 (typeof userId !== "undefined" ? userId : null);
//             if (!userId) {
//                 alert(
//                     "User ID tidak terdeteksi — data tidak bisa disimpan. Pastikan kamu masuk melalui alur yang benar."
//                 );
//                 stopBtn.disabled = false;
//                 return;
//             }

//             // kirim semua record buffer ke server
//             try {
//                 console.log(
//                     `[DEBUG] Mengirim ${emotionBuffer.length} record ke server...`
//                 );
//                 const results = await sendAllRecords();
//                 console.log("[DEBUG] Semua record dikirim:", results);

//                 // kosongkan buffer setelah sukses
//                 emotionBuffer = [];

//                 // redirect — contoh ke halaman final (sesuaikan route-mu)
//                 window.location.href = `/final/${userId}`;
//             } catch (err) {
//                 console.error("[ERROR] Gagal menyimpan data ke server:", err);
//                 alert(
//                     "Terjadi kesalahan saat menyimpan data. Cek console atau ulangi."
//                 );
//                 stopBtn.disabled = false;
//             }
//         });
//     }
// }

// init();

// ==================== CONFIG ====================
let webcamStream = null;
let model = null;
let isDetecting = false;
let detectionInterval = null;
let emotionData = [];

const EMOTION_API = "/api/emotion-records";
const MOOD_FINAL_API = (userId) => `/api/mood-final/${userId}`;

// ==================== MODEL LOAD ====================
async function loadModel() {
    if (model) return model;
    model = await tf.loadGraphModel("/tf_model/model.json");
    console.log("✅ Model emosi berhasil dimuat");
    return model;
}

// ==================== START DETECTION ====================
async function startDetection(userId) {
    if (isDetecting) return;
    isDetecting = true;
    emotionData = [];

    const webcam = document.getElementById("webcam");
    if (!webcam) return alert("Kamera tidak ditemukan di halaman ini.");

    webcamStream = await navigator.mediaDevices.getUserMedia({ video: true });
    webcam.srcObject = webcamStream;

    await loadModel();

    console.log("🎥 Face detection dimulai...");
    detectionInterval = setInterval(async () => {
        if (!isDetecting || !model) return;

        try {
            const imgTensor = tf.tidy(() =>
                tf.browser
                    .fromPixels(webcam)
                    .resizeNearestNeighbor([96, 96])
                    .toFloat()
                    .div(255.0)
                    .expandDims(0)
                    .transpose([0, 3, 1, 2])
            );

            const prediction = model.execute(imgTensor);
            const result = await prediction.data();

            const emotions = [
                "angry",
                "disgust",
                "fear",
                "happy",
                "sad",
                "surprise",
                "neutral",
            ];
            const maxIndex = result.indexOf(Math.max(...result));
            const dominantEmotion = emotions[maxIndex];
            const confidence = Math.round(result[maxIndex] * 100);

            // ✅ Debug tiap frame (3 detik sekali)
            console.log(`🧠 Emosi: ${dominantEmotion} (${confidence}%)`);

            // Simpan hasil
            emotionData.push({ emotion: dominantEmotion, confidence });

            tf.dispose([imgTensor, prediction]);
        } catch (err) {
            console.warn("❌ Gagal mendeteksi wajah:", err);
        }
    }, 1000); // jeda 1 detik antar frame
}

// ==================== STOP DETECTION ====================
async function stopDetection(userId) {
    if (!isDetecting) return;
    isDetecting = false;

    clearInterval(detectionInterval);
    detectionInterval = null;

    if (webcamStream) {
        webcamStream.getTracks().forEach((track) => track.stop());
        webcamStream = null;
    }

    const moodScoreMap = {
        happy: 100,
        surprise: 85,
        neutral: 60,
        sad: 5,
        angry: 10,
        fear: 5,
        disgust: 10,
    };

    if (emotionData.length === 0) {
        console.warn("⚠️ Tidak ada data emosi yang terekam.");
        return 50; // netral default
    }

    // Hitung rata-rata semua frame
    const totalScore = emotionData.reduce((sum, e) => {
        return sum + (moodScoreMap[e.emotion] ?? 50);
    }, 0);
    const avgScore = Math.round(totalScore / emotionData.length);

    // Hitung emosi dominan
    const counts = {};
    emotionData.forEach((e) => {
        counts[e.emotion] = (counts[e.emotion] || 0) + 1;
    });

    const dominantEmotion = Object.keys(counts).reduce((a, b) =>
        counts[a] > counts[b] ? a : b
    );

    const dominantScore = moodScoreMap[dominantEmotion] || 50;

    // Gabungkan rata-rata dan dominan (biar hasil stabil)
    const finalScore = Math.round((avgScore + dominantScore) / 2);

    // ✅ Debug hasil akhir
    console.log("📊 Rata-rata skor mood wajah:", avgScore);
    console.log(`🏆 Emosi dominan: ${dominantEmotion} → skor ${dominantScore}`);
    console.log(`💡 Skor akhir gabungan: ${finalScore}`);

    return finalScore; // kirim balik ke video.blade
}
