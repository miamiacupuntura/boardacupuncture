<?php
// Shared navigation for resources opened from the Chinese Herbology Library.

defined('MOODLE_INTERNAL') || die();

function block_myprogress_from_herbology_library(): bool {
    // Library is the permanent navigation hub for the Board Level herbology resources.
    return true;
}

function block_myprogress_render_herbology_library_navigation(bool $ises, string $currentlabel): string {
    $langparams = $ises ? ['lang' => 'es'] : [];
    $libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', [
        'area' => 'herbology',
        'view' => 'library',
    ] + $langparams);
    $guidesurl = clone $libraryurl;
    $guidesurl->set_anchor('library-group-core-guides');
    $librarycontext = ['from' => 'library'] + $langparams;
    $practiceurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'herbs'] + $librarycontext);
    $quizurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'quiz'] + $librarycontext);
    $examsurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'exams'] + $librarycontext);
    $routes = [
        [$ises ? 'Inicio de Library' : 'Library home', $libraryurl, true],
        [$ises ? 'Guías de estudio' : 'Study Guides', $guidesurl, false],
        ['Herbology Practice', $practiceurl, false],
        ['Quiz', $quizurl, false],
        [$ises ? 'Exámenes' : 'Exams', $examsurl, false],
    ];
    ob_start();
    ?>
    <style>
    .herbology-library-context{margin:0 0 12px;padding:10px 12px;border:1px solid #b8d5d2;border-radius:10px;background:#f7fcfb;box-shadow:0 2px 8px rgba(15,23,42,.04)}
    .herbology-library-context__top{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 0 8px}.herbology-library-context__trail{display:flex;align-items:center;gap:8px;flex-wrap:wrap;color:#64748b;font-size:12px;font-weight:800}.herbology-library-context__trail a{color:#245fa7;text-decoration:none}.herbology-library-context__step-back{padding-right:8px;border-right:1px solid #bfd0da}.herbology-library-context__trail-current{color:#17232a}.herbology-library-context__toggle{display:none;min-height:38px;align-items:center;justify-content:center;padding:6px 10px;border:1px solid #0b817d;border-radius:8px;background:#fff;color:#075f5b;font-size:12px;font-weight:850;cursor:pointer}
    .herbology-library-context__routes{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:7px}.herbology-library-context__route{display:flex;min-height:38px;align-items:center;justify-content:center;padding:6px 9px;border:1px solid #bfd0da;border-radius:7px;background:#fff;color:#17344a;font-size:12px;font-weight:850;text-align:center;text-decoration:none}.herbology-library-context__route:hover,.herbology-library-context__route:focus{border-color:#0b817d;background:#edf9f6;color:#075f5b;text-decoration:none}.herbology-library-context__route--library{border-color:#0b817d;background:#0b817d;color:#fff}.herbology-library-context__route--library:hover,.herbology-library-context__route--library:focus{background:#076b67;color:#fff}
    .herbology-sheet-hero{min-height:0!important;margin:0 0 18px!important;padding:18px 22px!important;border:1px solid #cbdce7!important;border-top:5px solid #f47e2c!important;border-radius:12px!important;background:linear-gradient(135deg,#f8fcfb 0%,#fff9f4 55%,#faf7fd 100%)!important;box-shadow:0 4px 14px rgba(23,50,77,.06)!important}.herbology-sheet-hero :is(.single-herbs-guide__eyebrow,.single-herbs-guide__lead,.formula-study-guide__eyebrow,.formula-study-guide__lead,.formula-study-guide__marker-note,.herb-formula-map__eyebrow,.herb-formula-map__lead,.herb-combos__eyebrow){display:none!important}.herbology-sheet-hero.herb-combos__hero>p{display:none!important}.herbology-sheet-hero :is(h1,h2){margin:0!important;font-size:clamp(28px,4vw,40px)!important;line-height:1.12!important}
    @media(max-width:700px){.herbology-library-context{padding:9px 10px}.herbology-library-context__top{margin:0}.herbology-library-context__trail{min-width:0}.herbology-library-context__toggle{display:inline-flex;flex:0 0 auto}.herbology-library-context__routes{display:none;grid-template-columns:1fr 1fr;margin-top:9px}.herbology-library-context[data-expanded="true"] .herbology-library-context__routes{display:grid}.herbology-library-context__route{min-height:44px}.herbology-library-context__route:last-child{grid-column:1/-1}.herbology-sheet-hero{padding:15px 17px!important}.herbology-sheet-hero :is(h1,h2){font-size:clamp(25px,8vw,32px)!important}}
    </style>
    <nav class="herbology-library-context" aria-label="<?php echo s($ises ? 'Navegación de Library' : 'Library navigation'); ?>" data-herbology-library-context>
        <div class="herbology-library-context__top">
            <div class="herbology-library-context__trail">
                <a class="herbology-library-context__step-back" data-library-step-back href="<?php echo $libraryurl->out(false); ?>"><?php echo s($ises ? '← Atrás' : '← Back'); ?></a>
                <a href="<?php echo $libraryurl->out(false); ?>"><?php echo s($ises ? 'Biblioteca' : 'Library'); ?></a>
                <span aria-hidden="true">›</span>
                <span class="herbology-library-context__trail-current" aria-current="page"><?php echo s($currentlabel); ?></span>
            </div>
            <button class="herbology-library-context__toggle" type="button" aria-expanded="false" aria-controls="herbology-library-context-routes"><?php echo s($ises ? 'Ver navegación' : 'Show navigation'); ?></button>
        </div>
        <div class="herbology-library-context__routes" id="herbology-library-context-routes">
            <?php foreach ($routes as [$label, $url, $active]): ?>
                <a class="herbology-library-context__route<?php echo $active ? ' herbology-library-context__route--library' : ''; ?>" href="<?php echo $url->out(false); ?>"><?php echo s($label); ?></a>
            <?php endforeach; ?>
        </div>
    </nav>
    <script>
    (() => {
        const stepBackScrollKey = 'herbology-step-back-scroll';
        const revealStepBackDestination = event => {
            try {
                if (window.sessionStorage.getItem(stepBackScrollKey) !== 'top') {
                    return;
                }
                window.sessionStorage.removeItem(stepBackScrollKey);
                if (event?.persisted) {
                    window.location.reload();
                    return;
                }
                window.scrollTo(0, 0);
                window.requestAnimationFrame(() => window.scrollTo(0, 0));
            } catch (error) {
                // Navigation still works when session storage is unavailable.
            }
        };
        window.addEventListener('pageshow', revealStepBackDestination);
        revealStepBackDestination();
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
        const toggle = panel?.querySelector('.herbology-library-context__toggle');
        const stepBack = panel?.querySelector('[data-library-step-back]');
        const sheetHero = panel?.parentElement?.querySelector(':scope > .herbology-sheet-hero');
        if (panel && sheetHero) {
            panel.parentElement.insertBefore(panel, sheetHero);
        }
        stepBack?.addEventListener('click', event => {
            if (window.history.length > 1) {
                event.preventDefault();
                try {
                    window.sessionStorage.setItem(stepBackScrollKey, 'top');
                } catch (error) {
                    // History navigation still works when session storage is unavailable.
                }
                window.history.back();
            }
            // The Library URL on the link remains the safe fallback without history.
        });
        toggle?.addEventListener('click', () => {
            const expanded = panel.dataset.expanded !== 'true';
            panel.dataset.expanded = String(expanded);
            toggle.setAttribute('aria-expanded', String(expanded));
            toggle.textContent = expanded
                ? '<?php echo $ises ? 'Ocultar navegación' : 'Hide navigation'; ?>'
                : '<?php echo $ises ? 'Ver navegación' : 'Show navigation'; ?>';
        });
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
