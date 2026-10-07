/* Utilidades compartidas por los módulos: window.SP */
(function () {
  'use strict';
  const F = Object.assign({ sim: window.MONEDA || 'S/', pos: 'antes_espacio', dec: 2, sd: '.', sm: ',' }, window.FMT || {});
  const SP = window.SP = {};
  SP.$ = s => document.querySelector(s);
  SP.esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  /** Número con los separadores de la empresa. `dec` fija los decimales (por defecto, los configurados). */
  SP.num = (n, dec) => {
    const d = dec === undefined || dec === null ? F.dec : dec, v = Number(n) || 0, neg = v < 0;
    const [i, f = ''] = Math.abs(v).toFixed(d).split('.');
    return (neg ? '-' : '') + i.replace(/\B(?=(\d{3})+(?!\d))/g, F.sm) + (d ? F.sd + f : '');
  };
  /** Cantidades (hasta `max` decimales, sin ceros sobrantes). */
  SP.qty = (n, max = 3) => { const t = SP.num(n, max); return F.sd && t.includes(F.sd) ? t.replace(new RegExp('0+$'), '').replace(new RegExp('\\' + F.sd + '$'), '') : t; };
  SP.money = (n, dec) => { const v = SP.num(n, dec); return F.pos === 'antes' ? F.sim + v : F.pos === 'despues' ? v + ' ' + F.sim : F.sim + ' ' + v; };
  SP.r2 = n => Math.round(n * 100) / 100;
  SP.mdl = id => bootstrap.Modal.getOrCreateInstance(document.getElementById(id));
  SP.toast = function (msg, type = 'ok') {
    let wrap = SP.$('#toastWrap'); if (!wrap) { wrap = document.createElement('div'); wrap.id = 'toastWrap'; wrap.className = 'toast-wrap'; document.body.appendChild(wrap); }
    const t = document.createElement('div'); t.className = 'toast-x ' + type;
    t.innerHTML = '<i class="bi ' + (type === 'ok' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill') + '"></i><span>' + SP.esc(msg) + '</span>';
    wrap.appendChild(t); setTimeout(() => t.remove(), 3800);
  };
  /** Llamada a una API JSON. `body` puede ser objeto (JSON) o FormData (subida de archivos). */
  SP.call = async function (base, action, { method = 'GET', body, params = {} } = {}) {
    const qs = new URLSearchParams({ action, ...params });
    const isForm = body instanceof FormData;
    const headers = { 'X-CSRF-Token': window.CSRF }; if (!isForm) headers['Content-Type'] = 'application/json';
    const res = await fetch(base + '?' + qs, { method, credentials: 'same-origin', headers, body: body ? (isForm ? body : JSON.stringify(body)) : undefined });
    let d; try { d = await res.json(); } catch (e) { throw new Error('Respuesta inválida del servidor'); }
    if (!d.ok) throw new Error(d.error || 'Error inesperado');
    return d;
  };
  SP.showErr = (sel, m) => { const e = SP.$(sel); e.textContent = m; e.classList.remove('d-none'); };
  SP.hideErr = sel => SP.$(sel).classList.add('d-none');
  /** Color del margen / food cost */
  SP.fcClass = pct => pct === null ? 'na' : pct <= 35 ? 'good' : pct <= 45 ? 'mid' : 'bad';
})();
