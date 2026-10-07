@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="bi bi-robot me-2"></i>
                                Chat IA
                            </h4>

                            <p class="text-muted mb-0 small">
                                Consulta información del restaurante utilizando lenguaje natural.
                            </p>
                        </div>

                        <span class="badge bg-success-subtle text-success px-3 py-2">
                            <i class="bi bi-database-check me-1"></i>
                            Datos del sistema
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">

                    <div
                        id="aiMessages"
                        class="p-4"
                        style="height: 500px; overflow-y: auto;"
                    >
                        <div class="d-flex gap-3 mb-4">
                            <div
                                class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 42px; height: 42px;"
                            >
                                <i class="bi bi-robot"></i>
                            </div>

                            <div
                                class="bg-light rounded-3 p-3"
                                style="max-width: 80%;"
                            >
                                <strong>Chat IA</strong>

                                <div class="mt-1">
                                    Hola. Puedo ayudarte a consultar información
                                    registrada en el restaurante.
                                </div>

                                <div class="text-muted small mt-2">
                                    Por ejemplo: “¿Cuáles son los productos más vendidos?”
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-top p-3">
                        <form id="aiChatForm">
                            @csrf

                            <div class="input-group">
                                <textarea
                                    id="aiMessage"
                                    class="form-control"
                                    rows="2"
                                    maxlength="500"
                                    placeholder="Escribe una pregunta sobre el restaurante..."
                                    required
                                ></textarea>

                                <button
                                    id="aiSendButton"
                                    class="btn btn-primary px-4"
                                    type="submit"
                                >
                                    <i class="bi bi-send-fill me-1"></i>
                                    Enviar
                                </button>
                            </div>

                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">
                                    La IA solo consulta información autorizada.
                                </small>

                                <small class="text-muted">
                                    <span id="aiCharacterCount">0</span>/500
                                </small>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('aiChatForm');
    const input = document.getElementById('aiMessage');
    const button = document.getElementById('aiSendButton');
    const messages = document.getElementById('aiMessages');
    const counter = document.getElementById('aiCharacterCount');

    input.addEventListener('input', function () {
        counter.textContent = input.value.length;
    });

    function addMessage(type, text) {

        const wrapper = document.createElement('div');

        wrapper.className =
            'd-flex gap-3 mb-4 ' +
            (type === 'user' ? 'justify-content-end' : '');

        const bubble = document.createElement('div');

        bubble.className =
            type === 'user'
                ? 'bg-primary text-white rounded-3 p-3'
                : 'bg-light rounded-3 p-3';

        bubble.style.maxWidth = '80%';

        /*
         * textContent evita interpretar HTML recibido
         * desde respuestas externas.
         */
        bubble.textContent = text;

        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);

        messages.scrollTop = messages.scrollHeight;
    }

    form.addEventListener('submit', async function (event) {

        event.preventDefault();

        const message = input.value.trim();

        if (!message) {
            return;
        }

        addMessage('user', message);

        input.value = '';
        counter.textContent = '0';

        button.disabled = true;
        input.disabled = true;

        const originalButton = button.innerHTML;

        button.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

        try {

            const response = await fetch(
                '{{ route('ai.chat.ask') }}',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector(
                                'input[name="_token"]'
                            ).value
                    },

                    body: JSON.stringify({
                        message: message
                    })
                }
            );

            const data = await response.json();

            if (data.success) {
                addMessage('assistant', data.answer);
            } else {
                addMessage(
                    'assistant',
                    data.message ||
                    'No fue posible procesar la consulta.'
                );
            }

        } catch (error) {

            addMessage(
                'assistant',
                'No se pudo establecer comunicación con el servidor.'
            );

        } finally {

            button.disabled = false;
            input.disabled = false;
            button.innerHTML = originalButton;

            input.focus();
        }
    });

});
</script>
@endsection