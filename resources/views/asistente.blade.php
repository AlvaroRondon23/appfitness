<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Asistente Virtual de IA') }}
        </h2>
    </x-slot>

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
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Asistente Deportivo IA</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Personalizado con tus datos bioestadísticos en tiempo real</p>
                    </div>
                </div>

                <!-- Historial de Mensajes -->
                <div id="chat-box" class="flex-1 p-4 overflow-y-auto space-y-4 text-xs sm:text-sm">
                    <div class="flex items-start gap-2.5">
                        <div class="bg-purple-50 dark:bg-purple-950/40 border border-purple-100 dark:border-purple-900/40 p-3.5 rounded-2xl max-w-[80%] text-gray-700 dark:text-gray-300">
                            ¡Hola, <strong>{{ Auth::user()->name }}</strong>! 👋 Soy tu asistente de rendimiento físico. Conozco tus parámetros actuales y estoy preparado para responder preguntas sobre tus rutinas, carga de trabajo o nutrición. ¿En qué te ayudo hoy?
                        </div>
                    </div>
                </div>

                <!-- Formulario de Entrada -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/40 rounded-b-2xl">
                    <form id="chat-form" onsubmit="enviarConsulta(event)" class="flex gap-2">
                        @csrf
                        <input type="text" id="prompt-input" placeholder="Escribe tu consulta (ej: ¿Cuánta proteína debo consumir según mi objetivo?)..." class="flex-1 text-xs sm:text-sm bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500" required>
                        <button type="submit" id="btn-enviar" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs sm:text-sm font-bold rounded-xl transition-colors flex items-center gap-1">
                            <span>Enviar</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        async function enviarConsulta(e) {
            e.preventDefault();
            
            const input = document.getElementById('prompt-input');
            const btn = document.getElementById('btn-enviar');
            const chatBox = document.getElementById('chat-box');
            const prompt = input.value.trim();

            if (!prompt) return;

            // 1. Mostrar el mensaje del usuario de inmediato
            chatBox.innerHTML += `
                <div class="flex items-start justify-end gap-2.5">
                    <div class="bg-purple-600 text-white p-3.5 rounded-2xl max-w-[80%]">
                        ${escapeHtml(prompt)}
                    </div>
                </div>
            `;

            input.value = '';
            input.disabled = true;
            btn.disabled = true;
            chatBox.scrollTop = chatBox.scrollHeight;

            // 2. Mostrar indicador de "Procesando respuesta..."
            const loadingId = 'loading-' + Date.now();
            chatBox.innerHTML += `
                <div id="${loadingId}" class="flex items-start gap-2.5">
                    <div class="bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-3.5 rounded-2xl text-gray-500 text-xs flex items-center gap-2">
                        <span class="animate-pulse">Procesando respuesta...</span>
                    </div>
                </div>
            `;
            chatBox.scrollTop = chatBox.scrollHeight;

            // 3. Enviar la consulta al controlador
            try {
                const response = await fetch("{{ route('asistente.consultar') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prompt: prompt, mensaje: prompt })
                });

                const data = await response.json();
                
                // Borrado seguro del loader
                const loader = document.getElementById(loadingId);
                if (loader) loader.remove();

                // Soporta tanto data.respuesta como data.response
                const textoRespuesta = data.respuesta || data.response || 'No se recibió una respuesta del servidor.';

                // 4. Mostrar la respuesta recibida de la IA
                chatBox.innerHTML += `
                    <div class="flex items-start gap-2.5">
                        <div class="bg-purple-50 dark:bg-purple-950/40 border border-purple-100 dark:border-purple-900/40 p-3.5 rounded-2xl max-w-[80%] text-gray-700 dark:text-gray-300 leading-relaxed">
                            ${textoRespuesta.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                `;
            } catch (error) {
                // Borrado seguro del loader en caso de fallo de red
                const loader = document.getElementById(loadingId);
                if (loader) loader.remove();

                chatBox.innerHTML += `
                    <div class="flex items-start gap-2.5">
                        <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-900/40 p-3.5 rounded-2xl text-rose-700 dark:text-rose-400">
                            Ocurrió un error al intentar comunicar con el servidor de IA.
                        </div>
                    </div>
                `;
            } finally {
                input.disabled = false;
                btn.disabled = false;
                input.focus();
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }

        function escapeHtml(text) {
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }
    </script>
</x-app-layout>