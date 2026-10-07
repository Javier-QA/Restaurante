document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('aiFloatingAssistant')) return;

    const style = document.createElement('style');
    style.textContent = `
        #aiFloatingAssistant{position:fixed;right:24px;bottom:24px;z-index:1090;font-family:inherit}
        #aiFloatingButton{width:62px;height:62px;border:0;border-radius:50%;background:var(--primary,#ff8c00);color:#fff;box-shadow:0 8px 24px rgba(0,0,0,.22);display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;animation:aiPulse 2s infinite}
        #aiFloatingButton i{font-size:25px}
        #aiFloatingButton:before{content:"";position:absolute;inset:-7px;border:2px solid var(--primary,#ff8c00);border-radius:50%;opacity:.45;animation:aiRing 2s infinite}
        #aiFloatingPanel{position:absolute;right:0;bottom:78px;width:340px;background:#fff;border-radius:18px;box-shadow:0 18px 50px rgba(0,0,0,.22);overflow:hidden;display:none;border:1px solid rgba(0,0,0,.08)}
        #aiFloatingPanel.open{display:block;animation:aiUp .18s ease-out}
        .ai-float-head{background:var(--dark-bg,#063970);color:#fff;padding:14px 16px;display:flex;align-items:center;justify-content:space-between}
        .ai-float-head strong{font-size:15px}.ai-float-head small{opacity:.8;display:block}
        .ai-float-body{padding:14px}.ai-float-option{width:100%;border:1px solid #e7e7e7;background:#fff;border-radius:11px;padding:11px 12px;margin-bottom:9px;text-align:left;cursor:pointer}
        .ai-float-option:hover{border-color:var(--primary,#ff8c00);background:#fff8ef}
        .ai-float-option i{color:var(--primary,#ff8c00);margin-right:8px}
        .ai-float-foot{padding:10px 14px;border-top:1px solid #eee;text-align:center}
        .ai-float-foot a{color:var(--primary,#ff8c00);font-weight:600;text-decoration:none}
        @keyframes aiPulse{0%,100%{box-shadow:0 8px 24px rgba(0,0,0,.22)}50%{box-shadow:0 8px 30px rgba(0,0,0,.28),0 0 0 8px color-mix(in srgb,var(--primary,#ff8c00) 18%,transparent)}}
        @keyframes aiRing{0%{transform:scale(.95);opacity:.5}70%{transform:scale(1.12);opacity:0}100%{opacity:0}}
        @keyframes aiUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
        @media(max-width:480px){#aiFloatingAssistant{right:14px;bottom:14px}#aiFloatingPanel{width:min(340px,calc(100vw - 28px))}}
    `;
    document.head.appendChild(style);

    const root = document.createElement('div');
    root.id = 'aiFloatingAssistant';
    root.innerHTML = `
        <div id="aiFloatingPanel" role="dialog" aria-label="Inteligencia Artificial">
            <div class="ai-float-head">
                <div><strong><i class="bi bi-stars me-1"></i> Inteligencia Artificial</strong><small>Asistente del restaurante</small></div>
                <button type="button" class="btn btn-sm text-white" id="aiFloatClose" aria-label="Cerrar"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="ai-float-body">
                <button class="ai-float-option" data-ai-url="{{ route('ai.chat') }}"><i class="bi bi-chat-dots"></i><strong>Chat IA</strong><br><small class="text-muted ms-4">Haz preguntas en lenguaje natural.</small></button>
                <button class="ai-float-option" data-ai-url="{{ route('ai.assistant') }}"><i class="bi bi-graph-up"></i><strong>Asistente IA</strong><br><small class="text-muted ms-4">Analiza ventas, productos e inventario.</small></button>
                <button class="ai-float-option" data-ai-url="{{ route('settings.index') }}#ia"><i class="bi bi-sliders"></i><strong>Configuración IA</strong><br><small class="text-muted ms-4">Configura las opciones del sistema.</small></button>
            </div>
            <div class="ai-float-foot"><a href="{{ route('ai.assistant') }}">Abrir centro de Inteligencia IA <i class="bi bi-arrow-right"></i></a></div>
        </div>
        <button id="aiFloatingButton" type="button" aria-label="Abrir Inteligencia Artificial" aria-expanded="false"><i class="bi bi-stars"></i></button>
    `;
    document.body.appendChild(root);

    const button = document.getElementById('aiFloatingButton');
    const panel = document.getElementById('aiFloatingPanel');
    const close = document.getElementById('aiFloatClose');

    const toggle = () => {
        const open = panel.classList.toggle('open');
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    button.addEventListener('click', toggle);
    close.addEventListener('click', () => { panel.classList.remove('open'); button.setAttribute('aria-expanded','false'); });
    root.querySelectorAll('[data-ai-url]').forEach(el => el.addEventListener('click', () => { window.location.href = el.dataset.aiUrl; }));
    document.addEventListener('click', e => { if (!root.contains(e.target)) { panel.classList.remove('open'); button.setAttribute('aria-expanded','false'); } });
});
