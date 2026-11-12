<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Asistente de Postura - IA en Tiempo Real') }}
            </h2>
            <span id="estado-sistema" class="px-3 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 text-xs font-semibold rounded-full">
                Cargando modelo...
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Contenedor Principal Adaptable (Grid) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Columna de Video y Canvas -->
                <div class="lg:col-span-2 bg-black rounded-2xl overflow-hidden shadow-lg relative flex items-center justify-center aspect-video">
                    <video id="webcam" class="w-full h-full object-cover transform -scale-x-100" autoplay playsinline muted></video>
                    <canvas id="output_canvas" class="absolute inset-0 w-full h-full object-cover transform -scale-x-100 pointer-events-none"></canvas>
                    
                    <!-- 🌟 Miniguía Visual Flotante (Esquina Superior Derecha Forzada) -->
                    <div id="miniguia-card" style="position: absolute; top: 16px; right: 16px; z-index: 30;" class="bg-gray-900/90 backdrop-blur-md text-white px-3.5 py-2.5 rounded-xl border border-gray-700/60 shadow-2xl flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-600/30 border border-blue-500/40 flex items-center justify-center text-blue-400 font-bold text-lg" id="miniguia-icon">
                            🏋️‍♂️
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Referencia Ideal</span>
                            <span id="miniguia-texto" class="text-xs font-semibold text-blue-300">Selecciona un ejercicio</span>
                        </div>
                    </div>
                </div>

                <!-- Columna de Controles, Estadísticas y Feedback (Panel Lateral) -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 flex flex-col justify-between space-y-4">
                    
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Panel de Control</h3>
                        
                        <!-- Selector de Ejercicio -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase mb-2">Seleccionar Ejercicio</label>
                            <select id="selector-ejercicio" class="w-full text-sm bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-blue-500 text-gray-800 dark:text-white">
                                <option value="sentadilla">🏋️‍♂️ Sentadillas (Squats)</option>
                                <option value="biceps">💪 Curl de Bíceps (Brazos)</option>
                                <option value="flexion">🤸‍♂️ Flexiones de Pecho (Push-ups)</option>
                            </select>
                        </div>

                        <!-- Contador de Repeticiones -->
                        <div class="p-4 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-100 dark:border-blue-900/50 text-center">
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Repeticiones Válidas</span>
                            <div class="text-4xl font-black text-blue-700 dark:text-blue-300 font-mono mt-1" id="contador-reps">0</div>
                        </div>

                        <!-- Métricas en vivo -->
                        <div class="space-y-3">
                            <div class="p-3 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-800">
                                <span class="text-xs font-bold text-gray-500 uppercase">Ángulos Articulares</span>
                                <div class="text-sm font-bold text-gray-800 dark:text-white mt-1" id="val-angulo">--°</div>
                            </div>
                            
                            <div class="p-3 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-800">
                                <span class="text-xs font-bold text-gray-500 uppercase">Estado Actual</span>
                                <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-1" id="val-estado">ARRIBA</div>
                            </div>

                            <!-- Mensaje de Instrucción Lateral -->
                            <div id="feedback-panel" class="p-3.5 bg-gray-900 text-gray-100 rounded-xl border border-gray-700 text-xs font-medium leading-relaxed transition-colors duration-200">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase mb-0.5">Indicación de la IA:</span>
                                <span id="instruccion-texto">Colócate frente a la cámara para iniciar...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="space-y-2 pt-2">
                        <button id="btn-iniciar" onclick="toggleCamara()" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Iniciar Cámara
                        </button>
                        
                        <a href="/dashboard" class="w-full py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                            Volver al Panel
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- MediaPipe Pose en LOCAL (funciona sin internet). Archivos en public/js/mediapipe/pose/ -->
    <script src="{{ asset('js/mediapipe/pose/pose.js') }}"></script>

    <script>
        const videoElement = document.getElementById('webcam');
        const canvasElement = document.getElementById('output_canvas');
        const canvasCtx = canvasElement.getContext('2d');
        
        let contadorReps = 0;
        let estadoEjercicio = "ARRIBA";
        let camaraActiva = false;
        let mediaStream = null;
        let processingInterval = null;
        let inicioSesion = null;
        let modeloListo = false;
        let procesandoFrame = false;
        let ejercicioActual = document.getElementById('selector-ejercicio').value;

        // Envía la sesión actual al servidor. Captura repeticiones y tiempo al instante de llamarla.
        async function guardarSesion(ejercicio) {
            const repeticiones = contadorReps;
            const tiempoSegundos = inicioSesion ? Math.round((Date.now() - inicioSesion) / 1000) : 0;

            if (repeticiones < 1) return false; // no se guardan sesiones vacías

            try {
                const respuesta = await fetch("{{ route('entrenamiento.sesiones.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ejercicio: ejercicio,
                        repeticiones: repeticiones,
                        tiempo_segundos: tiempoSegundos
                    })
                });
                if (!respuesta.ok) console.error('No se pudo guardar la sesión:', respuesta.status);
                return respuesta.ok;
            } catch (e) {
                console.error('Error guardando la sesión:', e);
                return false;
            }
        }

        function hablar(texto) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let utterance = new SpeechSynthesisUtterance(texto);
                utterance.lang = 'es-ES';
                utterance.rate = 1.0;
                window.speechSynthesis.speak(utterance);
            }
        }

        function calcularAngulo(puntoA, puntoB, puntoC) {
            let radianes = Math.atan2(puntoC[1] - puntoB[1], puntoC[0] - puntoB[0]) - 
                           Math.atan2(puntoA[1] - puntoB[1], puntoA[0] - puntoB[0]);
            let angulo = Math.abs(radianes * 180.0 / Math.PI);
            if (angulo > 180.0) {
                angulo = 360.0 - angulo;
            }
            return Math.round(angulo);
        }

        const pose = new Pose({
            // Antes se descargaban de internet (CDN). Ahora salen de tu propio servidor.
            locateFile: (file) => `{{ asset('js/mediapipe/pose') }}/${file}`
        });

        pose.setOptions({
            modelComplexity: 1,
            smoothLandmarks: true,
            enableSegmentation: false,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5
        });

        pose.onResults(onResults);

        const POSE_CONNECTIONS = [
            [11, 12], [11, 13], [13, 15], [12, 14], [14, 16],
            [11, 23], [12, 24], [23, 24],
            [23, 25], [25, 27], [24, 26], [26, 28]
        ];

        function onResults(results) {
            if (!camaraActiva) return;

            if (canvasElement.width !== videoElement.videoWidth || canvasElement.height !== videoElement.videoHeight) {
                canvasElement.width = videoElement.videoWidth || 640;
                canvasElement.height = videoElement.videoHeight || 480;
            }

            canvasCtx.save();
            canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);

            const alto = canvasElement.height;
            const ancho = canvasElement.width;
            const instruccionTexto = document.getElementById('instruccion-texto');
            const valAngulo = document.getElementById('val-angulo');
            const valEstado = document.getElementById('val-estado');
            const feedbackPanel = document.getElementById('feedback-panel');
            const miniguiaTexto = document.getElementById('miniguia-texto');
            const miniguiaIcon = document.getElementById('miniguia-icon');
            const tipoEjercicio = document.getElementById('selector-ejercicio').value;

            if (results.poseLandmarks) {
                const pose_landmarks = results.poseLandmarks;
                const puntos = pose_landmarks.map(lm => [Math.floor(lm.x * ancho), Math.floor(lm.y * alto)]);

                if (tipoEjercicio === 'sentadilla') {
                    // --- LÓGICA DE SENTADILLAS ---
                    const vis_pierna_izq = Math.min(pose_landmarks[23].visibility, pose_landmarks[25].visibility, pose_landmarks[27].visibility);
                    const vis_pierna_der = Math.min(pose_landmarks[24].visibility, pose_landmarks[26].visibility, pose_landmarks[28].visibility);
                    const UMBRAL_VISIBILIDAD = 0.5;

                    if (vis_pierna_izq < UMBRAL_VISIBILIDAD && vis_pierna_der < UMBRAL_VISIBILIDAD) {
                        instruccionTexto.innerText = "ALERTA: ¡Aléjate, no se ven tus piernas!";
                        feedbackPanel.className = "p-3.5 bg-red-900 text-white rounded-xl border border-red-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                        miniguiaTexto.innerText = "Posiciona cuerpo completo";
                    } else {
                        let idx_hombro, idx_cadera, idx_rodilla, idx_tobillo;
                        if (vis_pierna_izq >= vis_pierna_der) {
                            idx_hombro = 11; idx_cadera = 23; idx_rodilla = 25; idx_tobillo = 27;
                        } else {
                            idx_hombro = 12; idx_cadera = 24; idx_rodilla = 26; idx_tobillo = 28;
                        }

                        const hombro = puntos[idx_hombro];
                        const cadera = puntos[idx_cadera];
                        const rodilla = puntos[idx_rodilla];
                        const tobillo = puntos[idx_tobillo];

                        const angulo_rodilla = calcularAngulo(cadera, rodilla, tobillo);
                        const angulo_torso = calcularAngulo(hombro, cadera, rodilla);

                        valAngulo.innerText = `Rodilla: ${angulo_rodilla}° | Torso: ${angulo_torso}°`;

                        POSE_CONNECTIONS.forEach(([inicio, fin]) => {
                            canvasCtx.beginPath();
                            canvasCtx.moveTo(puntos[inicio][0], puntos[inicio][1]);
                            canvasCtx.lineTo(puntos[fin][0], puntos[fin][1]);
                            canvasCtx.strokeStyle = '#00FF00';
                            canvasCtx.lineWidth = 2;
                            canvasCtx.stroke();
                        });

                        puntos.forEach(pt => {
                            canvasCtx.beginPath();
                            canvasCtx.arc(pt[0], pt[1], 4, 0, 2 * Math.PI);
                            canvasCtx.fillStyle = '#FF0000';
                            canvasCtx.fill();
                        });

                        const error_postura = angulo_torso < 35;

                        if (error_postura) {
                            instruccionTexto.innerText = "Cuidado: Inclinación excesiva del torso";
                            feedbackPanel.className = "p-3.5 bg-amber-900 text-white rounded-xl border border-amber-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "¡Endereza la espalda!";
                            miniguiaIcon.innerText = "⚠️";
                        } else if (angulo_rodilla <= 90 && estadoEjercicio === "ARRIBA") {
                            estadoEjercicio = "ABAJO";
                            instruccionTexto.innerText = "¡Excelente! Ahora sube";
                            feedbackPanel.className = "p-3.5 bg-emerald-900 text-white rounded-xl border border-emerald-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "Fase: Abajo (Profundo)";
                            miniguiaIcon.innerText = "⬇️";
                        } else if (angulo_rodilla > 160 && estadoEjercicio === "ABAJO") {
                            estadoEjercicio = "ARRIBA";
                            contadorReps += 1;
                            document.getElementById('contador-reps').innerText = contadorReps;
                            instruccionTexto.innerText = `¡Repetición ${contadorReps} completada!`;
                            feedbackPanel.className = "p-3.5 bg-blue-950 text-white rounded-xl border border-blue-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "Fase: Arriba (Completa)";
                            miniguiaIcon.innerText = "⬆️";
                            hablar(contadorReps.toString());
                        } else if (angulo_rodilla > 90 && angulo_rodilla < 140 && estadoEjercicio === "ARRIBA" && !error_postura) {
                            instruccionTexto.innerText = "Baja un poco más...";
                            feedbackPanel.className = "p-3.5 bg-amber-900 text-white rounded-xl border border-amber-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "Baja la cadera más";
                            miniguiaIcon.innerText = "📉";
                        }

                        valEstado.innerText = estadoEjercicio;
                    }

                } else if (tipoEjercicio === 'biceps') {
                    // --- LÓGICA DE CURL DE BÍCEPS ---
                    const vis_brazo_izq = Math.min(pose_landmarks[11].visibility, pose_landmarks[13].visibility, pose_landmarks[15].visibility);
                    const vis_brazo_der = Math.min(pose_landmarks[12].visibility, pose_landmarks[14].visibility, pose_landmarks[16].visibility);
                    const UMBRAL_VISIBILIDAD = 0.5;

                    if (vis_brazo_izq < UMBRAL_VISIBILIDAD && vis_brazo_der < UMBRAL_VISIBILIDAD) {
                        instruccionTexto.innerText = "ALERTA: Aléjate o muestra tus brazos a la cámara";
                        feedbackPanel.className = "p-3.5 bg-red-900 text-white rounded-xl border border-red-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                        miniguiaTexto.innerText = "Posiciona el tren superior";
                    } else {
                        let idx_hombro, idx_codo, idx_muneca;
                        if (vis_brazo_izq >= vis_brazo_der) {
                            idx_hombro = 11; idx_codo = 13; idx_muneca = 15;
                        } else {
                            idx_hombro = 12; idx_codo = 14; idx_muneca = 16;
                        }

                        const hombro = puntos[idx_hombro];
                        const codo = puntos[idx_codo];
                        const muneca = puntos[idx_muneca];

                        const angulo_codo = calcularAngulo(hombro, codo, muneca);

                        valAngulo.innerText = `Codo: ${angulo_codo}°`;

                        POSE_CONNECTIONS.forEach(([inicio, fin]) => {
                            canvasCtx.beginPath();
                            canvasCtx.moveTo(puntos[inicio][0], puntos[inicio][1]);
                            canvasCtx.lineTo(puntos[fin][0], puntos[fin][1]);
                            canvasCtx.strokeStyle = '#00FF00';
                            canvasCtx.lineWidth = 2;
                            canvasCtx.stroke();
                        });

                        puntos.forEach(pt => {
                            canvasCtx.beginPath();
                            canvasCtx.arc(pt[0], pt[1], 4, 0, 2 * Math.PI);
                            canvasCtx.fillStyle = '#FF0000';
                            canvasCtx.fill();
                        });

                        if (angulo_codo <= 50 && (estadoEjercicio === "ARRIBA" || estadoEjercicio === "EXTENDIDO")) {
                            estadoEjercicio = "FLEXIONADO";
                            contadorReps += 1;
                            document.getElementById('contador-reps').innerText = contadorReps;
                            instruccionTexto.innerText = `¡Bien! Repetición ${contadorReps}`;
                            feedbackPanel.className = "p-3.5 bg-emerald-900 text-white rounded-xl border border-emerald-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "Fase: Arriba (Flexionado)";
                            miniguiaIcon.innerText = "💪";
                            hablar(contadorReps.toString());
                        } else if (angulo_codo >= 150 && estadoEjercicio === "FLEXIONADO") {
                            estadoEjercicio = "EXTENDIDO";
                            instruccionTexto.innerText = "Extensión completa. Vuelve a subir";
                            feedbackPanel.className = "p-3.5 bg-blue-950 text-white rounded-xl border border-blue-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "Fase: Abajo (Extendido)";
                            miniguiaIcon.innerText = "⬇️";
                        } else if (angulo_codo > 50 && angulo_codo < 150) {
                            if (estadoEjercicio === "EXTENDIDO") {
                                instruccionTexto.innerText = "Sube el peso hacia tu hombro...";
                            } else {
                                instruccionTexto.innerText = "Extiende el brazo al bajar...";
                            }
                            feedbackPanel.className = "p-3.5 bg-amber-900 text-white rounded-xl border border-amber-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "En movimiento...";
                            miniguiaIcon.innerText = "🔄";
                        }

                        valEstado.innerText = estadoEjercicio;
                    }

                } else if (tipoEjercicio === 'flexion') {
                    // --- LÓGICA DE FLEXIONES DE PECHO (PUSH-UPS) ---
                    const vis_brazo_izq = Math.min(pose_landmarks[11].visibility, pose_landmarks[13].visibility, pose_landmarks[15].visibility);
                    const vis_brazo_der = Math.min(pose_landmarks[12].visibility, pose_landmarks[14].visibility, pose_landmarks[16].visibility);
                    const UMBRAL_VISIBILIDAD = 0.5;

                    if (vis_brazo_izq < UMBRAL_VISIBILIDAD && vis_brazo_der < UMBRAL_VISIBILIDAD) {
                        instruccionTexto.innerText = "ALERTA: Colócate de perfil, no se ve bien tu brazo";
                        feedbackPanel.className = "p-3.5 bg-red-900 text-white rounded-xl border border-red-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                        miniguiaTexto.innerText = "Posiciónate de perfil";
                    } else {
                        let idx_hombro, idx_codo, idx_muneca;
                        if (vis_brazo_izq >= vis_brazo_der) {
                            idx_hombro = 11; idx_codo = 13; idx_muneca = 15;
                        } else {
                            idx_hombro = 12; idx_codo = 14; idx_muneca = 16;
                        }

                        const hombro = puntos[idx_hombro];
                        const codo = puntos[idx_codo];
                        const muneca = puntos[idx_muneca];

                        const angulo_codo = calcularAngulo(hombro, codo, muneca);
                        valAngulo.innerText = `Codo: ${angulo_codo}°`;

                        POSE_CONNECTIONS.forEach(([inicio, fin]) => {
                            canvasCtx.beginPath();
                            canvasCtx.moveTo(puntos[inicio][0], puntos[inicio][1]);
                            canvasCtx.lineTo(puntos[fin][0], puntos[fin][1]);
                            canvasCtx.strokeStyle = '#00FF00';
                            canvasCtx.lineWidth = 2;
                            canvasCtx.stroke();
                        });

                        puntos.forEach(pt => {
                            canvasCtx.beginPath();
                            canvasCtx.arc(pt[0], pt[1], 4, 0, 2 * Math.PI);
                            canvasCtx.fillStyle = '#FF0000';
                            canvasCtx.fill();
                        });

                        if (angulo_codo <= 90 && (estadoEjercicio === "ARRIBA" || estadoEjercicio === "EXTENDIDO")) {
                            estadoEjercicio = "ABAJO";
                            instruccionTexto.innerText = "¡Muy bien! Ahora extiende los brazos";
                            feedbackPanel.className = "p-3.5 bg-emerald-900 text-white rounded-xl border border-emerald-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "Fase: Abajo (Flexión)";
                            miniguiaIcon.innerText = "⬇️";
                        } else if (angulo_codo > 150 && estadoEjercicio === "ABAJO") {
                            estadoEjercicio = "ARRIBA";
                            contadorReps += 1;
                            document.getElementById('contador-reps').innerText = contadorReps;
                            instruccionTexto.innerText = `¡Flexión ${contadorReps} completada!`;
                            feedbackPanel.className = "p-3.5 bg-blue-950 text-white rounded-xl border border-blue-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "Fase: Arriba (Estirado)";
                            miniguiaIcon.innerText = "⬆️";
                            hablar(contadorReps.toString());
                        } else if (angulo_codo > 90 && angulo_codo <= 140 && estadoEjercicio === "ARRIBA") {
                            instruccionTexto.innerText = "Baja más el pecho...";
                            feedbackPanel.className = "p-3.5 bg-amber-900 text-white rounded-xl border border-amber-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                            miniguiaTexto.innerText = "Flexiona los codos";
                            miniguiaIcon.innerText = "📉";
                        }

                        valEstado.innerText = estadoEjercicio;
                    }
                }
            } else {
                instruccionTexto.innerText = "Encuadra tu cuerpo completo para iniciar";
                feedbackPanel.className = "p-3.5 bg-gray-900 text-gray-100 rounded-xl border border-gray-700 text-xs font-medium leading-relaxed transition-colors duration-200";
                miniguiaTexto.innerText = "Esperando detección...";
            }
            canvasCtx.restore();
        }

        // Carga el modelo al abrir la página, para que al pulsar "Iniciar Cámara" ya esté listo
        async function cargarModelo() {
            const btn = document.getElementById('btn-iniciar');
            const estadoSistema = document.getElementById('estado-sistema');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            estadoSistema.innerText = "Cargando modelo...";

            try {
                await pose.initialize();
                modeloListo = true;
                estadoSistema.innerText = "Sistema Listo 🟡";
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } catch (e) {
                console.error("Error cargando el modelo:", e);
                estadoSistema.innerText = "Error al cargar el modelo 🔴";
            }
        }

        // Procesa un frame a la vez: no se acumulan envíos si el equipo va lento
        async function bucleDeteccion() {
            if (!camaraActiva) return;

            if (!procesandoFrame && videoElement.readyState >= 2) {
                procesandoFrame = true;
                try {
                    await pose.send({ image: videoElement });
                } catch (e) {
                    console.error("Error enviando frame:", e);
                }
                procesandoFrame = false;
            }
            requestAnimationFrame(bucleDeteccion);
        }

        async function toggleCamara() {
            const btn = document.getElementById('btn-iniciar');
            const estadoSistema = document.getElementById('estado-sistema');

            if (!camaraActiva && !modeloListo) return; // el botón está bloqueado hasta que cargue el modelo

            if (!camaraActiva) {
                try {
                    mediaStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: "user" },
                        audio: false
                    });

                    videoElement.srcObject = mediaStream;
                    
                    await new Promise((resolve) => {
                        videoElement.onloadedmetadata = () => {
                            videoElement.play();
                            resolve();
                        };
                    });

                    camaraActiva = true;
                    inicioSesion = Date.now();
                    btn.innerText = "Detener Cámara";
                    btn.classList.replace('bg-blue-600', 'bg-red-600');
                    btn.classList.replace('hover:bg-blue-700', 'hover:bg-red-700');
                    estadoSistema.innerText = "IA Activa 🟢";
                    estadoSistema.className = "px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 text-xs font-semibold rounded-full";

                    bucleDeteccion();

                } catch (err) {
                    alert("No se pudo acceder a la cámara. Verifica los permisos del navegador.");
                    console.error(err);
                }
            } else {
                camaraActiva = false;
                if (processingInterval) clearInterval(processingInterval);
                
                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                }
                videoElement.srcObject = null;

                await guardarSesion(ejercicioActual);
                window.location.reload();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            cargarModelo();

            // Reiniciar contadores y estados al cambiar de ejercicio en el menú
            document.getElementById('selector-ejercicio').addEventListener('change', async (e) => {
                // Guarda lo hecho con el ejercicio anterior antes de reiniciar (la captura de datos es inmediata)
                const guardado = guardarSesion(ejercicioActual);
                ejercicioActual = e.target.value;
                inicioSesion = Date.now();

                contadorReps = 0;
                document.getElementById('contador-reps').innerText = contadorReps;
                estadoEjercicio = "ARRIBA";
                document.getElementById('val-estado').innerText = estadoEjercicio;
                document.getElementById('instruccion-texto').innerText = "Ejercicio cambiado. Listo para iniciar.";
                document.getElementById('val-angulo').innerText = "--°";

                await guardado;
            });
        });
    </script>
</x-app-layout>