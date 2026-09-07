/**
 * EXS.LV - Rezonanse (Synth Pulse Arena)
 * Interactive rhythm-action game with real-time procedural Web Audio synthesis.
 */

(function () {
	'use strict';

	// =========================================================================
	// 1. CONSTANTS & CONFIGURATION
	// =========================================================================
	const CANVAS_SIZE = 560;
	const CENTER_X = CANVAS_SIZE / 2;
	const CENTER_Y = CANVAS_SIZE / 2;
	const ARENA_RADIUS = 245;

	const BPM = 128;
	const BEAT_DURATION = 60 / BPM; // ~0.46875s per beat
	const LOOKAHEAD = 0.035; // 35ms scheduling window
	const SCHEDULE_INTERVAL = 25; // 25ms clock interval

	// Pentatonic scale frequencies in Hz (A minor: A, C, D, E, G)
	const SCALE_FREQS = [
		110.00, // A2
		130.81, // C3
		146.83, // D3
		164.81, // E3
		196.00, // G3
		220.00, // A3
		261.63, // C4
		293.66, // D4
		329.63, // E4
		392.00, // G4
		440.00, // A5
		523.25, // C5
		587.33, // D5
		659.25  // E5
	];

	// =========================================================================
	// 2. PROCEDURAL SYNTH SOUND ENGINE (WEB AUDIO API)
	// =========================================================================
	class SynthSoundEngine {
		constructor() {
			this.ctx = null;
			this.masterGain = null;
			this.isMuted = false;
			this.isPlaying = false;
			this.currentBeat = 0;
			this.nextBeatTime = 0;
			this.timerId = null;
			this.comboLevel = 1;
			this.isOverdrive = false;

			// Noise buffer for percussion
			this.noiseBuffer = null;
		}

		init() {
			if (this.ctx) {
				if (this.ctx.state === 'suspended') {
					this.ctx.resume();
				}
				return;
			}

			const AudioCtx = window.AudioContext || window.webkitAudioContext;
			if (!AudioCtx) return;

			this.ctx = new AudioCtx();
			this.masterGain = this.ctx.createGain();
			this.masterGain.gain.setValueAtTime(0.35, this.ctx.currentTime);
			this.masterGain.connect(this.ctx.destination);

			this.createNoiseBuffer();
		}

		createNoiseBuffer() {
			if (!this.ctx) return;
			const bufferSize = this.ctx.sampleRate * 1.5;
			this.noiseBuffer = this.ctx.createBuffer(1, bufferSize, this.ctx.sampleRate);
			const output = this.noiseBuffer.getChannelData(0);
			for (let i = 0; i < bufferSize; i++) {
				output[i] = Math.random() * 2 - 1;
			}
		}

		toggleMute() {
			this.isMuted = !this.isMuted;
			if (this.masterGain && this.ctx) {
				this.masterGain.gain.setTargetAtTime(this.isMuted ? 0 : 0.35, this.ctx.currentTime, 0.03);
			}
			return this.isMuted;
		}

		startClock() {
			if (!this.ctx) this.init();
			if (!this.ctx) return;

			this.isPlaying = true;
			this.currentBeat = 0;
			this.nextBeatTime = this.ctx.currentTime + 0.05;

			clearInterval(this.timerId);
			this.timerId = setInterval(() => this.scheduler(), SCHEDULE_INTERVAL);
		}

		stopClock() {
			this.isPlaying = false;
			clearInterval(this.timerId);
		}

		scheduler() {
			if (!this.ctx || !this.isPlaying) return;

			while (this.nextBeatTime < this.ctx.currentTime + LOOKAHEAD) {
				this.scheduleBeat(this.currentBeat, this.nextBeatTime);
				this.currentBeat++;
				this.nextBeatTime += BEAT_DURATION;
			}
		}

		scheduleBeat(beatIndex, time) {
			const quarter = beatIndex % 4;

			// Kick on quarter notes
			this.playKick(time);

			// Snare on beats 1 and 3 (2nd and 4th quarters)
			if (quarter === 1 || quarter === 3) {
				this.playSnare(time);
			}

			// Hi-hat offbeats (unlocked at combo >= 3 or in Overdrive)
			if (this.comboLevel >= 3 || this.isOverdrive) {
				this.playHiHat(time + BEAT_DURATION * 0.5);
			}

			// Synth Bass Arpeggiator (unlocked at combo >= 6 or in Overdrive)
			if (this.comboLevel >= 6 || this.isOverdrive) {
				const bassNotes = [110.00, 130.81, 146.83, 164.81]; // A2, C3, D3, E3
				const note = bassNotes[(beatIndex % bassNotes.length)];
				this.playSynthBass(note, time, BEAT_DURATION * 0.4);
			}

			// Ambient Lead Arp (unlocked at combo >= 12 or in Overdrive)
			if (this.comboLevel >= 12 || this.isOverdrive) {
				const leadNotes = [440.00, 523.25, 587.33, 659.25, 587.33, 523.25];
				const leadNote = leadNotes[(beatIndex % leadNotes.length)];
				this.playLeadSynth(leadNote, time + BEAT_DURATION * 0.25, BEAT_DURATION * 0.3);
			}
		}

		playKick(time) {
			if (!this.ctx) return;
			const osc = this.ctx.createOscillator();
			const gain = this.ctx.createGain();

			osc.type = 'sine';
			osc.frequency.setValueAtTime(130, time);
			osc.frequency.exponentialRampToValueAtTime(38, time + 0.11);

			gain.gain.setValueAtTime(0.7, time);
			gain.gain.exponentialRampToValueAtTime(0.001, time + 0.12);

			osc.connect(gain);
			gain.connect(this.masterGain);

			osc.start(time);
			osc.stop(time + 0.13);
		}

		playSnare(time) {
			if (!this.ctx || !this.noiseBuffer) return;

			// Noise Component
			const noise = this.ctx.createBufferSource();
			noise.buffer = this.noiseBuffer;

			const filter = this.ctx.createBiquadFilter();
			filter.type = 'highpass';
			filter.frequency.setValueAtTime(800, time);

			const gain = this.ctx.createGain();
			gain.gain.setValueAtTime(0.3, time);
			gain.gain.exponentialRampToValueAtTime(0.001, time + 0.15);

			noise.connect(filter);
			filter.connect(gain);
			gain.connect(this.masterGain);

			noise.start(time);
			noise.stop(time + 0.16);

			// Tonal body
			const osc = this.ctx.createOscillator();
			const oscGain = this.ctx.createGain();
			osc.type = 'triangle';
			osc.frequency.setValueAtTime(180, time);
			osc.frequency.exponentialRampToValueAtTime(70, time + 0.08);

			oscGain.gain.setValueAtTime(0.25, time);
			oscGain.gain.exponentialRampToValueAtTime(0.001, time + 0.09);

			osc.connect(oscGain);
			oscGain.connect(this.masterGain);

			osc.start(time);
			osc.stop(time + 0.1);
		}

		playHiHat(time) {
			if (!this.ctx || !this.noiseBuffer) return;
			const noise = this.ctx.createBufferSource();
			noise.buffer = this.noiseBuffer;

			const filter = this.ctx.createBiquadFilter();
			filter.type = 'bandpass';
			filter.frequency.setValueAtTime(7500, time);

			const gain = this.ctx.createGain();
			gain.gain.setValueAtTime(0.12, time);
			gain.gain.exponentialRampToValueAtTime(0.001, time + 0.04);

			noise.connect(filter);
			filter.connect(gain);
			gain.connect(this.masterGain);

			noise.start(time);
			noise.stop(time + 0.05);
		}

		playSynthBass(freq, time, duration) {
			if (!this.ctx) return;
			const osc = this.ctx.createOscillator();
			const filter = this.ctx.createBiquadFilter();
			const gain = this.ctx.createGain();

			osc.type = 'sawtooth';
			osc.frequency.setValueAtTime(freq, time);

			filter.type = 'lowpass';
			filter.frequency.setValueAtTime(450, time);
			filter.frequency.exponentialRampToValueAtTime(120, time + duration);

			gain.gain.setValueAtTime(0.22, time);
			gain.gain.exponentialRampToValueAtTime(0.001, time + duration);

			osc.connect(filter);
			filter.connect(gain);
			gain.connect(this.masterGain);

			osc.start(time);
			osc.stop(time + duration + 0.01);
		}

		playLeadSynth(freq, time, duration) {
			if (!this.ctx) return;
			const osc = this.ctx.createOscillator();
			const gain = this.ctx.createGain();

			osc.type = 'triangle';
			osc.frequency.setValueAtTime(freq, time);

			gain.gain.setValueAtTime(0.12, time);
			gain.gain.exponentialRampToValueAtTime(0.001, time + duration);

			osc.connect(gain);
			gain.connect(this.masterGain);

			osc.start(time);
			osc.stop(time + duration + 0.01);
		}

		// Interactive Player Sounds
		playResonanceHit(isPerfect, combo) {
			if (!this.ctx) return;
			const now = this.ctx.currentTime;

			// Pick note according to combo
			const noteIndex = Math.min(SCALE_FREQS.length - 1, 5 + (combo % 8));
			const freq = SCALE_FREQS[noteIndex];

			const osc = this.ctx.createOscillator();
			const gain = this.ctx.createGain();

			osc.type = isPerfect ? 'sine' : 'triangle';
			osc.frequency.setValueAtTime(freq, now);
			if (isPerfect) {
				// Pitch bend up for joyous perfect hit
				osc.frequency.exponentialRampToValueAtTime(freq * 1.5, now + 0.18);
			}

			gain.gain.setValueAtTime(isPerfect ? 0.35 : 0.2, now);
			gain.gain.exponentialRampToValueAtTime(0.001, now + (isPerfect ? 0.28 : 0.16));

			osc.connect(gain);
			gain.connect(this.masterGain);

			osc.start(now);
			osc.stop(now + 0.3);
		}

		playDamage() {
			if (!this.ctx) return;
			const now = this.ctx.currentTime;
			const osc = this.ctx.createOscillator();
			const gain = this.ctx.createGain();

			osc.type = 'sawtooth';
			osc.frequency.setValueAtTime(150, now);
			osc.frequency.linearRampToValueAtTime(45, now + 0.25);

			gain.gain.setValueAtTime(0.35, now);
			gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);

			osc.connect(gain);
			gain.connect(this.masterGain);

			osc.start(now);
			osc.stop(now + 0.26);
		}

		playOverdriveStart() {
			if (!this.ctx) return;
			const now = this.ctx.currentTime;

			// Rapid chromatic arpeggio swell
			const notes = [220, 277, 330, 440, 554, 660, 880];
			notes.forEach((f, i) => {
				const osc = this.ctx.createOscillator();
				const gain = this.ctx.createGain();
				osc.type = 'sine';
				osc.frequency.setValueAtTime(f, now + i * 0.04);
				gain.gain.setValueAtTime(0.2, now + i * 0.04);
				gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.04 + 0.15);
				osc.connect(gain);
				gain.connect(this.masterGain);
				osc.start(now + i * 0.04);
				osc.stop(now + i * 0.04 + 0.16);
			});
		}

		playGameOver() {
			if (!this.ctx) return;
			const now = this.ctx.currentTime;
			const osc = this.ctx.createOscillator();
			const gain = this.ctx.createGain();

			osc.type = 'sawtooth';
			osc.frequency.setValueAtTime(260, now);
			osc.frequency.exponentialRampToValueAtTime(30, now + 0.6);

			gain.gain.setValueAtTime(0.3, now);
			gain.gain.exponentialRampToValueAtTime(0.001, now + 0.6);

			osc.connect(gain);
			gain.connect(this.masterGain);

			osc.start(now);
			osc.stop(now + 0.62);
		}
	}

	// =========================================================================
	// 3. MAIN GAME ENGINE
	// =========================================================================
	class RezonanseGame {
		constructor() {
			this.canvas = document.getElementById('rezonanse-canvas');
			if (!this.canvas) return;
			this.ctx = this.canvas.getContext('2d');

			this.sound = new SynthSoundEngine();

			// Game States: 'START', 'PLAYING', 'PAUSED', 'GAMEOVER'
			this.state = 'START';
			this.lastTime = 0;
			this.gameTime = 0;

			// Score & Combo
			this.score = 0;
			this.combo = 1;
			this.maxCombo = 1;
			this.perfectHits = 0;

			// Player
			this.player = {
				x: CENTER_X,
				y: CENTER_Y + 70,
				targetX: CENTER_X,
				targetY: CENTER_Y + 70,
				radius: 12,
				speed: 6,
				shields: 3,
				maxShields: 3,
				invulnerableTimer: 0,
				pulseCooldown: 0
			};

			// Overdrive system
			this.overdrive = {
				charge: 0, // 0 to 100
				active: false,
				timer: 0,
				duration: 6.0
			};

			// Beat Ring Waves (Expand from edges inward towards center)
			this.beatRings = [];
			this.beatTimer = 0;

			// Entities
			this.dissonanceSpikes = [];
			this.shards = [];
			this.particles = [];
			this.floatingTexts = [];

			// Visual Shake
			this.screenShake = 0;

			// Anti-Cheat Token
			this.sessionToken = '';

			// Keyboard Input Tracker
			this.keys = {};

			this.initDOM();
			this.initEvents();
			this.initToken();
			this.loop(0);
		}

		initDOM() {
			this.startOverlay = document.getElementById('rezonanse-start-overlay');
			this.gameoverOverlay = document.getElementById('rezonanse-gameover-overlay');
			this.pauseOverlay = document.getElementById('rezonanse-pause-overlay');
			this.hud = document.getElementById('rezonanse-hud');

			this.scoreValEl = document.getElementById('hud-score-val');
			this.comboValEl = document.getElementById('hud-combo-val');
			this.shieldsEl = document.getElementById('hud-shields');
			this.overdriveFillEl = document.getElementById('hud-overdrive-fill');
			this.beatIndicatorEl = document.getElementById('hud-beat-indicator');

			this.finalScoreEl = document.getElementById('final-score');
			this.finalComboEl = document.getElementById('final-combo');
			this.finalPerfectsEl = document.getElementById('final-perfects');
			this.newRecordEl = document.getElementById('rezonanse-new-record');
			this.rankBannerEl = document.getElementById('rezonanse-rank-banner');
			this.rankValEl = document.getElementById('rezonanse-rank-val');

			this.soundBtn = document.getElementById('rezonanse-sound-btn');
			this.pauseBtn = document.getElementById('rezonanse-pause-btn');
			this.mobilePulseBtn = document.getElementById('rezonanse-mobile-pulse-btn');
			this.mobileOverdriveBtn = document.getElementById('rezonanse-mobile-overdrive-btn');
		}

		initToken() {
			fetch('/rezonanse?action=init_token')
				.then(res => res.json())
				.then(data => {
					if (data && data.token) {
						this.sessionToken = data.token;
					}
				})
				.catch(err => console.warn('Could not init rezonanse token:', err));
		}

		initEvents() {
			// Start Button
			const startBtn = document.getElementById('rezonanse-start-btn');
			if (startBtn) {
				startBtn.addEventListener('click', () => this.startGame());
			}

			// Restart Button
			const restartBtn = document.getElementById('rezonanse-restart-btn');
			if (restartBtn) {
				restartBtn.addEventListener('click', () => this.restartGame());
			}

			// Resume Button
			const resumeBtn = document.getElementById('rezonanse-resume-btn');
			if (resumeBtn) {
				resumeBtn.addEventListener('click', () => this.togglePause());
			}

			// Sound Mute Toggle
			if (this.soundBtn) {
				this.soundBtn.addEventListener('click', () => {
					const muted = this.sound.toggleMute();
					const icon = document.getElementById('sound-icon');
					if (icon) icon.textContent = muted ? '🔇' : '🔊';
				});
			}

			// Pause Button
			if (this.pauseBtn) {
				this.pauseBtn.addEventListener('click', () => this.togglePause());
			}

			// Mouse Movement
			this.canvas.addEventListener('mousemove', (e) => {
				const rect = this.canvas.getBoundingClientRect();
				const scaleX = CANVAS_SIZE / rect.width;
				const scaleY = CANVAS_SIZE / rect.height;
				this.player.targetX = (e.clientX - rect.left) * scaleX;
				this.player.targetY = (e.clientY - rect.top) * scaleY;
			});

			// Canvas Click (Pulse)
			let lastClickTime = 0;
			this.canvas.addEventListener('click', (e) => {
				e.preventDefault();
				this.sound.init();

				if (this.state === 'START') {
					this.startGame();
				} else if (this.state === 'GAMEOVER') {
					this.restartGame();
				} else if (this.state === 'PLAYING') {
					const now = performance.now();
					// Double click detection for Overdrive
					if (now - lastClickTime < 280 && this.overdrive.charge >= 100) {
						this.triggerOverdrive();
					} else {
						this.triggerPulse();
					}
					lastClickTime = now;
				}
			});

			// Touch Navigation
			const handleTouch = (e) => {
				if (e.touches && e.touches[0]) {
					const rect = this.canvas.getBoundingClientRect();
					const scaleX = CANVAS_SIZE / rect.width;
					const scaleY = CANVAS_SIZE / rect.height;
					this.player.targetX = (e.touches[0].clientX - rect.left) * scaleX;
					this.player.targetY = (e.touches[0].clientY - rect.top) * scaleY;
				}
			};

			this.canvas.addEventListener('touchstart', (e) => {
				handleTouch(e);
				if (this.state === 'PLAYING') {
					this.triggerPulse();
				}
			}, { passive: true });

			this.canvas.addEventListener('touchmove', handleTouch, { passive: true });

			// Mobile Action Buttons
			if (this.mobilePulseBtn) {
				this.mobilePulseBtn.addEventListener('click', (e) => {
					e.preventDefault();
					this.triggerPulse();
				});
			}

			if (this.mobileOverdriveBtn) {
				this.mobileOverdriveBtn.addEventListener('click', (e) => {
					e.preventDefault();
					this.triggerOverdrive();
				});
			}

			// Keyboard controls
			window.addEventListener('keydown', (e) => {
				if (['Space', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.code)) {
					// Prevent page scroll when playing
					if (this.state === 'PLAYING') e.preventDefault();
				}

				this.keys[e.code] = true;

				if (e.code === 'Space') {
					this.sound.init();
					if (this.state === 'START') {
						this.startGame();
					} else if (this.state === 'GAMEOVER') {
						this.restartGame();
					} else if (this.state === 'PLAYING') {
						this.triggerPulse();
					}
				} else if (e.code === 'KeyP') {
					this.togglePause();
				} else if (e.code === 'KeyM') {
					if (this.soundBtn) this.soundBtn.click();
				} else if (e.code === 'ShiftLeft' || e.code === 'ShiftRight') {
					if (this.state === 'PLAYING') {
						this.triggerOverdrive();
					}
				}
			});

			window.addEventListener('keyup', (e) => {
				this.keys[e.code] = false;
			});

			// Sidebar Tab Switcher
			const tabToday = document.getElementById('tab-today');
			const tabAlltime = document.getElementById('tab-alltime');
			const contentToday = document.getElementById('content-today');
			const contentAlltime = document.getElementById('content-alltime');

			if (tabToday && tabAlltime && contentToday && contentAlltime) {
				tabToday.addEventListener('click', () => {
					tabToday.classList.add('active');
					tabAlltime.classList.remove('active');
					contentToday.classList.add('active');
					contentAlltime.classList.remove('active');
				});

				tabAlltime.addEventListener('click', () => {
					tabAlltime.classList.add('active');
					tabToday.classList.remove('active');
					contentAlltime.classList.add('active');
					contentToday.classList.remove('active');
				});
			}
		}

		startGame() {
			this.sound.init();
			this.sound.startClock();

			this.state = 'PLAYING';
			this.score = 0;
			this.combo = 1;
			this.maxCombo = 1;
			this.perfectHits = 0;
			this.gameTime = 0;

			this.player.x = CENTER_X;
			this.player.y = CENTER_Y + 70;
			this.player.targetX = CENTER_X;
			this.player.targetY = CENTER_Y + 70;
			this.player.shields = this.player.maxShields;
			this.player.invulnerableTimer = 0;

			this.overdrive.charge = 0;
			this.overdrive.active = false;
			this.overdrive.timer = 0;

			this.beatRings = [];
			this.dissonanceSpikes = [];
			this.shards = [];
			this.particles = [];
			this.floatingTexts = [];
			this.beatTimer = 0;

			this.startOverlay.style.display = 'none';
			this.gameoverOverlay.style.display = 'none';
			this.pauseOverlay.style.display = 'none';
			this.hud.style.display = 'flex';
			if (this.pauseBtn) this.pauseBtn.style.display = 'inline-block';

			this.updateHUD();
		}

		restartGame() {
			this.initToken();
			this.startGame();
		}

		togglePause() {
			if (this.state === 'PLAYING') {
				this.state = 'PAUSED';
				this.sound.stopClock();
				this.pauseOverlay.style.display = 'flex';
			} else if (this.state === 'PAUSED') {
				this.state = 'PLAYING';
				this.sound.startClock();
				this.pauseOverlay.style.display = 'none';
			}
		}

		triggerPulse() {
			if (this.state !== 'PLAYING') return;

			// Create outward shockwave at player position
			const shockwave = {
				x: this.player.x,
				y: this.player.y,
				radius: 10,
				maxRadius: 75,
				opacity: 1
			};
			this.particles.push(shockwave);

			// Check intersection with any incoming beat ring
			const playerDist = Math.hypot(this.player.x - CENTER_X, this.player.y - CENTER_Y);
			let hitRing = null;
			let bestDiff = 999;

			for (let i = 0; i < this.beatRings.length; i++) {
				const ring = this.beatRings[i];
				const diff = Math.abs(ring.radius - playerDist);
				if (diff < 32 && diff < bestDiff) {
					bestDiff = diff;
					hitRing = ring;
				}
			}

			if (hitRing) {
				const isPerfect = bestDiff < 14;
				const pts = (isPerfect ? 300 : 120) * this.combo;
				this.score += pts;

				this.combo = Math.min(32, this.combo + 1);
				this.maxCombo = Math.max(this.maxCombo, this.combo);
				if (isPerfect) this.perfectHits++;

				// Sound feedback
				this.sound.comboLevel = this.combo;
				this.sound.playResonanceHit(isPerfect, this.combo);

				// Overdrive energy charge
				this.overdrive.charge = Math.min(100, this.overdrive.charge + (isPerfect ? 10 : 5));

				// Visual feedback text
				this.addFloatingText(
					isPerfect ? `PERFEKTI! +${pts}` : `LABI! +${pts}`,
					this.player.x,
					this.player.y - 20,
					isPerfect ? '#38bdf8' : '#34d399'
				);

				// Vaporize dissonance spikes near player
				this.vaporizeNearbySpikes(this.player.x, this.player.y, 85);

				// Spawn visual sparkles
				this.spawnSparks(this.player.x, this.player.y, isPerfect ? 18 : 10, isPerfect ? '#38bdf8' : '#a7f3d0');

				// Consume ring hit state
				hitRing.hit = true;
			} else {
				// Off-beat miss: slight combo penalty
				if (this.combo > 1) {
					this.combo = Math.max(1, this.combo - 1);
					this.sound.comboLevel = this.combo;
				}
			}

			this.updateHUD();
		}

		triggerOverdrive() {
			if (this.overdrive.charge < 100 || this.overdrive.active) return;

			this.overdrive.active = true;
			this.overdrive.timer = this.overdrive.duration;
			this.overdrive.charge = 0;
			this.sound.isOverdrive = true;
			this.sound.playOverdriveStart();

			this.addFloatingText('🔥 OVERDRIVE! 🔥', CENTER_X, CENTER_Y - 50, '#f59e0b', 24);
			this.screenShake = 12;

			// Transform all existing dissonance spikes into golden shards
			for (const spike of this.dissonanceSpikes) {
				this.shards.push({
					x: spike.x,
					y: spike.y,
					vx: (Math.random() - 0.5) * 2,
					vy: (Math.random() - 0.5) * 2,
					radius: 8,
					value: 250
				});
			}
			this.dissonanceSpikes = [];
		}

		vaporizeNearbySpikes(x, y, radius) {
			for (let i = this.dissonanceSpikes.length - 1; i >= 0; i--) {
				const spike = this.dissonanceSpikes[i];
				const dist = Math.hypot(spike.x - x, spike.y - y);
				if (dist <= radius) {
					// Destruct spike
					this.score += 80 * this.combo;
					this.spawnSparks(spike.x, spike.y, 12, '#f43f5e');

					// Chance to drop shard
					if (Math.random() < 0.45) {
						this.shards.push({
							x: spike.x,
							y: spike.y,
							vx: (Math.random() - 0.5) * 2,
							vy: (Math.random() - 0.5) * 2,
							radius: 6,
							value: 150
						});
					}

					this.dissonanceSpikes.splice(i, 1);
				}
			}
		}

		spawnSparks(x, y, count, color) {
			for (let i = 0; i < count; i++) {
				const angle = Math.random() * Math.PI * 2;
				const speed = 1.5 + Math.random() * 4.5;
				this.particles.push({
					x: x,
					y: y,
					vx: Math.cos(angle) * speed,
					vy: Math.sin(angle) * speed,
					radius: 2 + Math.random() * 2.5,
					color: color,
					alpha: 1,
					decay: 0.03 + Math.random() * 0.03
				});
			}
		}

		addFloatingText(text, x, y, color, size = 15) {
			this.floatingTexts.push({
				text: text,
				x: x,
				y: y,
				color: color,
				size: size,
				alpha: 1,
				vy: -1.2
			});
		}

		handleDamage() {
			if (this.player.invulnerableTimer > 0 || this.overdrive.active) return;

			this.player.shields--;
			this.combo = 1;
			this.sound.comboLevel = 1;
			this.player.invulnerableTimer = 1.8;
			this.screenShake = 16;
			this.sound.playDamage();

			this.spawnSparks(this.player.x, this.player.y, 25, '#ef4444');
			this.addFloatingText('TRIECIENS!', this.player.x, this.player.y - 30, '#ef4444', 18);

			this.updateHUD();

			if (this.player.shields <= 0) {
				this.gameOver();
			}
		}

		gameOver() {
			this.state = 'GAMEOVER';
			this.sound.stopClock();
			this.sound.playGameOver();

			this.finalScoreEl.textContent = this.score.toLocaleString('lv-LV');
			this.finalComboEl.textContent = `${this.maxCombo}x`;
			this.finalPerfectsEl.textContent = this.perfectHits;

			this.gameoverOverlay.style.display = 'flex';
			if (this.pauseBtn) this.pauseBtn.style.display = 'none';

			this.submitScore();
		}

		submitScore() {
			if (!this.sessionToken || this.score <= 0) return;

			const params = new URLSearchParams();
			params.append('token', this.sessionToken);
			params.append('score', this.score);
			params.append('combo', this.maxCombo);
			params.append('duration', Math.floor(this.gameTime));
			params.append('perfects', this.perfectHits);

			fetch('/rezonanse?action=push', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: params.toString()
			})
				.then(res => res.json())
				.then(data => {
					if (data.success) {
						if (data.isNewRecord && this.newRecordEl) {
							this.newRecordEl.style.display = 'block';
						}
						if (data.rank && this.rankBannerEl && this.rankValEl) {
							this.rankValEl.textContent = `#${data.rank}`;
							this.rankBannerEl.style.display = 'block';
						}
						// Update bottom best score display if higher
						const bottomBestEl = document.getElementById('bottom-best-score');
						if (bottomBestEl && data.highScore) {
							bottomBestEl.textContent = `${data.highScore.toLocaleString('lv-LV')} pt`;
						}
					}
				})
				.catch(err => console.warn('Score push error:', err));
		}

		updateHUD() {
			if (this.scoreValEl) this.scoreValEl.textContent = this.score.toLocaleString('lv-LV');
			if (this.comboValEl) this.comboValEl.textContent = `${this.combo}x`;

			// Update Shield dots
			if (this.shieldsEl) {
				const dots = this.shieldsEl.querySelectorAll('.shield-dot');
				dots.forEach((dot, index) => {
					if (index < this.player.shields) {
						dot.classList.add('active');
					} else {
						dot.classList.remove('active');
					}
				});
			}

			// Update Overdrive fill
			if (this.overdriveFillEl) {
				const pct = this.overdrive.active
					? (this.overdrive.timer / this.overdrive.duration) * 100
					: this.overdrive.charge;
				this.overdriveFillEl.style.width = `${pct}%`;
			}

			// Update Mobile button
			if (this.mobileOverdriveBtn) {
				this.mobileOverdriveBtn.disabled = this.overdrive.charge < 100 && !this.overdrive.active;
			}
		}

		// =========================================================================
		// 4. UPDATE LOOP
		// =========================================================================
		update(dt) {
			this.gameTime += dt;

			// Handle Keyboard Movement
			if (this.keys['ArrowLeft'] || this.keys['KeyA']) this.player.targetX -= this.player.speed * 60 * dt;
			if (this.keys['ArrowRight'] || this.keys['KeyD']) this.player.targetX += this.player.speed * 60 * dt;
			if (this.keys['ArrowUp'] || this.keys['KeyW']) this.player.targetY -= this.player.speed * 60 * dt;
			if (this.keys['ArrowDown'] || this.keys['KeyS']) this.player.targetY += this.player.speed * 60 * dt;

			// Smooth Follow
			this.player.x += (this.player.targetX - this.player.x) * 0.22;
			this.player.y += (this.player.targetY - this.player.y) * 0.22;

			// Clamp Player inside circular Arena
			const distFromCenter = Math.hypot(this.player.x - CENTER_X, this.player.y - CENTER_Y);
			const maxRadius = ARENA_RADIUS - this.player.radius - 2;
			if (distFromCenter > maxRadius) {
				const angle = Math.atan2(this.player.y - CENTER_Y, this.player.x - CENTER_X);
				this.player.x = CENTER_X + Math.cos(angle) * maxRadius;
				this.player.y = CENTER_Y + Math.sin(angle) * maxRadius;
			}

			// Invulnerability countdown
			if (this.player.invulnerableTimer > 0) {
				this.player.invulnerableTimer -= dt;
			}

			// Overdrive countdown
			if (this.overdrive.active) {
				this.overdrive.timer -= dt;
				if (this.overdrive.timer <= 0) {
					this.overdrive.active = false;
					this.sound.isOverdrive = false;
				}
			}

			// Beat Ring Spawn & Movement (128 BPM)
			this.beatTimer += dt;
			if (this.beatTimer >= BEAT_DURATION) {
				this.beatTimer -= BEAT_DURATION;

				// Trigger beat indicator pulse
				if (this.beatIndicatorEl) {
					this.beatIndicatorEl.classList.add('pulse');
					setTimeout(() => this.beatIndicatorEl.classList.remove('pulse'), 90);
				}

				// Spawn inward converging beat ring
				this.beatRings.push({
					radius: ARENA_RADIUS,
					speed: ARENA_RADIUS / (BEAT_DURATION * 2), // reaches center in 2 beats
					hit: false,
					alpha: 1
				});

				// Periodically spawn Dissonance Spikes
				const spikeChance = Math.min(0.85, 0.4 + this.gameTime * 0.005);
				if (Math.random() < spikeChance && !this.overdrive.active) {
					const angle = Math.random() * Math.PI * 2;
					const speed = 75 + Math.random() * 50 + Math.min(60, this.gameTime * 0.8);
					this.dissonanceSpikes.push({
						x: CENTER_X + Math.cos(angle) * ARENA_RADIUS,
						y: CENTER_Y + Math.sin(angle) * ARENA_RADIUS,
						vx: -Math.cos(angle) * speed,
						vy: -Math.sin(angle) * speed,
						angle: angle,
						radius: 8,
						rot: Math.random() * Math.PI
					});
				}

				// Periodically spawn Resonance Shards
				if (Math.random() < 0.35) {
					const angle = Math.random() * Math.PI * 2;
					const r = 40 + Math.random() * (ARENA_RADIUS - 80);
					this.shards.push({
						x: CENTER_X + Math.cos(angle) * r,
						y: CENTER_Y + Math.sin(angle) * r,
						vx: (Math.random() - 0.5) * 15,
						vy: (Math.random() - 0.5) * 15,
						radius: 6,
						value: 120
					});
				}
			}

			// Update Beat Rings
			for (let i = this.beatRings.length - 1; i >= 0; i--) {
				const ring = this.beatRings[i];
				ring.radius -= ring.speed * dt;
				if (ring.radius <= 0) {
					this.beatRings.splice(i, 1);
				}
			}

			// Update Spikes & Collision
			for (let i = this.dissonanceSpikes.length - 1; i >= 0; i--) {
				const spike = this.dissonanceSpikes[i];
				spike.x += spike.vx * dt;
				spike.y += spike.vy * dt;
				spike.rot += 3 * dt;

				// Check collision with player
				const dist = Math.hypot(spike.x - this.player.x, spike.y - this.player.y);
				if (dist < spike.radius + this.player.radius) {
					if (this.overdrive.active) {
						// In Overdrive: instant destroy!
						this.score += 150 * this.combo;
						this.spawnSparks(spike.x, spike.y, 10, '#f59e0b');
						this.dissonanceSpikes.splice(i, 1);
						continue;
					} else {
						this.handleDamage();
						this.dissonanceSpikes.splice(i, 1);
						continue;
					}
				}

				// Check out of arena bounds
				const centerDist = Math.hypot(spike.x - CENTER_X, spike.y - CENTER_Y);
				if (centerDist > ARENA_RADIUS + 20) {
					this.dissonanceSpikes.splice(i, 1);
				}
			}

			// Update Shards & Gravitate towards player
			for (let i = this.shards.length - 1; i >= 0; i--) {
				const shard = this.shards[i];
				const dist = Math.hypot(this.player.x - shard.x, this.player.y - shard.y);

				// Magnetic pull if close
				if (dist < 110) {
					const angle = Math.atan2(this.player.y - shard.y, this.player.x - shard.x);
					shard.vx += Math.cos(angle) * 450 * dt;
					shard.vy += Math.sin(angle) * 450 * dt;
				}

				shard.x += shard.vx * dt;
				shard.y += shard.vy * dt;

				// Collect
				if (dist < shard.radius + this.player.radius) {
					this.score += shard.value * this.combo;
					this.overdrive.charge = Math.min(100, this.overdrive.charge + 8);
					this.spawnSparks(shard.x, shard.y, 8, '#38bdf8');
					this.addFloatingText(`+${shard.value * this.combo}`, shard.x, shard.y - 12, '#38bdf8', 12);
					this.shards.splice(i, 1);
				}
			}

			// Update Particles
			for (let i = this.particles.length - 1; i >= 0; i--) {
				const p = this.particles[i];
				if (p.maxRadius) {
					// Shockwave ring
					p.radius += (p.maxRadius - p.radius) * 12 * dt;
					p.opacity -= 2.2 * dt;
					if (p.opacity <= 0) {
						this.particles.splice(i, 1);
					}
				} else {
					// Sparkle
					p.x += p.vx;
					p.y += p.vy;
					p.alpha -= p.decay;
					if (p.alpha <= 0) {
						this.particles.splice(i, 1);
					}
				}
			}

			// Update Floating Texts
			for (let i = this.floatingTexts.length - 1; i >= 0; i--) {
				const ft = this.floatingTexts[i];
				ft.y += ft.vy;
				ft.alpha -= 1.3 * dt;
				if (ft.alpha <= 0) {
					this.floatingTexts.splice(i, 1);
				}
			}

			// Decay Screen Shake
			if (this.screenShake > 0) {
				this.screenShake = Math.max(0, this.screenShake - 28 * dt);
			}

			this.updateHUD();
		}

		// =========================================================================
		// 5. RENDER LOOP
		// =========================================================================
		render() {
			this.ctx.save();

			// Apply Camera Shake
			if (this.screenShake > 0) {
				const dx = (Math.random() - 0.5) * this.screenShake;
				const dy = (Math.random() - 0.5) * this.screenShake;
				this.ctx.translate(dx, dy);
			}

			// Clear background with lighter cyber-slate gradient
			const bgGrad = this.ctx.createRadialGradient(CENTER_X, CENTER_Y, 20, CENTER_X, CENTER_Y, ARENA_RADIUS * 1.25);
			bgGrad.addColorStop(0, '#1c263c');
			bgGrad.addColorStop(0.65, '#131b2e');
			bgGrad.addColorStop(1, '#0e1424');
			this.ctx.fillStyle = bgGrad;
			this.ctx.fillRect(0, 0, CANVAS_SIZE, CANVAS_SIZE);

			// Draw Arena Boundary & Radial Equalizer
			this.renderArena();

			// Draw Beat Ring Waves
			this.renderBeatRings();

			// Draw Dissonance Spikes
			this.renderSpikes();

			// Draw Shards
			this.renderShards();

			// Draw Particles & Shockwaves
			this.renderParticles();

			// Draw Player Orb
			this.renderPlayer();

			// Draw Floating Texts
			this.renderFloatingTexts();

			this.ctx.restore();
		}

		renderArena() {
			// Subtle radial grid lines
			this.ctx.strokeStyle = 'rgba(148, 163, 184, 0.08)';
			this.ctx.lineWidth = 1;
			for (let a = 0; a < Math.PI * 2; a += Math.PI / 4) {
				this.ctx.beginPath();
				this.ctx.moveTo(CENTER_X, CENTER_Y);
				this.ctx.lineTo(CENTER_X + Math.cos(a) * ARENA_RADIUS, CENTER_Y + Math.sin(a) * ARENA_RADIUS);
				this.ctx.stroke();
			}

			// Center pulse glow
			const glowRadius = 36 + Math.sin(this.gameTime * 4) * 8;
			const grad = this.ctx.createRadialGradient(CENTER_X, CENTER_Y, 2, CENTER_X, CENTER_Y, glowRadius);
			grad.addColorStop(0, 'rgba(6, 182, 212, 0.55)');
			grad.addColorStop(0.6, 'rgba(6, 182, 212, 0.18)');
			grad.addColorStop(1, 'rgba(6, 182, 212, 0)');
			this.ctx.fillStyle = grad;
			this.ctx.beginPath();
			this.ctx.arc(CENTER_X, CENTER_Y, glowRadius, 0, Math.PI * 2);
			this.ctx.fill();

			// Arena boundary circle
			this.ctx.strokeStyle = this.overdrive.active ? '#f59e0b' : 'rgba(99, 102, 241, 0.5)';
			this.ctx.lineWidth = 2.5;
			this.ctx.beginPath();
			this.ctx.arc(CENTER_X, CENTER_Y, ARENA_RADIUS, 0, Math.PI * 2);
			this.ctx.stroke();

			// Concentric reference rings
			this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.09)';
			this.ctx.lineWidth = 1;
			[0.35, 0.65].forEach(scale => {
				this.ctx.beginPath();
				this.ctx.arc(CENTER_X, CENTER_Y, ARENA_RADIUS * scale, 0, Math.PI * 2);
				this.ctx.stroke();
			});
		}

		renderBeatRings() {
			for (const ring of this.beatRings) {
				const alpha = Math.max(0.15, ring.radius / ARENA_RADIUS);
				this.ctx.strokeStyle = ring.hit
					? `rgba(52, 211, 153, ${alpha * 0.4})`
					: `rgba(6, 182, 212, ${alpha * 0.85})`;
				this.ctx.lineWidth = ring.hit ? 1.5 : 2.5;

				this.ctx.beginPath();
				this.ctx.arc(CENTER_X, CENTER_Y, ring.radius, 0, Math.PI * 2);
				this.ctx.stroke();
			}
		}

		renderSpikes() {
			for (const spike of this.dissonanceSpikes) {
				this.ctx.save();
				this.ctx.translate(spike.x, spike.y);
				this.ctx.rotate(spike.rot);

				// Red glowing triangle
				this.ctx.fillStyle = '#ef4444';
				this.ctx.shadowColor = '#ef4444';
				this.ctx.shadowBlur = 8;

				this.ctx.beginPath();
				this.ctx.moveTo(0, -spike.radius);
				this.ctx.lineTo(spike.radius, spike.radius);
				this.ctx.lineTo(-spike.radius, spike.radius);
				this.ctx.closePath();
				this.ctx.fill();

				this.ctx.restore();
			}
		}

		renderShards() {
			for (const shard of this.shards) {
				this.ctx.save();
				this.ctx.translate(shard.x, shard.y);
				this.ctx.rotate(this.gameTime * 2);

				this.ctx.fillStyle = '#38bdf8';
				this.ctx.shadowColor = '#38bdf8';
				this.ctx.shadowBlur = 10;

				// Diamond shape
				this.ctx.beginPath();
				this.ctx.moveTo(0, -shard.radius);
				this.ctx.lineTo(shard.radius, 0);
				this.ctx.lineTo(0, shard.radius);
				this.ctx.lineTo(-shard.radius, 0);
				this.ctx.closePath();
				this.ctx.fill();

				this.ctx.restore();
			}
		}

		renderPlayer() {
			// Blink if invulnerable
			if (this.player.invulnerableTimer > 0 && Math.floor(this.gameTime * 14) % 2 === 0) {
				return;
			}

			const isOverdrive = this.overdrive.active;
			const glowColor = isOverdrive ? '#f59e0b' : '#06b6d4';
			const coreColor = isOverdrive ? '#fbbf24' : '#ffffff';

			// Outer Pulse Aura
			this.ctx.save();
			this.ctx.shadowColor = glowColor;
			this.ctx.shadowBlur = isOverdrive ? 24 : 14;

			this.ctx.fillStyle = glowColor;
			this.ctx.beginPath();
			this.ctx.arc(this.player.x, this.player.y, this.player.radius + (isOverdrive ? 4 : 0), 0, Math.PI * 2);
			this.ctx.fill();

			// Inner Bright Core
			this.ctx.fillStyle = coreColor;
			this.ctx.beginPath();
			this.ctx.arc(this.player.x, this.player.y, this.player.radius * 0.55, 0, Math.PI * 2);
			this.ctx.fill();

			this.ctx.restore();
		}

		renderParticles() {
			for (const p of this.particles) {
				if (p.maxRadius) {
					// Expanding shockwave ring
					this.ctx.strokeStyle = `rgba(6, 182, 212, ${p.opacity})`;
					this.ctx.lineWidth = 3;
					this.ctx.beginPath();
					this.ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
					this.ctx.stroke();
				} else {
					// Sparkle dot
					this.ctx.fillStyle = p.color;
					this.ctx.globalAlpha = p.alpha;
					this.ctx.beginPath();
					this.ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
					this.ctx.fill();
					this.ctx.globalAlpha = 1;
				}
			}
		}

		renderFloatingTexts() {
			for (const ft of this.floatingTexts) {
				this.ctx.save();
				this.ctx.fillStyle = ft.color;
				this.ctx.globalAlpha = ft.alpha;
				this.ctx.font = `bold ${ft.size}px -apple-system, sans-serif`;
				this.ctx.textAlign = 'center';
				this.ctx.shadowColor = ft.color;
				this.ctx.shadowBlur = 8;
				this.ctx.fillText(ft.text, ft.x, ft.y);
				this.ctx.restore();
			}
		}

		loop(timestamp) {
			requestAnimationFrame((t) => this.loop(t));

			if (!this.lastTime) this.lastTime = timestamp;
			const dt = Math.min(0.1, (timestamp - this.lastTime) / 1000);
			this.lastTime = timestamp;

			if (this.state === 'PLAYING') {
				this.update(dt);
			}

			this.render();
		}
	}

	// Bootstrap on DOM Ready
	document.addEventListener('DOMContentLoaded', () => {
		window.rezonanseGame = new RezonanseGame();
	});
})();
