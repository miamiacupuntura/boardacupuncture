<?php
require_once(__DIR__ . '/../../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);

$course = $DB->get_record(
    'course',
    ['id' => $courseid],
    '*',
    MUST_EXIST
);

$section = $DB->get_record(
    'course_sections',
    ['id' => $sectionid, 'course' => $courseid],
    'id,course,section,name',
    MUST_EXIST
);

$existing = $DB->get_record(
    'scorm_studytopic_notes',
    [
        'userid' => $USER->id,
        'courseid' => $courseid,
        'sectionid' => $sectionid
    ]
);

$savednote = $existing ? $existing->note : '';

$topicuploadsdir = __DIR__ . '/uploads';

if (
    isset($_POST['upload_note_image']) &&
    isset($_FILES['note_image']) &&
    $_FILES['note_image']['error'] === UPLOAD_ERR_OK
) {
    require_sesskey();

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
        'image/bmp'  => 'bmp'
    ];

    $maxsize = 5 * 1024 * 1024;
    $mimetype = $_FILES['note_image']['type'] ?? '';
    $filesize = (int)($_FILES['note_image']['size'] ?? 0);

    if (isset($allowed[$mimetype]) && $filesize > 0 && $filesize <= $maxsize) {

        if (!is_dir($topicuploadsdir)) {
            mkdir($topicuploadsdir, 0755, true);
        }

        $extension = $allowed[$mimetype];

        $filename =
            'user_' . $USER->id .
            '_course_' . $courseid .
            '_section_' . $sectionid .
            '_' . time() .
            '_' . bin2hex(random_bytes(4)) .
            '.' . $extension;

        $destination = $topicuploadsdir . '/' . $filename;

        if (move_uploaded_file($_FILES['note_image']['tmp_name'], $destination)) {

            if ($existing && !empty($existing->imagefile)) {
                $oldimage = $topicuploadsdir . '/' . basename($existing->imagefile);

                if (is_file($oldimage)) {
                    unlink($oldimage);
                }
            }

            if ($existing) {
                $existing->imagefile = $filename;
                $existing->timemodified = time();
                $DB->update_record('scorm_studytopic_notes', $existing);
            } else {
                $newnote = new stdClass();
                $newnote->userid = $USER->id;
                $newnote->courseid = $courseid;
                $newnote->sectionid = $sectionid;
                $newnote->note = '';
                $newnote->imagefile = $filename;
                $newnote->timecreated = time();
                $newnote->timemodified = time();

                $DB->insert_record('scorm_studytopic_notes', $newnote);
            }
        }
    }

    redirect(
        new moodle_url('/mod/scorm/studytopic/index.php', [
            'courseid' => $courseid,
            'sectionid' => $sectionid
        ])
    );
}

if (optional_param('remove_image', 0, PARAM_BOOL) && $existing && !empty($existing->imagefile)) {
    require_sesskey();

    $imagepath = $topicuploadsdir . '/' . basename($existing->imagefile);

    if (is_file($imagepath)) {
        unlink($imagepath);
    }

    $existing->imagefile = null;
    $existing->timemodified = time();
    $DB->update_record('scorm_studytopic_notes', $existing);

    redirect(
        new moodle_url('/mod/scorm/studytopic/index.php', [
            'courseid' => $courseid,
            'sectionid' => $sectionid
        ])
    );
}

if (optional_param('save_note', 0, PARAM_BOOL)) {
    require_sesskey();

    $note = optional_param('note', '', PARAM_RAW);

    if ($existing) {
        $existing->note = $note;
        $existing->timemodified = time();
        $DB->update_record('scorm_studytopic_notes', $existing);
    } else {
        $newnote = new stdClass();
        $newnote->userid = $USER->id;
        $newnote->courseid = $courseid;
        $newnote->sectionid = $sectionid;
        $newnote->note = $note;
        $newnote->imagefile = null;
        $newnote->timecreated = time();
        $newnote->timemodified = time();

        $DB->insert_record('scorm_studytopic_notes', $newnote);
    }

    redirect(
        new moodle_url('/mod/scorm/studytopic/index.php', [
            'courseid' => $courseid,
            'sectionid' => $sectionid
        ])
    );
}

