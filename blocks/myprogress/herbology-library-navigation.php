<?php
// Shared navigation for resources opened from the Chinese Herbology Library.

defined('MOODLE_INTERNAL') || die();

function block_myprogress_from_herbology_library(): bool {
    return optional_param('from', '', PARAM_ALPHA) === 'library';
}

function block_myprogress_render_herbology_library_navigation(bool $ises, string $currentlabel): string {
    $langparams = $ises ? ['lang' => 'es'] : [];
    $libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', [
        'area' => 'herbology',
        'view' => 'library',
    ] + $langparams);
    $guidesurl = clone $libraryurl;
    $guidesurl->set_anchor('library-group-core-guides');
    $practiceurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'herbs'] + $langparams);
    $quizurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'quiz'] + $langparams);
    $examsurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'exams'] + $langparams);
    $routes = [
        [$ises ? 'Todos los recursos' : 'All resources', $libraryurl, true],
        [$ises ? 'Guías de estudio' : 'Study Guides', $guidesurl, false],
        ['Herbology Practice', $practiceurl, false],
        ['Quiz', $quizurl, false],
        [$ises ? 'Exámenes' : 'Exams', $examsurl, false],
    ];
    ob_start();
    ?>
    <style>
    .herbology-library-context{margin:0 0 18px;padding:14px;border:1px solid #b8d5d2;border-radius:11px;background:#f7fcfb;box-shadow:0 3px 12px rgba(15,23,42,.04)}
    .herbology-library-context__trail{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 0 11px;color:#64748b;font-size:12px;font-weight:800}.herbology-library-context__trail a{color:#245fa7;text-decoration:none}.herbology-library-context__trail-current{color:#17232a}
    .herbology-library-context__routes{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px}.herbology-library-context__route{display:flex;min-height:44px;align-items:center;justify-content:center;padding:8px 10px;border:1px solid #bfd0da;border-radius:8px;background:#fff;color:#17344a;font-size:12px;font-weight:850;text-align:center;text-decoration:none}.herbology-library-context__route:hover,.herbology-library-context__route:focus{border-color:#0b817d;background:#edf9f6;color:#075f5b;text-decoration:none}.herbology-library-context__route--library{border-color:#0b817d;background:#0b817d;color:#fff}.herbology-library-context__route--library:hover,.herbology-library-context__route--library:focus{background:#076b67;color:#fff}
    @media(max-width:700px){.herbology-library-context{padding:11px}.herbology-library-context__routes{grid-template-columns:1fr 1fr}.herbology-library-context__route{min-height:48px}.herbology-library-context__route:last-child{grid-column:1/-1}}
    </style>
    <nav class="herbology-library-context" aria-label="<?php echo s($ises ? 'Navegación de Library' : 'Library navigation'); ?>" data-herbology-library-context>
        <div class="herbology-library-context__trail">
            <a href="<?php echo $libraryurl->out(false); ?>"><?php echo s($ises ? 'Biblioteca' : 'Library'); ?></a>
            <span aria-hidden="true">›</span>
            <span class="herbology-library-context__trail-current" aria-current="page"><?php echo s($currentlabel); ?></span>
        </div>
        <div class="herbology-library-context__routes">
            <?php foreach ($routes as [$label, $url, $active]): ?>
                <a class="herbology-library-context__route<?php echo $active ? ' herbology-library-context__route--library' : ''; ?>" href="<?php echo $url->out(false); ?>"><?php echo s($label); ?></a>
            <?php endforeach; ?>
        </div>
    </nav>
    <script>
    (() => {
        const applyContext = () => {
            const allowed = /^(?:\/blocks\/myprogress\/(?:single-herbs-study-guide|formulas-study-guide|herb-formula-category-map|common-herbal-combinations)\.php|\/study-guide\/comparisons\/[^/]+\.html)$/;
            document.querySelectorAll('a[href]').forEach(link => {
                const destination = new URL(link.href, window.location.href);
                if (destination.origin === window.location.origin && allowed.test(destination.pathname)) {
                    destination.searchParams.set('from', 'library');
                    link.href = destination.toString();
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
