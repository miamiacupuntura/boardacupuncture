(function() {
    'use strict';

    const start = async () => {
        const currentUrl = new URL(window.location.href);
        document.querySelectorAll('a[hreflang][href$=".html"], a[hreflang][href*=".html?"]').forEach(link => {
            const languageUrl = new URL(link.href, window.location.href);
            currentUrl.searchParams.forEach((value, key) => languageUrl.searchParams.set(key, value));
            link.href = languageUrl.toString();
        });

        let profile;
        try {
            const response = await fetch('/blocks/menu/assessment-session.php', {
                credentials: 'same-origin',
                headers: {'Accept': 'application/json'},
            });
            if (!response.ok) {
                return;
            }
            profile = await response.json();
        } catch (error) {
            return;
        }
        if (!profile?.supervised || document.querySelector('[data-supervised-student-profile]')) {
            return;
        }

        const spanish = currentUrl.searchParams.get('lang') === 'es'
            || currentUrl.pathname.endsWith('-es.html')
            || document.documentElement.lang.toLowerCase().startsWith('es');
        const banner = document.createElement('aside');
        banner.dataset.supervisedStudentProfile = 'true';
        banner.setAttribute('role', 'status');
        banner.style.cssText = [
            'position:relative',
            'z-index:10000',
            'display:flex',
            'flex-wrap:wrap',
            'align-items:center',
            'justify-content:space-between',
            'gap:12px',
            'padding:14px 18px',
            'border:2px solid #d97706',
            'background:#fff7e6',
            'color:#5f3700',
            'font:700 15px/1.4 Arial,sans-serif',
        ].join(';');

        const identity = document.createElement('div');
        const heading = document.createElement('strong');
        heading.style.cssText = 'display:block;font-size:17px;color:#7c3f00';
        heading.textContent = spanish
            ? `Perfil activo: estudiante — ${profile.student}`
            : `Active profile: student — ${profile.student}`;
        const detail = document.createElement('span');
        detail.style.cssText = 'display:block;margin-top:2px;font-weight:600';
        detail.textContent = spanish
            ? `Administrador supervisor: ${profile.administrator}. Las respuestas se guardarán para este estudiante.`
            : `Supervising administrator: ${profile.administrator}. Answers will be saved for this student.`;
        identity.append(heading, detail);

        const exit = document.createElement('a');
        exit.href = profile.exiturl;
        exit.style.cssText = 'display:inline-flex;padding:9px 12px;border-radius:8px;background:#9a4d00;color:#fff;text-decoration:none;font-weight:900';
        exit.textContent = spanish ? 'Salir del perfil del estudiante' : 'Exit student profile';
        exit.title = spanish
            ? 'Moodle solicitará iniciar sesión nuevamente como administrador.'
            : 'Moodle will ask you to sign in again as administrator.';

        banner.append(identity, exit);
        document.body.prepend(banner);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, {once: true});
    } else {
        void start();
    }
})();
