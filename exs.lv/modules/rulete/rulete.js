/**
 * EXS.LV Roulette Game JavaScript Engine
 */

document.addEventListener('DOMContentLoaded', function () {
	const canvas = document.getElementById('roulette-wheel');
	if (!canvas) return;

	const ctx = canvas.getContext('2d');
	const resultDisplay = document.getElementById('roulette-result-display');
	const userGoldVal = document.getElementById('user-gold-val');
	const totalBetVal = document.getElementById('total-bet-val');
	const lastWinVal = document.getElementById('last-win-val');
	const spinBtn = document.getElementById('spin-btn');
	const clearBtn = document.getElementById('clear-btn');
	const doubleBtn = document.getElementById('double-btn');
	const chipBtns = document.querySelectorAll('.chip-btn');
	const cells = document.querySelectorAll('.cell');

	// Guest Mode LocalStorage Persistence
	const isGuestView = document.querySelector('.alert-info') !== null;
	if (isGuestView) {
		const todayStr = new Date().toISOString().slice(0, 10);
		const lastReset = localStorage.getItem('rulete_guest_reset');
		let guestGold = parseInt(localStorage.getItem('rulete_guest_gold'));

		if (isNaN(guestGold) || lastReset !== todayStr) {
			if (isNaN(guestGold) || guestGold < 100) {
				guestGold = 100;
			}
			localStorage.setItem('rulete_guest_reset', todayStr);
			localStorage.setItem('rulete_guest_gold', guestGold);
		}

		if (userGoldVal) {
			userGoldVal.textContent = guestGold;
		}
	}

	// European Wheel Order
	const wheelNumbers = [
		0, 32, 15, 19, 4, 21, 2, 25, 17, 34, 6, 27, 13, 36,
		11, 30, 8, 23, 10, 5, 24, 16, 33, 1, 20, 14, 31, 9,
		22, 18, 29, 7, 28, 12, 35, 3, 26
	];

	const redNumbers = [1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36];

	let currentSelectedChip = 1;
	let currentRotationAngle = 0;
	let isSpinning = false;
	let placedBets = {}; // key -> { type, val, amount, element }

	// Canvas dimensions
	const centerX = canvas.width / 2;
	const centerY = canvas.height / 2;
	const outerRadius = canvas.width / 2 - 10;
	const innerRadius = outerRadius - 40;
	const numSlices = wheelNumbers.length;
	const sliceAngle = (2 * Math.PI) / numSlices;

	// Render Roulette Wheel
	function drawWheel(rotation = 0) {
		ctx.clearRect(0, 0, canvas.width, canvas.height);

		// Outer Ring
		ctx.beginPath();
		ctx.arc(centerX, centerY, outerRadius + 8, 0, 2 * Math.PI);
		ctx.fillStyle = '#b45309';
		ctx.fill();

		ctx.beginPath();
		ctx.arc(centerX, centerY, outerRadius, 0, 2 * Math.PI);
		ctx.fillStyle = '#0f172a';
		ctx.fill();

		// Slices
		for (let i = 0; i < numSlices; i++) {
			const startAngle = rotation + i * sliceAngle;
			const endAngle = startAngle + sliceAngle;
			const num = wheelNumbers[i];

			let fillColor = '#16a34a'; // Green 0
			if (redNumbers.includes(num)) {
				fillColor = '#dc2626'; // Red
			} else if (num !== 0) {
				fillColor = '#1e293b'; // Black
			}

			// Draw Pocket Slice
			ctx.beginPath();
			ctx.moveTo(centerX, centerY);
			ctx.arc(centerX, centerY, outerRadius - 4, startAngle, endAngle);
			ctx.closePath();
			ctx.fillStyle = fillColor;
			ctx.fill();
			ctx.lineWidth = 1;
			ctx.strokeStyle = 'rgba(255,255,255,0.2)';
			ctx.stroke();

			// Draw Number Text
			ctx.save();
			ctx.translate(centerX, centerY);
			ctx.rotate(startAngle + sliceAngle / 2);
			ctx.textAlign = 'right';
			ctx.fillStyle = '#ffffff';
			ctx.font = 'bold 12px sans-serif';
			ctx.fillText(num.toString(), outerRadius - 12, 4);
			ctx.restore();
		}

		// Center Hub
		ctx.beginPath();
		ctx.arc(centerX, centerY, innerRadius - 20, 0, 2 * Math.PI);
		ctx.fillStyle = 'radial-gradient(circle, #f59e0b 0%, #78350f 100%)';
		ctx.fill();
		ctx.lineWidth = 3;
		ctx.strokeStyle = '#d4af37';
		ctx.stroke();
	}

	drawWheel(currentRotationAngle);

	// Select Chip
	chipBtns.forEach(btn => {
		btn.addEventListener('click', function () {
			chipBtns.forEach(b => b.classList.remove('active'));
			this.classList.add('active');
			currentSelectedChip = parseInt(this.getAttribute('data-value'));
		});
	});

	// Place Bet on Cell
	cells.forEach(cell => {
		cell.addEventListener('click', function () {
			if (isSpinning) return;

			const type = this.getAttribute('data-type');
			const val = this.hasAttribute('data-val') ? parseInt(this.getAttribute('data-val')) : null;
			const cellKey = type + (val !== null ? '_' + val : '');

			if (!placedBets[cellKey]) {
				placedBets[cellKey] = {
					type: type,
					val: val,
					amount: 0,
					element: this
				};
			}

			const currentGold = parseInt(userGoldVal.textContent) || 0;
			const totalCurrentBet = calculateTotalBet();

			if (totalCurrentBet + currentSelectedChip > currentGold) {
				resultDisplay.innerHTML = '<span style="color:#ef4444;">Nepietiekams žetonu daudzums šai likmei!</span>';
				return;
			}

			placedBets[cellKey].amount += currentSelectedChip;
			updateCellBadges();
			updateBetTotals();
		});
	});

	function calculateTotalBet() {
		let sum = 0;
		for (let key in placedBets) {
			sum += placedBets[key].amount;
		}
		return sum;
	}

	function updateBetTotals() {
		const total = calculateTotalBet();
		totalBetVal.textContent = total;
	}

	function updateCellBadges() {
		// Remove existing badges
		document.querySelectorAll('.cell-chip-badge').forEach(el => el.remove());

		for (let key in placedBets) {
			const bet = placedBets[key];
			if (bet.amount > 0) {
				const badge = document.createElement('div');
				badge.className = 'cell-chip-badge';
				badge.textContent = bet.amount > 999 ? '999+' : bet.amount;
				bet.element.appendChild(badge);
			}
		}
	}

	// Clear Bets
	clearBtn.addEventListener('click', function () {
		if (isSpinning) return;
		placedBets = {};
		updateCellBadges();
		updateBetTotals();
	});

	// Double Bets
	doubleBtn.addEventListener('click', function () {
		if (isSpinning) return;
		const currentGold = parseInt(userGoldVal.textContent) || 0;
		const total = calculateTotalBet();

		if (total === 0) return;
		if (total * 2 > currentGold) {
			resultDisplay.innerHTML = '<span style="color:#ef4444;">Nepietiek žetonu, lai dubultotu likmes!</span>';
			return;
		}

		for (let key in placedBets) {
			placedBets[key].amount *= 2;
		}
		updateCellBadges();
		updateBetTotals();
	});

	// Spin Button Handler
	spinBtn.addEventListener('click', function () {
		if (isSpinning) return;

		const totalBet = calculateTotalBet();
		if (totalBet <= 0) {
			resultDisplay.innerHTML = '<span style="color:#f59e0b;">Vispirms uzliec vismaz vienu likmi!</span>';
			return;
		}

		const betsArray = [];
		for (let key in placedBets) {
			if (placedBets[key].amount > 0) {
				betsArray.push({
					type: placedBets[key].type,
					val: placedBets[key].val,
					amount: placedBets[key].amount
				});
			}
		}

		isSpinning = true;
		spinBtn.disabled = true;
		resultDisplay.innerHTML = '🌀 <em>Griežam ruleti... Lai veicas!</em>';

		const currentGoldVal = parseInt(userGoldVal.textContent) || 100;

		// Send spin request to PHP backend
		fetch('/rulete?action=spin', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ bets: betsArray, guest_gold: currentGoldVal })
		})
		.then(res => res.json())
		.then(data => {
			if (!data.success) {
				resultDisplay.innerHTML = '<span style="color:#ef4444;">' + (data.error || 'Kļūda!') + '</span>';
				isSpinning = false;
				spinBtn.disabled = false;
				return;
			}

			// Animate wheel to winning number
			animateSpin(data.winning_number, function () {
				isSpinning = false;
				spinBtn.disabled = false;

				userGoldVal.textContent = data.new_gold;
				lastWinVal.textContent = data.payout;

				if (data.is_guest) {
					localStorage.setItem('rulete_guest_gold', data.new_gold);
				}

				let colorClass = data.color === 'red' ? 'red' : (data.color === 'black' ? 'black' : 'green');
				let outcomeText = '';

				if (data.net_gain > 0) {
					outcomeText = '<span style="color:#4ade80;">Uzvara! Apsveicam! +' + data.net_gain + ' žetoni!</span>';
				} else if (data.net_gain < 0) {
					outcomeText = '<span style="color:#f87171;">Šoreiz zaudējums (-' + Math.abs(data.net_gain) + ' žetoni).</span>';
				} else {
					outcomeText = '<span style="color:#38bdf8;">Likme atgriezta bešā.</span>';
				}

				resultDisplay.innerHTML = 'Uzvarošais skaitlis: <span class="badge-num ' + colorClass + '">' + data.winning_number + '</span> — ' + outcomeText;
			});
		})
		.catch(err => {
			resultDisplay.innerHTML = '<span style="color:#ef4444;">Tīkla kļūda! Lūdzu mēģini vēlreiz.</span>';
			isSpinning = false;
			spinBtn.disabled = false;
		});
	});

	// Animate Spin to Target Number
	function animateSpin(winningNum, onComplete) {
		const targetIndex = wheelNumbers.indexOf(winningNum);
		if (targetIndex === -1) return onComplete();

		// Calculate angle for target index so pointer at 12 o'clock (-Math.PI/2) points to target
		const sliceRad = (2 * Math.PI) / numSlices;
		const targetAngleOffset = -Math.PI / 2 - (targetIndex * sliceRad + sliceRad / 2);

		// Spin extra 5 full rotations (10 * Math.PI)
		const extraRounds = 10 * Math.PI;
		const finalAngle = currentRotationAngle + extraRounds + (targetAngleOffset - (currentRotationAngle % (2 * Math.PI)));

		const startTime = performance.now();
		const duration = 3200; // 3.2 seconds spin

		function easeOutCubic(t) {
			return 1 - Math.pow(1 - t, 3);
		}

		function step(now) {
			const elapsed = now - startTime;
			const progress = Math.min(elapsed / duration, 1);
			const easedProgress = easeOutCubic(progress);

			const currentAngle = currentRotationAngle + (finalAngle - currentRotationAngle) * easedProgress;
			drawWheel(currentAngle);

			if (progress < 1) {
				requestAnimationFrame(step);
			} else {
				currentRotationAngle = finalAngle % (2 * Math.PI);
				onComplete();
			}
		}

		requestAnimationFrame(step);
	}
});
