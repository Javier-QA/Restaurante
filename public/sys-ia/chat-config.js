(() => {
  const { $, esc, call, mdl, toast } = window.SP;
  const API = window.API_IA;
  let state;
  const provider = $('#cProv');
  const values = () => ({ proveedor: provider.value, url: $('#cUrl').value.trim(), modelo: $('#cMod').value.trim(), clave: $('#cKey').value.trim(), resumen: $('#cRes').checked ? 1 : 0 });
  function help() {
    const p = state.presets[provider.value];
    $('#cAyuda').textContent = p.ayuda;
    $('#cKeyH').textContent = !p.clave ? 'Este proveedor no necesita clave.' : state.cfg.clave_puesta ? 'Déjalo vacío para conservar la clave, o escribe __borrar__ para eliminarla.' : 'Pega tu clave. Se guarda cifrada.';
  }
  $('#btnChatCfg').onclick = async () => {
    try {
      state = await call(API, 'estado');
      provider.innerHTML = Object.entries(state.presets).map(([key, p]) => `<option value="${esc(key)}">${esc(p.nombre)}</option>`).join('');
      provider.value = state.cfg.proveedor;
      $('#cUrl').value = state.cfg.url; $('#cMod').value = state.cfg.modelo;
      $('#cKey').value = ''; $('#cRes').checked = !!state.cfg.resumen; $('#cErr').textContent = '';
      help(); mdl('mdlCfg').show();
    } catch (e) { toast(e.message, 'err'); }
  };
  provider.onchange = () => {
    const p = state.presets[provider.value];
    if (provider.value !== 'otro') { $('#cUrl').value = p.url; $('#cMod').value = p.modelo; }
    help();
  };
  $('#frmCfg').onsubmit = async e => {
    e.preventDefault(); $('#cErr').textContent = ''; $('#cSave').disabled = true;
    try { await call(API, 'guardar_config', { method: 'POST', body: values() }); mdl('mdlCfg').hide(); toast('Configuración guardada'); await window.sysIaRefreshChat(); }
    catch (e) { $('#cErr').textContent = e.message; }
    finally { $('#cSave').disabled = false; }
  };
  $('#cTest').onclick = async () => {
    $('#cErr').textContent = ''; $('#cTest').disabled = true;
    try { await call(API, 'guardar_config', { method: 'POST', body: values() }); await call(API, 'probar', { method: 'POST', body: {} }); state = await call(API, 'estado'); $('#cKey').value = ''; help(); toast('Conexión correcta'); await window.sysIaRefreshChat(); }
    catch (e) { $('#cErr').textContent = e.message; }
    finally { $('#cTest').disabled = false; }
  };
})();
