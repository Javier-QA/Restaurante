import './bootstrap';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/ai/settings', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) return;

        const data = await response.json();

        if (!data.admin || document.getElementById('ai-floating-widget')) {
            return;
        }

        const style = document.createElement('style');
        style.textContent = `
            #ai-floating-widget{position:fixed;right:24px;bottom:24px;z-index:1080;font-family:inherit}
            #ai-floating-bubble{width:60px;height:60px;border:0;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#fff;background:linear-gradient(135deg,var(--primary),var(--primary-hover));box-shadow:0 10px 28px rgba(0,0,0,.22)}
            #ai-floating-window{display:none;position:absolute;right:0;bottom:72px;width:min(390px,calc(100vw - 28px));height:min(600px,calc(100vh - 100px));background:#fff;border:1px solid #dce5ee;border-radius:18px;overflow:hidden;box-shadow:0 18px 50px rgba(0,0,0,.22)}
            #ai-floating-window.open{display:flex;flex-direction:column}
            .ai-head{padding:14px 16px;background:linear-gradient(135deg,var(--primary),var(--primary-hover));color:#fff;display:flex;align-items:center;justify-content:space-between}
            .ai-head-main{display:flex;align-items:center;gap:10px}.ai-head-icon{width:38px;height:38px;border-radius:11px;background:rgba(255,255,255,.15);display:grid;place-items:center}
            .ai-close{border:0;background:transparent;color:#fff;font-size:20px}.ai-messages{flex:1;overflow:auto;padding:14px;background:var(--light-bg)}
            .ai-msg{max-width:88%;padding:10px 12px;border-radius:13px;margin-bottom:9px;white-space:pre-wrap;font-size:14px;line-height:1.45}
            .ai-msg.bot{background:var(--card-bg);border:1px solid var(--border-soft)}.ai-msg.user{margin-left:auto;background:var(--primary);color:#fff}
            .ai-suggestions{padding:10px;background:#f5f8fb;display:flex;gap:6px;flex-wrap:wrap}.ai-suggestion{border:1px solid #d0dce7;background:#fff;border-radius:999px;padding:6px 9px;font-size:12px;color:var(--primary);cursor:pointer}
            .ai-form{display:flex;gap:8px;padding:10px;border-top:1px solid var(--border-soft)}.ai-input{flex:1;border:1px solid var(--border-soft);border-radius:11px;padding:9px;resize:none}.ai-send{width:44px;border:0;border-radius:11px;background:var(--primary);color:#fff}
            @media(max-width:600px){#ai-floating-widget{right:12px;bottom:12px}}
        `;
        document.head.appendChild(style);

        const widget = document.createElement('div');
        widget.id = 'ai-floating-widget';
        widget.innerHTML = `
            <div id="ai-floating-window" role="dialog" aria-label="Chat IA">
                <div class="ai-head">
                    <div class="ai-head-main"><div class="ai-head-icon"><i class="bi bi-stars"></i></div><div><strong>Chat IA</strong><div style="font-size:11px;opacity:.8">Asistente del restaurante</div></div></div>
                    <button class="ai-close" type="button" aria-label="Cerrar">×</button>
                </div>
                <div class="ai-messages" id="ai-floating-messages"><div class="ai-msg bot">Hola. ¿Qué deseas consultar del restaurante?</div></div>
                <div class="ai-suggestions">
                    <button class="ai-suggestion" data-question="¿Cuáles son los 5 productos más vendidos?">Más vendidos</button>
                    <button class="ai-suggestion" data-question="¿Cuánto se vendió hoy?">Ventas de hoy</button>
                    <button class="ai-suggestion" data-question="¿Qué productos tienen poco stock?">Stock bajo</button>
                </div>
                <form class="ai-form" id="ai-floating-form">
                    <textarea class="ai-input" id="ai-floating-input" rows="1" maxlength="500" placeholder="Escribe una consulta..."></textarea>
                    <button class="ai-send" type="submit" aria-label="Enviar"><i class="bi bi-send-fill"></i></button>
                </form>
            </div>
            <button id="ai-floating-bubble" type="button" aria-label="Abrir Chat IA" title="Chat IA"><i class="bi bi-stars fs-4"></i></button>
        `;
        document.body.appendChild(widget);

        const panel=widget.querySelector('#ai-floating-window');
        const bubble=widget.querySelector('#ai-floating-bubble');
        const close=widget.querySelector('.ai-close');
        const form=widget.querySelector('#ai-floating-form');
        const input=widget.querySelector('#ai-floating-input');
        const messages=widget.querySelector('#ai-floating-messages');
        const send=widget.querySelector('.ai-send');

        const add=(text,type='bot')=>{const el=document.createElement('div');el.className=`ai-msg ${type}`;el.textContent=text;messages.appendChild(el);messages.scrollTop=messages.scrollHeight};

        const ask=async(q)=>{
            q=q.trim(); if(!q||send.disabled)return;
            add(q,'user'); input.value=''; send.disabled=true;
            try{
                const csrf=document.querySelector('meta[name="csrf-token"]')?.content||'';
                const res=await fetch('/ai/chat/ask',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},credentials:'same-origin',body:JSON.stringify({message:q})});
                const body=await res.json();
                add(body.success?body.answer:(body.message||'No fue posible procesar la consulta.'));
            }catch(e){add('No se pudo establecer comunicación con el servidor.')}
            finally{send.disabled=false;input.focus()}
        };

        bubble.addEventListener('click',()=>{panel.classList.toggle('open');if(panel.classList.contains('open'))input.focus()});
        close.addEventListener('click',()=>panel.classList.remove('open'));
        form.addEventListener('submit',e=>{e.preventDefault();ask(input.value)});
        widget.querySelectorAll('.ai-suggestion').forEach(b=>b.addEventListener('click',()=>ask(b.dataset.question)));
    } catch (e) {
        console.warn('Chat IA flotante no disponible.');
    }
});
