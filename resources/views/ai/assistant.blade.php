@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Encabezado --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-stars me-2"></i>
                Asistente IA
            </h4>

            <p class="text-muted mb-0">
                Analiza la información del restaurante mediante lenguaje natural.
            </p>
        </div>

        <span class="badge bg-success-subtle text-success px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>
            Consulta segura
        </span>
    </div>

    <div class="row g-4">

        {{-- Panel principal --}}
        <div class="col-12 col-xl-8">

            {{-- Consulta --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-search me-2"></i>
                        Consulta analítica
                    </h5>
                </div>

                <div class="card-body">
                    <form id="aiAssistantForm">
                        @csrf

                        <div class="mb-3">
                            <label
                                for="assistantQuestion"
                                class="form-label fw-semibold"
                            >
                                ¿Qué deseas analizar?
                            </label>

                            <textarea
                                id="assistantQuestion"
                                class="form-control"
                                rows="3"
                                maxlength="500"
                                placeholder="Ejemplo: ¿Cuáles son los 10 productos más vendidos?"
                                required
                            ></textarea>

                            <div class="text-end mt-1">
                                <small class="text-muted">
                                    <span id="assistantCharacterCount">0</span>/500
                                </small>
                            </div>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-6">
                                <label
                                    for="assistantChartType"
                                    class="form-label fw-semibold"
                                >
                                    Visualización
                                </label>

                                <select
                                    id="assistantChartType"
                                    class="form-select"
                                >
                                    <option value="">
                                        Solo tabla
                                    </option>

                                    <option value="bar">
                                        Gráfico de barras
                                    </option>

                                    <option value="line">
                                        Gráfico de líneas
                                    </option>

                                    <option value="pie">
                                        Gráfico circular
                                    </option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <button
                                    id="assistantAnalyzeButton"
                                    type="submit"
                                    class="btn btn-primary w-100"
                                >
                                    <i class="bi bi-stars me-1"></i>
                                    Analizar información
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-lock-fill me-1"></i>
                                El asistente solo puede consultar información autorizada del sistema.
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Resultados --}}
            <div
                id="assistantResultsCard"
                class="card border-0 shadow-sm d-none"
            >
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-table me-2"></i>
                                Resultados
                            </h5>

                            <small
                                id="assistantResultCount"
                                class="text-muted"
                            ></small>
                        </div>

                        <button
                            id="assistantExportButton"
                            type="button"
                            class="btn btn-outline-success btn-sm"
                        >
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                            Exportar CSV
                        </button>
                    </div>
                </div>

                <div class="card-body">

                    <div
                        id="assistantChartContainer"
                        class="mb-4 d-none"
                        style="position: relative; height: 360px;"
                    >
                        <canvas
                            id="assistantChart"
                            style="max-height: 360px;"
                        ></canvas>
                    </div>

                    <div class="table-responsive">
                        <table
                            id="assistantResultsTable"
                            class="table table-hover align-middle mb-0"
                        >
                            <thead class="table-light"></thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div
                        id="assistantEmptyResults"
                        class="text-center text-muted py-5 d-none"
                    >
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No se encontraron resultados.
                    </div>

                </div>
            </div>

        </div>

        {{-- Panel lateral --}}
        <div class="col-12 col-xl-4">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <ul
                        class="nav nav-pills nav-fill"
                        id="assistantHistoryTabs"
                    >
                        <li class="nav-item">
                            <button
                                class="nav-link active"
                                type="button"
                                data-assistant-tab="history"
                            >
                                <i class="bi bi-clock-history me-1"></i>
                                Historial
                            </button>
                        </li>

                        <li class="nav-item">
                            <button
                                class="nav-link"
                                type="button"
                                data-assistant-tab="favorites"
                            >
                                <i class="bi bi-star-fill me-1"></i>
                                Favoritos
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div
                        id="assistantHistoryList"
                        style="max-height: 600px; overflow-y: auto;"
                    >
                        <div class="text-center text-muted p-4">
                            <div
                                class="spinner-border spinner-border-sm mb-2"
                                role="status"
                            ></div>

                            <div>
                                Cargando historial...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const question = document.getElementById('assistantQuestion');
    const counter = document.getElementById('assistantCharacterCount');
    const historyList = document.getElementById('assistantHistoryList');
    const tabButtons = document.querySelectorAll('[data-assistant-tab]');

    const urls = {
        history: @json(route('ai.assistant.history')),
        favorites: @json(route('ai.assistant.favorites'))
    };

    question.addEventListener('input', function () {
        counter.textContent = question.value.length;
    });

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    async function loadQueries(type = 'history') {

        historyList.innerHTML = `
            <div class="text-center text-muted p-4">
                <div class="spinner-border spinner-border-sm mb-2"></div>
                <div>Cargando...</div>
            </div>
        `;

        try {

            const response = await fetch(urls[type], {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error('No fue posible cargar las consultas.');
            }

            renderQueries(result.data, type);

        } catch (error) {

            historyList.innerHTML = `
                <div class="text-center text-danger p-4">
                    <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                    No fue posible cargar la información.
                </div>
            `;
        }
    }

    function renderQueries(queries, type) {

        historyList.innerHTML = '';

        if (!queries || queries.length === 0) {

            const empty = document.createElement('div');
            empty.className = 'text-center text-muted p-4';

            const icon = document.createElement('i');
            icon.className =
                'bi ' +
                (type === 'favorites'
                    ? 'bi-star'
                    : 'bi-clock-history') +
                ' fs-2 d-block mb-2';

            const message = document.createElement('div');
            message.textContent =
                type === 'favorites'
                    ? 'Aún no tienes consultas favoritas.'
                    : 'Aún no hay consultas en el historial.';

            empty.appendChild(icon);
            empty.appendChild(message);

            historyList.appendChild(empty);
            return;
        }

        queries.forEach(function (query) {

            const item = document.createElement('div');
            item.className = 'border-bottom p-3';

            const header = document.createElement('div');
            header.className =
                'd-flex justify-content-between gap-2';

            const info = document.createElement('div');
            info.className = 'flex-grow-1';

            const questionText = document.createElement('div');
            questionText.className = 'fw-semibold mb-1';
            questionText.textContent = query.question;

            const results = document.createElement('div');
            results.className = 'small text-muted';

            const resultIcon = document.createElement('i');
            resultIcon.className = 'bi bi-table me-1';

            results.appendChild(resultIcon);
            results.appendChild(
                document.createTextNode(
                    Number(query.result_count || 0) +
                    ' resultados'
                )
            );

            const date = document.createElement('div');
            date.className = 'small text-muted mt-1';

            const dateIcon = document.createElement('i');
            dateIcon.className = 'bi bi-calendar3 me-1';

            date.appendChild(dateIcon);
            date.appendChild(
                document.createTextNode(
                    query.created_at || ''
                )
            );

            info.appendChild(questionText);
            info.appendChild(results);
            info.appendChild(date);

            header.appendChild(info);
            item.appendChild(header);

            const actions = document.createElement('div');

            actions.className =
                'd-flex gap-2 mt-3';

            const favoriteButton =
                document.createElement('button');

            favoriteButton.type = 'button';

            favoriteButton.className =
                query.is_favorite
                    ? 'btn btn-warning btn-sm'
                    : 'btn btn-outline-warning btn-sm';

            const favoriteIcon =
                document.createElement('i');

            favoriteIcon.className =
                query.is_favorite
                    ? 'bi bi-star-fill me-1'
                    : 'bi bi-star me-1';

            favoriteButton.appendChild(favoriteIcon);
            favoriteButton.appendChild(
                document.createTextNode(
                    query.is_favorite
                        ? 'Favorito'
                        : 'Marcar favorito'
                )
            );

            favoriteButton.addEventListener(
                'click',
                function () {
                    toggleFavorite(
                        query.id,
                        favoriteButton
                    );
                }
            );

            const deleteButton =
                document.createElement('button');

            deleteButton.type = 'button';

            deleteButton.className =
                'btn btn-outline-danger btn-sm';

            const deleteIcon =
                document.createElement('i');

            deleteIcon.className =
                'bi bi-trash me-1';

            deleteButton.appendChild(deleteIcon);
            deleteButton.appendChild(
                document.createTextNode('Eliminar')
            );

            deleteButton.addEventListener(
                'click',
                function () {
                    deleteQuery(
                        query.id,
                        deleteButton
                    );
                }
            );

            actions.appendChild(favoriteButton);
            actions.appendChild(deleteButton);

            item.appendChild(actions);

            historyList.appendChild(item);
        });
    }

    async function toggleFavorite(queryId, button) {

        button.disabled = true;

        try {

            const response = await fetch(
                `/ai/assistant/${queryId}/favorite`,
                {
                    method: 'PATCH',

                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector(
                                '#aiAssistantForm input[name="_token"]'
                            ).value
                    }
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ||
                    'No fue posible modificar el favorito.'
                );
            }

            const activeTab =
                document.querySelector(
                    '[data-assistant-tab].active'
                );

            loadQueries(
                activeTab
                    ? activeTab.dataset.assistantTab
                    : 'history'
            );

        } catch (error) {

            alert(error.message);

            button.disabled = false;
        }
    }

    async function deleteQuery(queryId, button) {

        const confirmed = window.confirm(
            '¿Deseas eliminar esta consulta del historial?'
        );

        if (!confirmed) {
            return;
        }

        button.disabled = true;

        try {

            const response = await fetch(
                `/ai/assistant/${queryId}`,
                {
                    method: 'DELETE',

                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector(
                                '#aiAssistantForm input[name="_token"]'
                            ).value
                    }
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ||
                    'No fue posible eliminar la consulta.'
                );
            }

            const activeTab =
                document.querySelector(
                    '[data-assistant-tab].active'
                );

            loadQueries(
                activeTab
                    ? activeTab.dataset.assistantTab
                    : 'history'
            );

        } catch (error) {

            alert(error.message);

            button.disabled = false;
        }
    }
    tabButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            tabButtons.forEach(function (tab) {
                tab.classList.remove('active');
            });

            button.classList.add('active');

            loadQueries(button.dataset.assistantTab);
        });
    });

    const form = document.getElementById('aiAssistantForm');
    const analyzeButton = document.getElementById('assistantAnalyzeButton');
    const chartType = document.getElementById('assistantChartType');

    const resultsCard = document.getElementById('assistantResultsCard');
    const resultCount = document.getElementById('assistantResultCount');
    const resultsTable = document.getElementById('assistantResultsTable');
    const emptyResults = document.getElementById('assistantEmptyResults');
    const exportButton = document.getElementById('assistantExportButton');

    let currentRows = [];
    let currentColumns = [];
    let currentQueryId = null;
    let assistantChartInstance = null;

    form.addEventListener('submit', async function (event) {

        event.preventDefault();

        const userQuestion = question.value.trim();

        if (!userQuestion) {
            return;
        }

        analyzeButton.disabled = true;
        question.disabled = true;
        chartType.disabled = true;

        const originalButton = analyzeButton.innerHTML;

        analyzeButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span> Analizando...';

        try {

            const response = await fetch(
                @json(route('ai.assistant.ask')),
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector(
                                '#aiAssistantForm input[name="_token"]'
                            ).value
                    },

                    body: JSON.stringify({
                        question: userQuestion,
                        chart_type: chartType.value || null
                    })
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ||
                    'No fue posible procesar la consulta.'
                );
            }

            renderResults(result);

            /*
             * Actualizamos el historial porque la consulta
             * acaba de almacenarse en ai_queries.
             */
            loadQueries('history');

        } catch (error) {

            resultsCard.classList.remove('d-none');

            resultCount.textContent = '';

            resultsTable.querySelector('thead').innerHTML = '';
            resultsTable.querySelector('tbody').innerHTML = '';

            emptyResults.classList.remove('d-none');

            emptyResults.innerHTML = '';

            const icon = document.createElement('i');
            icon.className =
                'bi bi-exclamation-triangle text-danger fs-1 d-block mb-2';

            const message = document.createElement('div');
            message.className = 'text-danger';
            message.textContent =
                error.message ||
                'No fue posible procesar la consulta.';

            emptyResults.appendChild(icon);
            emptyResults.appendChild(message);

        } finally {

            analyzeButton.disabled = false;
            question.disabled = false;
            chartType.disabled = false;

            analyzeButton.innerHTML = originalButton;
        }
    });

    function renderResults(result) {

        currentQueryId = result.query_id ?? null;

        currentRows = Array.isArray(result.data)
            ? result.data
            : [];

        exportButton.disabled = !currentQueryId;

        resultsCard.classList.remove('d-none');

        const thead = resultsTable.querySelector('thead');
        const tbody = resultsTable.querySelector('tbody');

        thead.innerHTML = '';
        tbody.innerHTML = '';

        /*
         * No usamos innerHTML con datos provenientes
         * de la base de datos.
         */
        if (currentRows.length === 0) {

            currentColumns = [];

            resultCount.textContent = '0 resultados';

            emptyResults.innerHTML = `
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No se encontraron resultados.
            `;

            emptyResults.classList.remove('d-none');

            return;
        }

        emptyResults.classList.add('d-none');

        currentColumns = Object.keys(currentRows[0]);

        resultCount.textContent =
            currentRows.length +
            (currentRows.length === 1
                ? ' resultado'
                : ' resultados');

        const headerRow = document.createElement('tr');

        currentColumns.forEach(function (column) {

            const th = document.createElement('th');
            th.scope = 'col';
            th.textContent = formatColumnName(column);

            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);

        currentRows.forEach(function (row) {

            const tr = document.createElement('tr');

            currentColumns.forEach(function (column) {

                const td = document.createElement('td');

                const value = row[column];

                td.textContent =
                    value === null || value === undefined
                        ? ''
                        : String(value);

                tr.appendChild(td);
            });

            tbody.appendChild(tr);
        });

        renderChart(
            currentRows,
            result.chart_type || null
        );
    }

    function renderChart(rows, selectedType) {

        const container =
            document.getElementById('assistantChartContainer');

        const canvas =
            document.getElementById('assistantChart');

        if (assistantChartInstance) {
            assistantChartInstance.destroy();
            assistantChartInstance = null;
        }

        container.classList.add('d-none');

        if (
            !selectedType ||
            !Array.isArray(rows) ||
            rows.length === 0 ||
            typeof window.Chart === 'undefined'
        ) {
            return;
        }

        const columns = Object.keys(rows[0]);

        if (columns.length < 2) {
            return;
        }

        /*
         * Buscamos primero una columna no numérica
         * para usarla como etiqueta.
         */
        let labelColumn = columns.find(function (column) {

            return rows.some(function (row) {

                const value = row[column];

                return value !== null &&
                    value !== '' &&
                    !Number.isFinite(Number(value));
            });
        });

        /*
         * Si todas las columnas son numéricas,
         * utilizamos la primera como etiqueta.
         */
        if (!labelColumn) {
            labelColumn = columns[0];
        }

        /*
         * Elegimos una columna numérica diferente
         * de la columna utilizada como etiqueta.
         */
        const numericColumn = columns.find(function (column) {

            if (column === labelColumn) {
                return false;
            }

            const values = rows
                .map(function (row) {
                    return row[column];
                })
                .filter(function (value) {
                    return value !== null && value !== '';
                });

            return (
                values.length > 0 &&
                values.every(function (value) {
                    return Number.isFinite(Number(value));
                })
            );
        });

        if (!numericColumn) {
            return;
        }

        const labels = rows.map(function (row) {

            const value = row[labelColumn];

            return value === null || value === undefined
                ? ''
                : String(value);
        });

        const values = rows.map(function (row) {
            return Number(row[numericColumn]);
        });

        /*
         * Un gráfico circular con demasiadas categorías
         * pierde legibilidad. En ese caso dejamos la tabla.
         */
        if (
            selectedType === 'pie' &&
            rows.length > 12
        ) {
            return;
        }

        container.classList.remove('d-none');

        assistantChartInstance = new window.Chart(
            canvas.getContext('2d'),
            {
                type: selectedType,

                data: {
                    labels: labels,

                    datasets: [
                        {
                            label:
                                formatColumnName(numericColumn),

                            data: values,

                            borderWidth: 2
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display:
                                selectedType === 'pie'
                        }
                    },

                    scales:
                        selectedType === 'pie'
                            ? {}
                            : {
                                y: {
                                    beginAtZero: true
                                }
                            }
                }
            }
        );
    }

    function formatColumnName(column) {

        return String(column)
            .replace(/_/g, ' ')
            .replace(/\b\w/g, function (letter) {
                return letter.toUpperCase();
            });
    }

    exportButton.disabled = true;

    exportButton.addEventListener('click', async function () {

        if (!currentQueryId) {
            alert('Primero realiza una consulta para poder exportarla.');
            return;
        }

        const originalButton = exportButton.innerHTML;
        exportButton.disabled = true;

        exportButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span> Exportando...';

        try {

            const response = await fetch(
                @json(route('ai.assistant.export.csv')),
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'text/csv, application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector(
                                '#aiAssistantForm input[name="_token"]'
                            ).value
                    },

                    body: JSON.stringify({
                        query_id: currentQueryId
                    })
                }
            );

            if (!response.ok) {

                let message = 'No fue posible exportar el archivo CSV.';

                try {
                    const error = await response.json();

                    if (error.message) {
                        message = error.message;
                    }
                } catch (e) {
                    // La respuesta no era JSON.
                }

                throw new Error(message);
            }

            const blob = await response.blob();

            const disposition =
                response.headers.get('Content-Disposition') || '';

            let filename = 'asistente_ia.csv';

            const match = disposition.match(
                /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/
            );

            if (match && match[1]) {
                filename = match[1].replace(/['"]/g, '');
            }

            const downloadUrl = URL.createObjectURL(blob);

            const link = document.createElement('a');

            link.href = downloadUrl;
            link.download = filename;

            document.body.appendChild(link);
            link.click();
            link.remove();

            URL.revokeObjectURL(downloadUrl);

        } catch (error) {

            alert(
                error.message ||
                'No fue posible exportar el archivo CSV.'
            );

        } finally {

            exportButton.disabled = !currentQueryId;
            exportButton.innerHTML = originalButton;
        }
    });

    loadQueries('history');
});
</script>
@endsection