if (optional_param('clear_note', 0, PARAM_BOOL) && $existing) {
    require_sesskey();

    $existing->note = '';
    $existing->timemodified = time();
    $DB->update_record('scorm_studytopic_notes', $existing);

    $savednote = '';
}

$exams = $DB->get_records_sql(
    "SELECT cm.id AS cmid,
            cm.instance AS scormid,
            s.name,
            s.launch
       FROM {course_modules} cm
       JOIN {scorm} s ON s.id = cm.instance
      WHERE cm.course = :courseid
        AND cm.section = :sectionid
        AND cm.module = (
            SELECT id FROM {modules} WHERE name = 'scorm'
        )
      ORDER BY cm.id ASC",
    [
        'courseid' => $courseid,
        'sectionid' => $sectionid
    ]
);

$PAGE->set_url(new moodle_url('/mod/scorm/studytopic/index.php', [
    'courseid' => $courseid,
    'sectionid' => $sectionid
]));

$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_title('Optional Study Guide');
$PAGE->set_heading('');
$PAGE->set_pagelayout('incourse');

echo $OUTPUT->header();
?>

<style>
.topic-studyguide {
    max-width:1200px;
    margin:30px auto;
    padding:25px;
    background:#fff;
    border-radius:14px;
    box-shadow:0 4px 18px rgba(0,0,0,.08);
}

.topic-studyguide .top-course {
    margin:0 0 6px;
    font-size:24px;
    font-weight:700;
    color:#1f2937;
}

.topic-studyguide .topic-name {
    font-size:18px;
    font-weight:600;
    color:#374151;
    margin-bottom:4px;
}

.topic-studyguide .guide-label {
    font-size:16px;
    font-weight:500;
    color:#4b5563;
    margin-bottom:6px;
}

.topic-studyguide .optional-label {
    font-size:15px;
    font-weight:600;
    color:#6b7280;
    margin-bottom:14px;
}

.topic-studyguide .notice {
    color:#666;
    font-size:15px;
}

.topic-studyguide .navigation {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin:18px 0 25px;
    flex-wrap:wrap;
}

.topic-studyguide .button {
    display:inline-block;
    padding:12px 22px;
    border-radius:9px;
    color:white;
    text-decoration:none;
    font-weight:bold;
}

.topic-studyguide .button-back {
    background:#6c757d;
}

.topic-studyguide .button-orange {
    background:#f47e2c;
}

.topic-studyguide .content-card {
    margin:25px 0;
    padding:22px;
    border:1px solid #dfe5ee;
    border-radius:12px;
    background:#f8fafc;
}

.topic-studyguide .content-card h3 {
    margin:0 0 10px;
    color:#263238;
}

.topic-studyguide .study-question {
    background:#fff;
    border:1px solid #dfe5e8;
    border-radius:12px;
    padding:16px;
    margin:10px 0;
}

.topic-studyguide .memory-box {
    margin-top:15px;
    padding:14px 16px;
    background:#eef4ff;
    border-left:4px solid #3b82f6;
    border-radius:8px;
}

.topic-studyguide .exam-list {
    display:flex;
    flex-direction:column;
    gap:8px;
}

.topic-studyguide .exam-item {
    border:1px solid #d9d9d9;
    border-radius:8px;
    padding:12px 14px;
    background:#fff;
}

@media (max-width:600px) {
    .topic-studyguide {
        margin:15px auto;
        padding:16px;
        border-radius:12px;
    }

    .topic-studyguide .top-course {
        font-size:21px;
    }

    .topic-studyguide .navigation {
        align-items:stretch;
    }

    .topic-studyguide .button {
        text-align:center;
        width:100%;
    }
}
</style>

