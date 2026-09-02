(() => {
    const script = document.currentScript;
    const id = script.dataset.analyticsId;
    const key = 'turismosv_analytics_consent_v1';
    const lifetime = 180 * 86400000;
    let loaded = false;
    let choice = null;
    try {
        const saved = JSON.parse(localStorage.getItem(key));
        if (saved && ['accepted', 'rejected'].includes(saved.value) && saved.expires > Date.now()) choice = saved.value;
    } catch { /* Storage is optional; never assume consent. */ }

    function start() {
        if (!id || choice !== 'accepted') return;
        window['ga-disable-' + id] = false;
        if (loaded) return;
        loaded = true;
        window.dataLayer = window.dataLayer || [];
        window.gtag = function () { window.dataLayer.push(arguments); };
        window.gtag('consent', 'default', {
            analytics_storage: 'granted', ad_storage: 'denied',
            ad_user_data: 'denied', ad_personalization: 'denied',
        });
        window.gtag('js', new Date());
        // Do not send search terms, fragments, profile names or account pages.
        window.gtag('config', id, {
            page_location: location.origin + location.pathname,
            page_referrer: document.referrer ? new URL(document.referrer).origin : '',
            allow_google_signals: false,
            allow_ad_personalization_signals: false,
            cookie_expires: 15552000,
            cookie_update: false,
        });
        const tag = document.createElement('script');
        tag.async = true;
        tag.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(id);
        document.head.appendChild(tag);
    }

    function stop() {
        if (id) window['ga-disable-' + id] = true;
        // Remove GA cookies for this host and its parent domains.
        const parts = location.hostname.split('.');
        const domains = [''];
        for (let i = 0; i < parts.length - 1; i++) domains.push(parts.slice(i).join('.'));
        document.cookie.split(';').forEach(cookie => {
            const name = cookie.split('=')[0].trim();
            if (name !== '_ga' && !name.startsWith('_ga_')) return;
            domains.forEach(domain => {
                document.cookie = name + '=; Max-Age=0; path=/' + (domain ? '; domain=' + domain : '');
            });
        });
    }

    const style = document.createElement('style');
    style.textContent = '.analytics-notice{position:fixed;z-index:1100;bottom:20px;left:50%;transform:translateX(-50%);width:calc(100% - 32px);max-width:700px;padding:22px;border-radius:16px;background:#102d38;color:white;box-shadow:0 12px 40px #0004;font:15px/1.5 Arial,sans-serif}.analytics-notice p{margin:8px 0 16px}.analytics-actions{display:flex;gap:12px;flex-wrap:wrap;align-items:center}.analytics-actions button{padding:10px 16px;background:#f8f7f2;color:#102d38;border:0;border-radius:8px;cursor:pointer;font:inherit}.analytics-actions a{color:#fff;text-decoration:underline}.analytics-settings{position:fixed;bottom:8px;left:8px;z-index:1000;background:#102d38;color:#fff;border:1px solid #fff;padding:8px 12px;border-radius:8px;cursor:pointer}';
    document.head.appendChild(style);
    const settings = document.createElement('button');
    settings.type = 'button';
    settings.className = 'analytics-settings';
    settings.textContent = 'Preferencias de cookies';
    document.body.appendChild(settings);

    function show(focus = false) {
        if (document.querySelector('.analytics-notice')) return;
        const notice = document.createElement('aside');
        notice.className = 'analytics-notice';
        notice.setAttribute('role', 'dialog');
        notice.setAttribute('aria-label', 'Preferencias de cookies');
        notice.innerHTML = '<strong>Tú decides sobre la analítica</strong><p>Usamos cookies necesarias para que el sitio funcione. Si aceptas, Google Analytics nos ayuda a entender las visitas a nuestras páginas públicas. Puedes rechazarlo y seguir navegando.</p><div class="analytics-actions"><button type="button" data-choice="rejected">Rechazar</button><button type="button" data-choice="accepted">Aceptar analítica</button><a>Política de cookies</a></div>';
        const link = notice.querySelector('a');
        link.href = script.dataset.cookiePolicy;
        notice.querySelectorAll('button').forEach(button => button.addEventListener('click', () => {
            choice = button.dataset.choice;
            try { localStorage.setItem(key, JSON.stringify({ value: choice, expires: Date.now() + lifetime })); } catch { /* Keep choice for this page. */ }
            if (choice === 'accepted') start(); else stop();
            notice.remove();
            settings.focus();
        }));
        document.body.appendChild(notice);
        if (focus) notice.querySelector('button').focus();
    }
    settings.addEventListener('click', () => show(true));
    window.addEventListener('storage', event => {
        if (event.key === key || event.key === null) {
            choice = null;
            stop();
            // A fresh page reads the new preference before loading any tag.
            location.reload();
        }
    });
    if (choice === 'accepted') start();
    else stop();
    if (!choice && id) show();
})();
