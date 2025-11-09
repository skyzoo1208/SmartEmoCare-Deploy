<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Board - Perjalanan Hidup</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.16.0/dist/tf.min.js"></script>
   
   @vite(['resources/css/app.css'])
   <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* 3D Dice Styles */
        .dice {
            position: relative;
            width: 80px;
            height: 80px;
            transform-style: preserve-3d;
            transition: transform 1s ease-out;
            margin: 0 auto;
            cursor: pointer;
        }

        .dice-face {
            position: absolute;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ffffff, #f0f0f0);
            border: 3px solid #333;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            padding: 8px;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.1);
        }

        .dice-face.front { transform: rotateY(0deg) translateZ(40px); }
        .dice-face.back { transform: rotateY(180deg) translateZ(40px); }
        .dice-face.right { transform: rotateY(90deg) translateZ(40px); }
        .dice-face.left { transform: rotateY(-90deg) translateZ(40px); }
        .dice-face.top { transform: rotateX(90deg) translateZ(40px); }
        .dice-face.bottom { transform: rotateX(-90deg) translateZ(40px); }

        .dot {
            width: 12px;
            height: 12px;
            background: #333;
            border-radius: 50%;
            margin: 2px;
        }

        /* Dot arrangements for each face */
        .dots-1 { justify-content: center; align-items: center; }
        .dots-2 { justify-content: space-between; align-items: center; flex-direction: column; }
        .dots-3 { justify-content: space-between; align-items: stretch; }
        .dots-3 .dot:nth-child(1) { align-self: flex-start; }
        .dots-3 .dot:nth-child(2) { align-self: center; }
        .dots-3 .dot:nth-child(3) { align-self: flex-end; }
        .dots-4 { justify-content: space-between; align-content: space-between; }
        .dots-5 { justify-content: space-between; align-content: space-between; }
        .dots-5 .dot:nth-child(5) { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }
        .dots-6 { justify-content: space-between; align-content: space-between; }

        /* Board Styles */
        .board-cell {
            width: 80px;
            height: 80px;
            border: 2px solid #4a5568;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
        }

        .board-cell:hover {
            transform: scale(1.05);
            z-index: 2;
        }

        /* Pion Styles */
        .pion {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a5a);
            border-radius: 50%;
            position: absolute;
            transition: all 0.4s ease;
            z-index: 10;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            border: 3px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* Flip Card Animation */
        .flip-card {
            width: 100%;
            height: 300px;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s;
        }

        .flip-card.flipped {
            transform: rotateY(180deg);
        }

        .flip-card-front, .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
        }

        .flip-card-front {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .flip-card-back {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            transform: rotateY(180deg);
            padding: 20px;
            flex-direction: column;
        }

        /* Particle Background */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 1; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 0.5; }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: linear-gradient(135deg, #2d3748, #4a5568);
            border-radius: 12px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .modal-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 24px;
        }

        .modal-button {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.2s;
        }

        .modal-button:hover {
            transform: scale(1.05);
        }

        .continue-button {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .end-button {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .board-cell {
                width: 60px;
                height: 60px;
                font-size: 12px;
            }
            
            .dice {
                width: 60px;
                height: 60px;
            }
            
            .dice-face {
                width: 60px;
                height: 60px;
            }
            
            .pion {
                width: 25px;
                height: 25px;
            }
        }
    </style>
</head>
<body class=" min-h-screen" style="background-image: url('/img/Boardgame(4).png');">
    <video id="webcam" autoplay playsinline muted width="96" height="96" style="display:True;"></video>
    <!-- Particle Background -->
    <div class="particles">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
        <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 0.5s;"></div>
        <div class="particle" style="left: 80%; animation-delay: 1.5s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 2.5s;"></div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-6xl font-bold text-black mb-4 bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text">
                🎯 Perjalanan Hidup
            </h1>
            <p class="text-xl text-blue-400">Lempar dadu dan mulai petualangan!</p>

            <!-- Tombol Mulai & Selesai -->
<div class="mt-6 flex justify-center gap-4">
    <button id="startBtn" 
        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-full font-bold shadow-md transition-all duration-300">
        ▶️ Mulai
    </button>

    <button id="stopBtnManual" 
        class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-full font-bold shadow-md transition-all duration-300 hidden">
        ⏹️ Selesai
    </button>
</div>


        </div>

        <!-- Main Game Area -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Game Board -->
            <div class="flex-1">
                <div class="bg-green-200 backdrop-blur-sm rounded-3xl p-8 border border-white/20">
                    <h2 class="text-2xl font-bold text-black mb-6 text-center">🏁 Papan Permainan</h2>
                    
                    <div id="boardWrapper" class="relative">
                        <!-- Board Grid (6x6 grid with path) -->
                        <div class="grid grid-cols-6 gap-1 max-w-2xl mx-auto">
                            <!-- Row 1: Start to 5 -->
                            <div id="cell-1" class="board-cell bg-green-400 text-green-900">START</div>
                            <div id="cell-2" class="board-cell bg-blue-300 text-blue-900">2</div>
                            <div id="cell-3" class="board-cell bg-yellow-300 text-yellow-900">3</div>
                            <div id="cell-4" class="board-cell bg-pink-300 text-pink-900">4</div>
                            <div id="cell-5" class="board-cell bg-purple-300 text-purple-900">5</div>
                            <div id="cell-6" class="board-cell bg-indigo-300 text-indigo-900">6</div>
                            
                            <!-- Row 2: 12 to 7 (reverse) -->
                            <div id="cell-12" class="board-cell bg-red-300 text-red-900">12</div>
                            <div id="cell-11" class="board-cell bg-orange-300 text-orange-900">11</div>
                            <div id="cell-10" class="board-cell bg-teal-300 text-teal-900">10</div>
                            <div id="cell-9" class="board-cell bg-cyan-300 text-cyan-900">9</div>
                            <div id="cell-8" class="board-cell bg-lime-300 text-lime-900">8</div>
                            <div id="cell-7" class="board-cell bg-emerald-300 text-emerald-900">7</div>
                            
                            <!-- Row 3: 13 to 18 -->
                            <div id="cell-13" class="board-cell bg-violet-300 text-violet-900">13</div>
                            <div id="cell-14" class="board-cell bg-fuchsia-300 text-fuchsia-900">14</div>
                            <div id="cell-15" class="board-cell bg-rose-300 text-rose-900">15</div>
                            <div id="cell-16" class="board-cell bg-amber-300 text-amber-900">16</div>
                            <div id="cell-17" class="board-cell bg-green-300 text-green-900">17</div>
                            <div id="cell-18" class="board-cell bg-blue-300 text-blue-900">18</div>
                            
                            <!-- Row 4: 24 to 19 (reverse) -->
                            <div id="cell-24" class="board-cell bg-yellow-300 text-yellow-900">24</div>
                            <div id="cell-23" class="board-cell bg-pink-300 text-pink-900">23</div>
                            <div id="cell-22" class="board-cell bg-purple-300 text-purple-900">22</div>
                            <div id="cell-21" class="board-cell bg-indigo-300 text-indigo-900">21</div>
                            <div id="cell-20" class="board-cell bg-red-300 text-red-900">20</div>
                            <div id="cell-19" class="board-cell bg-orange-300 text-orange-900">19</div>
                            
                            <!-- Row 5: 25 to 30 -->
                            <div id="cell-25" class="board-cell bg-teal-300 text-teal-900">25</div>
                            <div id="cell-26" class="board-cell bg-cyan-300 text-cyan-900">26</div>
                            <div id="cell-27" class="board-cell bg-lime-300 text-lime-900">27</div>
                            <div id="cell-28" class="board-cell bg-emerald-300 text-emerald-900">28</div>
                            <div id="cell-29" class="board-cell bg-violet-300 text-violet-900">29</div>
                            <div id="cell-30" class="board-cell bg-fuchsia-300 text-fuchsia-900">30</div>
                            
                            <!-- Row 6: 35 to 31 (reverse) -->
                            <div id="cell-35" class="board-cell bg-gradient-to-r from-yellow-400 to-red-500 text-white font-bold">FINISH</div>
                            <div id="cell-34" class="board-cell bg-amber-300 text-amber-900">34</div>
                            <div id="cell-33" class="board-cell bg-green-300 text-green-900">33</div>
                            <div id="cell-32" class="board-cell bg-blue-300 text-blue-900">32</div>
                            <div id="cell-31" class="board-cell bg-rose-300 text-rose-900">31</div>
                            <div class="board-cell opacity-50 bg-gray-300"></div>
                        </div>
                        
                        <!-- Pion -->
                        <div id="pion" class="pion">🚀</div>
                    </div>
                </div>
            </div>

            <!-- Control Panel -->
            <div class="lg:w-80">
                <div class="bg-green-200 backdrop-blur-sm rounded-3xl p-8 border border-white/20">
                    <h2 class="text-2xl font-bold text-black mb-6 text-center">🎮 Kontrol Game</h2>
                    
                    <!-- 3D Dice Container -->
                    <div class="mb-8">
                        <div class="text-center mb-4">
                            <p class="text-black mb-2">Klik dadu untuk melempar!</p>
                        </div>
                        <div id="dice3d" class="perspective-1000 cursor-pointer hover:scale-110 transition-transform">
                            <!-- Dice will be created by JavaScript -->
                        </div>
                    </div>

                    <!-- Game Instructions -->
                    <div class="bg-blue-500/20 rounded-2xl p-6 border border-blue-400/30">
                        <h3 class="text-lg font-bold text-gray-700 mb-3">📋 Cara Bermain:</h3>
                        <ul class="text-gray-700 space-y-2 text-sm">
                            <li>• Klik dadu untuk melempar</li>
                            <li>• Pion akan bergerak otomatis</li>
                            <li>• Baca kartu yang muncul</li>
                            <li>• Capai kotak FINISH untuk menang</li>
                            <li>• Jika melewati kotak 35, mundur</li>
                        </ul>
                        <button id="exitGameBtn" class="mt-4 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white px-6 py-2 rounded-full font-bold text-sm transition-all duration-300 transform hover:scale-105">
                            Keluar Permainan
                        </button>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div class="bg-green-500/20 rounded-xl p-4 text-center border border-green-400/30">
                            <div class="text-2xl mb-1">🎯</div>
                            <div class="text-gray-500 text-sm">Target</div>
                            <div class="text-gray-700 font-bold">Kotak 35</div>
                        </div>
                        <div class="bg-purple-500/20 rounded-xl p-4 text-center border border-purple-400/30">
                            <div class="text-2xl mb-1">🎲</div>
                            <div class="text-gray-500 text-sm">Dadu</div>
                            <div class="text-gray-700 font-bold">1-6</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Modal -->
    <div id="flipCard" class=" fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="max-w-md w-full">
            <div class="flip-card">
                <!-- Front Side -->
                <div class="flip-card-front">
                    <div class="text-center">
                        <div class="text-6xl mb-4">🎴</div>
                        <div class="text-2xl font-bold">Kartu Petualangan</div>
                        <div class="text-lg opacity-90 mt-2">Menunggu...</div>
                    </div>
                </div>
                
                <!-- Back Side -->
                <div class="flip-card-back">
                    <div class="text-center mb-6">
                        <h3 id="cardColor" class="text-2xl font-bold mb-4">Kartu Perjalanan</h3>
                        <div class="bg-white/20 rounded-lg p-4 mb-6">
                            <p id="cardContent" class="text-lg leading-relaxed">
                                Pesan kartu akan muncul di sini...
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex justify-center">
                        <button id="nextTurnBtn" class="bg-gradient-to-r from-emerald-500 to-blue-500 hover:from-emerald-600 hover:to-blue-600 text-white px-8 py-3 rounded-full font-bold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                            ✨ Lanjutkan Perjalanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Game End Modal -->
    <div id="gameEndModal" class="modal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4">Perjalanan Selesai!</h2>
            <p class="text-lg mb-6">Ingin lanjut bercerita atau selesai sampai sini?</p>
            <div class="modal-buttons">
                <a href="/curhat/{{ $user->id }}" class="modal-button continue-button">
                    Lanjut Bercerita
                </a>
                <a id="stopBtn" href="/final/{{ $user->id }}" class="modal-button end-button">
                    Selesai
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Game data simulation (ambil dari server)
        window.gameData = {
            currentPosition: 1,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || 'dummy-token',
            userId: '{{ $user->id }}'
        };

        function createDiceElement() {
            const dice = document.createElement('div');
            dice.className = 'dice';

            const faceDots = {
                front: 1,
                back: 6,
                right: 3,
                left: 4,
                top: 2,
                bottom: 5
            };

            for (const [faceName, dotCount] of Object.entries(faceDots)) {
                const face = document.createElement('div');
                face.className = `dice-face ${faceName} dots-${dotCount}`;

                for (let i = 0; i < dotCount; i++) {
                    const dot = document.createElement('div');
                    dot.className = 'dot';
                    face.appendChild(dot);
                }

                dice.appendChild(face);
            }

            return dice;
        }

        function getCellPosition(cellId) {
            const cell = document.getElementById(cellId);
            const wrapper = document.getElementById('boardWrapper');
            const cellRect = cell.getBoundingClientRect();
            const wrapperRect = wrapper.getBoundingClientRect();

            return {
                top: cellRect.top - wrapperRect.top,
                left: cellRect.left - wrapperRect.left
            };
        }

        function showGameEndModal() {
            const gameEndModal = document.getElementById('gameEndModal');
            gameEndModal.style.display = 'flex';
        }

        

        let lastCardIndex = 0;
let totalCards = 15;

async function rollDiceAndMove() {
    const dice = document.querySelector('.dice');
    const pion = document.getElementById('pion');
    const diceContainer = document.getElementById('dice3d');

    diceContainer.style.pointerEvents = 'none';
    diceContainer.style.opacity = '0.7';

    // animasi roll dadu
    dice.style.transform = 'rotateX(720deg) rotateY(720deg)';
    await new Promise(resolve => setTimeout(resolve, 1000));

    // Langkah dikontrol agar 15 kartu selesai pas di FINISH
    const remainingCards = totalCards - lastCardIndex;
    const diceVal = Math.min(remainingCards, Math.floor(Math.random() * 3) + 1); // 1-3 langkah saja

    const rotationMap = {
        1: 'rotateX(0deg) rotateY(0deg)',
        2: 'rotateX(-90deg) rotateY(0deg)',
        3: 'rotateY(-90deg) rotateX(0deg)',
        4: 'rotateY(90deg) rotateX(0deg)',
        5: 'rotateX(90deg) rotateY(0deg)',
        6: 'rotateX(0deg) rotateY(180deg)'
    };
    dice.style.transform = rotationMap[diceVal];
    await new Promise(resolve => setTimeout(resolve, 1000));

    // Pindahkan pion
    let currentPos = window.gameData.currentPosition;
    let targetPos = Math.min(currentPos + diceVal, 35);
    for (let pos = currentPos + 1; pos <= targetPos; pos++) {
        const { top, left } = getCellPosition(`cell-${pos}`);
        pion.style.top = `${top}px`;
        pion.style.left = `${left}px`;
        await new Promise(resolve => setTimeout(resolve, 400));
    }
    window.gameData.currentPosition = targetPos;

    // Ambil kartu berikutnya
    try {
        const response = await fetch(`/boardgame/${window.gameData.userId}/draw-card?last_index=${lastCardIndex}`, {
            headers: { Accept: 'application/json' }
        });

        const card = await response.json();

        if (card.finished) {
            showGameEndModal();
            return;
        }

        lastCardIndex = card.index;
        showCardModal(card, targetPos);
    } catch (error) {
        console.error("Gagal ambil kartu:", error);
    }
}

function showCardModal(card, newPosition) {
    const flipCard = document.getElementById('flipCard');
    const cardInner = flipCard.querySelector('.flip-card');
    const cardColor = document.getElementById('cardColor');
    const cardContent = document.getElementById('cardContent');

    cardColor.textContent = card.title || "Kartu Perjalanan";
    cardContent.textContent = card.description || "Instruksi tidak tersedia.";

    flipCard.classList.remove('hidden');

    setTimeout(() => {
        cardInner.classList.add('flipped');
    }, 200);

    window.newPosition = newPosition;
}


        function closeModalAndContinue() {
            const flipCard = document.getElementById('flipCard');
            const cardInner = flipCard.querySelector('.flip-card');
            const diceContainer = document.getElementById('dice3d');

            cardInner.classList.remove('flipped');
            
            setTimeout(() => {
                flipCard.classList.add('hidden');
                
                if (window.newPosition >= 35) {
                    showGameEndModal();
                } else {
                    diceContainer.style.pointerEvents = 'auto';
                    diceContainer.style.opacity = '1';
                }
            }, 300);
        }

        window.addEventListener('DOMContentLoaded', () => {
            const diceContainer = document.getElementById('dice3d');
            diceContainer.innerHTML = '';
            const diceElement = createDiceElement();
            diceContainer.appendChild(diceElement);

            const pion = document.getElementById('pion');
            const current = window.gameData.currentPosition;
            const { top, left } = getCellPosition(`cell-${current}`);
            pion.style.top = `${top}px`;
            pion.style.left = `${left}px`;

            diceContainer.onclick = rollDiceAndMove;

            const nextTurnBtn = document.getElementById('nextTurnBtn');
            if (nextTurnBtn) {
                nextTurnBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="animate-spin mr-2">⏳</span>Memproses...';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                        closeModalAndContinue();
                    }, 500);
                });
            }

            const flipCard = document.getElementById('flipCard');
            if (flipCard) {
                flipCard.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModalAndContinue();
                    }
                });
            }

            const exitGameBtn = document.getElementById('exitGameBtn');
            if (exitGameBtn) {
                exitGameBtn.addEventListener('click', function() {
                    showGameEndModal();
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (!flipCard.classList.contains('hidden')) {
                        closeModalAndContinue();
                    } else if (document.getElementById('gameEndModal').style.display === 'flex') {
                        document.getElementById('gameEndModal').style.display = 'none';
                    }
                }
            });
        });
    </script>

    <script>
    window.gameMode = "{{ $mode }}";
