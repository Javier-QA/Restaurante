@extends('layouts.app')

@section('content')
<style>
.ai-sys-page{--ia-line:#e3e8ef;--ia-muted:#64748b;--ia-ink:#0f172a}
.ai-sys-page .page-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
.ai-sys-page .page-head h1{font-size:1.55rem;font-weight:800;margin:0;color:var(--ia-ink)}
.ai-sys-page .page-head p{margin:2px 0 0;color:#66809e}
.ia-btn{border:1px solid var(--ia-line);background:#fff;color:var(--ia-ink);border-radius:13px;padding:10px 16px;font-weight:700}
.ia-hero{background:linear-gradient(120deg,#0f172a,#1e293b 60%,#7c2d12);border-radius:18px;color:#fff;padding:22px 26px;margin-bottom:16px;position:relative;overflow:hidden}
.ia-hero:after{content:"";position:absolute;right:-50px;top:-70px;width:240px;height:240px;border-radius:50%;background:rgba(249,115,22,.22)}
.ia-hero h2{font-size:1.25rem;font-weight:800;margin:0 0 4px}.ia-hero p{margin:0 0 14px;opacity:.85;font-size:.88rem}
.ia-ask{display:flex;gap:8px;background:#fff;border-radius:14px;padding:6px;box-shadow:0 10px 30px -12px rgba(0,0,0,.5);position:relative;z-index:1}
.ia-ask textarea{flex:1;border:0;outline:0;resize:none;padding:.65rem .8rem;font-size:.97rem;border-radius:10px;background:transparent;color:#0f172a;min-height:48px;max-height:120px}
.ia-ask button{border:0;border-radius:11px;padding:0 20px;font-weight:700;color:#fff;background:linear-gradient(135deg,#f97316,#c2410c);display:flex;align-items:center;gap:8px}
.ia-chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;position:relative;z-index:1}.ia-chip{border:1px solid rgba(255,255,255,.3);background:rgba(255,255,255,.12);color:#fff;border-radius:999px;padding:7px 13px;font-size:.78rem;cursor:pointer}
.ia-card{background:#fff;border:1px solid var(--ia-line);border-radius:18px;box-shadow:0 8px 25px -18px rgba(15,23,42,.35);margin-bottom:16px}.ia-body{padding:14px 16px}
.ia-res h5{font-weight:800;margin:0;font-size:1.05rem}.ia-meta{color:var(--ia-muted);font-size:.78rem}
.ia-sum{background:linear-gradient(135deg,#fff7ed,#ffedd5);border:1px solid #fed7aa;border-radius:12px;padding:10px 14px;font-size:.88rem;color:#7c2d12;display:flex;gap:10px;margin:12px 0}
.ia-chart{position:relative;height:300px;margin:8px 0 14px}.ia-sql{background:#0f172a;color:#e2e8f0;border-radius:12px;padding:12px 14px;font-size:.78rem;white-space:pre-wrap;word-break:break-word;margin:10px 0 0}
.ia-tbl{max-height:420px;overflow:auto;border:1px solid var(--ia-line);border-radius:12px}.ia-tbl table{width:100%;border-collapse:separate;border-spacing:0}.ia-tbl th{font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:var(--ia-muted);padding:.7rem .6rem;border-bottom:1px solid var(--ia-line);white-space:nowrap;background:#fff;position:sticky;top:0;z-index:1}.ia-tbl td{padding:.8rem .6rem;border-bottom:1px solid #f1f4f9}
.ia-side .it{display:flex;gap:6px;align-items:center;padding:9px 4px;border-bottom:1px solid var(--ia-line);cursor:pointer;border-radius:8px}.ia-side .it:hover{background:#f8fafc}.ia-side .it:last-child{border:0}.ia-side .it .tx{flex:1;min-width:0;font-size:.82rem}.ia-side .it .tx small{color:var(--ia-muted);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.ia-side .it button{border:0;background:none;color:var(--ia-muted);padding:2px 4px}
.ia-dots span{display:inline-block;width:8px;height:8px;border-radius:50%;background:#f97316;margin-right:4px;animation:iad 1s infinite ease-in-out}.ia-dots span:nth-child(2){animation-delay:.15s}.ia-dots span:nth-child(3){animation-delay:.3s}@keyframes iad{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1);opacity:1}}
.ia-modal-pre{background:#0f172a;color:#e2e8f0;border-radius:12px;padding:14px;font-size:.76rem;white-space:pre-wrap;max-height:60vh;overflow:auto}.ia-accent-btn{border:0;background:#f97316;color:#fff;border-radius:10px;padding:9px 15px;font-weight:700}
.ai-sys-page .modal-content{border:0;border-radius:20px}.ai-sys-page .form-control,.ai-sys-page .form-select{border-radius:10px;min-height:42px}.ai-sys-page .form-label{font-weight:700;font-size:.78rem}
@media(max-width:768px){.ai-sys-page .page-head{flex-direction:column}.ia-ask{flex-direction:column}.ia-ask button{padding:12px;justify-content:center}}
</style>

<div class="ai-sys-page container-fluid">
 <div class="page-head">
  <div><h1>Asistente IA</h1><p>Pregunta en lenguaje natural y obtén tablas, gráficos y resúmenes de tu restaurante</p></div>
  <div class="d-flex gap-2 flex-wrap"><button class="ia-btn" id="btnEsq"><i class="bi bi-diagram-3 me-1"></i> Qué datos ve la IA</button><button class="ia-btn" id="btnCfg"><i class="bi bi-gear me-1"></i> Configurar IA</button></div>
 </div>
 <div class="ia-hero">
  <h2><i class="bi bi-stars me-2"></i>¿Qué quieres saber de tu restaurante?</h2>
  <p>Escríbelo como se lo preguntarías a un analista. Ejemplo: «¿Cuáles fueron los 10 platos más vendidos este mes?»</p>
  <form class="ia-ask" id="frmAsk">@csrf<textarea id="iaQ" rows="1" maxlength="500" placeholder="Escribe tu pregunta aquí…"></textarea><button id="iaGo" type="submit"><i class="bi bi-send-fill"></i> Preguntar</button></form>
  <div class="ia-chips" id="iaChips"></div>
 </div>
 <div class="row g-3">
  <div class="col-lg-8"><div id="iaOut"><div class="ia-card"><div class="ia-body text-center text-muted py-5"><i class="bi bi-chat-square-text" style="font-size:2.4rem"></i><div class="mt-2">Aquí aparecerán tus resultados</div><div class="small">Puedes hacer preguntas de seguimiento, como «ahora solo delivery».</div></div></div></div></div>
  <div class="col-lg-4">
   <div class="ia-card ia-side"><div class="ia-body"><h6 class="fw-bold mb-2"><i class="bi bi-star-fill text-warning me-1"></i>Favoritas</h6><div id="iaFav"></div></div></div>
   <div class="ia-card ia-side"><div class="ia-body"><h6 class="fw-bold mb-2"><i class="bi bi-clock-history me-1"></i>Recientes</h6><div id="iaHis"></div></div></div>
  </div>
 </div>

 <div class="modal fade" id="mdlCfg" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
  <form id="frmCfg"><div class="modal-header border-0 px-4 pt-4"><h5 class="modal-title fw-bold"><i class="bi bi-gear me-2"></i>Configurar la IA</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body px-4"><div class="row g-3">
   <div class="col-12"><label class="form-label">Proveedor</label><select class="form-select" id="cProv"></select><div class="form-text" id="cAyuda"></div></div>
   <div class="col-md-7"><label class="form-label">URL de la API</label><input class="form-control" id="cUrl"></div><div class="col-md-5"><label class="form-label">Modelo</label><input class="form-control" id="cMod"></div>
   <div class="col-12"><label class="form-label">Clave (API key)</label><input type="password" class="form-control" id="cKey" autocomplete="new-password"><div class="form-text" id="cKeyH"></div></div>
   <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="cRes"><label class="form-check-label" for="cRes">Generar un resumen en lenguaje natural de cada resultado (usa una segunda consulta a la IA)</label></div></div>
   <div class="col-12"><div class="alert alert-light border small mb-0"><i class="bi bi-shield-lock me-1"></i><b>Privacidad y seguridad.</b> La IA solo ve información autorizada de lectura. La consulta se valida antes de ejecutarse y la clave se guarda cifrada. Con <b>Ollama</b> nada sale de tu PC.</div></div>
   <div class="col-12 text-danger small" id="cErr"></div>
  </div></div>
  <div class="modal-footer border-0 px-4 pb-4"><button type="button" class="btn btn-light me-auto" id="cTest"><i class="bi bi-plug"></i> Probar conexión</button><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button><button class="ia-accent-btn"><i class="bi bi-check2"></i> Guardar</button></div></form>
 </div></div></div>

 <div class="modal fade" id="mdlEsq" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"><div class="modal-content">
  <div class="modal-header border-0 px-4 pt-4"><h5 class="modal-title fw-bold"><i class="bi bi-diagram-3 me-2"></i>Datos que ve la IA</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body px-4"><p class="small text-muted">Solo información de lectura autorizada. Nunca se exponen contraseñas ni usuarios.</p><pre class="ia-modal-pre" id="esqTxt">Cargando…</pre></div>
 </div></div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded',()=>{
 const $=s=>document.querySelector(s),csrf=document.querySelector('input[name="_token"]').value;
 const esc=v=>String(v??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
 const money=v=>'S/ '+Number(v||0).toLocaleString('es-PE',{minimumFractionDigits:2,maximumFractionDigits:2});
 const SUG=['¿Cuáles fueron los 10 platos más vendidos este mes?','Ventas por día de los últimos 15 días','¿Cuánto vendimos por método de pago esta semana?','Ventas por mozo este mes','¿Qué insumos tienen stock bajo?'];
 let chart=null,busy=false;
 const api=(url,opt={})=>fetch(url,{credentials:'same-origin',headers:{Accept:'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf,...(opt.headers||{})},...opt}).then(async r=>{const d=await r.json().catch(()=>({}));if(!r.ok||d.success===false)throw new Error(d.message||'No fue posible completar la operación.');return d});
 const askUrl='{{ route('ai.assistant.ask') }}',histUrl='{{ route('ai.assistant.history') }}',favUrl='{{ route('ai.assistant.favorites') }}';
 const favUrlBase=id=>`{{ url('/ai/assistant') }}/${id}/favorite`,delUrl=id=>`{{ url('/ai/assistant') }}/${id}`;
 const modal=id=>bootstrap.Modal.getOrCreateInstance(document.getElementById(id));
 $('#iaChips').innerHTML=SUG.map(s=>`<button type="button" class="ia-chip">${esc(s)}</button>`).join('');
 $('#iaChips').onclick=e=>{const b=e.target.closest('.ia-chip');if(b){$('#iaQ').value=b.textContent;$('#frmAsk').requestSubmit();}};
 $('#iaQ').oninput=e=>{e.target.style.height='auto';e.target.style.height=Math.min(120,e.target.scrollHeight)+'px'};
 $('#iaQ').onkeydown=e=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();$('#frmAsk').requestSubmit()}};
 async function loadSide(){try{const[h,f]=await Promise.all([api(histUrl),api(favUrl)]);const make=(q,fav)=>`<div class="it" data-id="${q.id}"><div class="tx">${esc(q.question)}<small>${esc(q.created_at||'')}</small></div><button data-fav="${fav?0:1}"><i class="bi ${fav?'bi-star-fill text-warning':'bi-star'}"></i></button><button data-del><i class="bi bi-trash3"></i></button></div>`;$('#iaFav').innerHTML=f.data?.length?f.data.map(q=>make(q,true)).join(''):'<div class="small text-muted">Marca con ⭐ las consultas que repites.</div>';$('#iaHis').innerHTML=h.data?.length?h.data.map(q=>make(q,false)).join(''):'<div class="small text-muted">Aún no hay consultas.</div>'}catch(e){}}
 function loading(){$('#iaOut').innerHTML='<div class="ia-card"><div class="ia-body d-flex align-items-center gap-3 py-4"><span class="ia-dots"><span></span><span></span><span></span></span><div class="text-muted">Analizando tu pregunta y consultando los datos…</div></div></div>'}
 function draw(r,rows){if(!window.Chart||!rows.length)return;const cols=Object.keys(rows[0]),x=cols.find(c=>typeof rows[0][c]!=='number')||cols[0],y=cols.find(c=>typeof rows[0][c]==='number'&&c!==x);if(!y)return;const labels=rows.map(v=>String(v[x]??'—').slice(0,35)),vals=rows.map(v=>Number(v[y]||0));chart=new Chart(document.getElementById('iaCv'),{type:r.chart_type==='line'?'line':r.chart_type==='pie'?'doughnut':'bar',data:{labels,datasets:[{label:y.replace(/_/g,' '),data:vals,backgroundColor:r.chart_type==='pie'?labels.map((_,i)=>['#f97316','#3b82f6','#10b981','#8b5cf6','#f59e0b','#ef4444','#06b6d4','#ec4899'][i%8]):'#f97316',borderColor:'#f97316',borderRadius:6,tension:.3,fill:r.chart_type==='line'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:r.chart_type==='pie',position:'bottom'}}}})}
 function render(r){if(chart){chart.destroy();chart=null}const rows=r.data||[],cols=rows.length?Object.keys(rows[0]):[],n=rows.length;$('#iaOut').innerHTML=`<div class="ia-card ia-res"><div class="ia-body"><div class="d-flex align-items-start gap-2 flex-wrap"><div class="flex-grow-1"><h5>${esc(r.title||r.question)}</h5><div class="ia-meta">${n} fila${n===1?'':'s'} · «${esc(r.question)}»</div></div><div class="d-flex gap-1"><button class="btn btn-sm btn-light" id="iaStar"><i class="bi ${r.is_favorite?'bi-star-fill text-warning':'bi-star'}"></i></button><a class="btn btn-sm btn-light" href="{{ url('/ai/assistant/export/csv') }}?query_id=${r.query_id}"><i class="bi bi-filetype-csv"></i></a><button class="btn btn-sm btn-light" id="iaSqlB"><i class="bi bi-code-slash"></i></button></div></div>${r.summary?'<div class="ia-sum"><i class="bi bi-stars"></i><div>'+esc(r.summary)+'</div></div>':''}${r.chart_type&&n>1?'<div class="ia-chart"><canvas id="iaCv"></canvas></div>':''}${n?'<div class="ia-tbl"><table><thead><tr>'+cols.map(c=>'<th>'+esc(c.replace(/_/g,' '))+'</th>').join('')+'</tr></thead><tbody>'+rows.map(row=>'<tr>'+cols.map(c=>'<td class="'+(typeof row[c]==='number'?'text-end':'')+'">'+esc(typeof row[c]==='number'&&/(total|importe|venta|monto|precio|costo|utilidad|gasto|saldo|promedio|valor)/i.test(c)?money(row[c]):(row[c]??'—'))+'</td>').join('')+'</tr>').join('')+'</tbody></table></div>':'<div class="text-center text-muted py-4">La consulta no devolvió resultados para ese período.</div>'}<div class="form-check mt-3 small"><input class="form-check-input" type="checkbox" id="iaSeg"><label class="form-check-label" for="iaSeg">Mi próxima pregunta es de seguimiento de este resultado (p. ej. «ahora solo delivery»)</label></div><pre class="ia-sql" id="iaSql" hidden>${esc(r.sql||'')}</pre></div></div>`;$('#iaStar').onclick=async()=>{try{await api(favUrlBase(r.query_id),{method:'PATCH',body:JSON.stringify({value:r.is_favorite?0:1})});render({...r,is_favorite:!r.is_favorite});loadSide()}catch(e){alert(e.message)}};$('#iaSqlB').onclick=()=>$('#iaSql').hidden=!$('#iaSql').hidden;if(r.chart_type&&n>1)draw(r,rows)}
 $('#frmAsk').onsubmit=async e=>{e.preventDefault();if(busy)return;const q=$('#iaQ').value.trim();if(q.length<4)return;busy=true;$('#iaGo').disabled=true;loading();try{const r=await api(askUrl,{method:'POST',body:JSON.stringify({question:q,chart_type:null})});$('#iaQ').value='';render(r);loadSide()}catch(err){$('#iaOut').innerHTML='<div class="ia-card"><div class="ia-body"><div class="alert alert-danger mb-0">'+esc(err.message)+'</div></div></div>'}finally{busy=false;$('#iaGo').disabled=false}};
 document.addEventListener('click',async e=>{const it=e.target.closest('.ia-side .it');if(!it)return;const id=+it.dataset.id;try{if(e.target.closest('[data-fav]')){await api(favUrlBase(id),{method:'PATCH',body:JSON.stringify({value:+e.target.closest('[data-fav]').dataset.fav})});loadSide();return}if(e.target.closest('[data-del]')){await api(delUrl(id),{method:'DELETE'});loadSide();return}}catch(x){alert(x.message)}});
 async function cfgLoad(){const r=await api('{{ route('ai.settings') }}');window.IA_CFG=r.config;window.IA_PROVIDERS=r.providers||{};$('#cProv').innerHTML=Object.entries(window.IA_PROVIDERS).map(([k,v])=>`<option value="${k}">${esc(v.label)}</option>`).join('');$('#cProv').value=r.config.provider;cfgFill();$('#cRes').checked=!!r.config.summary}
 function cfgFill(){const p=window.IA_PROVIDERS[$('#cProv').value];if(!p)return;$('#cUrl').value=p.base_url;$('#cMod').value=p.model;$('#cAyuda').textContent=$('#cProv').value==='ollama'?'Ollama funciona localmente, sin enviar datos a internet.':'Proveedor compatible con Chat Completions.';$('#cKeyH').textContent=window.IA_CFG.configured?'Ya hay una clave guardada. Déjala vacía para conservarla.':'Pega aquí tu clave. Se guarda cifrada.'}
 $('#cProv').onchange=cfgFill;$('#btnCfg').onclick=async()=>{try{await cfgLoad();modal('mdlCfg').show()}catch(e){alert(e.message)}};
 async function saveCfg(){const body={provider:$('#cProv').value,base_url:$('#cUrl').value.trim(),model:$('#cMod').value.trim(),api_key:$('#cKey').value.trim(),summary:$('#cRes').checked?1:0};return api('{{ route('ai.settings.update') }}',{method:'POST',body:JSON.stringify(body)})}
 $('#frmCfg').onsubmit=async e=>{e.preventDefault();$('#cErr').textContent='';try{await saveCfg();modal('mdlCfg').hide();alert('Configuración guardada')}catch(x){$('#cErr').textContent=x.message}};
 $('#cTest').onclick=async()=>{const b=$('#cTest');b.disabled=true;try{await saveCfg();await api('{{ url('/ai/settings/test') }}',{method:'POST',body:'{}'});alert('Conexión correcta')}catch(x){$('#cErr').textContent=x.message}finally{b.disabled=false}};
 $('#btnEsq').onclick=async()=>{modal('mdlEsq').show();try{const r=await fetch('{{ route('ai.assistant.schema') }}',{credentials:'same-origin'});$('#esqTxt').textContent=await r.text()}catch(e){$('#esqTxt').textContent='No se pudo cargar.'}};
 loadSide();
});
</script>
@endsection