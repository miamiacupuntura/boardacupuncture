<?php
// Shared navigation for resources opened from the Chinese Herbology Board Level activity map.

defined('MOODLE_INTERNAL') || die();

function block_myprogress_from_herbology_library(): bool {
    // Library is the permanent navigation hub for the Board Level herbology resources.
    return true;
}

function block_myprogress_render_herbology_library_navigation(
    bool $ises,
    string $currentlabel,
    string $activesection = 'library'
): string {
    $langparams = $ises ? ['lang' => 'es'] : [];
    $libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', [
        'area' => 'herbology',
        'view' => 'library',
    ] + $langparams);
    ob_start();
    ?>
    <style>
    .herbology-library-context{margin:0 0 12px;padding:9px 12px;border:1px solid #b8d5d2;border-radius:10px;background:#f7fcfb;box-shadow:0 2px 8px rgba(15,23,42,.04)}
    .herbology-library-context__top{display:flex;align-items:center;gap:12px}.herbology-library-context__trail{display:flex;align-items:center;gap:8px;flex-wrap:wrap;color:#64748b;font-size:12px;font-weight:800}.herbology-library-context__step-back{display:inline-flex;min-height:36px;align-items:center;padding:6px 12px;border:1px solid #bfd0da;border-radius:7px;background:#fff;color:#245fa7;text-decoration:none}.herbology-library-context__step-back:hover,.herbology-library-context__step-back:focus{border-color:#0b817d;background:#edf9f6;color:#075f5b;text-decoration:none}.herbology-library-context__trail-current{color:#17232a}
    .herbology-sheet-hero{min-height:0!important;margin:0 0 18px!important;padding:18px 22px!important;border:1px solid #cbdce7!important;border-top:5px solid #f47e2c!important;border-radius:12px!important;background:linear-gradient(135deg,#f8fcfb 0%,#fff9f4 55%,#faf7fd 100%)!important;box-shadow:0 4px 14px rgba(23,50,77,.06)!important}.herbology-sheet-hero :is(.single-herbs-guide__eyebrow,.single-herbs-guide__lead,.formula-study-guide__eyebrow,.formula-study-guide__lead,.formula-study-guide__marker-note,.herb-formula-map__eyebrow,.herb-formula-map__lead,.herb-combos__eyebrow){display:none!important}.herbology-sheet-hero.herb-combos__hero>p{display:none!important}.herbology-sheet-hero :is(h1,h2){margin:0!important;font-size:clamp(28px,4vw,40px)!important;line-height:1.12!important}
    @media(max-width:700px){.herbology-library-context{padding:8px 9px}.herbology-library-context__trail{min-width:0}.herbology-library-context__step-back{min-height:40px}.herbology-sheet-hero{padding:15px 17px!important}.herbology-sheet-hero :is(h1,h2){font-size:clamp(25px,8vw,32px)!important}}
    </style>
    <nav class="herbology-library-context" aria-label="<?php echo s($ises ? 'Regresar a las actividades Board Level' : 'Return to Board Level activities'); ?>" data-herbology-library-context>
        <div class="herbology-library-context__top">
            <div class="herbology-library-context__trail">
                <a class="herbology-library-context__step-back" href="<?php echo $libraryurl->out(false); ?>"><?php echo s($ises ? '← Actividades Board Level' : '← Board Level Activities'); ?></a>
            </div>
        </div>
    </nav>
    <script>
    (() => {
        const allowed = /^(?:\/blocks\/myprogress\/(?:single-herbs-study-guide|formulas-study-guide|herb-formula-category-map|common-herbal-combinations)\.php|\/study-guide\/comparisons\/[^/]+\.html)$/;
        const applyContext = () => {
            document.querySelectorAll('a[href]').forEach(link => {
                const destination = new URL(link.href, window.location.href);
                if (destination.origin === window.location.origin && allowed.test(destination.pathname)) {
                    destination.searchParams.set('from', 'library');
                    link.href = destination.toString();
                } else if (destination.origin === window.location.origin
                        && /^\/my\/?$/.test(destination.pathname)
                        && /dashboard/i.test(link.textContent)) {
                    link.remove();
                }
            });
            document.querySelectorAll('form[action]').forEach(form => {
                const destination = new URL(form.action, window.location.href);
                if (destination.origin !== window.location.origin || !allowed.test(destination.pathname)) {
                    return;
                }
                let context = form.querySelector('input[name="from"]');
                if (!context) {
                    context = document.createElement('input');
                    context.type = 'hidden';
                    context.name = 'from';
                    form.appendChild(context);
                }
                context.value = 'library';
            });
        };
        const preserveLink = link => {
            if (!link) {
                return;
            }
            const destination = new URL(link.href, window.location.href);
            if (destination.origin === window.location.origin && allowed.test(destination.pathname)) {
                destination.searchParams.set('from', 'library');
                link.href = destination.toString();
            }
        };
        document.addEventListener('click', event => preserveLink(event.target.closest('a[href]')), true);
        document.addEventListener('submit', event => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }
            const destination = new URL(form.action, window.location.href);
            if (destination.origin !== window.location.origin || !allowed.test(destination.pathname)) {
                return;
            }
            let context = form.querySelector('input[name="from"]');
            if (!context) {
                context = document.createElement('input');
                context.type = 'hidden';
                context.name = 'from';
                form.appendChild(context);
            }
            context.value = 'library';
        }, true);
        const panel = document.querySelector('[data-herbology-library-context]');
        const sheetHero = panel?.parentElement?.querySelector(':scope > .herbology-sheet-hero');
        if (panel && sheetHero) {
            panel.parentElement.insertBefore(panel, sheetHero);
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyContext, {once: true});
        } else {
            applyContext();
        }
    })();
    </script>
    <?php
    return (string) ob_get_clean();
}
