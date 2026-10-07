/* Asistente IA: preguntas en lenguaje natural -> tabla + gráfico + resumen + SQL + CSV */
(() => {
  const { $, esc, money, toast, call, mdl } = window.SP;
  const API = window.API_IA;
  const SUG = ['¿Cuáles fueron los 10 platos más vendidos este mes?', 'Ventas por día de los últimos 15 días', '¿Cuánto vendimos por método de pago esta semana?', 'Ventas por mozo este mes',
    '¿Qué insumos tienen stock bajo?', 'Utilidad por categoría este mes', '¿Qué horas tienen más ventas?', 'Gastos por categoría este mes'];
  const MONEY = /(total|importe|ingreso|venta|monto|utilidad|costo|precio|valor|deuda|gasto|margen|saldo|promedio|propina|descuento|impuesto|ticket)/i;
  const NOMONEY = /(pct|porcentaje|cantidad|num_|nro|veces|minutos|hora)/i;
  const nf = (n, d = 0) => window.SP.num(n, d);
  const cel = (v, c) => v === null || v === undefined ? '—' : typeof v === 'number' ? (MONEY.test(c) && !NOMONEY.test(c) ? money(v) : nf(v, Number.isInteger(v) ? 0 : 2)) : esc(String(v));
  const chartTheme = () => { const c = getComputedStyle(document.querySelector('.sys-ia')); return { primary: c.getPropertyValue('--accent').trim(), text: c.getPropertyValue('--muted').trim(), line: c.getPropertyValue('--line').trim() }; };
  let chartResult;
  new MutationObserver(() => { if (chartResult) dibujar(chartResult.r, chartResult.g); }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-color-mode'] });
  new MutationObserver(() => { if (chartResult) dibujar(chartResult.r, chartResult.g); }).observe(document.body, { attributes: true, attributeFilter: ['class'] });
  let ST = null, last = null, chart = null, busy = false;

  const ta = $('#iaQ'); ta.addEventListener('input', () => { ta.style.height = 'auto'; ta.style.height = Math.min(120, ta.scrollHeight) + 'px'; });
  ta.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); $('#frmAsk').requestSubmit(); } });
  $('#iaChips').innerHTML = SUG.slice(0, 5).map(s => `<button type="button" class="ia-chip">${esc(s)}</button>`).join('');
  $('#iaChips').onclick = e => { const c = e.target.closest('.ia-chip'); if (c) { ta.value = c.textContent; $('#frmAsk').requestSubmit(); } };

  async function estado() {
    try { ST = await call(API, 'estado'); } catch (x) { toast(x.message, 'err'); return; }
    $('#iaSetup').style.display = ST.listo ? 'none' : '';
    paneles();
    if (new URLSearchParams(location.search).get("configurar") === "1" && !window.__sysIaConfigOpened) { window.__sysIaConfigOpened = true; abrirCfg(); }
  }
  function paneles() {
    const it = (c, fav) => `<div class="it" data-id="${c.id}"><div class="tx">${esc(c.titulo || c.pregunta)}<small>${esc(c.pregunta)}</small></div>
      <button data-fav="${fav ? 0 : 1}" title="${fav ? 'Quitar de favoritas' : 'Marcar favorita'}"><i class="bi ${fav ? 'bi-star-fill text-warning' : 'bi-star'}"></i></button><button data-del title="Borrar"><i class="bi bi-trash3"></i></button></div>`;
    $('#iaFav').innerHTML = ST.favoritos.length ? ST.favoritos.map(c => it(c, true)).join('') : '<div class="small text-muted">Marca con ⭐ las consultas que repites.</div>';
    $('#iaHis').innerHTML = ST.historial.length ? ST.historial.map(c => it(c, false)).join('') : '<div class="small text-muted">Aún no hay consultas.</div>';
  }
  document.addEventListener('click', async e => {
    const side = e.target.closest('.ia-side .it'); if (!side) return;
    const id = +side.dataset.id;
    try {
      if (e.target.closest('[data-fav]')) { await call(API, 'favorito', { method: 'POST', body: { id, valor: +e.target.closest('[data-fav]').dataset.fav } }); await estado(); if (last && last.id === id) { last.favorito = +e.target.closest('[data-fav]').dataset.fav; star(); } return; }
      if (e.target.closest('[data-del]')) { await call(API, 'borrar', { method: 'POST', body: { id } }); await estado(); return; }
      cargando(); const r = await call(API, 'ejecutar', { method: 'POST', body: { id } }); mostrar(r); estado();
    } catch (x) { $('#iaOut').innerHTML = errBox(x.message); }
  });

  const errBox = m => `<div class="card-x h-auto"><div class="body"><div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-1"></i>${esc(m)}</div></div></div>`;
  const cargando = () => { $('#iaOut').innerHTML = '<div class="card-x h-auto"><div class="body ia-load"><span class="ia-dots"><span></span><span></span><span></span></span><div>Analizando tu pregunta y consultando los datos…</div></div></div>'; };

  $('#frmAsk').onsubmit = async e => {
    e.preventDefault(); if (busy) return;
    const q = ta.value.trim(); if (q.length < 4) { toast('Escribe tu pregunta', 'err'); return; }
    if (ST && !ST.listo) { toast('Primero activa la IA con tu clave', 'err'); $('#iaSetup').scrollIntoView({ behavior: 'smooth' }); return; }
    busy = true; $('#iaGo').disabled = true; cargando();
    try {
      const seg = $('#iaSeg') && $('#iaSeg').checked && last ? { pregunta: last.pregunta, sql: last.sql } : null;
      const r = await call(API, 'preguntar', { method: 'POST', body: { pregunta: q, previo: seg } });
      if (r.mensaje) $('#iaOut').innerHTML = `<div class="card-x h-auto"><div class="body"><i class="bi bi-info-circle me-1" style="color:var(--accent)"></i>${esc(r.mensaje)}</div></div>`;
      else { mostrar(r); ta.value = ''; ta.style.height = 'auto'; resumen(r); estado(); }
    } catch (x) { $('#iaOut').innerHTML = errBox(x.message); }
    busy = false; $('#iaGo').disabled = false;
  };

  function star() { const b = $('#iaStar'); if (b && last) b.innerHTML = `<i class="bi ${last.favorito ? 'bi-star-fill text-warning' : 'bi-star'}"></i>`; }
  function mostrar(r) {
    last = r; if (chart) { chart.destroy(); chart = null; }
    const n = r.filas.length, g = r.grafico || { tipo: 'none' };
    $('#iaOut').innerHTML = `<div class="card-x h-auto ia-res"><div class="body">
      <div class="d-flex align-items-start gap-2 flex-wrap"><div class="flex-grow-1"><h5>${esc(r.titulo)}</h5><div class="ia-meta">${nf(n)} fila${n === 1 ? '' : 's'}${r.truncado ? ' (límite alcanzado)' : ''} · ${r.ms} ms · «${esc(r.pregunta)}»</div></div>
        <div class="d-flex gap-1"><button class="mini-btn" id="iaStar" title="Favorita"></button><a class="mini-btn" href="${API}?action=csv&id=${r.id}" title="Exportar CSV"><i class="bi bi-filetype-csv"></i></a><button class="mini-btn" id="iaSqlB" title="Ver SQL"><i class="bi bi-code-slash"></i></button></div></div>
      <div id="iaSum"></div>
      ${g.tipo !== 'none' && n > 1 ? '<div class="ia-chart"><canvas id="iaCv"></canvas></div>' : ''}
      ${n ? `<div class="ia-tbl mt-2"><table class="tbl"><thead><tr>${r.columnas.map(c => `<th>${esc(c.replace(/_/g, ' '))}</th>`).join('')}</tr></thead><tbody>${r.filas.map(f => `<tr>${f.map((v, i) => `<td class="${typeof v === 'number' ? 'text-end' : ''}">${cel(v, r.columnas[i])}</td>`).join('')}</tr>`).join('')}</tbody></table></div>` : '<div class="text-center text-muted py-4">La consulta no devolvió resultados para ese período.</div>'}
      <div class="form-check mt-3 small"><input class="form-check-input" type="checkbox" id="iaSeg"><label class="form-check-label" for="iaSeg">Mi próxima pregunta es de seguimiento de este resultado (p. ej. «ahora solo delivery»)</label></div>
      <pre class="ia-sql" id="iaSql" hidden>${esc(r.sql)}</pre></div></div>`;
    star(); $('#iaStar').onclick = async () => { const v = last.favorito ? 0 : 1; try { await call(API, 'favorito', { method: 'POST', body: { id: last.id, valor: v } }); last.favorito = v; star(); estado(); } catch (x) { toast(x.message, 'err'); } };
    $('#iaSqlB').onclick = () => { const s = $('#iaSql'); s.hidden = !s.hidden; };
    if (g.tipo !== 'none' && n > 1) dibujar(r, g);
    $('#iaOut').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
  function dibujar(r, g) {
    chartResult = { r, g };
    if (chart) { chart.destroy(); chart = null; }
    const theme = chartTheme(), COL = [theme.primary, getComputedStyle(document.body).getPropertyValue('--dark-bg-2').trim(), '#10b981', '#8b5cf6', '#f59e0b', '#ef4444'];
    const xi = r.columnas.indexOf(g.x), yi = r.columnas.indexOf(g.y); if (xi < 0 || yi < 0 || !window.Chart) return;
    const lab = r.filas.map(f => String(f[xi] ?? '—').slice(0, 40)), val = r.filas.map(f => f[yi]);
    const money_ = MONEY.test(g.y) && !NOMONEY.test(g.y), fmt = v => money_ ? money(v) : nf(v, Number.isInteger(v) ? 0 : 2);
    const horiz = g.tipo === 'bar' && (lab.length > 8 || lab.some(l => l.length > 14));
    const cfg = { type: g.tipo === 'line' ? 'line' : g.tipo === 'pie' ? 'doughnut' : 'bar', data: { labels: lab, datasets: [{ label: g.y.replace(/_/g, ' '), data: val,
      backgroundColor: g.tipo === 'pie' ? lab.map((_, i) => COL[i % COL.length]) : theme.primary, borderColor: theme.primary,
      borderRadius: g.tipo === 'bar' ? 6 : 0, fill: g.tipo === 'line', tension: .3, pointRadius: g.tipo === 'line' ? 3 : 0 }] },
      options: { responsive: true, maintainAspectRatio: false, indexAxis: horiz ? 'y' : 'x', plugins: { legend: { display: g.tipo === 'pie', position: 'bottom', labels: { color: theme.text } }, tooltip: { callbacks: { label: c => ' ' + (g.tipo === 'pie' ? c.label + ': ' : '') + fmt(c.parsed.y ?? c.parsed.x ?? c.parsed) } } },
        scales: g.tipo === 'pie' ? {} : { [horiz ? 'x' : 'y']: { beginAtZero: true, grid: { color: theme.line }, ticks: { color: theme.text, callback: v => fmt(v) } }, [horiz ? 'y' : 'x']: { grid: { display: false }, ticks: { color: theme.text } } } } };
    chart = new Chart($('#iaCv'), cfg);
  }
  async function resumen(r) {
    if (!ST || !ST.cfg.resumen || !r.filas.length) return;
    const b = $('#iaSum'); b.innerHTML = '<div class="ia-sum"><span class="ia-dots"><span></span><span></span><span></span></span><div class="text-muted">Redactando resumen…</div></div>';
    try { const s = await call(API, 'resumir', { method: 'POST', body: { id: r.id } }); if (last && last.id === r.id) b.innerHTML = `<div class="ia-sum"><i class="bi bi-stars"></i><div>${esc(s.resumen)}</div></div>`; }
    catch (x) { b.innerHTML = ''; }
  }

  /* ---- activación rápida ---- */
  $('#frmQuick').onsubmit = async e => {
    e.preventDefault(); const k = $('#qKey').value.trim(), er = $('#qErr'); er.textContent = '';
    if (k.length < 8) { er.textContent = 'Pega la clave completa.'; return; }
    $('#qGo').disabled = true; $('#qGo').textContent = 'Probando…';
    try { await window.iaActivar(k); toast('IA activada'); $('#qKey').value = ''; await estado(); } catch (x) { er.textContent = x.message; }
    $('#qGo').disabled = false; $('#qGo').textContent = 'Activar';
  };

  /* ---- configuración avanzada ---- */
  const prov = $('#cProv');
  function ayuda() { const p = ST.presets[prov.value]; $('#cAyuda').textContent = p.ayuda; $('#cKeyH').textContent = !p.clave ? 'Este proveedor no necesita clave.' : ST.cfg.clave_puesta && ST.cfg.proveedor === prov.value ? 'Ya hay una clave guardada. Déjalo vacío para conservarla, o escribe __borrar__ para eliminarla.' : 'Pega aquí tu clave. Se guarda cifrada.'; }
  function abrirCfg() {
    if (!ST) return;
    prov.innerHTML = Object.entries(ST.presets).map(([k, p]) => `<option value="${k}">${esc(p.nombre)}</option>`).join('');
    prov.value = ST.cfg.proveedor; $('#cUrl').value = ST.cfg.url; $('#cMod').value = ST.cfg.modelo; $('#cKey').value = ''; $('#cRes').checked = !!ST.cfg.resumen; $('#cErr').textContent = ''; ayuda(); mdl('mdlCfg').show();
  }
  prov.onchange = () => { const p = ST.presets[prov.value]; if (prov.value !== 'otro') { $('#cUrl').value = p.url; $('#cMod').value = p.modelo; } ayuda(); };
  const datos = () => ({ proveedor: prov.value, url: $('#cUrl').value.trim(), modelo: $('#cMod').value.trim(), clave: $('#cKey').value.trim(), resumen: $('#cRes').checked ? 1 : 0 });
  $('#btnCfg').onclick = abrirCfg; $('#btnCfg2').onclick = abrirCfg;
  $('#frmCfg').onsubmit = async e => { e.preventDefault(); $('#cErr').textContent = ''; try { await call(API, 'guardar_config', { method: 'POST', body: datos() }); toast('Configuración guardada'); mdl('mdlCfg').hide(); estado(); } catch (x) { $('#cErr').textContent = x.message; } };
  $('#cTest').onclick = async () => {
    $('#cErr').textContent = ''; $('#cTest').disabled = true;
    try { await call(API, 'guardar_config', { method: 'POST', body: datos() }); await call(API, 'probar', { method: 'POST', body: {} }); toast('Conexión correcta'); $('#cKey').value = ''; await estado(); }
    catch (x) { $('#cErr').textContent = x.message; }
    $('#cTest').disabled = false;
  };
  $('#btnEsq').onclick = async () => { mdl('mdlEsq').show(); $('#esqTxt').textContent = 'Cargando…'; try { const r = await fetch(API + '?action=esquema', { credentials: 'same-origin' }); $('#esqTxt').textContent = await r.text(); } catch (x) { $('#esqTxt').textContent = 'No se pudo cargar.'; } };

  estado();
})();
