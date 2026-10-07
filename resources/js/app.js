import './bootstrap'; // Configuración por defecto de Laravel
import 'bootstrap';   // Importar la librería visual de Bootstrap 5

// Chart.js para gráficos del Asistente IA
import Chart from 'chart.js/auto';

window.Chart = Chart;

/*
 * Chat IA flotante.
 *
 * Se activa únicamente cuando el endpoint protegido confirma que
 * el usuario autenticado tiene acceso de administrador.
 */
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/ai/settings', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok || !response.url.endsWith('/ai/settings')) {
            return;
        }

        const data = await response.json();

        if (!data.admin || document.getElementById('ai-floating-widget')) {
            return;
        }

        const style = document.createElement('style');
        style.textContent = `
            #ai-floating-widget {
                position: fixed;
                right: 24px;
                bottom: 24px;
                z-index: 1080;
                font-family: inherit;
            }

            #ai-floating-bubble {
                width: 58px;
                height: 58px;
                border: 0;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                color: #fff;
                background: linear-gradient(135deg, #063970, #ff8c00);
                box-shadow: 0 12px 30px rgba(6,57,112,.28);
                transition: transform .2s ease, box-shadow .2s ease;
            }

            #ai-floating-bubble:hover {
                transform: translateY(-3px) scale(1.03);
                box-shadow: 0 16px 34px rgba(6,57,112,.35);
            }

            #ai-floating-window {
                display: none;
                position: absolute;
                right: 0;
                bottom: 72px;
                width: min(390px, calc(100vw - 32px));
                height: min(620px, calc(100vh - 110px));
                background: #fff;
                border: 1px solid #dce7f1;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 55px rgba(6,57,112,.22);
            }

            #ai-floating-window.open {
                display: flex;
                flex-direction: column;
            }

            .ai-floating-header {
                padding: 14px 16px;
                color: #fff;
                background: linear-gradient(135deg, #063970, #0b4f8a);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .ai-floating-title {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .ai-floating-icon {
                width: 38px;
                height: 38px;
                border-radius: 12px;
                display: grid;
                place-items: center;
                background: rgba(255,255,255,.16);
            }

            .ai-floating-subtitle {
                font-size: .72rem;
                opacity: .8;
            }

            .ai-floating-close {
                border: 0;
                background: transparent;
                color: #fff;
                font-size: 1.2rem;
                cursor: pointer;
            }

            .ai-floating-messages {
                flex: 1;
                overflow-y: auto;
                padding: 14px;
                background: #f6f9fc;
            }

            .ai-floating-message {
                max-width: 88%;
                padding: 10px 12px;
                border-radius: 14px;
                margin-bottom: 10px;
                white-space: pre-wrap;
                line-height: 1.45;
                font-size: .88rem;
            }

            .ai-floating-message.bot {
                background: #fff;
                border: 1px solid #e1eaf2;
                color: #172033;
                margin-right: auto;
            }

            .ai-floating-message.user {
                color: #fff;
                background: #063970;
                margin-left: auto;
            }

            .ai-floating-suggestions {
                padding: 10px 14px 0;
                background: #f6f9fc;
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .ai-floating-suggestion {
                border: 1px solid #cfddea;
                background: #fff;
                color: #17446f;
                border-radius: 999px;
                padding: 6px 9px;
                font-size: .72rem;
                cursor: pointer;
            }

            .ai-floating-suggestion:hover {
                border-color: #ff8c00;
                color: #d97706;
            }

            .ai-floating-form {
                padding: 10px;
                border-top: 1px solid #e1e8ef;
                background: #fff;
                display: flex;
                gap: 8px;
            }

            .ai-floating-input {
                flex: 1;
                min-width: 0;
                resize: none;
                border: 1px solid #cfd9e3;
                border-radius: 12px;
                padding: 9px 10px;
                outline: none;
                font: inherit;
            }

            .ai-floating-input:focus {
                border-color: #0b84c6;
                box-shadow: 0 0 0 3px rgba(11,132,198,.10);
            }

            .ai-floating-send {
                width: 44px;
                height: 44px;
                border: 0;
                border-radius: 12px;
                color: #fff;
                background: #ff8c00;
                cursor: pointer;
            }

            .ai-floating-send:disabled {
                opacity: .55;
                cursor: not-allowed;
            }

            @media (max-width: 600px) {
                #ai-floating-widget {
                    right: 14px;
                    bottom: 14px;
                }

                #ai-floating-window {
                    bottom: 68px;
                }
            }
        `;
        document.head.appendChild(style);

        const widget = document.createElement('div');
        widget.id = 'ai-floating-widget';
        widget.innerHTML = `
            <div id="ai-floating-window" role="dialog" aria-label="Chat IA">
                <div class="ai-floating-header">
                    <div class="ai-floating-title">
                        <div class="ai-floating-icon"><i class="bi bi-robot"></i></div>
                        <div>
                            <strong>Chat IA</strong>
                            <div class="ai-floating-subtitle">Asistente del restaurante</div>
                        </div>
                    </div>
                    <button type="button" class="ai-floating-close" aria-label="Cerrar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="ai-floating-messages" id="ai-floating-messages">
                    <div class="ai-floating-message bot">
                        Hola, administrador. ¿Qué deseas consultar?
                    </div>
                </div>

                <div class="ai-floating-suggestions">
                    <button type="button" class="ai-floating-suggestion" data-question="¿Cuáles son los productos más vendidos?">Más vendidos</button>
                    <button type="button" class="ai-floating-suggestion" data-question="¿Cuánto se vendió hoy?">Ventas</button>
                    <button type="button" class="ai-floating-suggestion" data-question="¿Qué productos tienen poco stock?">Stock bajo</button>
                    <button type="button" class="ai-floating-suggestion" data-question="¿Cuáles son los métodos de pago más utilizados?">Pagos</button>
                </div>

                <form class="ai-floating-form" id="ai-floating-form">
                    <textarea
                        class="ai-floating-input"
                        id="ai-floating-input"
                        rows="1"
                        maxlength="500"
                        placeholder="Escribe una consulta..."
                        aria-label="Pregunta para Chat IA"
                    ></textarea>
                    <button class="ai-floating-send" type="submit" aria-label="Enviar">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>

            <button
                id="ai-floating-bubble"
                type="button"
                aria-label="Abrir Chat IA"
                title="Chat IA"
            >
                <i class="bi bi-stars fs-4"></i>
            </button>
        `;

        document.body.appendChild(widget);

        const bubble = document.getElementById('ai-floating-bubble');
        const panel = document.getElementById('ai-floating-window');
        const close = widget.querySelector('.ai-floating-close');
        const form = document.getElementById('ai-floating-form');
        const input = document.getElementById('ai-floating-input');
        const messages = document.getElementById('ai-floating-messages');
        const send = widget.querySelector('.ai-floating-send');

        const addMessage = (text, type = 'bot') => {
            const item = document.createElement('div');
            item.className = `ai-floating-message ${type}`;
            item.textContent = text;
            messages.appendChild(item);
            messages.scrollTop = messages.scrollHeight;
        };

        const addDataTable = (rows) => {
            if (!Array.isArray(rows) || rows.length === 0) {
                return;
            }

            const safeRows = rows.slice(0, 20);
            const columns = Object.keys(safeRows[0]).slice(0, 5);

            if (columns.length === 0) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'ai-floating-message bot';
            wrapper.style.maxWidth = '100%';
            wrapper.style.overflowX = 'auto';

            const table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            table.style.fontSize = '.76rem';

            const thead = document.createElement('thead');
            const headRow = document.createElement('tr');

            columns.forEach(column => {
                const th = document.createElement('th');
                th.textContent = column.replaceAll('_', ' ');
                th.style.textAlign = 'left';
                th.style.padding = '6px';
                th.style.borderBottom = '1px solid #dce7f1';
                headRow.appendChild(th);
            });

            thead.appendChild(headRow);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');

            safeRows.forEach(row => {
                const tr = document.createElement('tr');

                columns.forEach(column => {
                    const td = document.createElement('td');
                    const value = row[column];
                    td.textContent = value === null || value === undefined
                        ? 'Sin dato'
                        : String(value);
                    td.style.padding = '6px';
                    td.style.borderBottom = '1px solid #edf2f7';
                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            wrapper.appendChild(table);
            messages.appendChild(wrapper);
            messages.scrollTop = messages.scrollHeight;
        };

        const ask = async (question) => {
            question = question.trim();

            if (!question || send.disabled) {
                return;
            }

            addMessage(question, 'user');
            input.value = '';
            send.disabled = true;

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch('/ai/chat/ask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf || '',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ message: question }),
                });

                const data = await response.json();

                if (data.success) {
                    addMessage(data.answer);
                    addDataTable(data.data);
                } else {
                    addMessage(
                        data.message || 'No fue posible procesar la consulta.'
                    );
                }
            } catch (error) {
                addMessage('No se pudo establecer comunicación con el servidor.');
            } finally {
                send.disabled = false;
                input.focus();
            }
        };

        bubble.addEventListener('click', () => {
            panel.classList.toggle('open');
            if (panel.classList.contains('open')) {
                input.focus();
            }
        });

        close.addEventListener('click', () => {
            panel.classList.remove('open');
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            ask(input.value);
        });

        widget.querySelectorAll('.ai-floating-suggestion').forEach(button => {
            button.addEventListener('click', () => {
                ask(button.dataset.question || '');
            });
        });
    } catch (error) {
        // El widget es opcional; nunca debe impedir cargar el POS.
        console.warn('Chat IA flotante no disponible.');
    }
});
