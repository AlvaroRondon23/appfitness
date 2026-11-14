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

                    <!-- Aviso de cámara (permiso denegado, sin cámara, cámara ocupada, etc.) -->
                    <div id="aviso-camara" role="alert" style="display: none; position: absolute; inset: 0; z-index: 40;" class="bg-gray-900/95 text-center items-center justify-center p-6">
                        <div class="max-w-md space-y-3">
                            <div class="text-4xl">📷</div>
                            <h3 id="aviso-camara-titulo" class="text-lg font-bold text-red-300"></h3>
                            <p id="aviso-camara-detalle" class="text-sm text-gray-300 leading-relaxed"></p>
                        </div>
                    </div>

                    <!-- Miniguía Visual Flotante -->
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

                <!-- Panel lateral -->
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

                        <!-- Puntaje de técnica -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-800 text-center">
                                <span class="text-[10px] font-bold text-gray-500 uppercase">Última repetición</span>
                                <div class="text-2xl font-black font-mono text-gray-400" id="puntaje-ultima">--</div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-800 text-center">
                                <span class="text-[10px] font-bold text-gray-500 uppercase">Técnica (promedio)</span>
                                <div class="text-2xl font-black font-mono text-gray-400" id="puntaje-promedio">--</div>
                            </div>
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
                            <span id="btn-texto">Iniciar Cámara</span>
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
        // ======================================================================
        // CONFIGURACIÓN (aquí se ajustan umbrales y reglas del puntaje)
        // ======================================================================
        const UMBRAL_VISIBILIDAD = 0.5;      // visibilidad mínima de una articulación
        const TIEMPO_MIN_REP_MS = 800;       // una repetición más rápida que esto pierde puntos de control
        const FRAMES_ERROR_POSTURA = 5;      // frames con mala postura para penalizar la repetición

        // Cada ejercicio se define una sola vez. En todos, el ángulo principal BAJA en la fase "ABAJO".
        //  - izq / der: índices de MediaPipe de cada lado del cuerpo
        //  - visibles: posiciones de esa lista que deben verse para medir el ángulo principal
        //  - umbralAbajo: ángulo que debe alcanzarse para entrar en la fase "ABAJO"
        //  - umbralArriba: ángulo para volver a "ARRIBA" y contar la repetición
        //  - anguloIdeal: con este ángulo o menor, la profundidad vale 100 puntos
        //  - postura(p, v): devuelve null si no se puede medir, o { etiqueta, valor, ok, mensaje, guia }
        const EJERCICIOS = {
            sentadilla: {
                nombre: 'Sentadilla',
                etiquetaAngulo: 'Rodilla',
                fases: ['ARRIBA', 'ABAJO'],
                // [hombro, cadera, rodilla, tobillo]
                izq: [11, 23, 25, 27],
                der: [12, 24, 26, 28],
                visibles: [1, 2, 3],
                angulo: (p) => calcularAngulo(p[1], p[2], p[3]),
                umbralAbajo: 90, umbralArriba: 160, anguloIdeal: 80,
                postura: (p, v) => {
                    if (v[0] < UMBRAL_VISIBILIDAD) return null;
                    const valor = calcularAngulo(p[0], p[1], p[2]);
                    return {
                        etiqueta: 'Torso', valor, ok: valor >= 35,
                        mensaje: 'Cuidado: Inclinación excesiva del torso',
                        guia: '¡Endereza la espalda!'
                    };
                },
                textos: {
                    noVisible: 'ALERTA: ¡Aléjate, no se ven tus piernas!',
                    noVisibleGuia: 'Posiciona cuerpo completo',
                    parcial: 'Baja un poco más...', parcialGuia: 'Baja la cadera más', parcialIcono: '📉',
                    abajo: '¡Excelente! Ahora sube', abajoGuia: 'Fase: Abajo (Profundo)', abajoIcono: '⬇️',
                    arribaGuia: 'Fase: Arriba (Completa)', arribaIcono: '⬆️',
                    masProfundo: 'Intenta bajar más la cadera'
                }
            },
            biceps: {
                nombre: 'Curl de bíceps',
                etiquetaAngulo: 'Codo',
                fases: ['EXTENDIDO', 'FLEXIONADO'],
                // [hombro, codo, muñeca, cadera]
                izq: [11, 13, 15, 23],
                der: [12, 14, 16, 24],
                visibles: [0, 1, 2],
                angulo: (p) => calcularAngulo(p[0], p[1], p[2]),
                umbralAbajo: 50, umbralArriba: 150, anguloIdeal: 40,
                postura: (p, v) => {
                    if (v[3] < UMBRAL_VISIBILIDAD) return null; // sin la cadera visible no se mide
                    const valor = calcularAngulo(p[3], p[0], p[1]); // cadera-hombro-codo: el codo no debe adelantarse
                    return {
                        etiqueta: 'Codo-torso', valor, ok: valor <= 45,
                        mensaje: 'Cuidado: no balancees el codo hacia adelante',
                        guia: 'Pega el codo al torso'
                    };
                },
                textos: {
                    noVisible: 'ALERTA: Aléjate o muestra tus brazos a la cámara',
                    noVisibleGuia: 'Posiciona el tren superior',
                    parcial: 'Sube el peso hacia tu hombro...', parcialGuia: 'Flexiona más el codo', parcialIcono: '🔄',
                    abajo: '¡Bien arriba! Ahora extiende el brazo', abajoGuia: 'Fase: Arriba (Flexionado)', abajoIcono: '💪',
                    arribaGuia: 'Fase: Abajo (Extendido)', arribaIcono: '⬇️',
                    masProfundo: 'Intenta flexionar más el codo'
                }
            },
            flexion: {
                nombre: 'Flexión',
                etiquetaAngulo: 'Codo',
                fases: ['ARRIBA', 'ABAJO'],
                // [hombro, codo, muñeca, cadera, tobillo]
                izq: [11, 13, 15, 23, 27],
                der: [12, 14, 16, 24, 28],
                visibles: [0, 1, 2],
                angulo: (p) => calcularAngulo(p[0], p[1], p[2]),
                umbralAbajo: 90, umbralArriba: 150, anguloIdeal: 80,
                postura: (p, v) => {
                    if (v[3] < UMBRAL_VISIBILIDAD || v[4] < UMBRAL_VISIBILIDAD) return null;
                    const valor = calcularAngulo(p[0], p[3], p[4]); // hombro-cadera-tobillo: cuerpo recto
                    return {
                        etiqueta: 'Cuerpo', valor, ok: valor >= 150,
                        mensaje: 'Cuidado: mantén el cuerpo recto',
                        guia: 'Alinea cadera y espalda'
                    };
                },
                textos: {
                    noVisible: 'ALERTA: Colócate de perfil, no se ve bien tu brazo',
                    noVisibleGuia: 'Posiciónate de perfil',
                    parcial: 'Baja más el pecho...', parcialGuia: 'Flexiona los codos', parcialIcono: '📉',
                    abajo: '¡Muy bien! Ahora extiende los brazos', abajoGuia: 'Fase: Abajo (Flexión)', abajoIcono: '⬇️',
                    arribaGuia: 'Fase: Arriba (Estirado)', arribaIcono: '⬆️',
                    masProfundo: 'Intenta bajar más el pecho'
                }
            }
        };

        const POSE_CONNECTIONS = [
            [11, 12], [11, 13], [13, 15], [12, 14], [14, 16],
            [11, 23], [12, 24], [23, 24],
            [23, 25], [25, 27], [24, 26], [26, 28]
        ];

        const ESTILOS_FEEDBACK = {
            neutro: 'bg-gray-900 text-gray-100 border-gray-700',
            alerta: 'bg-red-900 text-white border-red-700',
            aviso: 'bg-amber-900 text-white border-amber-700',
            ok: 'bg-emerald-900 text-white border-emerald-700',
            info: 'bg-blue-950 text-white border-blue-700'
        };

        // ======================================================================
        // ELEMENTOS Y ESTADO
        // ======================================================================
        const videoElement = document.getElementById('webcam');
        const canvasElement = document.getElementById('output_canvas');
        const canvasCtx = canvasElement.getContext('2d');

        const el = {
            instruccion: document.getElementById('instruccion-texto'),
            feedbackPanel: document.getElementById('feedback-panel'),
            miniguiaTexto: document.getElementById('miniguia-texto'),
            miniguiaIcon: document.getElementById('miniguia-icon'),
            valAngulo: document.getElementById('val-angulo'),
            valEstado: document.getElementById('val-estado'),
            contador: document.getElementById('contador-reps'),
            puntajeUltima: document.getElementById('puntaje-ultima'),
            puntajePromedio: document.getElementById('puntaje-promedio'),
            btn: document.getElementById('btn-iniciar'),
            btnTexto: document.getElementById('btn-texto'),
            estadoSistema: document.getElementById('estado-sistema'),
            avisoCamara: document.getElementById('aviso-camara'),
            avisoTitulo: document.getElementById('aviso-camara-titulo'),
            avisoDetalle: document.getElementById('aviso-camara-detalle')
        };

        const CLASE_CHIP = 'px-3 py-1 text-xs font-semibold rounded-full ';
        const CHIP_LISTO = CLASE_CHIP + 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
        const CHIP_ACTIVO = CLASE_CHIP + 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
        const CHIP_ERROR = CLASE_CHIP + 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300';

        let contadorReps = 0;
        let fase = 'ARRIBA';              // 'ARRIBA' | 'ABAJO' (cada ejercicio le pone su nombre en pantalla)
        let repActual = null;             // datos de la repetición en curso
        let puntajes = [];                // puntaje de cada repetición completada
        let camaraActiva = false;
        let camaraSoportada = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
        let mediaStream = null;
        let inicioSesion = null;
        let modeloListo = false;
        let procesandoFrame = false;
        let ejercicioActual = document.getElementById('selector-ejercicio').value;

        nuevaRep();

        // ======================================================================
        // UTILIDADES
        // ======================================================================
        function calcularAngulo(puntoA, puntoB, puntoC) {
            let radianes = Math.atan2(puntoC[1] - puntoB[1], puntoC[0] - puntoB[0]) -
                           Math.atan2(puntoA[1] - puntoB[1], puntoA[0] - puntoB[0]);
            let angulo = Math.abs(radianes * 180.0 / Math.PI);
            if (angulo > 180.0) {
                angulo = 360.0 - angulo;
            }
            return Math.round(angulo);
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

        function mostrarFeedback(tipo, instruccion, guia, icono) {
            el.instruccion.innerText = instruccion;
            el.feedbackPanel.className = `p-3.5 rounded-xl border text-xs font-medium leading-relaxed transition-colors duration-200 ${ESTILOS_FEEDBACK[tipo]}`;
            if (guia !== undefined) el.miniguiaTexto.innerText = guia;
            if (icono) el.miniguiaIcon.innerText = icono;
        }

        function colorPuntaje(elemento, valor) {
            let color = 'text-gray-400';
            if (valor !== null) {
                color = valor >= 80 ? 'text-emerald-600 dark:text-emerald-400'
                      : valor >= 60 ? 'text-amber-600 dark:text-amber-400'
                      : 'text-red-600 dark:text-red-400';
            }
            elemento.className = `text-2xl font-black font-mono ${color}`;
        }

        function promedioPuntaje() {
            if (puntajes.length === 0) return null;
            return Math.round(puntajes.reduce((a, b) => a + b, 0) / puntajes.length);
        }

        function actualizarPuntajesUI() {
            const ultima = puntajes.length ? puntajes[puntajes.length - 1] : null;
            const promedio = promedioPuntaje();
            el.puntajeUltima.innerText = ultima === null ? '--' : ultima;
            el.puntajePromedio.innerText = promedio === null ? '--' : promedio;
            colorPuntaje(el.puntajeUltima, ultima);
            colorPuntaje(el.puntajePromedio, promedio);
        }

        function etiquetaFase() {
            const cfg = EJERCICIOS[ejercicioActual];
            return fase === 'ARRIBA' ? cfg.fases[0] : cfg.fases[1];
        }

        function nuevaRep() {
            repActual = { inicio: null, min: 180, framesError: 0 };
        }

        function reiniciarContadores() {
            contadorReps = 0;
            fase = 'ARRIBA';
            puntajes = [];
            nuevaRep();
            el.contador.innerText = '0';
            el.valEstado.innerText = etiquetaFase();
            el.valAngulo.innerText = '--°';
            actualizarPuntajesUI();
        }

        function dibujarEsqueleto(puntos) {
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
        }

        // ======================================================================
        // AVISOS DE CÁMARA
        // ======================================================================
        function mostrarAvisoCamara(titulo, detalle) {
            el.avisoTitulo.innerText = titulo;
            el.avisoDetalle.innerText = detalle;
            el.avisoCamara.style.display = 'flex';
            el.estadoSistema.innerText = 'Sin cámara 🔴';
            el.estadoSistema.className = CHIP_ERROR;
        }

        function ocultarAvisoCamara() {
            el.avisoCamara.style.display = 'none';
        }

        function describirErrorCamara(err) {
            switch (err && err.name) {
                case 'NotAllowedError':
                case 'PermissionDeniedError':
                case 'SecurityError':
                    return {
                        titulo: 'Permiso de cámara denegado',
                        detalle: 'Haz clic en el icono de candado junto a la dirección del navegador, permite el acceso a la cámara y pulsa "Iniciar Cámara" de nuevo.'
                    };
                case 'NotFoundError':
                case 'DevicesNotFoundError':
                case 'OverconstrainedError':
                    return {
                        titulo: 'No se encontró ninguna cámara',
                        detalle: 'Conecta una cámara web o revisa que el equipo la detecte, y pulsa "Iniciar Cámara" de nuevo.'
                    };
                case 'NotReadableError':
                case 'TrackStartError':
                case 'AbortError':
                    return {
                        titulo: 'La cámara está ocupada',
                        detalle: 'Otra aplicación o pestaña está usando la cámara. Ciérrala y pulsa "Iniciar Cámara" de nuevo.'
                    };
                default:
                    return {
                        titulo: 'No se pudo iniciar la cámara',
                        detalle: 'Ocurrió un error inesperado. Recarga la página e inténtalo de nuevo.'
                    };
            }
        }

        // ======================================================================
        // GUARDADO DE LA SESIÓN
        // ======================================================================
        // Envía la sesión actual al servidor. Captura repeticiones, tiempo y puntaje al instante de llamarla.
        async function guardarSesion(ejercicio, alSalir = false) {
            const repeticiones = contadorReps;
            const tiempoSegundos = inicioSesion ? Math.round((Date.now() - inicioSesion) / 1000) : 0;
            const puntaje = promedioPuntaje();

            if (repeticiones < 1) return false; // no se guardan sesiones vacías

            try {
                const respuesta = await fetch("{{ route('entrenamiento.sesiones.store') }}", {
                    method: 'POST',
                    keepalive: alSalir, // permite que el envío termine aunque se cierre la pestaña
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ejercicio: ejercicio,
                        repeticiones: repeticiones,
                        tiempo_segundos: tiempoSegundos,
                        puntaje: puntaje
                    })
                });
                if (!respuesta.ok) console.error('No se pudo guardar la sesión:', respuesta.status);
                return respuesta.ok;
            } catch (e) {
                console.error('Error guardando la sesión:', e);
                return false;
            }
        }

        // ======================================================================
        // MEDIAPIPE
        // ======================================================================
        const pose = new Pose({
            // Los archivos salen de tu propio servidor, no de internet (CDN)
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

        // Elige el lado del cuerpo mejor visible. Devuelve null si ninguno se ve.
        function elegirLado(landmarks, puntos, cfg) {
            const visibilidad = (ids) => Math.min(...cfg.visibles.map(i => landmarks[ids[i]].visibility));
            const visIzq = visibilidad(cfg.izq);
            const visDer = visibilidad(cfg.der);

            if (visIzq < UMBRAL_VISIBILIDAD && visDer < UMBRAL_VISIBILIDAD) return null;

            const ids = visIzq >= visDer ? cfg.izq : cfg.der;
            return {
                p: ids.map(i => puntos[i]),
                v: ids.map(i => landmarks[i].visibility)
            };
        }

        // Calcula el puntaje (0-100) de la repetición que se acaba de completar:
        //  50% profundidad (qué tanto se acercó al ángulo ideal) + 50% control (postura y ritmo)
        function completarRepeticion(cfg, ahora) {
            const duracion = ahora - repActual.inicio;

            let profundidad = 100;
            if (repActual.min > cfg.anguloIdeal) {
                const rango = cfg.umbralAbajo - cfg.anguloIdeal;
                profundidad = 70 + 30 * (cfg.umbralAbajo - repActual.min) / rango;
            }
            profundidad = Math.max(0, Math.min(100, profundidad));

            let control = 100;
            const malaPostura = repActual.framesError >= FRAMES_ERROR_POSTURA;
            const muyRapida = duracion < TIEMPO_MIN_REP_MS;
            if (malaPostura) control -= 40;
            if (muyRapida) control -= 30;

            const puntaje = Math.round(0.5 * profundidad + 0.5 * control);

            let consejo = '¡Técnica excelente!';
            if (malaPostura) consejo = repActual.guiaError || 'Cuida tu postura';
            else if (profundidad < 85) consejo = cfg.textos.masProfundo;
            else if (muyRapida) consejo = 'Controla el movimiento, hazlo más despacio';

            puntajes.push(puntaje);
            contadorReps += 1;
            el.contador.innerText = contadorReps;
            actualizarPuntajesUI();

            fase = 'ARRIBA';
            nuevaRep();

            return { puntaje, consejo, malaPostura };
        }

        function procesarEjercicio(cfg, lado) {
            const ahora = Date.now();
            const angulo = cfg.angulo(lado.p);
            const post = cfg.postura(lado.p, lado.v);
            const malaPosturaAhora = !!(post && !post.ok);

            el.valAngulo.innerText = `${cfg.etiquetaAngulo}: ${angulo}°` + (post ? ` | ${post.etiqueta}: ${post.valor}°` : '');

            // Acumula datos de la repetición en curso (mínimo ángulo y frames con mala postura)
            const acumular = () => {
                repActual.min = Math.min(repActual.min, angulo);
                if (malaPosturaAhora) {
                    repActual.framesError += 1;
                    repActual.mensajeError = post.mensaje;
                    repActual.guiaError = post.guia;
                }
            };

            let evento = null;

            if (fase === 'ARRIBA') {
                if (angulo >= cfg.umbralArriba) {
                    // Volvió arriba sin llegar al fondo: se descarta el intento
                    if (repActual.inicio !== null) nuevaRep();
                } else {
                    if (repActual.inicio === null) repActual.inicio = ahora;
                    acumular();
                    if (angulo <= cfg.umbralAbajo) {
                        fase = 'ABAJO';
                        evento = 'abajo';
                    } else {
                        evento = 'parcial';
                    }
                }
            } else {
                acumular();
                if (angulo > cfg.umbralArriba) {
                    evento = 'completada';
                }
            }

            let resultado = null;
            if (evento === 'completada') {
                resultado = completarRepeticion(cfg, ahora);
            }

            el.valEstado.innerText = etiquetaFase();

            // Feedback visual (prioridad: repetición completada > mala postura > fase)
            if (resultado) {
                const tipo = resultado.puntaje >= 80 ? 'info' : 'aviso';
                mostrarFeedback(
                    tipo,
                    `¡Repetición ${contadorReps}! Técnica: ${resultado.puntaje}/100. ${resultado.consejo}`,
                    cfg.textos.arribaGuia,
                    cfg.textos.arribaIcono
                );
                hablar(contadorReps.toString());
            } else if (malaPosturaAhora && repActual.inicio !== null) {
                mostrarFeedback('aviso', post.mensaje, post.guia, '⚠️');
            } else if (fase === 'ABAJO') {
                mostrarFeedback('ok', cfg.textos.abajo, cfg.textos.abajoGuia, cfg.textos.abajoIcono);
            } else if (evento === 'parcial') {
                mostrarFeedback('aviso', cfg.textos.parcial, cfg.textos.parcialGuia, cfg.textos.parcialIcono);
            }
        }

        function onResults(results) {
            if (!camaraActiva) return;

            if (canvasElement.width !== videoElement.videoWidth || canvasElement.height !== videoElement.videoHeight) {
                canvasElement.width = videoElement.videoWidth || 640;
                canvasElement.height = videoElement.videoHeight || 480;
            }

            canvasCtx.save();
            canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);

            if (results.poseLandmarks) {
                const cfg = EJERCICIOS[ejercicioActual];
                const ancho = canvasElement.width;
                const alto = canvasElement.height;
                const puntos = results.poseLandmarks.map(lm => [Math.floor(lm.x * ancho), Math.floor(lm.y * alto)]);
                const lado = elegirLado(results.poseLandmarks, puntos, cfg);

                if (!lado) {
                    mostrarFeedback('alerta', cfg.textos.noVisible, cfg.textos.noVisibleGuia);
                } else {
                    dibujarEsqueleto(puntos);
                    procesarEjercicio(cfg, lado);
                }
            } else {
                mostrarFeedback('neutro', 'Encuadra tu cuerpo completo para iniciar', 'Esperando detección...');
            }

            canvasCtx.restore();
        }

        // Carga el modelo al abrir la página, para que al pulsar "Iniciar Cámara" ya esté listo
        async function cargarModelo() {
            el.btn.disabled = true;
            el.btn.classList.add('opacity-50', 'cursor-not-allowed');
            el.estadoSistema.innerText = 'Cargando modelo...';

            try {
                await pose.initialize();
                modeloListo = true;

                if (!camaraSoportada) return; // el aviso de navegador ya está visible y el botón sigue bloqueado

                el.estadoSistema.innerText = 'Sistema Listo 🟡';
                el.btn.disabled = false;
                el.btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } catch (e) {
                console.error('Error cargando el modelo:', e);
                el.estadoSistema.innerText = 'Error al cargar el modelo 🔴';
                el.estadoSistema.className = CHIP_ERROR;
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
                    console.error('Error enviando frame:', e);
                }
                procesandoFrame = false;
            }
            requestAnimationFrame(bucleDeteccion);
        }

        // ======================================================================
        // CÁMARA: INICIAR / DETENER
        // ======================================================================
        async function iniciarCamara() {
            ocultarAvisoCamara();

            try {
                mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                    audio: false
                });
            } catch (err) {
                console.error(err);
                const aviso = describirErrorCamara(err);
                mostrarAvisoCamara(aviso.titulo, aviso.detalle);
                return;
            }

            videoElement.srcObject = mediaStream;

            await new Promise((resolve) => {
                videoElement.onloadedmetadata = () => {
                    videoElement.play();
                    resolve();
                };
            });

            // Si la cámara se desconecta o se bloquea durante la sesión
            mediaStream.getVideoTracks().forEach(track => {
                track.addEventListener('ended', () => {
                    if (camaraActiva) {
                        detenerCamara({
                            titulo: 'Se perdió la conexión con la cámara',
                            detalle: 'Revisa que la cámara siga conectada y pulsa "Iniciar Cámara" de nuevo. Tu sesión hasta ese momento se guardó.'
                        });
                    }
                });
            });

            reiniciarContadores();
            camaraActiva = true;
            inicioSesion = Date.now();

            el.btnTexto.innerText = 'Detener Cámara';
            el.btn.classList.replace('bg-blue-600', 'bg-red-600');
            el.btn.classList.replace('hover:bg-blue-700', 'hover:bg-red-700');
            el.estadoSistema.innerText = 'IA Activa 🟢';
            el.estadoSistema.className = CHIP_ACTIVO;
            mostrarFeedback('neutro', 'Colócate frente a la cámara para iniciar...', 'Esperando detección...');

            bucleDeteccion();
        }

        // Detiene la cámara, guarda la sesión y deja la pantalla lista para otra sesión (sin recargar)
        async function detenerCamara(avisoCamara = null) {
            camaraActiva = false;

            if (mediaStream) {
                mediaStream.getTracks().forEach(track => track.stop());
                mediaStream = null;
            }
            videoElement.srcObject = null;
            canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            const reps = contadorReps;
            const promedio = promedioPuntaje();
            const guardada = await guardarSesion(ejercicioActual);

            el.btnTexto.innerText = 'Iniciar Cámara';
            el.btn.classList.replace('bg-red-600', 'bg-blue-600');
            el.btn.classList.replace('hover:bg-red-700', 'hover:bg-blue-700');
            el.estadoSistema.innerText = 'Sistema Listo 🟡';
            el.estadoSistema.className = CHIP_LISTO;
            inicioSesion = null;

            if (reps < 1) {
                mostrarFeedback('neutro', 'Cámara detenida. No hubo repeticiones, así que no se guardó la sesión.', 'Selecciona un ejercicio');
            } else if (guardada) {
                mostrarFeedback('info', `Sesión guardada: ${reps} repeticiones, técnica ${promedio}/100.`, 'Sesión guardada');
            } else {
                mostrarFeedback('alerta', 'No se pudo guardar la sesión. Revisa tu conexión.', 'Error al guardar');
            }

            // Los números de la sesión quedan visibles hasta iniciar otra o cambiar de ejercicio
            if (avisoCamara) mostrarAvisoCamara(avisoCamara.titulo, avisoCamara.detalle);
        }

        async function toggleCamara() {
            if (!camaraActiva) {
                if (!modeloListo || !camaraSoportada) return; // el botón está bloqueado hasta que cargue el modelo
                await iniciarCamara();
            } else {
                await detenerCamara();
            }
        }

        // ======================================================================
        // EVENTOS
        // ======================================================================
        document.addEventListener('DOMContentLoaded', () => {
            if (!camaraSoportada) {
                mostrarAvisoCamara(
                    'Tu navegador no permite usar la cámara',
                    'Usa una versión actualizada de Chrome, Edge o Firefox y abre la aplicación desde localhost o con HTTPS.'
                );
            }

            cargarModelo();

            // Reiniciar contadores y estados al cambiar de ejercicio en el menú
            document.getElementById('selector-ejercicio').addEventListener('change', async (e) => {
                // Guarda lo hecho con el ejercicio anterior antes de reiniciar (la captura de datos es inmediata)
                const guardado = guardarSesion(ejercicioActual);
                ejercicioActual = e.target.value;
                inicioSesion = camaraActiva ? Date.now() : null;

                reiniciarContadores();
                mostrarFeedback('neutro', 'Ejercicio cambiado. Listo para iniciar.', EJERCICIOS[ejercicioActual].nombre);

                await guardado;
            });
        });

        // Si se cierra la pestaña o se navega a otra página con la cámara activa, guarda la sesión en curso
        window.addEventListener('pagehide', () => {
            if (camaraActiva && contadorReps > 0) {
                camaraActiva = false; // evita guardarla dos veces
                guardarSesion(ejercicioActual, true);
            }
        });
    </script>
</x-app-layout>