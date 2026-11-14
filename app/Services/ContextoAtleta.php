<?php

namespace App\Services;

use App\Models\SesionEntrenamiento;
use App\Models\User;

/**
 * Construye el texto con los datos del usuario que se envía al asistente de IA
 * como instrucción de sistema, para que sus respuestas sean personalizadas.
 */
class ContextoAtleta
{
    public function __construct(
        private PerfilCalculos $calculos,
        private EstadisticasEntrenamiento $estadisticas,
    ) {
    }

    public function construir(User $user): string
    {
        $lineas = [
            'Eres el asistente deportivo de una aplicación de rendimiento físico.',
            'Responde siempre en español, de forma clara y breve (unas 200 palabras como máximo, salvo que el usuario pida más detalle).',
            'Usa los datos del usuario solo cuando sean relevantes y no los repitas sin necesidad.',
            'No diagnostiques enfermedades ni sustituyas a un médico. Si hay dolor, lesiones o condiciones médicas, recomienda consultar a un profesional de la salud.',
            'Si la consulta no trata de ejercicio, nutrición, descanso o salud general, redirige amablemente al tema.',
            'Basa tus respuestas solo en los datos de abajo; si falta información, dilo en lugar de inventarla.',
            '',
        ];

        $perfil = $user->physicalProfile;

        if ($perfil) {
            $c = $this->calculos->para($perfil);
            $condiciones = trim((string) $perfil->medical_conditions);
            $condiciones = $condiciones !== '' ? mb_substr($condiciones, 0, 500) : 'Ninguna declarada';

            $lineas[] = 'DATOS DEL USUARIO:';
            $lineas[] = "- Edad: {$c['edad']} años";
            $lineas[] = "- Género: {$perfil->gender}";
            $lineas[] = "- Peso: {$perfil->weight} kg | Estatura: {$perfil->height} cm";
            $lineas[] = "- IMC: {$c['imc']} ({$c['categoria_imc']})";
            $lineas[] = "- Nivel de actividad: {$perfil->activity_level}";
            $lineas[] = "- Gasto basal estimado: {$c['tmb']} kcal/día | Gasto diario estimado: {$c['tdee']} kcal/día";
            $lineas[] = "- Lesiones o condiciones médicas declaradas: {$condiciones}";
        } else {
            $lineas[] = 'El usuario aún no ha completado su perfil físico.';
        }

        $stats = $this->estadisticas->resumen($user);
        $lineas[] = '';
        $lineas[] = 'ENTRENAMIENTOS REGISTRADOS EN LA APP:';

        if ($stats['sesiones'] === 0) {
            $lineas[] = '- Todavía no hay sesiones registradas.';
        } else {
            $minutos = intdiv($stats['segundos'], 60);
            $lineas[] = "- Total: {$stats['sesiones']} sesiones, {$stats['repeticiones']} repeticiones, {$minutos} minutos.";
            $lineas[] = "- Días activos en los últimos 30 días: {$stats['dias_activos']} | Racha actual: {$stats['racha']} días.";

            foreach ($stats['por_ejercicio'] as $e) {
                $lineas[] = "- {$e->ejercicio}: {$e->sesiones} sesiones, {$e->repeticiones} repeticiones (mejor sesión: {$e->mejor}).";
            }

            $ultimas = SesionEntrenamiento::where('user_id', $user->id)->latest()->take(5)->get();
            $lineas[] = '- Últimas sesiones:';
            foreach ($ultimas as $s) {
                $lineas[] = "  * {$s->created_at->format('d/m/Y')}: {$s->ejercicio}, {$s->repeticiones} repeticiones, {$s->tiempo_segundos} s.";
            }
        }

        return implode("\n", $lineas);
    }
}
