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

    // Main function untuk roll dice dan move
    async function rollDiceAndMove() {
        const dice = document.querySelector('.dice');
        const diceResult = document.getElementById('diceResult');
        const pion = document.getElementById('pion');
        const posText = document.getElementById('currentPositionText');
        let currentPos = parseInt(posText.textContent.match(/\d+/)[0]);

        // Disable dice sementara
        const diceContainer = document.getElementById('dice3d');
        diceContainer.style.pointerEvents = 'none';
        diceContainer.style.opacity = '0.7';

        // Animasi putar dadu
        dice.style.transform = 'rotateX(720deg) rotateY(720deg)';
        await new Promise(resolve => setTimeout(resolve, 1000));

        try {
            const res = await fetch('/game/roll', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.gameData.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();
            const diceVal = data.dice;

            const rotationMap = {
                1: 'rotateX(0deg) rotateY(0deg)',
                2: 'rotateX(-90deg) rotateY(0deg)',
                3: 'rotateY(-90deg) rotateX(0deg)',
                4: 'rotateY(90deg) rotateX(0deg)',
                5: 'rotateX(90deg) rotateY(0deg)',
                6: 'rotateX(0deg) rotateY(180deg)'
            };

            dice.style.transform = rotationMap[diceVal];
            diceResult.textContent = `🎲 Hasil: ${diceVal}`;

            await new Promise(resolve => setTimeout(resolve, 1000));

            let targetPos = currentPos + diceVal;
            if (targetPos > 35) targetPos = 35 - (targetPos - 35);

            // Animasi gerakan pion
            let pos = currentPos;
            while (pos !== targetPos) {
                pos += (pos < targetPos) ? 1 : -1;
                const { top, left } = getCellPosition(`cell-${pos}`);
                pion.style.top = `${top}px`;
                pion.style.left = `${left}px`;
                await new Promise(resolve => setTimeout(resolve, 400));
            }

            // Update posisi di server
            const res2 = await fetch('/game/next', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.gameData.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ steps: diceVal })
            });

            const data2 = await res2.json();
            const card = data2.card;

            // Tampilkan modal kartu
            showCardModal(card, targetPos);

        } catch (error) {
            console.error('Error:', error);
            // Re-enable dice on error
            diceContainer.style.pointerEvents = 'auto';
            diceContainer.style.opacity = '1';
        }
    }

    // Function untuk menampilkan modal kartu
    function showCardModal(card, newPosition) {
        const flipCard = document.getElementById('flipCard');
        const cardInner = flipCard.querySelector('.flip-card');
        const cardColor = document.getElementById('cardColor');
        const cardContent = document.getElementById('cardContent');

        // Set content
        cardColor.textContent = `Kartu ${card.color || 'Perjalanan'}`;
        cardContent.textContent = card.content || 'Pesan kartu tidak tersedia.';

        // Show modal
        flipCard.classList.remove('hidden');

        // Start flip animation
        setTimeout(() => {
            cardInner.classList.add('flipped');
        }, 200);

        // Store new position for when modal closes
        window.newPosition = newPosition;
    }

    // Function untuk menutup modal dan update posisi
    function closeModalAndContinue() {
        const flipCard = document.getElementById('flipCard');
        const cardInner = flipCard.querySelector('.flip-card');
        const posText = document.getElementById('currentPositionText');
        const diceContainer = document.getElementById('dice3d');

        // Reset flip animation
        cardInner.classList.remove('flipped');
        
        // Hide modal
        setTimeout(() => {
            flipCard.classList.add('hidden');
            
            // Update position text
            if (window.newPosition) {
                posText.innerHTML = `<span class="text-xl">📍</span>
                    Posisi Saat Ini: <span class="bg-emerald-200 px-3 py-1 rounded-full">${window.newPosition}</span>`;
                    
                // Check if reached finish
                if (window.newPosition >= 35) {
                    setTimeout(() => {
                        window.location.href = '{{ route("game.choice", $session->session_token) }}';
                    }, 1000);
                }
            }
            
            // Re-enable dice
            diceContainer.style.pointerEvents = 'auto';
            diceContainer.style.opacity = '1';
            
        }, 300);
    }

    // Initialize game saat DOM loaded
    window.addEventListener('DOMContentLoaded', () => {
        // Create dice
        const diceContainer = document.getElementById('dice3d');
        diceContainer.innerHTML = '';
        const diceElement = createDiceElement();
        diceContainer.appendChild(diceElement);

        // Set initial pion position
        const pion = document.getElementById('pion');
        const current = window.gameData.currentPosition;
        const { top, left } = getCellPosition(`cell-${current}`);
        pion.style.top = `${top}px`;
        pion.style.left = `${left}px`;

        // Add dice click event
        diceContainer.onclick = rollDiceAndMove;

        // Event listeners untuk modal buttons
        const nextTurnBtn = document.getElementById('nextTurnBtn');

        if (nextTurnBtn) {
            nextTurnBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Loading state
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

        // Close modal saat klik di luar
        const flipCard = document.getElementById('flipCard');
        if (flipCard) {
            flipCard.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModalAndContinue();
                }
            });
        }

        // ESC key untuk tutup modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && flipCard && !flipCard.classList.contains('hidden')) {
                closeModalAndContinue();
            }
        });
    });