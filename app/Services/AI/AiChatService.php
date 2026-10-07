<?php

namespace App\Services\AI;

use RuntimeException;

class AiChatService
{
    public function __construct(
        protected AiService $ai,
        protected AiQueryService $queryService,
        protected AiSqlService $sqlService,
        protected AiContextService $context
    ) {
    }

    /**
     * Responde una pregunta utilizando información real del restaurante.
     */
    public function ask(string $question): array
    {
        $question = trim($question);

        if ($question === '') {
            throw new RuntimeException(
                'La pregunta no puede estar vacía.'
            );
        }

        if (mb_strlen($question) > 500) {
            throw new RuntimeException(
                'La pregunta supera el límite de 500 caracteres.'
            );
        }

        // 1. Gemini genera SQL.
        $sql = $this->queryService->generateSql($question);

        // 2. El SQL vuelve a validarse y se ejecuta en modo seguro.
        $rows = $this->sqlService->execute($sql);

        // 3. Convertimos los resultados a JSON para que Gemini los interprete.
        $data = json_encode(
            $rows,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT
        );

        if ($data === false) {
            throw new RuntimeException(
                'No se pudieron preparar los resultados para la IA.'
            );
        }

        // 4. Gemini convierte los datos técnicos en una respuesta natural.
        $answer = $this->ai->chat([
            [
                'role' => 'system',
                'content' => $this->context->getChatContext()
                    . "\n\n"
                    . "Debes responder exclusivamente usando los datos "
                    . "proporcionados. No inventes cifras ni productos. "
                    . "Si no existen resultados, indícalo claramente. "
                    . "No muestres SQL al usuario. "
                    . "Usa soles (S/) cuando correspondan importes monetarios."
            ],
            [
                'role' => 'user',
                'content' =>
                    "Pregunta del usuario:\n"
                    . $question
                    . "\n\n"
                    . "Datos obtenidos de la base de datos:\n"
                    . $data
                    . "\n\n"
                    . "Responde en español de manera clara y concisa."
            ],
        ], 0.2, 1200);

        return [
            'question' => $question,
            'answer' => $answer,
            'sql' => $sql,
            'data' => $rows,
            'total_rows' => count($rows),
        ];
    }
}