</script>


    <!-- Load Script Model -->
    <script src="{{ asset('tf_model/model_script.js') }}"></script>

    <script>
document.addEventListener("DOMContentLoaded", () => {
    const userId = {{ $user->id }};
    const mode = "{{ $mode }}"; // board / sound
    const startBtn = document.getElementById("startBtn");
    const stopBtnManual = document.getElementById("stopBtnManual");
    let therapyAudio = null;

    startBtn.addEventListener("click", async () => {
        startBtn.disabled = true;
        startBtn.textContent = "⏳ Memulai...";

        // Aktifkan kamera (face detection)
        await startDetection(userId);

        // Jika mode sound, hidupkan musik terapi
        if (mode === "sound") {
            therapyAudio = new Audio("{{ asset('sounds/sound_calm.mp3') }}");
            therapyAudio.loop = true;
            therapyAudio.play().catch(err => console.warn("Gagal putar audio:", err));
        }


        // Tampilkan tombol selesai
        setTimeout(() => {
            startBtn.classList.add("hidden");
            stopBtnManual.classList.remove("hidden");
        }, 500);
    });

    stopBtnManual.addEventListener("click", async () => {
        stopBtnManual.disabled = true;
        stopBtnManual.textContent = "⏳ Menyimpan...";

        try {
            // Hentikan deteksi & ambil skor akhir
            const avgScore = await stopDetection(userId);

            // Hentikan musik jika aktif
            if (therapyAudio) {
                therapyAudio.pause();
                therapyAudio = null;
            }

            // Simpan hasil akhir ke backend
            await fetch(`/api/mood-final/${userId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ final_score: avgScore }),
            });

            // Redirect ke halaman final
            window.location.href = `/final/${userId}`;
        } catch (error) {
            console.error("Gagal menyimpan hasil:", error);
            stopBtnManual.textContent = "Gagal, Coba Lagi";
            stopBtnManual.disabled = false;
        }
    });
});
    </script>



</body>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
    const stopBtn = document.getElementById("stopBtn");
    const userId = {{ $user->id }};

    if (stopBtn) {
        stopBtn.addEventListener("click", async (e) => {
            e.preventDefault();
            stopBtn.textContent = "Memproses...";
            stopBtn.disabled = true;

            try {
                const avgScore = await stopDetection(userId);

                await fetch(`/api/mood-final/${userId}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ final_score: avgScore }),
                });

                window.location.href = `/final/${userId}`;
            } catch (err) {
                console.error("Gagal menyimpan hasil:", err);
                stopBtn.textContent = "Coba Lagi";
                stopBtn.disabled = false;
            }
        });
    }
});

    </script>


</html>