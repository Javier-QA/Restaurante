document.addEventListener('DOMContentLoaded',()=>{
 if(window.__sysChatIA)return;window.__sysChatIA=true;
 const esc=t=>String(t??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
 const chatLink=[...document.querySelectorAll('a[href]')].find(a=>a.href.includes('/ai/chat'));
 const askUrl=chatLink?chatLink.href.replace(/\/$/,'')+'/ask':'/ai/chat/ask';
 const stateUrl=chatLink?chatLink.href.replace(/\/$/,'')+'/state':'/ai/chat/state';
 const clearUrl=chatLink?chatLink.href.replace(/\/$/,'')+'/clear':'/ai/chat/clear';
 const csrf=document.querySelector('meta[name="csrf-token"]')?.content||document.querySelector('input[name="_token"]')?.value||'';
 const suggestions=['¿Cuánto vendimos hoy?','¿Cuáles son los 5 platos más vendidos este mes?','¿Qué insumos tienen stock bajo?','¿Qué mozo vendió más esta semana?','¿Cómo registro una compra?'];
 let root,panel,msgs,input,sending=false,opened=false;
 function build(){
  if(document.getElementById('sysChatRoot'))return;
  const st=document.createElement('style');st.textContent=`
   #sysChatRoot{position:fixed;right:20px;bottom:20px;z-index:1045}
   #sysChatFab{position:relative;width:62px;height:62px;border:0;border-radius:50%;color:#fff;font-size:1.5rem;background:linear-gradient(135deg,#f97316,#c2410c);box-shadow:0 14px 30px -8px rgba(249,115,22,.7);display:grid;place-items:center;transition:transform .2s;animation:sysChatFloat 2.6s ease-in-out infinite}
   #sysChatFab:before,#sysChatFab:after{content:"";position:absolute;inset:-5px;border:2px solid #f97316;border-radius:50%;pointer-events:none;opacity:0;animation:sysChatPulse 2.6s ease-out infinite}
   #sysChatFab:after{animation-delay:1.3s}#sysChatFab:hover{transform:scale(1.09)}
   #sysChatPanel{position:absolute;right:0;bottom:78px;width:390px;height:min(620px,calc(100dvh - 120px));border:1px solid #e3e8ef;border-radius:20px;box-shadow:0 30px 70px -20px rgba(15,23,42,.45);overflow:hidden;background:#fff;display:flex;flex-direction:column}
   #sysChatPanel[hidden]{display:none}
   .sys-chat-head{display:flex;align-items:center;gap:10px;padding:12px 14px;color:#fff;background:linear-gradient(120deg,#111827,#1f2937 55%,#c2410c)}
   .sys-chat-head b{display:block;line-height:1.1}.sys-chat-head small{opacity:.8;font-size:.72rem}.sys-chat-head button{border:0;background:rgba(255,255,255,.15);color:#fff;border-radius:9px;width:32px;height:32px}.sys-chat-av{width:36px;height:36px;border-radius:12px;background:rgba(255,255,255,.2);display:grid;place-items:center}
   .sys-chat-msgs{flex:1;overflow-y:auto;padding:14px;background:#f4f7fb;display:flex;flex-direction:column;gap:10px}.sys-chat-m{display:flex}.sys-chat-m.me{justify-content:flex-end}.sys-chat-b{max-width:88%;padding:9px 13px;border-radius:16px;font-size:.88rem;line-height:1.45;word-break:break-word}.sys-chat-m.bot .sys-chat-b{background:#fff;border:1px solid #e3e8ef;border-bottom-left-radius:5px}.sys-chat-m.me .sys-chat-b{background:linear-gradient(135deg,#f97316,#c2410c);color:#fff;border-bottom-right-radius:5px}
   .sys-chat-sugs{display:flex;flex-wrap:wrap;gap:6px}.sys-chat-sug{border:1px solid #e3e8ef;background:#fff;border-radius:999px;padding:5px 11px;font-size:.78rem;color:#0f172a}.sys-chat-sug:hover{border-color:#f97316}
   .sys-chat-in{display:flex;gap:8px;padding:10px;border-top:1px solid #e3e8ef;background:#fff;align-items:flex-end}.sys-chat-in textarea{flex:1;resize:none;border:1px solid #e3e8ef;border-radius:12px;padding:.55rem .75rem;font-size:16px;background:#f4f7fb;color:#0f172a;outline:0;max-height:110px}.sys-chat-in textarea:focus{border-color:#f97316}.sys-chat-in button{width:42px;height:42px;border:0;border-radius:12px;color:#fff;background:linear-gradient(135deg,#f97316,#c2410c)}
   .sys-chat-tg{border:0;background:none;color:#c2410c;font-size:.78rem;font-weight:700;padding:0;margin-top:8px}.sys-chat-tbl{margin-top:6px;border:1px solid #e3e8ef;border-radius:10px;overflow:auto}.sys-chat-tbl table{width:100%;font-size:.74rem}.sys-chat-tbl th,.sys-chat-tbl td{padding:.35rem .5rem}
   .sys-chat-dots span{display:inline-block;width:8px;height:8px;border-radius:50%;background:#f97316;margin-right:4px;animation:sysChatDot 1s infinite}.sys-chat-dots span:nth-child(2){animation-delay:.15s}.sys-chat-dots span:nth-child(3){animation-delay:.3s}
   @keyframes sysChatFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}@keyframes sysChatPulse{0%{transform:scale(.95);opacity:.55}70%{transform:scale(1.15);opacity:0}100%{opacity:0}}@keyframes sysChatDot{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1);opacity:1}}
   @media(max-width:575px){#sysChatRoot{right:14px;bottom:16px}#sysChatFab{width:54px;height:54px}#sysChatPanel{position:fixed;inset:0;width:auto;height:auto;border-radius:0}body.sys-chat-open #sysChatFab{display:none}}
  `;document.head.appendChild(st);
  root=document.createElement('div');root.id='sysChatRoot';
  root.innerHTML=`<div id="sysChatPanel" hidden><div class="sys-chat-head"><div class="sys-chat-av"><i class="bi bi-stars"></i></div><div class="flex-grow-1"><b>Asistente</b><small id="sysChatSub">Pregúntame sobre tu restaurante</small></div><button id="sysChatClear" title="Nueva conversación"><i class="bi bi-arrow-counterclockwise"></i></button><button id="sysChatClose" title="Cerrar"><i class="bi bi-x-lg"></i></button></div><div class="sys-chat-msgs" id="sysChatMsgs"></div><form class="sys-chat-in" id="sysChatForm"><textarea id="sysChatInput" rows="1" maxlength="400" placeholder="Escribe tu pregunta…"></textarea><button id="sysChatSend"><i class="bi bi-send-fill"></i></button></form></div><button id="sysChatFab" aria-label="Abrir Chat IA"><i class="bi bi-chat-dots-fill"></i></button>`;
  document.body.appendChild(root);panel=root.querySelector('#sysChatPanel');msgs=root.querySelector('#sysChatMsgs');input=root.querySelector('#sysChatInput');
  root.querySelector('#sysChatFab').onclick=toggle;root.querySelector('#sysChatClose').onclick=toggle;root.querySelector('#sysChatClear').onclick=clear;
  root.querySelector('#sysChatForm').onsubmit=e=>{e.preventDefault();send()};
  input.onkeydown=e=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();send()}};
  input.oninput=()=>{input.style.height='auto';input.style.height=Math.min(110,input.scrollHeight)+'px'};
  msgs.onclick=e=>{const b=e.target.closest('.sys-chat-sug');if(b){input.value=b.textContent;send()}const t=e.target.closest('.sys-chat-tg');if(t){const n=t.nextElementSibling;n.hidden=!n.hidden}};
  renderState();
 }
 function toggle(){const open=panel.hidden;panel.hidden=!open;document.body.classList.toggle('sys-chat-open',open);if(open){renderState();setTimeout(()=>input.focus(),80)}}
 async function call(url,opt={}){const r=await fetch(url,{credentials:'same-origin',headers:{Accept:'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf,...(opt.headers||{})},...opt});const d=await r.json().catch(()=>({}));if(!r.ok||d.success===false)throw new Error(d.message||'No fue posible completar la operación.');return d}
 function add(type,text,html=false){const d=document.createElement('div');d.className='sys-chat-m '+(type==='user'?'me':'bot');const b=document.createElement('div');b.className='sys-chat-b';if(html)b.innerHTML=text;else b.textContent=text;d.appendChild(b);msgs.appendChild(d);msgs.scrollTop=msgs.scrollHeight;return b}
 function suggestions(){const d=document.createElement('div');d.className='sys-chat-sugs';d.innerHTML=suggestions.map(s=>`<button type="button" class="sys-chat-sug">${esc(s)}</button>`).join('');msgs.appendChild(d)}
 function renderState(){if(!msgs.children.length){add('bot','¡Hola! Soy el asistente de tu restaurante. Puedo consultar tus ventas, platos, insumos, compras, clientes y gastos, o explicarte cómo usar el sistema.');suggestions()}}
 function table(d){const rows=d.data||[],cols=d.columns||(rows.length?Object.keys(rows[0]):[]);if(!rows.length)return '';return '<button class="sys-chat-tg">Ver datos ('+rows.length+')</button><div class="sys-chat-tbl" hidden><table><thead><tr>'+cols.map(c=>'<th>'+esc(c.replace(/_/g,' '))+'</th>').join('')+'</tr></thead><tbody>'+rows.slice(0,10).map(r=>'<tr>'+cols.map(c=>'<td>'+esc(r[c]??'—')+'</td>').join('')+'</tr>').join('')+'</tbody></table></div>'}
 async function send(){const m=input.value.trim();if(!m||sending)return;sending=true;msgs.querySelector('.sys-chat-sugs')?.remove();add('user',m);input.value='';input.style.height='auto';const b=add('bot','',true);b.innerHTML='<span class="sys-chat-dots"><span></span><span></span><span></span></span>';try{const r=await call(askUrl,{method:'POST',body:JSON.stringify({message:m})});b.innerHTML=esc(r.answer||'No obtuve una respuesta.')+table(r)}catch(e){b.innerHTML='<span class="text-danger">'+esc(e.message)+'</span>'}finally{sending=false;input.focus()}}
 async function clear(){try{await call(clearUrl,{method:'POST',body:'{}'})}catch(e){}msgs.innerHTML='';renderState();suggestions()}
 build();
});