<div class="topic-studyguide">

    <h2 class="top-course">
        <?php echo strtoupper(format_string($course->shortname)); ?>
    </h2>

    <div class="topic-name">
        <?php echo format_string($section->name); ?>
    </div>

    <div class="guide-label">
        Topic Study Guide
    </div>

    <div class="optional-label">
        Optional Study Guide
    </div>

    <p class="notice">
        Review this material before starting the exams in this Topic.
        This study guide is optional and does not affect your attempts or grades.
    </p>

    <hr>

    <div class="navigation">

        <a
            href="/mod/scorm/examhistory/?courseid=<?php echo (int)$courseid; ?>&sectionid=<?php echo (int)$sectionid; ?>"
            class="button button-back"
        >
            ← Back to Exam History
        </a>

    </div>

    <div class="content-card">

        <h3>👨‍🏫 Instructor Study Materials</h3>

        <p style="margin:0;color:#667085;">
            Topic-level study materials will be placed here.
        </p>

    </div>

    <div class="content-card">

        <h3>🖼️ Instructor Visual Materials</h3>

        <p style="margin:0;color:#667085;">
            Visual study materials for this Topic will be placed here.
        </p>

    </div>

    <div class="content-card">

        <h3>🤖 Instructor AI</h3>

        <p style="margin:0 0 15px;color:#667085;">
            Use Instructor AI to create, improve, compare, explain, and organize
            study material for this Topic.
        </p>

        <textarea
            id="topic_instructor_ai_prompt"
            rows="5"
            style="width:100%;box-sizing:border-box;padding:12px;border:1px solid #d0d5dd;border-radius:8px;resize:vertical;"
            placeholder="Write your request to Instructor AI..."
        ></textarea>

        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;">

            <button
                type="button"
                class="topic-ai-action"
                data-prompt="Create a concise study explanation of the main concepts in this Topic.">
                Explain Topic
            </button>

            <button
                type="button"
                class="topic-ai-action"
                data-prompt="Create useful comparisons between the related concepts contained in this Topic.">
                Compare Concepts
            </button>

            <button
                type="button"
                class="topic-ai-action"
                data-prompt="Identify the most important board-examination clues and relationships contained in this Topic.">
                Board Clues
            </button>

            <button
                type="button"
                class="topic-ai-action"
                data-prompt="Suggest memory strategies based only on the material contained in this Topic.">
                Memory Strategies
            </button>

            <button
                type="button"
                id="topic_instructor_ai_button"
                style="background:#475467;color:#fff;border:0;border-radius:7px;padding:9px 14px;font-weight:bold;cursor:pointer;"
            >
                Ask Instructor AI
            </button>

        </div>

        <div
            id="topic_instructor_ai_status"
            style="margin-top:12px;color:#667085;font-size:14px;"
        ></div>

        <div
            id="topic_instructor_ai_response"
            style="display:none;margin-top:15px;padding:15px;background:#f8fafc;border:1px solid #e4e7ec;border-radius:8px;white-space:pre-wrap;line-height:1.55;"
        ></div>

    </div>

    <div class="content-card">

        <h3>📝 Create Questions</h3>

        <p style="margin:0 0 15px;color:#667085;">
            Generate new Topic-based questions for review, editing,
            approval, and future use in the Question Bank or Mock Exams.
        </p>

        <div style="margin-bottom:12px;">

            <label for="topic_question_type" style="display:block;font-weight:bold;margin-bottom:6px;">
                Question Type
            </label>

            <select
                id="topic_question_type"
                style="width:100%;box-sizing:border-box;padding:10px;border:1px solid #d0d5dd;border-radius:8px;"
            >
                <option value="multiple_choice">Multiple Choice</option>
                <option value="true_false">True / False</option>
                <option value="matching">Matching</option>
                <option value="visual_identification">Visual Identification</option>
                <option value="mock">Mock</option>
            </select>

        </div>

        <div style="margin-bottom:12px;">

            <label for="topic_question_count" style="display:block;font-weight:bold;margin-bottom:6px;">
                Number of Questions
            </label>

            <select
                id="topic_question_count"
                style="width:100%;box-sizing:border-box;padding:10px;border:1px solid #d0d5dd;border-radius:8px;"
            >
                <option value="1">1</option>
                <option value="5" selected>5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>

        </div>

        <button
            type="button"
            id="topic_generate_questions_button"
            style="background:#475467;color:#fff;border:0;border-radius:7px;padding:9px 14px;font-weight:bold;cursor:pointer;"
        >
            Generate Questions
        </button>

        <div
            id="topic_question_generation_status"
            style="margin-top:12px;color:#667085;font-size:14px;"
        ></div>

        <div
            id="topic_generated_questions"
            style="display:none;margin-top:15px;"
        ></div>

    </div>

    <div class="content-card">

        <h3>👨‍🎓 My Study Guide</h3>

        <p style="margin:0 0 15px;color:#667085;">
            Build your own study guide using your notes, key concepts,
            comparisons, questions, and explanations.
        </p>

        <div class="study-question">
            <strong>Possible Question Types</strong>
            <p style="margin:8px 0 0;color:#555;">
                Use this Topic Guide to identify what you should be able
                to recognize, compare, order, connect, or identify before
                taking the exams.
            </p>
        </div>

        <div class="study-question">
            <strong>How to Approach the Questions</strong>
            <p style="margin:8px 0 0;color:#555;">
                Focus first on the relationship being tested. Then identify
                the category, sequence, connection, or visual clue that
                determines the answer.
            </p>
        </div>

        <div class="memory-box">
            <strong>Memory Strategy</strong>
            <p style="margin:8px 0 0;color:#555;">
                Build short memory associations and compare related items
                instead of memorizing isolated facts.
            </p>
        </div>

    </div>

    <div class="content-card">

        <h3>📝 My Notes</h3>

        <p style="margin:0 0 12px;color:#667085;">
            Write your personal notes for this Topic here.
        </p>

        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">

            <textarea
                name="note"
                style="width:100%;min-height:180px;padding:15px;border:1px solid #ddd;border-radius:10px;"
                placeholder="Write your personal notes here..."
            ><?php echo htmlspecialchars($savednote ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

            <?php if (!empty($existing) && !empty($existing->imagefile)): ?>

                <div style="margin:15px 0;text-align:center;">
                    <img
                        src="/mod/scorm/studytopic/uploads/<?php echo htmlspecialchars($existing->imagefile, ENT_QUOTES, 'UTF-8'); ?>"
                        style="display:block;max-width:600px;width:100%;height:auto;margin:0 auto;border-radius:10px;"
                        alt="My Study Guide image"
                    >
                </div>

            <?php endif; ?>

            <div style="margin-top:12px;">
                <label style="font-weight:bold;">Add Image</label><br>
                <input type="file" name="note_image" accept="image/*">
            </div>

            <div style="margin-top:10px;">
                <button
                    type="submit"
                    name="upload_note_image"
                    value="1"
                    style="padding:10px 18px;border:0;border-radius:8px;background:#4f6bed;color:white;font-weight:bold;"
                >
                    Upload Image
                </button>
            </div>

            <?php if (!empty($existing) && !empty($existing->imagefile)): ?>

                <div style="margin-top:10px;">
                    <button
                        type="submit"
                        name="remove_image"
                        value="1"
                        onclick="return confirm('Remove the current image?');"
                        style="padding:10px 18px;border:0;border-radius:8px;background:#dc3545;color:white;font-weight:bold;"
                    >
                        Remove Image
                    </button>
                </div>

            <?php endif; ?>

            <div style="margin-top:15px;display:flex;gap:10px;flex-wrap:wrap;">

                <button
                    type="submit"
                    name="save_note"
                    value="1"
                    style="padding:10px 18px;border:0;border-radius:8px;background:#f47e2c;color:white;font-weight:bold;"
                >
                    Save Note
                </button>

                <?php if (!empty($existing) && !empty($existing->note)): ?>

                    <button
                        type="submit"
                        name="clear_note"
                        value="1"
                        onclick="return confirm('Clear your note?');"
                        style="padding:10px 18px;border:0;border-radius:8px;background:#dc3545;color:white;font-weight:bold;"
                    >
                        Clear Note
                    </button>

                <?php endif; ?>

            </div>
        </form>

    </div>

    <div class="content-card">

        <h3>📚 Exams in this Topic</h3>

        <?php if (empty($exams)): ?>

            <p>No exams were found for this Topic.</p>

        <?php else: ?>

            <div class="exam-list">

                <?php foreach ($exams as $exam): ?>

                    <div class="exam-item">
                        <strong>
                            <?php echo format_string($exam->name); ?>
                        </strong>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<script>
(function() {

    const promptBox = document.getElementById('topic_instructor_ai_prompt');
    const askButton = document.getElementById('topic_instructor_ai_button');
    const statusBox = document.getElementById('topic_instructor_ai_status');
    const responseBox = document.getElementById('topic_instructor_ai_response');

    if (!promptBox || !askButton || !statusBox || !responseBox) {
        return;
    }

    document.querySelectorAll('.topic-ai-action').forEach(function(button) {
        button.addEventListener('click', function() {
            promptBox.value = this.getAttribute('data-prompt') || '';
            promptBox.focus();
        });
    });

    askButton.addEventListener('click', async function() {

        const prompt = promptBox.value.trim();

        if (!prompt) {
            statusBox.textContent = 'Please enter an Instructor AI request.';
            responseBox.style.display = 'none';
            return;
        }

        askButton.disabled = true;
        statusBox.textContent = 'Instructor AI is thinking...';
        responseBox.style.display = 'none';

        try {

            const params = new URLSearchParams({
                courseid: '<?php echo (int)$courseid; ?>',
                sectionid: '<?php echo (int)$sectionid; ?>',
                prompt: prompt,
                sesskey: M.cfg.sesskey
            });

            const response = await fetch(
                '<?php echo (new moodle_url('/mod/scorm/studytopic/instructor_ai.php'))->out(false); ?>',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: params.toString()
                }
            );

            const data = await response.json();

            if (data.success) {
                responseBox.textContent = data.response || '';
                responseBox.style.display = 'block';
                statusBox.textContent = '';
            } else {
                statusBox.textContent = data.error || 'Instructor AI request failed.';
            }

        } catch (error) {

            statusBox.textContent = 'Unable to connect to the Instructor AI service.';

        } finally {

            askButton.disabled = false;

        }

    });

})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const generateButton = document.getElementById('topic_generate_questions_button');
    const typeBox = document.getElementById('topic_question_type');
    const countBox = document.getElementById('topic_question_count');
    const statusBox = document.getElementById('topic_question_generation_status');
    const questionsBox = document.getElementById('topic_generated_questions');
    if (!generateButton || !typeBox || !countBox || !statusBox || !questionsBox) {
        return;
    }

    generateButton.addEventListener('click', async function() {

        const questiontype = typeBox.value;
        const count = countBox.value;

        generateButton.disabled = true;
        statusBox.textContent = 'Generating questions...';
        questionsBox.style.display = 'none';
        questionsBox.innerHTML = '';

        try {

            const params = new URLSearchParams({
                courseid: '<?php echo (int)$courseid; ?>',
                sectionid: '<?php echo (int)$sectionid; ?>',
                questiontype: questiontype,
                count: count,
                sesskey: '<?php echo sesskey(); ?>',
                source_question: ''
            });

            const response = await fetch(
                'create_questions.php',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: params.toString()
                }
            );

            const data = await response.json();

            if (!data.success) {
                statusBox.textContent = data.error || 'Question generation failed.';
                return;
            }

            statusBox.textContent = 'Questions generated successfully.';
            questionsBox.style.display = 'block';

            data.questions.forEach(function(item, index) {

                const card = document.createElement('div');

                card.style.cssText =
                    'margin-bottom:15px;' +
                    'padding:15px;' +
                    'background:#f8fafc;' +
                    'border:1px solid #e4e7ec;' +
                    'border-radius:8px;';

                const title = document.createElement('strong');
                title.textContent = 'Question ' + (index + 1);
                card.appendChild(title);

                if (item.image_url) {
                    const image = document.createElement('img');
                    image.src = item.image_url;
                    image.alt = 'Visual Identification';
                    image.style.display = 'block';
                    image.style.maxWidth = '100%';
                    image.style.width = 'auto';
                    image.style.maxHeight = '420px';
                    image.style.margin = '12px auto';
                    image.style.border = '1px solid #ddd';
                    image.style.borderRadius = '4px';
                    card.appendChild(image);
                }

                const question = document.createElement('p');
                question.style.margin = '10px 0';
                question.textContent = item.question || '';
                card.appendChild(question);

                if (Array.isArray(item.options)) {

                    const options = document.createElement('div');
                    options.style.marginBottom = '10px';

                    item.options.forEach(function(option, optionIndex) {
                        const optionElement = document.createElement('div');
                        optionElement.textContent =
                            String.fromCharCode(65 + optionIndex) + '. ' + option;
                        options.appendChild(optionElement);
                    });

                    card.appendChild(options);
                }

                if (Array.isArray(item.pairs)) {

                    const matchingAnswer = document.createElement('div');
                    matchingAnswer.style.margin = '8px 0';

                    const matchingTitle = document.createElement('strong');
                    matchingTitle.textContent = 'Answer:';
                    matchingAnswer.appendChild(matchingTitle);

                    const matchingList = document.createElement('div');
                    matchingList.style.marginTop = '6px';

                    item.pairs.forEach(function(pair) {

                        const pairRow = document.createElement('div');
                        pairRow.style.marginBottom = '4px';

                        pairRow.textContent =
                            (pair.left || '') + ' → ' + (pair.right || '');

                        matchingList.appendChild(pairRow);
                    });

                    matchingAnswer.appendChild(matchingList);
                    card.appendChild(matchingAnswer);

                } else {

                    const answer = document.createElement('p');
                    answer.style.margin = '8px 0';
                    answer.innerHTML = '<strong>Answer:</strong> ';
                    answer.appendChild(
                        document.createTextNode(item.answer || '')
                    );
                    card.appendChild(answer);
                }

                const explanation = document.createElement('p');
                explanation.style.margin = '8px 0 0';
                explanation.innerHTML = '<strong>Explanation:</strong> ';
                explanation.appendChild(
                    document.createTextNode(item.explanation || '')
                );
                card.appendChild(explanation);

                if (
                    questiontype === 'mock' &&
                    item.choice_feedback &&
                    typeof item.choice_feedback === 'object'
                ) {
                    const choiceFeedbackTitle = document.createElement('p');
                    choiceFeedbackTitle.style.margin = '12px 0 4px';
                    choiceFeedbackTitle.innerHTML =
                        '<strong>Why the other choices are correct or incorrect:</strong>';
                    card.appendChild(choiceFeedbackTitle);

                    ['A', 'B', 'C', 'D'].forEach(function(letter) {
                        const feedbackText = item.choice_feedback[letter];

                        if (!feedbackText) {
                            return;
                        }

                        const feedbackItem = document.createElement('p');
                        feedbackItem.style.margin = '4px 0';
                        feedbackItem.appendChild(
                            document.createTextNode(letter + '. ' + feedbackText)
                        );
                        card.appendChild(feedbackItem);
                    });
                }

                questionsBox.appendChild(card);
            });

        } catch (error) {

            statusBox.textContent =
                'Unable to connect to the question generation service.';

        } finally {

            generateButton.disabled = false;

        }

    });

});
</script>

<?php
echo $OUTPUT->footer();
