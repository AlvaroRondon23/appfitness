<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Asistente Virtual de IA') }}
        </h2>
    </x-slot>

    <style>
        .md-respuesta p { margin: .4rem 0; }
        .md-respuesta p:first-child { margin-top: 0; }
        .md-respuesta p:last-child { margin-bottom: 0; }
        .md-respuesta ul { list-style: disc; padding-left: 1.25rem; margin: .4rem 0; }
        .md-respuesta ol { list-style: decimal; padding-left: 1.25rem; margin: .4rem 0; }
        .md-respuesta li { margin: .15rem 0; }
        .md-respuesta h1, .md-respuesta h2, .md-respuesta h3, .md-respuesta h4 { font-weight: 700; margin: .6rem 0 .3rem; }
        .md-respuesta strong { font-weight: 700; }
    </style>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col h-[600px]">

                <!-- Encabezado del Chat -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Asistente Deportivo IA</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Personalizado con tu perfil físico y tu historial de entrenamiento</p>
                    </div>

                    @if ($historial->isNotEmpty())
                        <form method="POST" action="{{ route('asistente.limpiar') }}" onsubmit="return confirm('¿Borrar toda la conversación guardada con el asistente?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-gray-500 hover:text-rose-600 dark:text-gray-400 dark:hover:text-rose-400 transition-colors">
                                Borrar conversación
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Historial de Mensajes -->
                <div id="chat-box" class="flex-1 p-4 overflow-y-auto space-y-4 text-xs sm:text-sm">
                    <div class="flex items-start gap-2.5">
                        <div class="bg-purple-50 dark:bg-purple-950/40 border border-purple-100 dark:border-purple-900/40 p-3.5 rounded-2xl max-w-[80%] text-gray-700 dark:text-gray-300">
                            ¡Hola, <strong>{{ Auth::user()->name }}</strong>! 👋 Soy tu asistente de rendimiento físico. Conozco tu perfil y tus sesiones registradas, y estoy preparado para responder preguntas sobre tus rutinas, carga de trabajo o nutrición. ¿En qué te ayudo hoy?
                        </div>
                    </div>

                    @foreach ($historial as $consulta)
                        <div class="flex items-start justify-end gap-2.5">
                            <div class="bg-purple-600 text-white p-3.5 rounded-2xl max-w-[80%]">{{ $consulta->pregunta }}</div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <div class="md-respuesta bg-purple-50 dark:bg-purple-950/40 border border-purple-100 dark:border-purple-900/40 p-3.5 rounded-2xl max-w-[80%] text-gray-700 dark:text-gray-300 leading-relaxed">
                                {!! \Illuminate\Support\Str::markdown($consulta->respuesta, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Formulario de Entrada -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/40 rounded-b-2xl">
                    <form id="chat-form" onsubmit="enviarConsulta(event)" class="flex gap-2">
                        @csrf
                        <input type="text" id="prompt-input" maxlength="1000" placeholder="Escribe tu consulta (ej: ¿Cuánta proteína debo consumir según mi objetivo?)..." class="flex-1 text-xs sm:text-sm bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500" required>
                        <button type="submit" id="btn-enviar" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs sm:text-sm font-bold rounded-xl transition-colors flex items-center gap-1">
                            <span>Enviar</span>
                        </button>
                    </form>
                    <p class="mt-2 text-[11px] text-gray-400 dark:text-gray-500">
                        Tus consultas se guardan para darte mejores respuestas y se envían, junto con tu perfil, a un servicio de IA externo. Este asistente no sustituye a un profesional de la salud.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        document.addEventListener('DOMContentLoaded', () => { chatBox.scrollTop = chatBox.scrollHeight; });

        function burbujaIA(contenidoHtml, esError = false) {
            const clases = esError
                ? 'bg-rose-50 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-900/40 text-rose-700 dark:text-rose-400'
                : 'bg-purple-50 dark:bg-purple-950/40 border border-purple-100 dark:border-purple-900/40 text-gray-700 dark:text-gray-300';
            chatBox.insertAdjacentHTML('beforeend', `
                <div class="flex items-start gap-2.5">
                    <div class="md-respuesta ${clases} p-3.5 rounded-2xl max-w-[80%] leading-relaxed">${contenidoHtml}</div>
                </div>
            `);
        }

        async function enviarConsulta(e) {
            e.preventDefault();

            const input = document.getElementById('prompt-input');
            const btn = document.getElementById('btn-enviar');
            const prompt = input.value.trim();

            if (!prompt) return;

            // 1. Mostrar el mensaje del usuario de inmediato
            chatBox.insertAdjacentHTML('beforeend', `
                <div class="flex items-start justify-end gap-2.5">
                    <div class="bg-purple-600 text-white p-3.5 rounded-2xl max-w-[80%]">${escapeHtml(prompt)}</div>
                </div>
            `);

            input.value = '';
            input.disabled = true;
            btn.disabled = true;
            chatBox.scrollTop = chatBox.scrollHeight;

            // 2. Indicador de "Procesando respuesta..."
            const loadingId = 'loading-' + Date.now();
            chatBox.insertAdjacentHTML('beforeend', `
                <div id="${loadingId}" class="flex items-start gap-2.5">
                    <div class="bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-3.5 rounded-2xl text-gray-500 text-xs flex items-center gap-2">
                        <span class="animate-pulse">Procesando respuesta...</span>
                    </div>
                </div>
            `);
            chatBox.scrollTop = chatBox.scrollHeight;

            // 3. Enviar la consulta al controlador
            try {
                const response = await fetch("{{ route('asistente.consultar') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prompt: prompt })
                });

                let data = {};
                try { data = await response.json(); } catch (_) {}

                const loader = document.getElementById(loadingId);
                if (loader) loader.remove();

                if (response.ok && data.html) {
                    // El HTML lo genera el servidor a partir de markdown, con el HTML crudo escapado
                    burbujaIA(data.html);
                } else {
                    let mensaje = data.respuesta
                        || (data.errors && data.errors.prompt && data.errors.prompt[0])
                        || data.message;

                    if (!mensaje) {
                        mensaje = response.status === 429
                            ? 'Has enviado demasiadas consultas seguidas. Espera un momento e inténtalo de nuevo.'
                            : 'No se recibió una respuesta del servidor.';
                    }
                    burbujaIA(escapeHtml(mensaje), true);
                }
            } catch (error) {
                const loader = document.getElementById(loadingId);
                if (loader) loader.remove();
                burbujaIA('Ocurrió un error al intentar comunicar con el servidor de IA.', true);
            } finally {
                input.disabled = false;
                btn.disabled = false;
                input.focus();
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }

        function escapeHtml(text) {
            return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }
    </script>
</x-app-layout>
