/**
 * HexGL 3D Futuristic Racing Engine for EXS.LV
 * Built with Three.js & Web Audio API
 */

(function () {
	'use strict';

	// Game state variables
	let scene, camera, renderer;
	let ship, trackMesh, boostPads = [], checkpoints = [];
	let starField, gridHelper;
	let animationFrameId = null;

	let gameState = 'START'; // START, RACING, FINISHED, GAMEOVER
	let sessionToken = '';

	// Physics & Craft parameters
	let shipPos = { x: 0, y: 0.5, z: 0 };
	let speed = 0;
	let maxSpeed = 1.4; // ~180 km/h base
	let boostSpeed = 2.4; // ~300 km/h boosted
	let isBoosting = false;
	let boostTimer = 0;
	let accel = 0.015;
	let friction = 0.985;
	let angle = 0;
	let tilt = 0;
	let turnSpeed = 0.035;
	let shield = 100;

	// Track layout parameters (closed loop curve)
	let trackCurve;
	let trackLength = 0;

	// Race metrics
	let currentLap = 1;
	let totalLaps = 3;
	let lastCheckpointPassed = 0;
	let raceStartTime = 0;
	let currentLapTime = 0;
	let totalRaceTime = 0;
	let bestLapTime = Infinity;
	let boostsHit = 0;

	// Controls state
	let keys = { up: false, down: false, left: false, right: false, boost: false };

	// Sound Synth (Web Audio API)
	let audioCtx = null;
	let engineOsc = null;
	let engineGain = null;

	function initAudio() {
		try {
			if (!audioCtx) {
				const AudioContext = window.AudioContext || window.webkitAudioContext;
				audioCtx = new AudioContext();

				engineOsc = audioCtx.createOscillator();
				engineGain = audioCtx.createGain();

				engineOsc.type = 'sawtooth';
				engineOsc.frequency.setValueAtTime(60, audioCtx.currentTime);
				engineGain.gain.setValueAtTime(0.02, audioCtx.currentTime);

				engineOsc.connect(engineGain);
				engineGain.connect(audioCtx.destination);
				engineOsc.start();
			} else if (audioCtx.state === 'suspended') {
				audioCtx.resume();
			}
		} catch (e) {
			console.log('Audio init failed:', e);
		}
	}

	function updateAudio() {
		if (engineOsc && engineGain && audioCtx) {
			if (gameState === 'RACING') {
				const targetFreq = 60 + (speed / maxSpeed) * 180 + (isBoosting ? 80 : 0);
				engineOsc.frequency.setTargetAtTime(targetFreq, audioCtx.currentTime, 0.05);
				engineGain.gain.setTargetAtTime(isBoosting ? 0.05 : 0.03, audioCtx.currentTime, 0.05);
			} else {
				engineGain.gain.setTargetAtTime(0.001, audioCtx.currentTime, 0.1);
			}
		}
	}

	function playSFX(type) {
		if (!audioCtx) return;
		try {
			const now = audioCtx.currentTime;
			const osc = audioCtx.createOscillator();
			const gain = audioCtx.createGain();
			osc.connect(gain);
			gain.connect(audioCtx.destination);

			if (type === 'boost') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(300, now);
				osc.frequency.exponentialRampToValueAtTime(900, now + 0.3);
				gain.gain.setValueAtTime(0.15, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.3);
				osc.start(now);
				osc.stop(now + 0.3);
			} else if (type === 'crash') {
				osc.type = 'square';
				osc.frequency.setValueAtTime(120, now);
				osc.frequency.linearRampToValueAtTime(40, now + 0.2);
				gain.gain.setValueAtTime(0.2, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.2);
				osc.start(now);
				osc.stop(now + 0.2);
			} else if (type === 'lap') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(523.25, now); // C5
				osc.frequency.setValueAtTime(659.25, now + 0.15); // E5
				gain.gain.setValueAtTime(0.15, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.35);
				osc.start(now);
				osc.stop(now + 0.35);
			}
		} catch (e) {}
	}

	// 3D Scene Initialization
	function init3D() {
		const container = document.getElementById('hexgl-canvas');
		if (!container) return;

		container.innerHTML = '';
		const width = container.clientWidth || 800;
		const height = container.clientHeight || 450;

		// Renderer
		renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
		renderer.setSize(width, height);
		renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
		renderer.shadowMap.enabled = true;
		container.appendChild(renderer.domElement);

		// Scene & Camera
		scene = new THREE.Scene();
		scene.background = new THREE.Color(0x060814);
		scene.fog = new THREE.FogExp2(0x060814, 0.008);

		camera = new THREE.PerspectiveCamera(65, width / height, 0.1, 1000);

		// Lighting
		const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
		scene.add(ambientLight);

		const dirLight = new THREE.DirectionalLight(0x00f3ff, 0.8);
		dirLight.position.set(50, 100, 50);
		scene.add(dirLight);

		const dirLight2 = new THREE.DirectionalLight(0xff007f, 0.5);
		dirLight2.position.set(-50, 50, -50);
		scene.add(dirLight2);

		// Track generation using CatmullRomCurve3
		const trackPoints = [
			new THREE.Vector3(0, 0, 0),
			new THREE.Vector3(0, 0, -150),
			new THREE.Vector3(100, 0, -280),
			new THREE.Vector3(220, 10, -220),
			new THREE.Vector3(250, 0, -80),
			new THREE.Vector3(180, 0, 80),
			new THREE.Vector3(120, -5, 200),
			new THREE.Vector3(0, 0, 250),
			new THREE.Vector3(-120, 0, 200),
			new THREE.Vector3(-180, 5, 80),
			new THREE.Vector3(-150, 0, -80)
		];
		trackCurve = new THREE.CatmullRomCurve3(trackPoints, true);
		trackLength = trackCurve.getLength();

		// Track Ribbon Mesh
		const tubeGeo = new THREE.TubeGeometry(trackCurve, 200, 12, 12, true);
		const tubeMat = new THREE.MeshPhongMaterial({
			color: 0x12182b,
			wireframe: false,
			shininess: 80,
			specular: 0x00f3ff
		});
		trackMesh = new THREE.Mesh(tubeGeo, tubeMat);
		scene.add(trackMesh);

		// Track Wireframe Glow Overlay
		const wireGeo = new THREE.TubeGeometry(trackCurve, 200, 12.2, 8, true);
		const wireMat = new THREE.MeshBasicMaterial({
			color: 0x00f3ff,
			wireframe: true,
			transparent: true,
			opacity: 0.18
		});
		const wireMesh = new THREE.Mesh(wireGeo, wireMat);
		scene.add(wireMesh);

		// Boost Pads & Checkpoints along curve
		createTrackFeatures();

		// Starfield / Particles
		const starsGeo = new THREE.BufferGeometry();
		const starsCount = 800;
		const starPositions = new Float32Array(starsCount * 3);
		for (let i = 0; i < starsCount * 3; i += 3) {
			starPositions[i] = (Math.random() - 0.5) * 800;
			starPositions[i + 1] = Math.random() * 400 + 20;
			starPositions[i + 2] = (Math.random() - 0.5) * 800;
		}
		starsGeo.setAttribute('position', new THREE.BufferAttribute(starPositions, 3));
		const starsMat = new THREE.PointsMaterial({ color: 0x00f3ff, size: 1.5, transparent: true, opacity: 0.7 });
		starField = new THREE.Points(starsGeo, starsMat);
		scene.add(starField);

		// Player Craft Mesh
		createPlayerShip();

		// Resize Listener
		window.addEventListener('resize', onWindowResize);
	}

	function createPlayerShip() {
		ship = new THREE.Group();

		// Main fuselage
		const bodyGeo = new THREE.ConeGeometry(1.2, 4.5, 5);
		bodyGeo.rotateX(Math.PI / 2);
		const bodyMat = new THREE.MeshPhongMaterial({
			color: 0x00f3ff,
			emissive: 0x003344,
			shininess: 100
		});
		const body = new THREE.Mesh(bodyGeo, bodyMat);
		ship.add(body);

		// Cockpit canopy
		const canopyGeo = new THREE.SphereGeometry(0.7, 8, 8);
		canopyGeo.scale(0.8, 0.5, 1.4);
		const canopyMat = new THREE.MeshPhongMaterial({ color: 0xff007f, emissive: 0x550022, shininess: 120 });
		const canopy = new THREE.Mesh(canopyGeo, canopyMat);
		canopy.position.set(0, 0.4, 0.2);
		ship.add(canopy);

		// Wings
		const wingGeo = new THREE.BoxGeometry(4.2, 0.1, 1.2);
		const wingMat = new THREE.MeshPhongMaterial({ color: 0x1e293b, shininess: 60 });
		const wings = new THREE.Mesh(wingGeo, wingMat);
		wings.position.set(0, 0, 0.5);
		ship.add(wings);

		// Thruster Glow
		const engineGeo = new THREE.CylinderGeometry(0.4, 0.1, 0.8, 8);
		engineGeo.rotateX(Math.PI / 2);
		const engineMat = new THREE.MeshBasicMaterial({ color: 0x00ffff });
		const engine = new THREE.Mesh(engineGeo, engineMat);
		engine.position.set(0, 0, 2.2);
		ship.add(engine);

		scene.add(ship);
	}

	function createTrackFeatures() {
		boostPads = [];
		checkpoints = [];

		// Place 4 Boost Pads along the track curve
		const boostDistances = [0.15, 0.4, 0.65, 0.85];
		boostDistances.forEach((t) => {
			const pt = trackCurve.getPointAt(t);
			const tangent = trackCurve.getTangentAt(t);

			const padGeo = new THREE.BoxGeometry(10, 0.3, 4);
			const padMat = new THREE.MeshBasicMaterial({ color: 0x00ffff });
			const pad = new THREE.Mesh(padGeo, padMat);
			pad.position.copy(pt);
			pad.lookAt(pt.clone().add(tangent));
			scene.add(pad);
			boostPads.push({ mesh: pad, t: t });
		});

		// Place Checkpoints (Finish = t 0.0 / 1.0, CP1 = 0.33, CP2 = 0.66)
		const cpTs = [0.0, 0.33, 0.66];
		cpTs.forEach((t, index) => {
			const pt = trackCurve.getPointAt(t);
			const tangent = trackCurve.getTangentAt(t);

			const archGeo = new THREE.TorusGeometry(12, 0.4, 8, 16, Math.PI);
			const archMat = new THREE.MeshBasicMaterial({ color: index === 0 ? 0xff007f : 0x00f3ff });
			const arch = new THREE.Mesh(archGeo, archMat);
			arch.position.copy(pt);
			arch.lookAt(pt.clone().add(tangent));
			scene.add(arch);

			checkpoints.push({ index: index, t: t, pt: pt });
		});
	}

	function onWindowResize() {
		const container = document.getElementById('hexgl-canvas');
		if (!container || !renderer || !camera) return;
		const width = container.clientWidth;
		const height = container.clientHeight;
		camera.aspect = width / height;
		camera.updateProjectionMatrix();
		renderer.setSize(width, height);
	}

	// Game Control Handlers
	function setupInput() {
		window.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowUp' || e.key === 'w' || e.key === 'W') keys.up = true;
			if (e.key === 'ArrowDown' || e.key === 's' || e.key === 'S') keys.down = true;
			if (e.key === 'ArrowLeft' || e.key === 'a' || e.key === 'A') keys.left = true;
			if (e.key === 'ArrowRight' || e.key === 'd' || e.key === 'D') keys.right = true;
			if (e.key === ' ' || e.key === 'Shift') keys.boost = true;
		});

		window.addEventListener('keyup', (e) => {
			if (e.key === 'ArrowUp' || e.key === 'w' || e.key === 'W') keys.up = false;
			if (e.key === 'ArrowDown' || e.key === 's' || e.key === 'S') keys.down = false;
			if (e.key === 'ArrowLeft' || e.key === 'a' || e.key === 'A') keys.left = false;
			if (e.key === 'ArrowRight' || e.key === 'd' || e.key === 'D') keys.right = false;
			if (e.key === ' ' || e.key === 'Shift') keys.boost = false;
		});

		// Touch Controls
		const setupTouchBtn = (id, keyName) => {
			const btn = document.getElementById(id);
			if (!btn) return;
			btn.addEventListener('touchstart', (e) => { e.preventDefault(); keys[keyName] = true; });
			btn.addEventListener('touchend', (e) => { e.preventDefault(); keys[keyName] = false; });
			btn.addEventListener('mousedown', () => { keys[keyName] = true; });
			btn.addEventListener('mouseup', () => { keys[keyName] = false; });
		};

		setupTouchBtn('hexgl-touch-left', 'left');
		setupTouchBtn('hexgl-touch-right', 'right');
		setupTouchBtn('hexgl-touch-accel', 'up');
		setupTouchBtn('hexgl-touch-boost', 'boost');
	}

	// Main Game Update Loop
	let progressT = 0; // 0.0 to 1.0 along track curve

	function updateGame() {
		if (gameState !== 'RACING') return;

		initAudio();

		// Time calculations
		const now = performance.now();
		currentLapTime = now - raceStartTime;

		// Acceleration & Steering
		if (keys.up) {
			speed += accel;
		} else if (keys.down) {
			speed -= accel * 1.5;
		} else {
			speed *= friction;
		}

		// Boost active
		if (isBoosting) {
			boostTimer -= 0.016;
			speed = boostSpeed;
			if (boostTimer <= 0) {
				isBoosting = false;
				document.getElementById('hexgl-boost-banner')?.classList.remove('active');
			}
		} else {
			speed = Math.max(0, Math.min(maxSpeed, speed));
		}

		// Steering & Tilt
		if (keys.left) {
			angle += turnSpeed * (0.4 + (speed / maxSpeed) * 0.6);
			tilt = Math.min(0.4, tilt + 0.04);
		} else if (keys.right) {
			angle -= turnSpeed * (0.4 + (speed / maxSpeed) * 0.6);
			tilt = Math.max(-0.4, tilt - 0.04);
		} else {
			tilt *= 0.85;
		}

		// Advance position along curve based on speed
		const deltaT = (speed * 0.45) / trackLength;
		progressT += deltaT;
		if (progressT >= 1.0) {
			progressT -= 1.0;
			checkLapFinish();
		}

		// Calculate 3D position on track curve
		const curvePoint = trackCurve.getPointAt(progressT);
		const curveTangent = trackCurve.getTangentAt(progressT);

		// Offset sideways based on steering angle
		const upVec = new THREE.Vector3(0, 1, 0);
		const sideVec = new THREE.Vector3().crossVectors(curveTangent, upVec).normalize();

		shipPos.x = curvePoint.x + sideVec.x * Math.sin(angle) * 8;
		shipPos.y = curvePoint.y + 1.2;
		shipPos.z = curvePoint.z + sideVec.z * Math.sin(angle) * 8;

		// Update Ship Mesh Position & Rotation
		ship.position.set(shipPos.x, shipPos.y, shipPos.z);
		ship.lookAt(shipPos.x + curveTangent.x, shipPos.y + curveTangent.y, shipPos.z + curveTangent.z);
		ship.rotation.z = tilt;

		// Track Collision Detection (Wall boundary check)
		const distFromCenter = Math.abs(Math.sin(angle) * 8);
		if (distFromCenter > 9.5) {
			speed *= 0.6;
			shield = Math.max(0, shield - 4);
			playSFX('crash');
			if (shield <= 0) {
				gameOver();
				return;
			}
		}

		// Boost Pad Collision Check
		boostPads.forEach((bp) => {
			if (Math.abs(progressT - bp.t) < 0.015) {
				if (!isBoosting) {
					isBoosting = true;
					boostTimer = 1.8;
					boostsHit++;
					playSFX('boost');
					document.getElementById('hexgl-boost-banner')?.classList.add('active');
				}
			}
		});

		// Checkpoint Check
		checkpoints.forEach((cp) => {
			if (cp.index > 0 && Math.abs(progressT - cp.t) < 0.02) {
				if (lastCheckpointPassed === cp.index - 1) {
					lastCheckpointPassed = cp.index;
				}
			}
		});

		// Camera positioning behind craft
		const camOffset = curveTangent.clone().multiplyScalar(-14).add(new THREE.Vector3(0, 5, 0));
		camera.position.copy(ship.position).add(camOffset);
		camera.lookAt(ship.position.clone().add(new THREE.Vector3(0, 1, 0)));

		// Update Audio & HUD
		updateAudio();
		updateHUD();
	}

	function checkLapFinish() {
		if (lastCheckpointPassed >= 2) {
			lastCheckpointPassed = 0;
			bestLapTime = Math.min(bestLapTime, currentLapTime);
			playSFX('lap');

			if (currentLap < totalLaps) {
				currentLap++;
			} else {
				finishRace();
			}
		}
	}

	function updateHUD() {
		const speedKmh = Math.round(speed * 130);
		document.getElementById('hexgl-speed-val').textContent = speedKmh;
		document.getElementById('hexgl-lap-val').textContent = currentLap + ' / ' + totalLaps;

		const seconds = (currentLapTime / 1000).toFixed(2);
		document.getElementById('hexgl-time-val').textContent = seconds + 's';

		const shieldBar = document.getElementById('hexgl-shield-bar');
		if (shieldBar) shieldBar.style.width = shield + '%';
	}

	function render() {
		updateGame();
		if (renderer && scene && camera) {
			renderer.render(scene, camera);
		}
		animationFrameId = requestAnimationFrame(render);
	}

	function startRace() {
		gameState = 'RACING';
		speed = 0;
		progressT = 0;
		currentLap = 1;
		lastCheckpointPassed = 0;
		shield = 100;
		boostsHit = 0;
		raceStartTime = performance.now();

		document.getElementById('hexgl-start-overlay').style.display = 'none';
		document.getElementById('hexgl-end-overlay').style.display = 'none';

		// Request anti-cheat token
		fetch('/hexgl?action=init_token')
			.then((res) => res.json())
			.then((data) => {
				if (data.success) {
					sessionToken = data.token;
				}
			})
			.catch((err) => console.log('Token error:', err));
	}

	function finishRace() {
		gameState = 'FINISHED';
		totalRaceTime = performance.now() - raceStartTime;
		const totalSec = totalRaceTime / 1000;

		// Calculate Score: 10,000 base + time bonus + shield bonus + boost bonus
		const timeBonus = Math.max(0, Math.round(180000 - totalRaceTime));
		const shieldBonus = Math.round(shield * 50);
		const boostBonus = boostsHit * 250;
		const finalScore = Math.max(500, 10000 + Math.round(timeBonus / 10) + shieldBonus + boostBonus);

		document.getElementById('hexgl-end-title').textContent = 'FINIŠS sasniegts!';
		document.getElementById('hexgl-end-msg').innerHTML = `
			Kopējais laiks: <strong>${totalSec.toFixed(2)} sek</strong><br>
			Vairoga atlikums: <strong>${shield}%</strong> (+${shieldBonus} pnk)<br>
			Galīgais rezultāts: <strong style="color:#00f3ff;font-size:22px;">${finalScore.toLocaleString()} punkti</strong>
		`;
		document.getElementById('hexgl-end-overlay').style.display = 'flex';

		// Push high score via AJAX
		submitScore(finalScore, Math.round(totalSec));
	}

	function gameOver() {
		gameState = 'GAMEOVER';
		document.getElementById('hexgl-end-title').textContent = 'KUĢIS IZNĪCINĀTS!';
		document.getElementById('hexgl-end-title').style.color = '#ff0055';
		document.getElementById('hexgl-end-msg').textContent = 'Tavs kuģis avarēja pret trases malām un tika iznīcināts!';
		document.getElementById('hexgl-end-overlay').style.display = 'flex';
	}

	function submitScore(score, duration) {
		if (!sessionToken) return;

		const formData = new FormData();
		formData.append('token', sessionToken);
		formData.append('score', score);
		formData.append('duration', duration);

		fetch('/hexgl?action=push', {
			method: 'POST',
			body: formData
		})
			.then((res) => res.json())
			.then((data) => {
				if (data.success) {
					if (data.isNewRecord) {
						alert('🎉 Apsveicam! Uzstādīts jauns personīgais rekords HexGL spēlē: ' + data.highScore + ' punkti!');
					}
				}
			})
			.catch((err) => console.log('Score submit error:', err));
	}

	// Initialization on DOM ready
	document.addEventListener('DOMContentLoaded', () => {
		init3D();
		setupInput();
		render();

		document.getElementById('hexgl-start-btn')?.addEventListener('click', startRace);
		document.getElementById('hexgl-restart-btn')?.addEventListener('click', startRace);
	});
})();
