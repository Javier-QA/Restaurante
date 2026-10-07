<?php

namespace App\Services\AI;

use RuntimeException;

class AiQueryService
{
    public function __construct(
        protected AiService $ai,
        protected AiContextService $context,
        protected AiSqlService $sql
    ) {
    }

    /**
     * Convierte una pregunta del usuario en SQL de solo lectura.
     */
    public function generateSql(string $question): string
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

        $response = $this->ai->chat([
            [
                'role' => 'system',
                'content' => $this->context->getSqlContext(),
            ],
            [
                'role' => 'user',
                'content' =>
                    "Genera únicamente la consulta SQL necesaria para responder esta pregunta:\n\n"
                    . $question
                    . "\n\nDevuelve solamente SQL, sin Markdown ni explicaciones.",
            ],
        ], 0.1, 1000);

        $sql = $this->sql->cleanSql($response);

        $this->sql->validate($sql);

        return $sql;
    }
}