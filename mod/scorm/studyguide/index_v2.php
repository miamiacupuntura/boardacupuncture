<?php

require_once(__DIR__ . '/../../../config.php');

require_login();
$scormid = required_param('id', PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);

if (!$cmid) {
    $scormmoduleid = $DB->get_field('modules', 'id', ['name' => 'scorm']);

    if ($scormmoduleid) {
        $cmrecord = $DB->get_record(
            'course_modules',
            ['module' => $scormmoduleid, 'instance' => $scormid],
            'id,course,section,instance'
        );

        if ($cmrecord) {
            $cmid = $cmrecord->id;
        }
    }
}

$studysection = $cmid ? $DB->get_record('course_modules', ['id' => $cmid], 'id,course,section,instance') : false;
$studysectionname = '';
$studymodule = '';

$context = $cmid ? context_module::instance($cmid) : null;
if ($studysectionname !== '') {
    $studymodule = 'Acupuncture and Point Location';
}
if ($studysection) {
    $sectionrecord = $DB->get_record('course_sections', ['id' => $studysection->section], 'id,name');
    $studysectionname = $sectionrecord ? trim($sectionrecord->name) : '';
}
$scorm = $DB->get_record('scorm', ['id' => $scormid], 'id,name,intro,introformat,launch');
$instructorstudyguide = $scorm ? $scorm->intro : '';

$instructorrecord = $DB->get_record(
    'scorm_studyguide_instructor',
    ['scormid' => $scormid]
);
$instructormaterial = $instructorrecord ? $instructorrecord->content : '';

if (
    isset($_POST['save_instructor_materials']) &&
    is_siteadmin()
) {
    require_sesskey();

    $newinstructormaterial = optional_param('instructor_materials', '', PARAM_RAW);
    $now = time();

    if ($instructorrecord) {
        $instructorrecord->content = $newinstructormaterial;
        $instructorrecord->timemodified = $now;
        $DB->update_record('scorm_studyguide_instructor', $instructorrecord);
    } else {
        $newrecord = new stdClass();
        $newrecord->scormid = $scormid;
        $newrecord->content = $newinstructormaterial;
        $newrecord->timecreated = $now;
        $newrecord->timemodified = $now;
        $DB->insert_record('scorm_studyguide_instructor', $newrecord);
    }

    $redirecturl = new moodle_url(
        '/mod/scorm/studyguide/index.php',
        ['id' => $scormid, 'cmid' => $cmid]
    );
    redirect($redirecturl);
}

if ($scormid === 610) {
    $generatedguidefile = __DIR__ . '/generated_studyguide_610_test.txt';
    if (is_readable($generatedguidefile)) {
        $instructorstudyguide = file_get_contents($generatedguidefile);
        $scorm->introformat = FORMAT_PLAIN;
    }

    $masterstudycardfile = __DIR__ . '/master_study_card_yin_yang.txt';
    if (is_readable($masterstudycardfile)) {
        $masterstudycard = file_get_contents($masterstudycardfile);
        if ($masterstudycard !== false && trim($masterstudycard) !== '') {
            $instructorstudyguide =
                $masterstudycard .
                "\n\n============================================================\n\n" .
                $instructorstudyguide;
        }
    }
}
$existing = $DB->get_record(
    'scorm_studyguide_notes',
    ['userid' => $USER->id, 'scormid' => $scormid]
);

$savednote = "";
if ($existing) {
    $savednote = $existing->note;
}
$note = optional_param('note', '', PARAM_RAW);
$filename = "";
$noteimage = $_FILES["note_image"] ?? null;
$filename = "";

if (
    isset($_POST["upload_note_image"]) &&
    $noteimage &&
    $noteimage["error"] === UPLOAD_ERR_OK
) {
    $allowed = ["image/jpeg", "image/png", "image/gif", "image/webp", "image/bmp"];

    if (in_array($noteimage["type"], $allowed, true)) {
        $ext = strtolower(pathinfo($noteimage["name"], PATHINFO_EXTENSION));
        $filename = "user_" . $USER->id . "_scorm_" . $scormid . "_" . time() . "." . $ext;
        $destination = __DIR__ . "/uploads/" . $filename;

        if (move_uploaded_file($noteimage["tmp_name"], $destination)) {
            error_log("STUDYGUIDE IMAGE FILENAME: " . $filename);

            $existing = $DB->get_record(
                "scorm_studyguide_notes",
                ["userid" => $USER->id, "scormid" => $scormid]
            );

            $now = time();

            if ($existing) {
                $existing->imagefile = $filename;
                $existing->timemodified = $now;
                $DB->update_record("scorm_studyguide_notes", $existing);
            } else {
                $record = new stdClass();
                $record->userid = $USER->id;
                $record->scormid = $scormid;
                $record->note = "";
                $record->imagefile = $filename;
                $record->timecreated = $now;
                $record->timemodified = $now;
                $DB->insert_record("scorm_studyguide_notes", $record);

                $existing = $record;
            }
        }
    }
}

error_log("STUDYGUIDE EXISTING IMAGE: " . ($existing->imagefile ?? "EMPTY"));
$clearnote = optional_param("clear_note", 0, PARAM_BOOL);
if ($clearnote && $existing) {
    require_sesskey();
    $existing->note = '';
    $existing->timemodified = time();
    $DB->update_record("scorm_studyguide_notes", $existing);
$note = '';
$savednote = '';
}

$removeimage = optional_param("remove_image", 0, PARAM_BOOL);
if ($removeimage && $existing && !empty($existing->imagefile)) {
    $imagepath = __DIR__ . "/uploads/" . $existing->imagefile;
    if (is_file($imagepath)) { unlink($imagepath); }
    $existing->imagefile = null;
    $existing->timemodified = time();
    $DB->update_record("scorm_studyguide_notes", $existing);
}
if (
    $note !== '' &&
    (
        isset($_POST['save_note']) ||
        isset($_POST['upload_note_image']) ||
        isset($_POST['remove_image'])
    )
) {
    global $DB, $USER;
    $now = time();

    if ($existing) {
        $existing->note = $note;

        if ($filename !== '') {
            $existing->imagefile = $filename;
        }

        $existing->timemodified = $now;
        $DB->update_record('scorm_studyguide_notes', $existing);
    } else {
        $record = new stdClass();
        $record->userid = $USER->id;
        $record->scormid = $scormid;
        $record->note = $note;

        if ($filename !== '') {
            $record->imagefile = $filename;
        }

        $record->timecreated = $now;
        $record->timemodified = $now;
        $DB->insert_record('scorm_studyguide_notes', $record);
    }$savednote = $note;
}
/* 🖼️ Instructor Visual Materials - independent from student My Notes */
$instructorimagesdir = __DIR__ . "/instructor_uploads";
$instructorimagesmeta = $instructorimagesdir . "/scorm_" . $scormid . ".json";

if (!is_dir($instructorimagesdir)) {
    mkdir($instructorimagesdir, 0755, true);
}

$instructorimages = [];
if (is_readable($instructorimagesmeta)) {
    $decoded = json_decode(file_get_contents($instructorimagesmeta), true);
    if (is_array($decoded)) {
        $instructorimages = $decoded;
    }
}


/* Delete Instructor Image */
if (
    isset($_POST['delete_instructor_image']) &&
    is_siteadmin()
) {
    require_sesskey();

    $deletefilename = clean_param(
        optional_param('delete_instructor_filename', '', PARAM_FILE),
        PARAM_FILE
    );

    if ($deletefilename !== '' && !empty($instructorimages)) {
        $updatedimages = [];

        foreach ($instructorimages as $image) {
            if (($image['filename'] ?? '') === $deletefilename) {
                $deletepath = $instructorimagesdir . "/" . $deletefilename;

                if (is_file($deletepath)) {
                    unlink($deletepath);
                }

                continue;
            }

            $updatedimages[] = $image;
        }

        $instructorimages = $updatedimages;

        file_put_contents(
            $instructorimagesmeta,
            json_encode($instructorimages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}

if (
    isset($_POST['upload_instructor_image']) &&
    is_siteadmin()
) {
    require_sesskey();

    $uploads = $_FILES['instructor_image'] ?? null;


    if ($uploads && isset($uploads['name']) && is_array($uploads['name'])) {
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'image/bmp'  => 'bmp'
        ];

        $maximages = 20;
        $maxsize = 5 * 1024 * 1024;
        $currentcount = count($instructorimages);

        $filecount = count($uploads['name']);

        for ($i = 0; $i < $filecount; $i++) {
            if ($currentcount >= $maximages) {
                break;
            }

            if (($uploads['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }

            $filesize = (int)($uploads['size'][$i] ?? 0);

            if ($filesize <= 0 || $filesize > $maxsize) {
                continue;
            }

            $mimetype = $uploads['type'][$i] ?? '';

            if (!isset($allowed[$mimetype])) {
                continue;
            }

            $extension = $allowed[$mimetype];
            $filename = "scorm_" . $scormid . "_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;

            $destination = $instructorimagesdir . "/" . $filename;

            if (move_uploaded_file($uploads['tmp_name'][$i], $destination)) {
                $instructorimages[] = [
                    'filename' => $filename,
                    'title' => pathinfo($uploads['name'][$i], PATHINFO_FILENAME),
                    'timecreated' => time()
                ];

                $currentcount++;
            }
        }

        file_put_contents(
            $instructorimagesmeta,
            json_encode($instructorimages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}

$PAGE->set_url(new moodle_url('/mod/scorm/studyguide/index.php', ['id' => $scormid]));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Optional Study Guide');
$PAGE->set_heading('');
$PAGE->set_pagelayout('incourse');

echo $OUTPUT->header();

?>

<div style="max-width:1200px;margin:30px auto;padding:25px;background:#fff;border-radius:14px;box-shadow:0 4px 18px rgba(0,0,0,.08);">

    <h2 style="margin:0 0 6px;font-size:24px;font-weight:700;color:#1f2937;">
        <?php echo strtoupper(format_string($DB->get_field('course', 'shortname', ['id' => $studysection->course]))); ?>
    </h2>

    <?php if (!empty($studysectionname)): ?>
        <div style="font-size:18px;font-weight:600;color:#374151;margin-bottom:4px;">
            <?php echo format_string($studysectionname); ?>
        </div>
    <?php endif; ?>

    <div style="font-size:16px;font-weight:500;color:#4b5563;margin-bottom:6px;">
        <?php echo format_string($scorm->name); ?>
    </div>

    <div style="font-size:15px;font-weight:600;color:#6b7280;margin-bottom:14px;">
        Optional Study Guide
    </div>

    <p style="color:#666;font-size:15px;">
        Review this material before starting your exam.
        This study guide is optional and does not affect your attempts or grade.
    </p>

    <hr>

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin:18px 0 25px;flex-wrap:wrap;">
        <a href="/course/view.php?id=<?php echo (int)$studysection->course; ?>#module-<?php echo (int)$cmid; ?>"
           style="display:inline-block;padding:12px 22px;border-radius:9px;background:#6c757d;color:white;text-decoration:none;font-weight:bold;">
            ← Back
        </a>

        <a href="/mod/scorm/player.php?scoid=<?php echo (int)$scorm->launch; ?>&cm=<?php echo $cmid; ?>&newattempt=on&display=popup"
           style="display:inline-block;padding:12px 22px;border-radius:9px;background:#f47e2c;color:white;text-decoration:none;font-weight:bold;">
            ▶ Start Exam
        </a>
    </div>

    <?php if ($scormid === 610 && !empty($masterstudycard)): ?>
        <div style="margin:25px 0;padding:22px;border:1px solid #dfe5ee;border-radius:12px;background:#f8fafc;">
            <h3 style="margin:0 0 15px;">📚 Master Study Card — Yin Yang Theory</h3>
            <div style="padding:20px;background:#fff;border-radius:10px;border:1px solid #e5e7eb;line-height:1.65;white-space:pre-wrap;font-size:15px;">
                <?php
              $masterstudycardhtml = htmlspecialchars($masterstudycard, ENT_QUOTES, 'UTF-8');
              $masterstudycardhtml = preg_replace_callback(
                  '/(^|\\n)([^\\n]+)/',
                  function($m) {
                      $line = $m[2];
                      $headings = [
                          '1. CORE IDEA',
                          '2. FIVE FUNDAMENTAL RELATIONSHIPS',
                          '3. RELATIVE YIN-YANG & TIME',
                          '4. BODY STRUCTURE',
                          '5. PHYSIOLOGY',
                          '6. YIN WITHIN YANG / YANG WITHIN YIN',
                          '7. DYNAMIC BALANCE',
                          '8. THREE YIN & THREE YANG',
                          '9. TAIJITU',
                          '10. BOARD EXAM MEMORY',
                          '11. THEORY vs PATTERNS'
                      ];

                      foreach ($headings as $heading) {
                          if (strpos($line, $heading) === 0) {
                              return $m[1] . '<span style="display:block;margin:10px 0 5px;padding:6px 10px;background:#eef4ff;border-left:4px solid #3b82f6;border-radius:6px;font-weight:700;">' . $line . '</span>';
                          }
                      }

                      return $m[1] . $line;
                  },
                  $masterstudycardhtml
              );
              echo $masterstudycardhtml;
              ?>
            </div>
        </div>
    <?php endif; ?>

    <h3>👨‍🏫 Instructor Study Materials</h3>

    <?php if ($context && has_capability('moodle/course:manageactivities', $context)): ?>
        <form method="post" style="margin:15px 0;">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
            <textarea
                name="instructor_materials"
                style="width:100%;min-height:260px;padding:15px;border:1px solid #ddd;border-radius:10px;"
                placeholder="Write the instructor study materials here..."
            ><?php echo htmlspecialchars($instructormaterial); ?></textarea>

            <div style="margin-top:10px;">
                <button
                    type="submit"
                    name="save_instructor_materials"
                    value="1"
                    style="background:#f58220;color:#fff;border:0;border-radius:8px;padding:10px 18px;cursor:pointer;"
                >
                    Save Instructor Materials
                </button>
            </div>
        </form>
    <?php endif; ?>

    <?php if (!empty($instructormaterial) && !($context && has_capability('moodle/course:manageactivities', $context))): ?>
        <div style="padding:20px;background:#f7f9fc;border-radius:10px;margin:15px 0;line-height:1.6;">
            <?php echo nl2br(htmlspecialchars($instructormaterial)); ?>
        </div>
<hr style="margin:30px 0;">

    <div style="margin:25px 0;padding:20px;border:1px solid #dfe5ee;border-radius:12px;background:#f8fafc;">
        <div style="font-size:20px;font-weight:700;margin-bottom:6px;">
            🖼️ Instructor Visual Materials
        </div>

        <div style="color:#667085;margin-bottom:15px;">
            Add visual study materials to this Study Guide.
        </div>

        <?php if (!empty($instructorimages)) { ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin:20px 0;">
                <?php foreach ($instructorimages as $image) { ?>
                    <div style="background:#fff;border:1px solid #e1e6ef;border-radius:12px;padding:12px;text-align:center;max-width:360px;width:100%;box-sizing:border-box;">
                        <img
                            src="/mod/scorm/studyguide/instructor_uploads/<?php echo htmlspecialchars($image['filename']); ?>"
                            alt="<?php echo htmlspecialchars($image['title'] ?? 'Instructor Image'); ?>"
                            onclick="openInstructorImage(this.src, this.alt);"
                            style="display:block;width:100%;height:auto;border-radius:8px;cursor:zoom-in;"
                        >
                        <div style="margin-top:8px;font-weight:600;color:#344054;">
                            <?php echo htmlspecialchars($image['title'] ?? 'Instructor Image'); ?>
                        </div>

                        <?php if ($context && has_capability('moodle/course:manageactivities', $context)): ?>
                            <form method="post" action="" style="margin-top:10px;">
                                <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
                                <input type="hidden" name="delete_instructor_filename" value="<?php echo htmlspecialchars($image['filename']); ?>">

                                <button type="submit"
                                        name="delete_instructor_image"
                                        value="1"
                                        onclick="return confirm('Delete this instructor image?');"
                                        style="padding:8px 14px;border:0;border-radius:7px;background:#dc3545;color:white;font-weight:bold;">
                                    Delete
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>


        <?php if ($context && has_capability('moodle/course:manageactivities', $context)): ?>
            <form id="student_notes_form" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">

                <div style="margin-bottom:12px;">
                    <label style="font-weight:600;display:block;margin-bottom:6px;">
                        Upload Image
                    </label>
                    <input type="file" name="instructor_image[]" accept="image/*" multiple>
                </div>

                <button type="submit"
                        name="upload_instructor_image"
                        value="1"
                        style="padding:10px 18px;border:0;border-radius:8px;background:#4f6bed;color:white;font-weight:bold;">
                    Upload Image
                </button>
            </form>
        <?php endif; ?>
    </div>


    <?php endif; ?>

<div style="margin:25px 0;padding:20px;background:#eef4ff;border:1px solid #c9d8ff;border-radius:12px;">
    <h3 style="margin-top:0;">🤖 Instructor AI</h3>

    <p style="color:#667085;">
        Use AI to create, improve, compare, and organize study material for this Study Guide.
    </p>

    <textarea
        id="instructor_ai_prompt"
        style="width:100%;min-height:120px;padding:12px;border:1px solid #ccd5e0;border-radius:10px;box-sizing:border-box;"
        placeholder="Example: Create 5 board-style questions about Goiter with answers and explanations."
    ></textarea>

    <div style="margin-top:12px;display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
        <button
            type="button"
            id="instructor_ai_button"
            style="padding:10px 18px;border:0;border-radius:8px;background:#4f6bed;color:white;font-weight:bold;"
        >
            Ask Instructor AI
        </button>

        <span style="font-weight:bold;color:#475467;margin-left:8px;">
            Generate Q&A:
        </span>

        <button
            type="button"
            class="instructor_generate_qa_button"
            data-count="5"
            style="padding:10px 14px;border:0;border-radius:8px;background:#667085;color:white;font-weight:bold;"
        >
            5
        </button>

        <button
            type="button"
            class="instructor_generate_qa_button"
            data-count="10"
            style="padding:10px 14px;border:0;border-radius:8px;background:#667085;color:white;font-weight:bold;"
        >
            10
        </button>

        <button
            type="button"
            class="instructor_generate_qa_button"
            data-count="20"
            style="padding:10px 14px;border:0;border-radius:8px;background:#667085;color:white;font-weight:bold;"
        >
            20
        </button>
    </div>

    <div
        id="instructor_ai_status"
        style="margin-top:12px;color:#667085;"
    ></div>

    <div
        id="instructor_ai_response"
        style="display:none;margin-top:15px;padding:15px;background:white;border:1px solid #d8dee8;border-radius:10px;white-space:pre-wrap;line-height:1.6;"
    ></div>
</div>


<div id="instructor_image_lightbox"
     onclick="closeInstructorImage();"
     style="display:none;position:fixed;z-index:99999;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.88);align-items:center;justify-content:center;padding:20px;box-sizing:border-box;cursor:zoom-out;">

    <button type="button"
            onclick="event.stopPropagation();closeInstructorImage();"
            style="position:absolute;top:20px;right:25px;background:#fff;border:0;border-radius:50%;width:42px;height:42px;font-size:24px;font-weight:bold;cursor:pointer;">
        ×
    </button>

    <img id="instructor_image_lightbox_img"
         src=""
         alt=""
         style="max-width:95%;max-height:90%;object-fit:contain;border-radius:8px;box-shadow:0 10px 40px rgba(0,0,0,0.5);">
</div>

<script>
function openInstructorImage(src, alt) {
    const box = document.getElementById('instructor_image_lightbox');
    const img = document.getElementById('instructor_image_lightbox_img');

    img.src = src;
    img.alt = alt || 'Instructor Image';
    box.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeInstructorImage() {
    const box = document.getElementById('instructor_image_lightbox');
    const img = document.getElementById('instructor_image_lightbox_img');

    box.style.display = 'none';
    img.src = '';
    document.body.style.overflow = '';
}
</script>

<script>
document.getElementById('instructor_ai_button').addEventListener('click', async function() {
    const prompt = document.getElementById('instructor_ai_prompt').value.trim();
    const status = document.getElementById('instructor_ai_status');
    const responseBox = document.getElementById('instructor_ai_response');

    if (!prompt) {
        status.textContent = 'Please enter a request.';
        return;
    }

    this.disabled = true;
    status.textContent = 'Instructor AI is thinking...';
    responseBox.style.display = 'none';

    try {
        const params = new URLSearchParams({
            id: <?php echo (int)$scormid; ?>,
            prompt: prompt,
            sesskey: M.cfg.sesskey
        });

        const response = await fetch('<?php echo (new moodle_url('/mod/scorm/studyguide/instructor_ai.php'))->out(false); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params.toString()
        });

        const data = await response.json();

        if (data.success) {
            responseBox.textContent = data.response;
            responseBox.style.display = 'block';
            status.textContent = '';
        } else {
            status.textContent = data.error || 'Instructor AI request failed.';
        }
    } catch (error) {
        status.textContent = 'Unable to connect to the Instructor AI service.';
    } finally {
        this.disabled = false;
    }
});
</script>

<script>
document.querySelectorAll('.instructor_generate_qa_button').forEach(function(button) {
    button.addEventListener('click', async function() {
        const count = this.getAttribute('data-count');
        const status = document.getElementById('instructor_ai_status');
        const responseBox = document.getElementById('instructor_ai_response');

        const prompt =
            'Generate exactly ' + count + ' board-examination study questions with answers. ' +
            'Use ONLY the AcupunctureTests Study Guide and Master Study Card provided to Instructor AI as the source for the questions, answers, and explanations. ' +
            'Every question, answer, explanation, term, example, and distractor must be directly supported by the provided materials. ' +
            'Do NOT introduce concepts, terminology, examples, clinical facts, or general knowledge that are not present in the provided materials. ' +
            'Do NOT invent information merely to complete a question. ' +
            'Avoid NOT, EXCEPT, or negative questions unless the provided materials explicitly support the required contrast. ' +
            'If the provided materials do not contain enough information for a requested number of questions, state that limitation rather than inventing content. ' +
            'Keep the terminology and educational framing of AcupunctureTests. ' +
            'For each item, provide: Question, Answer, and a brief Explanation based only on the provided materials. ' +
            'Number all questions from 1 to ' + count + '.';

        this.disabled = true;
        status.textContent = 'Generating ' + count + ' questions...';
        responseBox.style.display = 'none';

        try {
            const params = new URLSearchParams({
                id: <?php echo (int)$scormid; ?>,
                cmid: <?php echo (int)$cmid; ?>,
                prompt: prompt,
                count: count,
                sesskey: M.cfg.sesskey
            });

            const response = await fetch('<?php echo (new moodle_url('/mod/scorm/studyguide/instructor_ai.php'))->out(false); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });

            const data = await response.json();

            if (data.success) {
                responseBox.textContent = data.response;
                responseBox.style.display = 'block';
                status.textContent = '';
            } else {
                status.textContent = data.error || 'Question generation failed.';
            }
        } catch (error) {
            status.textContent = 'Unable to connect to the Instructor AI service.';
        } finally {
            this.disabled = false;
        }
    });
});
</script>

<!-- STUDY GUIDE V2 START -->

<div style="margin:30px 0 18px;padding:22px;background:#ffffff;border:1px solid #dfe5ee;border-radius:14px;">
    <h2 style="margin:0 0 5px;">Study Guide</h2>
    <div style="font-size:18px;font-weight:600;color:#344054;">
        Topic: The Channels and Collaterals
    </div>
</div>

<div style="margin:0 0 22px;padding:16px 20px;background:#eef4ff;border-left:5px solid #2f6fed;border-radius:10px;">
    <strong>INSTRUCTOR-DEVELOPED LEARNING CONTENT</strong>
    <div style="margin-top:5px;color:#475467;">
        Developed, reviewed, and approved by the instructor or administrator.
    </div>
</div>

<h3 style="margin:25px 0 12px;">FORM B — LEARNING PLAN</h3>

<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;background:#ffffff;">
    <thead>
        <tr style="background:#f2f4f7;">
            <th style="padding:12px;border:1px solid #d0d5dd;width:45px;">#</th>
            <th style="padding:12px;border:1px solid #d0d5dd;text-align:left;">LEARNER OBJECTIVE</th>
            <th style="padding:12px;border:1px solid #d0d5dd;text-align:left;">SUBJECT MATTER</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding:12px;border:1px solid #d0d5dd;text-align:center;"><strong>1</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;"><strong>Describe the circulation of the 12 Primary Channels.</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;">Circulation patterns and directional sequence of the 12 Primary Channels, including the Hand Yin, Hand Yang, Foot Yin and Foot Yang channel groups and their continuous circulation relationship.</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #d0d5dd;text-align:center;"><strong>2</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;"><strong>Identify the internal connections of the Primary Channels.</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;">Internal connections and relationships of the Primary Channels covered within this Topic.</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #d0d5dd;text-align:center;"><strong>3</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;"><strong>Identify the naming system of the 12 Primary Channels.</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;">Classification and nomenclature of the 12 Primary Channels according to Hand/Foot, Yin/Yang and the six channel divisions.</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #d0d5dd;text-align:center;"><strong>4</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;"><strong>Arrange the channel system from most superficial to deepest.</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;">Relative depth and sequential organization of the channel divisions covered within this Topic.</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #d0d5dd;text-align:center;"><strong>5</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;"><strong>Recognize the organization and characteristics of the Regular Channels.</strong></td>
            <td style="padding:12px;border:1px solid #d0d5dd;">Organization, classification, relationships and characteristics of the Regular Channels covered within this Topic.</td>
        </tr>
    </tbody>
</table>
</div>

<div style="margin:22px 0;padding:18px 20px;background:#f8fafc;border:1px solid #dfe5ee;border-radius:12px;">
    <h3 style="margin:0 0 8px;">TEACHING METHOD</h3>
    <div style="color:#475467;">
        Study the developed Subject Matter; use Flashcards; review Key Concepts and Memory Aids &amp; Study Tips; answer Review Questions; complete the assigned SCORM exam(s).
    </div>
</div>

<div style="margin:22px 0 10px;">
    <h3 style="margin-bottom:12px;">OBJECTIVES</h3>

    <details style="margin-bottom:10px;padding:14px 16px;background:#ffffff;border:1px solid #dfe5ee;border-radius:10px;">
        <summary style="cursor:pointer;font-weight:700;">OBJECTIVE 1 — Describe the circulation of the 12 Primary Channels.</summary>

        <div style="padding:18px 4px 4px;">

            <div id="objective1_subject_matter" style="margin-bottom:12px;padding:16px;background:#f8fafc;border:1px solid #e4e7ec;border-radius:9px;">
                <strong>SUBJECT MATTER</strong>

                <p style="margin:10px 0 14px;color:#475467;">
                    The 12 Primary Channels are connected to form a continuous circuit around the body.
                    Their general circulation follows four channel groups.
                </p>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:10px;">

                    <div style="padding:12px;background:#ffffff;border:1px solid #e4e7ec;border-radius:8px;">
                        <strong>1. Hand Yin Channels</strong>
                        <div style="margin-top:5px;">Lung • Pericardium • Heart</div>
                        <div style="margin-top:5px;color:#667085;">Chest → Hands</div>
                    </div>

                    <div style="padding:12px;background:#ffffff;border:1px solid #e4e7ec;border-radius:8px;">
                        <strong>2. Hand Yang Channels</strong>
                        <div style="margin-top:5px;">Large Intestine • Sanjiao • Small Intestine</div>
                        <div style="margin-top:5px;color:#667085;">Hands → Head</div>
                    </div>

                    <div style="padding:12px;background:#ffffff;border:1px solid #e4e7ec;border-radius:8px;">
                        <strong>3. Foot Yang Channels</strong>
                        <div style="margin-top:5px;">Stomach • Gall Bladder • Bladder</div>
                        <div style="margin-top:5px;color:#667085;">Head → Feet</div>
                    </div>

                    <div style="padding:12px;background:#ffffff;border:1px solid #e4e7ec;border-radius:8px;">
                        <strong>4. Foot Yin Channels</strong>
                        <div style="margin-top:5px;">Spleen • Liver • Kidneys</div>
                        <div style="margin-top:5px;color:#667085;">Feet → Chest</div>
                    </div>

                </div>
            </div>

            <div style="margin-bottom:12px;padding:16px;background:#ffffff;border:1px solid #e4e7ec;border-radius:9px;">
                <strong>FLASHCARDS</strong>

                <div style="margin-top:7px;color:#667085;">
                    Generate flashcards using only the approved Subject Matter for this Objective.
                </div>

                <button type="button"
                        id="objective1_generate_flashcards"
                        style="margin-top:12px;padding:8px 14px;border:0;border-radius:6px;background:#0d6efd;color:#ffffff;font-weight:600;cursor:pointer;">
                    Generate from Subject Matter
                </button>

                <span id="objective1_flashcards_status"
                      style="margin-left:10px;color:#667085;"></span>

                <div id="objective1_flashcards_result"
                     style="margin-top:14px;display:none;"></div>

                <div style="margin-top:16px;padding-top:14px;border-top:1px solid #e4e7ec;">
                    <button type="button"
                            id="objective1_generate_illustration"
                            style="padding:9px 16px;border:0;border-radius:6px;background:#198754;color:white;font-weight:600;cursor:pointer;">
                        Generate Illustration
                    </button>

                    <span id="objective1_illustration_status"
                          style="margin-left:10px;color:#667085;"></span>

                    <div id="objective1_illustration_preview"
                         style="margin-top:14px;display:none;"></div>
                </div>
            </div>

            <div style="margin-bottom:12px;padding:16px;background:#ffffff;border:1px solid #e4e7ec;border-radius:9px;">
                <strong>KEY CONCEPTS</strong>
                <div style="margin-top:7px;color:#667085;">
                    Key concepts generated from the approved Subject Matter will appear here.
                </div>
            </div>

            <div style="margin-bottom:12px;padding:16px;background:#ffffff;border:1px solid #e4e7ec;border-radius:9px;">
                <strong>MEMORY AIDS &amp; STUDY TIPS</strong>
                <div style="margin-top:7px;color:#667085;">
                    Memory aids and study tips for this objective will appear here.
                </div>
            </div>

            <div style="padding:16px;background:#ffffff;border:1px solid #e4e7ec;border-radius:9px;">
                <strong>REVIEW QUESTIONS</strong>
                <div style="margin-top:7px;color:#667085;">
                    Review questions generated from the approved Subject Matter will appear here.
                </div>
            </div>

        </div>
    </details>

    <details style="margin-bottom:10px;padding:14px 16px;background:#ffffff;border:1px solid #dfe5ee;border-radius:10px;">
        <summary style="cursor:pointer;font-weight:700;">OBJECTIVE 2 — Identify the internal connections of the Primary Channels.</summary>
        <div style="padding:15px 4px 4px;color:#667085;">Study materials will be developed here.</div>
    </details>

    <details style="margin-bottom:10px;padding:14px 16px;background:#ffffff;border:1px solid #dfe5ee;border-radius:10px;">
        <summary style="cursor:pointer;font-weight:700;">OBJECTIVE 3 — Identify the naming system of the 12 Primary Channels.</summary>
        <div style="padding:15px 4px 4px;color:#667085;">Study materials will be developed here.</div>
    </details>

    <details style="margin-bottom:10px;padding:14px 16px;background:#ffffff;border:1px solid #dfe5ee;border-radius:10px;">
        <summary style="cursor:pointer;font-weight:700;">OBJECTIVE 4 — Arrange the channel system from most superficial to deepest.</summary>
        <div style="padding:15px 4px 4px;color:#667085;">Study materials will be developed here.</div>
    </details>

    <details style="margin-bottom:10px;padding:14px 16px;background:#ffffff;border:1px solid #dfe5ee;border-radius:10px;">
        <summary style="cursor:pointer;font-weight:700;">OBJECTIVE 5 — Recognize the organization and characteristics of the Regular Channels.</summary>
        <div style="padding:15px 4px 4px;color:#667085;">Study materials will be developed here.</div>
    </details>
</div>

<div style="margin:28px 0;padding:20px;background:#ffffff;border:2px solid #d0d5dd;border-radius:12px;">
    <h3 style="margin:0 0 5px;">SCORM EXAMS — OFFICIAL ASSESSMENT</h3>
    <div style="color:#667085;">The official Topic exams will appear together in this area.</div>
</div>

<div style="margin:28px 0;padding:18px 20px;background:#f0fdf4;border-left:5px solid #16a34a;border-radius:10px;">
    <strong>STUDENT LEARNING AREA</strong>
    <div style="margin-top:5px;color:#475467;">
        This area is completed and managed by the student.
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('objective1_generate_flashcards');
    const source = document.getElementById('objective1_subject_matter');
    const status = document.getElementById('objective1_flashcards_status');
    const result = document.getElementById('objective1_flashcards_result');

    if (!button || !source || !status || !result) {
        return;
    }

    button.addEventListener('click', async function() {
        const subjectMatter = source.innerText.trim();

        if (!subjectMatter) {
            status.textContent = 'No approved Subject Matter found.';
            return;
        }

        button.disabled = true;
        status.textContent = 'Generating flashcards...';
        result.style.display = 'none';
        result.textContent = '';

        try {
            const params = new URLSearchParams({
                id: <?php echo (int)$scormid; ?>,
                cmid: <?php echo (int)$cmid; ?>,
                mode: 'flashcards',
                prompt: subjectMatter,
                sesskey: M.cfg.sesskey
            });

            const response = await fetch('<?php echo (new moodle_url('/mod/scorm/studyguide/instructor_ai.php'))->out(false); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });

            const data = await response.json();

            if (data.success) {
                result.innerHTML = '';

                const cards = data.response
                    .split(/VISUAL FLASHCARD:/i)
                    .map(card => card.trim())
                    .filter(Boolean);

                let displayed = 0;

                cards.forEach((card) => {
                    const titleMatch = card.match(/Title:\s*(.*?)(?=\nLearning Focus:|$)/is);
                    const focusMatch = card.match(/Learning Focus:\s*(.*?)(?=\nIllustration:|$)/is);
                    const illustrationMatch = card.match(/Illustration:\s*(.*?)(?=\nOn-Image Text:|$)/is);
                    const textMatch = card.match(/On-Image Text:\s*(.*)$/is);

                    if (!titleMatch || !focusMatch || !illustrationMatch || !textMatch) {
                        return;
                    }

                    displayed++;

                    const wrapper = document.createElement('div');
                    wrapper.style.cssText =
                        'margin-bottom:14px;padding:16px;background:#f8fafc;' +
                        'border:1px solid #dfe5ee;border-radius:10px;';

                    const number = document.createElement('div');
                    number.style.cssText =
                        'font-size:12px;font-weight:700;color:#667085;margin-bottom:6px;';
                    number.textContent = 'VISUAL FLASHCARD ' + displayed;

                    const title = document.createElement('div');
                    title.style.cssText =
                        'font-size:17px;font-weight:700;color:#101828;margin-bottom:10px;';
                    title.textContent = titleMatch[1].trim();

                    const focus = document.createElement('div');
                    focus.style.cssText = 'margin-bottom:8px;color:#344054;';
                    focus.innerHTML = '<strong>Learning Focus:</strong> ';
                    focus.appendChild(document.createTextNode(focusMatch[1].trim()));

                    const illustration = document.createElement('div');
                    illustration.style.cssText =
                        'margin-bottom:8px;padding:12px;background:#ffffff;' +
                        'border-left:4px solid #0d6efd;border-radius:6px;color:#344054;';
                    illustration.innerHTML = '<strong>Illustration:</strong> ';
                    illustration.appendChild(
                        document.createTextNode(illustrationMatch[1].trim())
                    );

                    const onImage = document.createElement('div');
                    onImage.style.cssText = 'color:#344054;';
                    onImage.innerHTML = '<strong>On-Image Text:</strong> ';
                    onImage.appendChild(document.createTextNode(textMatch[1].trim()));

                    wrapper.appendChild(number);
                    wrapper.appendChild(title);
                    wrapper.appendChild(focus);
                    wrapper.appendChild(illustration);
                    wrapper.appendChild(onImage);
                    result.appendChild(wrapper);
                });

                result.style.display = 'block';
                status.textContent = displayed
                    ? ''
                    : 'No Visual Flashcards could be displayed.';
            } else {
                status.textContent = data.error || 'Flashcard generation failed.';
            }
        } catch (error) {
            status.textContent = 'Unable to connect to the Instructor AI service.';
        } finally {
            button.disabled = false;
        }
    });
});
</script>

<!-- objective1_image_generation_handler -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('objective1_generate_illustration');
    const status = document.getElementById('objective1_illustration_status');
    const preview = document.getElementById('objective1_illustration_preview');

    if (!button || !status || !preview) {
        return;
    }

    button.addEventListener('click', async function() {
        if (!confirm('Generate one AI illustration? This may incur an OpenAI API charge.')) {
            return;
        }

        button.disabled = true;
        status.textContent = 'Generating illustration...';
        preview.style.display = 'none';
        preview.replaceChildren();

        try {
            const params = new URLSearchParams({
                id: '<?php echo (int)$scormid; ?>',
                cmid: '<?php echo (int)$cmid; ?>',
                mode: 'image',
                prompt: 'Hand Yin Channels — Chest to Hands',
                sesskey: M.cfg.sesskey
            });

            const response = await fetch(
                '<?php echo (new moodle_url('/mod/scorm/studyguide/instructor_ai.php'))->out(false); ?>',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: params.toString()
                }
            );

            const raw = await response.text();
            let data;
            try {
                data = JSON.parse(raw);
            } catch (parseError) {
                throw new Error(
                    'HTTP ' + response.status +
                    ' — Server returned HTML/text: ' +
                    raw.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 350)
                );
            }

            if (data.success && data.drafturl) {
                const img = document.createElement('img');
                img.src = data.drafturl;
                img.alt = 'Hand Yin Channels educational illustration';
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                img.style.borderRadius = '10px';

                preview.appendChild(img);
                preview.style.display = 'block';
                status.textContent = 'Illustration generated — instructor review required.';
            } else {
                status.textContent = data.error || 'Image generation failed.';
            }
        } catch (error) {
            status.textContent = 'Illustration error: ' + (error.message || String(error));
        } finally {
            button.disabled = false;
        }
    });
});
</script>

<!-- OLD STUDY GUIDE BELOW - TEMPORARILY PRESERVED -->

<div style="margin:30px 0 15px;padding:18px 20px;background:#f7f9fc;border:1px solid #e1e6ef;border-radius:12px;">
    <h3 style="margin:0 0 6px;">👨‍🎓 My Study Guide</h3>
    <p style="margin:0;color:#667085;">
        Build your own study guide using your notes, key concepts, comparisons, questions, and explanations.
    </p>
</div>

<!-- GOITER MASTER CARD -->
<style>

.core-concept-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
}

@media (max-width: 900px) {
    .core-concept-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .core-concept-grid {
        grid-template-columns: 1fr;
    }
}


* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 24px 14px;
    background: #f3f6f8;
    font-family: Arial, Helvetica, sans-serif;
    color: #263238;
}

.card {
    max-width: 1200px;
    margin: auto;
    background: #ffffff;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.10);
}

/* HEADER */

.header {
    padding: 28px;
    background: linear-gradient(135deg, #e8f5e9, #e3f2fd);
}

.header h1 {
    margin: 0;
    font-size: 30px;
}

.header p {
    margin: 8px 0 0;
    color: #546e7a;
    font-size: 16px;
}

/* CONTENT */

.content {
    padding: 24px;
}

.section {
    margin-bottom: 26px;
}

.section-title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 12px;
}

/* SELF CHECK */

.self-check {
    background: #fff8e1;
    border-left: 6px solid #f9a825;
    border-radius: 12px;
    padding: 18px;
}

.self-check strong {
    display: block;
    margin-bottom: 8px;
}

/* DECISION TREE */


.clinical-tree {
    padding: 24px;
    text-align: center;
}

.clinical-root {
    display: inline-block;
    padding: 12px 22px;
    background: #ffffff;
    border: 2px solid #1976d2;
    border-radius: 12px;
    font-weight: bold;
}

.clinical-stem {
    font-size: 24px;
    color: #1976d2;
    line-height: 1;
    margin: 8px 0;
}

.clinical-question {
    display: inline-block;
    padding: 12px 20px;
    background: #e3f2fd;
    border-radius: 12px;
    font-weight: bold;
}

.clinical-branches {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-top: 22px;
    position: relative;
}

.clinical-branches::before {
    content: "";
    position: absolute;
    top: -12px;
    left: 12.5%;
    right: 12.5%;
    height: 2px;
    background: #90a4ae;
}

.clinical-branch {
    position: relative;
    padding: 18px 12px;
    background: #ffffff;
    border: 1px solid #dfe5e8;
    border-radius: 12px;
}

.clinical-branch::before {
    content: "";
    position: absolute;
    top: -12px;
    left: 50%;
    width: 2px;
    height: 12px;
    background: #90a4ae;
}

.clinical-branch .tree-step {
    min-height: 48px;
    font-size: 15px;
}

.branch-line {
    color: #1976d2;
    font-size: 22px;
    margin: 6px 0;
}

.branch-answer {
    font-weight: bold;
    color: #37474f;
}

@media (max-width: 800px) {
    .clinical-branches {
        grid-template-columns: repeat(2, 1fr);
    }

    .clinical-branches::before {
        display: none;
    }
}

@media (max-width: 600px) {
    .clinical-branches {
        grid-template-columns: 1fr;
    }
}


.formula-family {
    background: #f8fbfd;
    border-radius: 14px;
    padding: 22px;
    position: relative;
}

.formula-root {
    width: fit-content;
    margin: 0 auto 24px;
    padding: 12px 24px;
    background: #ffffff;
    border: 2px solid #1976d2;
    border-radius: 12px;
    font-weight: bold;
    text-align: center;
}

.formula-connection-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    position: relative;
    margin-top: 28px;
}

.formula-connection-grid::before {
    content: "";
    position: absolute;
    top: -14px;
    left: 16.66%;
    right: 16.66%;
    height: 2px;
    background: #90a4ae;
}

.formula-card {
    position: relative;
    background: #ffffff;
    border: 1px solid #dfe5e8;
    border-radius: 14px;
    padding: 18px;
    text-align: center;
}

.formula-card::before {
    content: "";
    position: absolute;
    top: -14px;
    left: 50%;
    width: 2px;
    height: 14px;
    background: #90a4ae;
}

.formula-pattern {
    font-size: 13px;
    font-weight: bold;
    color: #546e7a;
}

.formula-arrow {
    font-size: 22px;
    line-height: 1.2;
    color: #1976d2;
    margin: 7px 0;
}

.formula-clue {
    font-weight: bold;
    font-size: 15px;
}

.formula-name {
    font-size: 17px;
    font-weight: bold;
    line-height: 1.4;
}

.formula-explanation {
    margin-top: 10px;
    font-size: 14px;
    line-height: 1.5;
    color: #455a64;
}

.formula-board {
    margin-top: 14px;
    padding: 9px;
    background: #fff8e1;
    border-radius: 9px;
    font-size: 12px;
    font-weight: bold;
}

.formula-memory {
    margin-top: 24px;
    padding: 18px;
    background: #eef6ff;
    border-radius: 12px;
    text-align: center;
}

.formula-memory-title {
    font-weight: bold;
    margin-bottom: 14px;
}

.formula-memory-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.formula-memory-grid > div {
    background: #ffffff;
    padding: 12px;
    border-radius: 10px;
    font-size: 13px;
}

@media (max-width: 800px) {
    .formula-connection-grid,
    .formula-memory-grid {
        grid-template-columns: 1fr;
    }

    .formula-connection-grid::before {
        display: none;
    }
}


.modification-family {
    background: #f8fbfd;
    border-radius: 14px;
    padding: 22px;
    position: relative;
}

.mod-step {
    max-width: 620px;
    margin: 0 auto;
    padding: 16px 20px;
    background: #ffffff;
    border: 1px solid #dfe5e8;
    border-radius: 14px;
    text-align: center;
}

.mod-label {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 7px;
}

.mod-text {
    font-size: 14px;
    line-height: 1.5;
    color: #455a64;
}

.mod-arrow {
    text-align: center;
    font-size: 25px;
    color: #1976d2;
    line-height: 1.2;
    margin: 6px 0;
}

.mod-base {
    border-top: 4px solid #1976d2;
}

.mod-question {
    border-top: 4px solid #f9a825;
}

.mod-relative {
    border-top: 4px solid #43a047;
}

.mod-result {
    border-top: 4px solid #8e24aa;
}

.mod-example {
    margin-top: 24px;
    padding: 18px;
    background: #eef6ff;
    border-radius: 12px;
}

.mod-example-title {
    text-align: center;
    font-weight: bold;
    margin-bottom: 16px;
}

.mod-example-flow {
    display: grid;
    grid-template-columns: 1fr auto 1fr auto 1.5fr;
    gap: 10px;
    align-items: center;
}

.mod-example-box {
    background: #ffffff;
    border-radius: 10px;
    padding: 14px;
    text-align: center;
    font-size: 13px;
}

.mod-highlight {
    background: #fff8e1;
    border: 1px solid #f9a825;
}

.mod-plus {
    font-size: 20px;
    font-weight: bold;
    text-align: center;
}

.mod-note {
    margin-top: 14px;
    font-size: 13px;
    line-height: 1.5;
    color: #455a64;
    text-align: center;
}

@media (max-width: 800px) {
    .mod-example-flow {
        grid-template-columns: 1fr;
    }

    .mod-plus {
        margin: 0;
    }
}


.scientific-family {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    padding: 6px 0;
}

.scientific-card {
    background: #ffffff;
    border: 1px solid #dfe5e8;
    border-radius: 14px;
    padding: 18px;
    text-align: center;
}

.scientific-center {
    grid-column: 1 / 3;
    max-width: 48%;
    width: 100%;
    justify-self: center;
}

.scientific-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 7px;
}

.latin-name {
    font-size: 13px;
    font-style: italic;
    color: #607d8b;
    line-height: 1.5;
}

.plant-part {
    margin-top: 12px;
    display: inline-block;
    padding: 6px 12px;
    background: #eef6ff;
    border-radius: 8px;
    font-size: 12px;
    font-weight: bold;
}

@media (max-width: 650px) {
    .scientific-family {
        grid-template-columns: 1fr;
    }

    .scientific-center {
        grid-column: auto;
        max-width: none;
    }
}

.tree {
    background: #eef6ff;
    border-left: 6px solid #1976d2;
    border-radius: 12px;
    padding: 20px;
}

.tree-step {
    padding: 8px 0;
    font-weight: bold;
}

.arrow {
    text-align: center;
    font-size: 22px;
    color: #1976d2;
}

/* FAMILY */

.family {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 18px;
    position: relative;
    padding: 18px 10px 10px;
    align-items: start;
}


.family .herb:nth-child(1) {
    grid-column: 1 / 3;
    grid-row: 1;
}

.family .herb:nth-child(2) {
    grid-column: 5 / 7;
    grid-row: 1;
}

.family .herb:nth-child(3) {
    grid-column: 3 / 5;
    grid-row: 2;
}

.family .herb:nth-child(4) {
    grid-column: 1 / 4;
    grid-row: 3;
}

.family .herb:nth-child(5) {
    grid-column: 4 / 7;
    grid-row: 3;
}

.herb {
    border: 1px solid #dfe5e8;
    border-radius: 14px;
    padding: 17px;
    background: #ffffff;
}

.herb-name {
    font-size: 19px;
    font-weight: bold;
    margin-bottom: 4px;
}

.scientific {
    font-size: 13px;
    font-style: italic;
    color: #607d8b;
    margin-bottom: 10px;
}

.signature {
    font-weight: bold;
    margin-top: 9px;
}

/* CLUE */

.clue {
    background: #f1f8e9;
    border-radius: 14px;
    padding: 18px;
}

.clue-item {
    margin: 8px 0;
}

/* CONFUSION */

.confuse {
    background: #fff3f3;
    border-left: 6px solid #d32f2f;
    border-radius: 12px;
    padding: 18px;
}

/* FORMULAS */

.formulas {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}

.formula {
    border: 1px solid #dfe5e8;
    border-radius: 14px;
    padding: 17px;
}

.formula-name {
    font-weight: bold;
    font-size: 18px;
}

.formula-memory {
    margin-top: 8px;
    font-weight: bold;
}

/* BOARD */

.board {
    background: #263238;
    color: white;
    border-radius: 14px;
    padding: 20px;
}

.board strong {
    color: #ffecb3;
}

/* MEMORY */

.memory {
    background: #e8eaf6;
    border-radius: 14px;
    padding: 20px;
    font-size: 17px;
    line-height: 1.7;
}

/* FACT */

.fact {
    background: #fce4ec;
    border-radius: 14px;
    padding: 18px;
}

/* MOBILE */

@media (max-width: 650px) {

    body {
        padding: 10px;
    }

    .header h1 {
        font-size: 24px;
    }

    .content {
        padding: 16px;
    }

    .family,
    .formulas {
        grid-template-columns: 1fr;
    }

}

</style>

<style>
/* ===== GOITER INTEGRATED MASTER MAP ===== */

.goiter-master-map {
    max-width: 1200px;
    margin: 24px auto;
    padding: 28px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,.06);
}

.goiter-master-map .header {
    margin-bottom: 20px;
    padding: 24px;
    background: linear-gradient(135deg, #f0fdf4, #ffffff);
    border-left: 6px solid #22c55e;
    border-radius: 14px;
}

.goiter-master-map .header h1 {
    margin: 4px 0 8px;
    font-size: 32px;
    font-weight: 800;
    color: #172033;
}

.goiter-master-map .header p {
    margin: 4px 0;
    color: #526070;
}

.goiter-visual {
    margin: 18px 0 24px;
    padding: 24px;
    text-align: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
}

.goiter-visual strong {
    display: block;
    margin-top: 8px;
    font-size: 20px;
    color: #172033;
}

.goiter-visual span {
    display: block;
    margin-top: 6px;
    color: #64748b;
}

.goiter-board-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin: 20px 0;
}

.goiter-board-card {
    padding: 20px;
    background: #ffffff;
    border: 1px solid #dbe3ec;
    border-radius: 14px;
    transition: transform .15s ease, box-shadow .15s ease;
}

.goiter-board-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,.08);
}

.goiter-board-card h3 {
    margin: 0 0 8px;
    font-size: 18px;
    color: #172033;
}

.goiter-board-card p {
    margin: 0;
    color: #5b6778;
    line-height: 1.5;
}

.goiter-board-card.current {
    border: 2px solid #f59e0b;
    background: #fffbeb;
}

.goiter-board-card .current-label {
    display: inline-block;
    margin-top: 10px;
    padding: 4px 9px;
    border-radius: 999px;
    background: #f59e0b;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
}

.goiter-reasoning {
    margin: 20px 0;
    padding: 18px 20px;
    text-align: center;
    background: #f1f5f9;
    border-radius: 12px;
    font-weight: 700;
    color: #334155;
}

.goiter-master-map details {
    margin: 12px 0;
    border: 1px solid #dbe3ec;
    border-radius: 12px;
    background: #ffffff;
    overflow: hidden;
}

.goiter-master-map details > summary {
    cursor: pointer;
    padding: 16px 18px;
    background: #f8fafc;
    font-weight: 700;
    color: #263449;
    list-style-position: inside;
}

.goiter-master-map details[open] > summary {
    background: #eef6ff;
    border-bottom: 1px solid #dbe3ec;
}

.goiter-module {
    padding: 20px;
}

.goiter-module h2 {
    margin-top: 0;
    color: #172033;
}

.goiter-module h3 {
    color: #334155;
}

.goiter-point-box {
    margin: 16px 0;
    padding: 18px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.goiter-core-prescription {
    margin: 14px 0;
    padding: 16px;
    text-align: center;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 12px;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: .04em;
    color: #9a3412;
}

.goiter-60-review {
    margin-top: 20px;
    padding: 18px;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 12px;
}

@media (max-width: 768px) {
    .goiter-master-map {
        margin: 12px 0;
        padding: 14px;
        border-radius: 12px;
    }

    .goiter-master-map .header {
        padding: 18px;
    }

    .goiter-master-map .header h1 {
        font-size: 26px;
    }

    .goiter-board-grid {
        grid-template-columns: 1fr;
    }

    .goiter-board-card {
        padding: 16px;
    }
}
</style>


<style>
/* ===== GOITER MAP — MATCH EXISTING HTML CLASSES ===== */

.goiter-master-map {
    max-width: 1200px;
    margin: 24px auto;
    padding: 28px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 5px 20px rgba(15,23,42,.07);
}

.goiter-master-map .header {
    padding: 24px;
    margin-bottom: 20px;
    background: linear-gradient(135deg,#f0fdf4,#ffffff);
    border-left: 6px solid #22c55e;
    border-radius: 14px;
}

.goiter-master-map .header h1 {
    margin: 0 0 8px;
    font-size: 32px;
    color: #172033;
}

.goiter-visual {
    margin: 20px 0 24px;
    padding: 28px 20px;
    text-align: center;
    background: #f8fafc;
    border: 1px solid #dbe3ec;
    border-radius: 16px;
}

.goiter-visual strong {
    display: block;
    margin: 8px 0 5px;
    font-size: 21px;
    color: #172033;
}

.goiter-visual span {
    color: #64748b;
}

.board-grid,
.goiter-board-grid {
    display: grid;
    grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 16px;
    margin: 20px 0;
}

.board-card,
.goiter-board-card {
    padding: 20px;
    background: #fff;
    border: 1px solid #dbe3ec;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(15,23,42,.04);
}

.board-card h3,
.goiter-board-card h3 {
    margin: 0 0 8px;
    color: #172033;
}

.board-card p,
.goiter-board-card p {
    margin: 0;
    color: #5b6778;
    line-height: 1.5;
}

.board-card.current,
.goiter-board-card.current {
    border: 2px solid #f59e0b;
    background: #fffbeb;
}

.current-label {
    display: inline-block;
    margin-top: 10px;
    padding: 4px 10px;
    border-radius: 999px;
    background: #f59e0b;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
}

.board-reasoning,
.goiter-reasoning {
    margin: 20px 0;
    padding: 17px 20px;
    text-align: center;
    background: #f1f5f9;
    border-radius: 12px;
    font-weight: 700;
    color: #334155;
}

/* ===== BOARD MODULES ===== */

.board-module {
    margin: 14px 0;
    background: #fff;
    border: 1px solid #dbe3ec;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(15,23,42,.04);
}

.board-module > summary {
    padding: 17px 20px;
    cursor: pointer;
    background: #f8fafc;
    color: #263449;
    font-weight: 700;
    list-style-position: inside;
}

.board-module[open] > summary {
    background: #eef6ff;
    border-bottom: 1px solid #dbe3ec;
}

.board-module > summary em {
    margin-left: 8px;
    color: #d97706;
    font-size: 12px;
}

.module-content {
    padding: 22px;
}

.module-intro {
    margin-bottom: 18px;
}

.module-intro strong {
    display: block;
    font-size: 20px;
    color: #172033;
    margin-bottom: 5px;
}

.module-intro p {
    margin: 0;
    color: #64748b;
}

/* ===== POINT LOCATION ===== */

.point-prescription {
    margin: 18px 0;
    padding: 18px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 12px;
}

.point-title {
    margin-bottom: 10px;
    font-size: 12px;
    font-weight: 800;
    color: #9a3412;
    letter-spacing: .05em;
}

.point-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.point-list span {
    padding: 8px 13px;
    background: #fff;
    border: 1px solid #fdba74;
    border-radius: 999px;
    font-weight: 800;
    color: #9a3412;
}

.point-groups {
    display: grid;
    grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 16px;
}

.point-group {
    padding: 18px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

.point-group-title {
    margin-bottom: 12px;
    font-weight: 800;
    color: #334155;
}

.point-item {
    padding: 11px 0;
    border-top: 1px solid #e2e8f0;
}

.point-item:first-of-type {
    border-top: 0;
}

.point-item strong {
    display: block;
    margin-bottom: 3px;
    color: #172033;
}

.point-item span {
    color: #64748b;
    font-size: 14px;
    line-height: 1.45;
}

/* ===== MOBILE ===== */

@media (max-width:768px) {
    .goiter-master-map {
        margin: 12px 0;
        padding: 14px;
        border-radius: 12px;
    }

    .goiter-master-map .header {
        padding: 18px;
    }

    .goiter-master-map .header h1 {
        font-size: 26px;
    }

    .board-grid,
    .goiter-board-grid,
    .point-groups {
        grid-template-columns: 1fr;
    }

    .module-content {
        padding: 16px;
    }
}
</style>


<style>
/* ===== GOITER FOUR BOARD MAP ===== */

.goiter-four-boards {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    width: 100%;
    margin: 22px 0;
}

.goiter-four-boards .board-card {
    min-height: 120px;
    padding: 20px;
    background: #ffffff;
    border: 1px solid #dbe3ec;
    border-radius: 14px;
    box-shadow: 0 3px 10px rgba(15,23,42,.05);
    box-sizing: border-box;
}

.goiter-four-boards .board-icon {
    font-size: 24px;
    margin-bottom: 8px;
}

.goiter-four-boards .board-name {
    font-size: 16px;
    font-weight: 800;
    color: #172033;
    margin-bottom: 7px;
}

.goiter-four-boards .board-flow {
    font-size: 13px;
    line-height: 1.45;
    color: #64748b;
}

.goiter-four-boards .active-board {
    border: 2px solid #f59e0b;
    background: #fffbeb;
}

.goiter-four-boards .current-module {
    display: inline-block;
    margin-top: 10px;
    padding: 4px 9px;
    border-radius: 999px;
    background: #f59e0b;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .04em;
}

@media (max-width: 768px) {
    .goiter-four-boards {
        grid-template-columns: 1fr;
    }
}
</style>


<style>
/* ===== GOITER MASTER MAP — FULL AVAILABLE WIDTH ===== */

.card:has(.goiter-master-map) {
    width: 100%;
    max-width: 1200px;
    box-sizing: border-box;
}

.goiter-master-map {
    width: 100%;
    max-width: none;
    box-sizing: border-box;
}
</style>


<style>
/* ===== GOITER 60-SECOND REVIEW ===== */

.goiter-quick-review {
    margin: 24px 0;
    padding: 22px;
    background: #f8fafc;
    border: 1px solid #dbe3ec;
    border-radius: 14px;
}

.quick-review-title {
    margin-bottom: 18px;
    font-size: 18px;
    font-weight: 800;
    color: #172033;
}

.quick-review-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.quick-review-card {
    padding: 15px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}

.quick-review-card strong {
    display: block;
    margin-bottom: 6px;
    color: #263449;
}

.quick-review-card span {
    color: #64748b;
    font-size: 13px;
    line-height: 1.45;
}

.quick-review-final {
    margin-top: 14px;
    padding: 15px;
    text-align: center;
    background: #eef6ff;
    border: 1px solid #cbdff5;
    border-radius: 10px;
}

.quick-review-final strong {
    display: block;
    margin-bottom: 6px;
    color: #263449;
}

.quick-review-final span {
    font-weight: 800;
    color: #334155;
}

.quick-review-bottom {
    margin-top: 12px;
    text-align: center;
    font-size: 12px;
    font-weight: 800;
    color: #64748b;
    letter-spacing: .03em;
}

@media (max-width: 768px) {
    .quick-review-grid {
        grid-template-columns: 1fr;
    }
}
</style>


<style>
/* ===== GOITER FOUNDATIONS CARD ===== */

.foundations-module .module-content {
    padding: 24px;
}

.foundations-self-check,
.foundations-section,
.foundations-decision,
.foundations-board-point,
.foundations-memory {
    margin-bottom: 18px;
}

.foundations-self-check {
    padding: 18px;
    background: #fffaf0;
    border: 1px solid #f3dfb3;
    border-radius: 12px;
}

.section-label {
    margin-bottom: 10px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .05em;
    color: #64748b;
}

.foundations-self-check strong {
    color: #172033;
}

.foundations-self-check ul {
    margin: 10px 0 0 18px;
    padding: 0;
    color: #475569;
    line-height: 1.55;
}

.foundations-flow {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 9px;
    padding: 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #172033;
}

.foundations-flow span {
    color: #94a3b8;
}

.foundations-section > p {
    color: #64748b;
    line-height: 1.55;
}

.foundation-grid,
.pathology-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.foundation-card,
.pathology-card {
    padding: 17px;
    background: #ffffff;
    border: 1px solid #dbe3ec;
    border-radius: 12px;
}

.foundation-card-title {
    margin-bottom: 9px;
    font-weight: 800;
    color: #172033;
}

.foundation-card p,
.pathology-card span {
    color: #64748b;
    line-height: 1.5;
}

.foundation-clue {
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid #e5e7eb;
    font-size: 13px;
    color: #475569;
    line-height: 1.45;
}

.pathology-card strong {
    display: block;
    margin-bottom: 8px;
    color: #172033;
}

.manifestation-list {
    display: grid;
    gap: 8px;
}

.manifestation-list > div {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 13px 15px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
}

.manifestation-list strong {
    color: #172033;
}

.manifestation-list span {
    color: #475569;
    font-weight: 700;
}

.foundations-decision {
    padding: 18px;
    background: #eef6ff;
    border: 1px solid #cbdff5;
    border-radius: 12px;
}

.decision-flow {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
    text-align: center;
    color: #334155;
    font-weight: 700;
}

.decision-flow span {
    color: #94a3b8;
}

.foundations-board-point {
    padding: 18px;
    background: #f8fafc;
    border: 1px solid #dbe3ec;
    border-radius: 12px;
}

.foundations-board-point p {
    margin: 0;
    color: #334155;
    line-height: 1.55;
}

.foundations-memory {
    padding: 18px;
    background: #f8fafc;
    border: 1px solid #dbe3ec;
    border-radius: 12px;
}

.foundations-memory > div:not(.section-label) {
    display: flex;
    justify-content: space-between;
    gap: 18px;
    padding: 9px 0;
    border-bottom: 1px solid #e5e7eb;
}

.foundations-memory > div:last-child {
    border-bottom: 0;
}

.foundations-memory strong {
    color: #172033;
}

.foundations-memory span {
    color: #64748b;
    text-align: right;
}

@media (max-width: 768px) {
    .foundation-grid,
    .pathology-grid {
        grid-template-columns: 1fr;
    }

    .manifestation-list > div,
    .foundations-memory > div:not(.section-label) {
        flex-direction: column;
        gap: 5px;
    }

    .foundations-memory span {
        text-align: left;
    }

    .foundations-flow,
    .decision-flow {
        flex-direction: column;
    }
}


<style>
/* BIOMEDICINE VISUAL CARD — v1 */

/* Main Biomedicine module */
.biomedicine-module .module-content {
    max-width: 1100px;
    margin: 0 auto;
    padding: 24px 28px 30px;
}

/* Intro */
.biomedicine-module .module-intro {
    background: linear-gradient(135deg, #f5f9ff, #ffffff);
    border: 1px solid #dbe7f5;
    border-radius: 16px;
    padding: 20px 22px;
    margin-bottom: 20px;
}

.biomedicine-module .module-intro strong {
    display: block;
    font-size: 1.35rem;
    margin-bottom: 7px;
}

.biomedicine-module .module-intro p {
    margin: 0;
    color: #52657a;
    font-size: 0.95rem;
}

/* 10-second self check */
.biomedicine-self-check {
    background: #fff8e8;
    border: 1px solid #f1d99a;
    border-radius: 16px;
    padding: 20px 22px;
    margin: 20px 0;
}

.biomedicine-self-check h3,
.biomedicine-self-check strong {
    color: #684d00;
}

.biomedicine-self-check ul {
    margin-bottom: 0;
}

/* Individual visual sections */
.biomedicine-section {
    background: #ffffff;
    border: 1px solid #e1e7ee;
    border-radius: 16px;
    padding: 20px 22px;
    margin: 18px 0;
    box-shadow: 0 3px 12px rgba(40, 60, 80, 0.06);
}

.biomedicine-section h3 {
    margin-top: 0;
    margin-bottom: 12px;
    font-size: 1.05rem;
}

/* Anatomy / physiology / general cards */
.biomedicine-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 18px;
    margin: 10px 0;
}

.biomedicine-card strong {
    display: inline-block;
    margin-bottom: 5px;
}

/* Physiology flow */
.biomedicine-flow {
    background: #f1f7ff;
    border: 1px solid #d4e4f7;
    border-radius: 14px;
    padding: 18px;
    margin: 14px 0;
    text-align: center;
    font-weight: 700;
    line-height: 1.8;
}

/* Board clue */
.biomedicine-board-clue {
    background: #eef8f1;
    border-left: 5px solid #4d8b63;
    border-radius: 12px;
    padding: 18px 20px;
    margin: 18px 0;
}

.biomedicine-board-clue strong {
    display: block;
    font-size: 1.05rem;
    margin-bottom: 6px;
}

/* Three functional states */
.biomedicine-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin: 14px 0;
}

.biomedicine-state {
    border: 1px solid #dfe5ec;
    border-radius: 14px;
    padding: 18px;
    background: #ffffff;
}

.biomedicine-state strong {
    display: block;
    font-size: 1rem;
    margin-bottom: 7px;
}

/* Clinical clues */
.biomedicine-pattern {
    border-radius: 14px;
    padding: 16px 18px;
    margin: 10px 0;
    background: #fafafa;
    border: 1px solid #e3e3e3;
}

.biomedicine-pattern strong {
    display: block;
    margin-bottom: 6px;
}

/* Warning / distinction */
.biomedicine-warning {
    background: #fff3f3;
    border: 1px solid #efcaca;
    border-radius: 14px;
    padding: 18px 20px;
    margin: 18px 0;
}

.biomedicine-warning strong {
    display: block;
    margin-bottom: 6px;
}

/* Laboratory section */
.biomedicine-lab {
    background: #f7f5ff;
    border: 1px solid #ddd7f2;
    border-radius: 14px;
    padding: 18px 20px;
    margin: 12px 0;
}

.biomedicine-lab strong {
    display: block;
    margin-bottom: 6px;
}

/* Decision / reasoning */
.biomedicine-decision {
    background: #f5f8fb;
    border: 1px solid #d9e1ea;
    border-radius: 16px;
    padding: 20px;
    margin: 20px 0;
    text-align: center;
}

.biomedicine-decision strong {
    display: block;
    line-height: 1.8;
}

/* High-yield memory */
.biomedicine-memory {
    background: #edf5ff;
    border: 1px solid #cfe0f5;
    border-radius: 16px;
    padding: 20px 22px;
    margin-top: 20px;
}

.biomedicine-memory strong {
    display: block;
    margin-bottom: 7px;
}

/* Better spacing inside lists */
.biomedicine-module li {
    margin-bottom: 5px;
}

/* Mobile */
@media (max-width: 767px) {

    .biomedicine-module .module-content {
        padding: 16px 14px 22px;
    }

    .biomedicine-section,
    .biomedicine-self-check,
    .biomedicine-memory,
    .biomedicine-board-clue,
    .biomedicine-warning,
    .biomedicine-decision {
        padding: 16px;
        border-radius: 13px;
    }

    .biomedicine-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .biomedicine-flow {
        font-size: 0.92rem;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }
}
</style>

</style>

</head>



<?php if ($scormid === 284): ?>
<div class="card">


    <!-- GOITER INTEGRATED MASTER MAP -->

    <div class="goiter-master-map">

        <div class="goiter-map-header">
            <div class="goiter-map-kicker">INTEGRATED BOARD STUDY</div>
            <h1>🌿 GOITER</h1>
            <div class="goiter-map-subtitle">Thyroid Enlargement</div>
            <p>One Clinical Topic • Four Board Perspectives</p>
        </div>

        <div class="goiter-visual">
            <div class="goiter-visual-icon">🦋</div>
            <div>
                <strong>GOITER / THYROID ENLARGEMENT</strong>
                <span>Start here → recognize the topic, then choose the board perspective you need.</span>
            </div>
        </div>

        <div class="goiter-four-boards">

            <div class="board-card foundations-card">
                <div class="board-icon">🧠</div>
                <div class="board-name">FOUNDATIONS</div>
                <div class="board-flow">Pattern → Pathomechanism → Treatment</div>
            </div>

            <div class="board-card biomed-card">
                <div class="board-icon">🧬</div>
                <div class="board-name">BIOMEDICINE</div>
                <div class="board-flow">Anatomy → Physiology → Signs → Labs</div>
            </div>

            <div class="board-card point-card active-board">
                <div class="board-icon">📍</div>
                <div class="board-name">ACUPUNCTURE</div>
                <div class="board-flow">Point → Location → Action → Selection</div>
                <div class="current-module">CURRENT MODULE</div>
            </div>

            <div class="board-card herb-card">
                <div class="board-icon">🌿</div>
                <div class="board-name">CHINESE HERBOLOGY</div>
                <div class="board-flow">Pattern → Principle → Herb / Formula → Action</div>
            </div>

        </div>



    </div>


    <!-- CURRENT MODULE: POINT LOCATION -->

    <details class="board-module point-module" open>
        <summary>
            <span>📍</span>
            <strong>Acupuncture with Point Location</strong>
            <em>Current Module</em>
        </summary>

        <div class="module-content">

            <div class="module-intro">
                <strong>GOITER — Acupuncture with Point Location</strong>
                <p>Point → Location → Action → Selection</p>
            </div>

            <div class="point-prescription">
                <div class="point-title">CORE PRESCRIPTION</div>
                <div class="point-list">
                    <span>SJ13</span>
                    <span>LI17</span>
                    <span>SI17</span>
                    <span>CV22</span>
                    <span>LI4</span>
                    <span>ST36</span>
                </div>
            </div>

            <div class="point-groups">

                <div class="point-group">
                    <div class="point-group-title">LOCAL POINTS</div>

                    <div class="point-item">
                        <strong>SJ13 — Naohui</strong>
                        <span>Removes obstruction from the meridians to relieve Qi stagnation and Phlegm accumulation in the Goiter prescription.</span>
                        <div class="point-location">📍 <strong>Location:</strong> Posterior aspect of the arm, posteroinferior to the border of the deltoid muscle, 3 B-cun inferior to the acromial angle.</div>
                    </div>

                    <div class="point-item">
                        <strong>LI17 — Tianding <span class="point-safety">⚠️ SAFETY</span></strong>
                        <span>Local point included in the Goiter prescription.</span>
                        <div class="point-location">📍 <strong>Location:</strong> Anterior aspect of the neck, at the level of the cricoid cartilage, just posterior to the border of the sternocleidomastoid muscle.</div>
                        <div class="point-safety-note">⚠️ <strong>Safety:</strong> Cervical region with important deep anatomical structures. Use appropriate anatomical knowledge and standardized needling technique.</div>
                    </div>

                    <div class="point-item">
                        <strong>SI17 — Tianrong</strong>
                        <span>Local point included in the Goiter prescription.</span>
                        <div class="point-location">📍 <strong>Location:</strong> Lateral neck, posterior to the angle of the mandible, in the depression between the mandible and sternocleidomastoid muscle.</div>
                    </div>

                    <div class="point-item">
                        <strong>CV22 — Tiantu <span class="point-safety">⚠️⚠️ SAFETY</span></strong>
                        <span>Local point included in the Goiter prescription.</span>
                        <div class="point-location">📍 <strong>Location:</strong> Anterior midline of the neck, in the center of the suprasternal fossa.</div>
                        <div class="point-safety-note">⚠️ <strong>Safety:</strong> Anatomically sensitive suprasternal region. Deep structures include the trachea and major vessels; standardized technique and depth precautions are essential.</div>
                    </div>
                </div>

                <div class="point-group">
                    <div class="point-group-title">DISTAL POINTS</div>

                    <div class="point-item">
                        <strong>LI4 — Hegu</strong>
                        <span>Distal point included in the basic Goiter acupuncture prescription.</span>
                        <div class="point-location">📍 <strong>Location:</strong> On the dorsum of the hand, radial to the midpoint of the second metacarpal bone.</div>
                    </div>

                    <div class="point-item">
                        <strong>ST36 — Zusanli</strong>
                        <span>Distal point included in the basic Goiter acupuncture prescription.</span>
                        <div class="point-location">📍 <strong>Location:</strong> On the lower leg, below the knee, on the anterior aspect of the leg, lateral to the tibial crest.</div>
                    </div>
                </div>

            </div>

            <div class="point-reasoning">
                <div class="clinical-modifications" style="margin-top:18px;padding:16px;border:1px solid #ffcc80;border-radius:12px;background:#fffaf2;">
    <div style="font-weight:700;font-size:16px;color:#e65100;margin-bottom:12px;">
        🎯 CLINICAL MODIFICATIONS
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;">

        <div style="padding:12px;border:1px solid #e0e0e0;border-radius:10px;background:#ffffff;">
            <strong>Liver Qi Stagnation</strong>
            <div style="margin-top:5px;">→ CV17 + LV3</div>
            <div class="point-location" style="margin-top:10px;">📍 <strong>CV17 — Danzhong:</strong> Anterior midline of the chest, at the level of the 4th intercostal space.</div>
            <div class="point-location" style="margin-top:6px;">📍 <strong>LV3 — Taichong:</strong> On the dorsum of the foot, in the depression distal to the junction of the 1st and 2nd metatarsal bones.</div>
        </div>



        <div style="padding:12px;border:1px solid #e0e0e0;border-radius:10px;background:#ffffff;">
            <strong>Palpitation</strong>
            <div style="margin-top:5px;">→ PC6 + HT7</div>
            <div class="point-location" style="margin-top:10px;">📍 <strong>PC6 — Neiguan:</strong> On the anterior forearm, 2 B-cun proximal to the wrist crease, between the tendons of palmaris longus and flexor carpi radialis.</div>
            <div class="point-location" style="margin-top:6px;">📍 <strong>HT7 — Shenmen:</strong> On the anteromedial aspect of the wrist, radial to the tendon of flexor carpi ulnaris, at the wrist crease.</div>
        </div>

        <div style="padding:12px;border:1px solid #e0e0e0;border-radius:10px;background:#ffffff;">
            <strong>Exophthalmos</strong>
            <div style="margin-top:5px;">→ SJ23 + BL2 + BL1 + GB20</div>

            <div class="point-location" style="margin-top:10px;">
                📍 <strong>SJ23 — Sizhukong:</strong>
                On the head, in the depression at the lateral end of the eyebrow.
            </div>

            <div class="point-location" style="margin-top:6px;">
                📍 <strong>BL2 — Zanzhu:</strong>
                On the face, in the depression at the medial end of the eyebrow, near the supraorbital notch.
            </div>

            <div class="point-location" style="margin-top:6px;">
                📍 <strong>BL1 — Jingming:</strong> ⚠️
                On the face, 0.1 B-cun superior to the inner canthus of the eye.
                <div style="margin-top:4px;color:#9a6700;">
                    ⚠️ Anatomically sensitive periocular region; precise localization and appropriate technique are essential.
                </div>
            </div>

            <div class="point-location" style="margin-top:6px;">
                📍 <strong>GB20 — Fengchi:</strong>
                On the posterior aspect of the neck, inferior to the occipital bone, in the depression between the origins of the sternocleidomastoid and trapezius muscles.
            </div>
        </div>

        <div style="padding:12px;border:1px solid #e0e0e0;border-radius:10px;background:#ffffff;">
            <strong>Hot temper / Excessive perspiration</strong>
            <div style="margin-top:5px;">→ SP6 + KD7</div>

            <div class="point-location" style="margin-top:10px;">
                📍 <strong>SP6 — Sanyinjiao:</strong>
                On the medial aspect of the lower leg, 3 B-cun superior to the prominence of the medial malleolus, posterior to the medial border of the tibia.
            </div>

            <div class="point-location" style="margin-top:6px;">
                📍 <strong>KD7 — Fuliu:</strong>
                On the medial aspect of the lower leg, 2 B-cun superior to KD3, anterior to the Achilles tendon.
            </div>
        </div>

    </div>
</div>

<div style="font-weight:700;font-size:16px;color:#333;margin-top:4px;">🎯 POINT SELECTION</div>

                <div style="margin-top:12px;padding:16px;border:1px solid #d7e3f4;border-radius:12px;background:#f8fbff;">

                    <div style="font-weight:700;text-align:center;color:#24527a;font-size:16px;">
                        GOITER
                    </div>

                    <div style="text-align:center;margin:8px 0;color:#78909c;font-size:18px;">↓</div>

                    <div style="padding:12px;border:1px solid #c5d7e8;border-radius:10px;background:#ffffff;text-align:center;">
                        <strong>START WITH THE CORE PRESCRIPTION</strong>
                        <div style="margin-top:6px;font-size:14px;">
                            Local: SJ13 + LI17 + SI17 + CV22
                        </div>
                        <div style="font-size:14px;">
                            Distal: LI4 + ST36
                        </div>
                    </div>

                    <div style="text-align:center;margin:8px 0;color:#78909c;font-size:18px;">↓</div>

                    <div style="font-weight:700;text-align:center;margin-bottom:10px;color:#455a64;">
                        WHAT CLINICAL MANIFESTATION IS PRESENT?
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;">

                        <div style="padding:12px;border:1px solid #dfe5ec;border-radius:10px;background:#ffffff;text-align:center;">
                            <strong>Liver Qi Stagnation</strong>
                            <div style="margin-top:6px;color:#24527a;">Add CV17 + LV3</div>
                            <div class="point-location" style="text-align:left;margin-top:10px;">📍 <strong>CV17 — Danzhong:</strong> Anterior midline of the chest, at the level of the 4th intercostal space.</div>
                            <div class="point-location" style="text-align:left;margin-top:6px;">📍 <strong>LV3 — Taichong:</strong> On the dorsum of the foot, in the depression distal to the junction of the 1st and 2nd metatarsal bones.</div>
                            
                            
                        </div>

                        <div style="padding:12px;border:1px solid #dfe5ec;border-radius:10px;background:#ffffff;text-align:center;">
                            <strong>Palpitation</strong>
                            <div style="margin-top:6px;color:#24527a;">Add PC6 + HT7</div>
                            <div class="point-location" style="text-align:left;margin-top:10px;">📍 <strong>PC6 — Neiguan:</strong> On the anterior forearm, 2 B-cun proximal to the wrist crease, between the tendons of palmaris longus and flexor carpi radialis.</div>
                            <div class="point-location" style="text-align:left;margin-top:6px;">📍 <strong>HT7 — Shenmen:</strong> On the anteromedial aspect of the wrist, radial to the tendon of flexor carpi ulnaris, at the wrist crease.</div>
                        </div>

                        <div style="padding:12px;border:1px solid #dfe5ec;border-radius:10px;background:#ffffff;text-align:center;">
                            <strong>Exophthalmos</strong>
                            <div style="margin-top:6px;color:#24527a;">Add SJ23 + BL2 + BL1 + GB20</div>
                            <div class="point-location" style="text-align:left;margin-top:10px;">📍 <strong>SJ23 — Sizhukong:</strong> On the head, in the depression at the lateral end of the eyebrow.</div>
                            <div class="point-location" style="text-align:left;margin-top:6px;">📍 <strong>BL2 — Zanzhu:</strong> On the face, in the depression at the medial end of the eyebrow, near the supraorbital notch.</div>
                            <div class="point-location" style="text-align:left;margin-top:6px;">📍 <strong>BL1 — Jingming:</strong> ⚠️ On the face, 0.1 B-cun superior to the inner canthus of the eye.
                                <div style="margin-top:4px;color:#9a6700;">⚠️ Anatomically sensitive periocular region; precise localization and appropriate technique are essential.</div>
                            </div>
                            <div class="point-location" style="text-align:left;margin-top:6px;">📍 <strong>GB20 — Fengchi:</strong> On the posterior aspect of the neck, inferior to the occipital bone, in the depression between the origins of the sternocleidomastoid and trapezius muscles.</div>
                        </div>

                        <div style="padding:12px;border:1px solid #dfe5ec;border-radius:10px;background:#ffffff;text-align:center;">
                            <strong>Hot temper / Excessive perspiration</strong>
                            <div style="margin-top:6px;color:#24527a;">Add SP6 + KD7</div>
                            <div class="point-location" style="text-align:left;margin-top:10px;">📍 <strong>SP6 — Sanyinjiao:</strong> On the medial aspect of the lower leg, 3 B-cun superior to the prominence of the medial malleolus, posterior to the medial border of the tibia.</div>
                            <div class="point-location" style="text-align:left;margin-top:6px;">📍 <strong>KD7 — Fuliu:</strong> On the medial aspect of the lower leg, 2 B-cun superior to KD3, anterior to the Achilles tendon.</div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </details>


    <!-- OTHER BOARD MODULES -->

    <details class="board-module foundations-module">
        <summary>
            <span>🧠</span>
            <strong>Foundations of Oriental Medicine</strong>
            <em>Open when needed</em>
        </summary>

        <div class="module-content">

            <div class="module-intro">
                <strong>GOITER — Foundations of Oriental Medicine</strong>
                <p>Pattern → Pathomechanism → Manifestation → Treatment Strategy</p>
            </div>

            <div class="foundations-self-check">
                <div class="section-label">⚡ 10-SECOND SELF-CHECK</div>
                <strong>Can you recognize the Goiter presentation before looking at the answer?</strong>
                <ul>
                    <li>What distinguishes <strong>Qi Goiter</strong>?</li>
                    <li>What findings point toward <strong>Flesh Goiter</strong>?</li>
                    <li>
                        Which manifestation connects Goiter with <strong>Liver-Qi Stagnation</strong>?
                        <details style="margin-top:6px;">
                            <summary style="cursor:pointer;font-weight:600;">💡 Show answer</summary>
                            <div style="margin-top:6px;padding:8px 10px;border-left:3px solid #6c7ae0;background:#f7f9fc;">
                                The swelling may <strong>change with emotions</strong>, suggesting a connection between the Goiter presentation and Liver-Qi Stagnation.
                            </div>
                        </details>
                    </li>
                    <li>
                        Which findings indicate <strong>Phlegm Accumulation, Blood Stasis, or Qi stagnation</strong>?
                        <details style="margin-top:6px;">
                            <summary style="cursor:pointer;font-weight:600;">💡 Show answer</summary>
                            <div style="margin-top:6px;padding:8px 10px;border-left:3px solid #6c7ae0;background:#f7f9fc;">
                                Look for the underlying pattern: <strong>Qi stagnation with Phlegm accumulation, Blood Stasis/agglomeration, or stagnation of Qi and Blood.</strong>
                            </div>
                        </details>
                    </li>
                </ul>
            </div>

            <div class="foundations-section">
                <div class="section-label">🧠 CORE CONCEPT</div>
                <div class="foundations-flow">
                    <strong>GOITER</strong>
                    <span>→</span>
                    <strong>DIFFERENTIATION</strong>
                    <span>→</span>
                    <strong>MANIFESTATION</strong>
                    <span>→</span>
                    <strong>TREATMENT STRATEGY</strong>
                </div>

                <div class="foundation-grid core-concept-grid" style="margin-top:14px;">

                    <div class="foundation-card">
                        <div class="foundation-card-title">1️⃣ GOITER — Recognize the clinical problem</div>
                        <p>
                            Start by recognizing the clinical problem:
                            an enlargement or swelling in the neck that represents the Goiter presentation.
                        </p>
                    </div>

                    <div class="foundation-card">
                        <div class="foundation-card-title">2️⃣ DIFFERENTIATION — Identify the pattern</div>
                        <p>
                            Next, determine the type of Goiter pattern.
                            In this material, the key differentiation is between
                            <strong>Qi Goiter</strong> and <strong>Flesh Goiter</strong>.
                        </p>
                    </div>

                    <div class="foundation-card">
                        <div class="foundation-card-title">3️⃣ MANIFESTATION — Identify what is present</div>
                        <p>
                            Then, identify the associated manifestations that help define
                            the clinical picture, such as changes with emotions,
                            exophthalmos, irritability, tremor, sweating, chest stuffiness,
                            or palpitation.
                        </p>
                    </div>

                    <div class="foundation-card">
                        <div class="foundation-card-title">4️⃣ TREATMENT STRATEGY — Apply the clinical reasoning</div>
                        <p>
                            Finally, use the differentiation and manifestations to determine
                            the treatment focus. The strategy follows the identified pattern
                            and the clinical picture rather than treating the swelling alone.
                        </p>
                    </div>

                </div>

                <p>
                    The source presents Goiter through differentiation and specific clinical
                    manifestations that guide the selection of supplementary points.
                </p>
            </div>

            <div class="foundations-section">
                <div class="section-label">🌳 DIFFERENTIATION</div>

                <div class="foundation-grid">

                    <div class="foundation-card">
                        <div class="foundation-card-title">🌬️ Qi Goiter</div>
                        <p>
                            Diffusive, soft swelling of the neck, gradually increasing in size,
                            with unclear margins and normal color.
                        </p>
                        <div class="foundation-clue">
                            <strong>Key clue:</strong> The size may change with emotions.
                        </div>
                    </div>

                    <div class="foundation-card">
                        <div class="foundation-card-title">🥩 Flesh Goiter</div>
                        <p>
                            Movable, smooth, painless lumps below the Adam’s apple.
                        </p>
                        <div class="foundation-clue">
                            <strong>Associated manifestations:</strong>
                            Exophthalmos • hot temper • irritability • tremor • sweating •
                            chest stuffiness • palpitation
                        </div>
                    </div>

                </div>
            </div>

            <div class="foundations-decision">
                <div class="section-label">🧭 CLINICAL DECISION TREE</div>

                <div class="decision-flow">

                    <div>GOITER PRESENTATION</div>
                    <span>↓</span>

                    <div>IDENTIFY THE DIFFERENTIATION</div>

                    <div class="decision-branches" style="margin-top:16px;">

                        <div class="decision-branch">
                            <div class="decision-branch-title">🌬️ Qi Goiter</div>
                            <div class="decision-branch-text">
                                <strong>Treatment Strategy:</strong>
                                Regulate Qi, address Qi stagnation and Phlegm accumulation,
                                and use the <strong>Basic Goiter Prescription</strong>.
                            </div>
                        </div>

                        <div class="decision-branch">
                            <div class="decision-branch-title">🥩 Flesh Goiter</div>
                            <div class="decision-branch-text">
                                <strong>Treatment Strategy:</strong>
                                Begin with the Goiter treatment approach and select the
                                therapeutic modification according to the dominant manifestation.
                            </div>
                        </div>

                    </div>

                    <span>↓</span>

                    <div>SELECT THE TREATMENT STRATEGY</div>

                </div>
            </div>


        </div>
    </details>


    <details class="board-module biomedicine-module">
        <summary>
            <span>🧬</span>
            <strong>Biomedicine</strong>
            <em>Open when needed</em>
        </summary>

        <div class="module-content">

            <div class="module-intro">
                <strong>GOITER — Biomedicine</strong>
                <p>Thyroid Anatomy → Physiology → Disease → Signs → Laboratory Findings</p>
            </div>

            <div class="biomedicine-self-check">
                <strong>⚡ 10-SECOND SELF-CHECK</strong>
                <p>Can you answer these before reading the card?</p>
                <ul>
                    <li>What does the word <strong>goiter</strong> actually tell you?</li>
                    <li>Does a goiter automatically mean hyperthyroidism?</li>
                    <li>What happens to TSH and Free T4 in primary hyperthyroidism?</li>
                    <li>Which combination points toward Graves Disease?</li>
                    <li>What is the difference between palpitation and tachycardia?</li>
                </ul>
            </div>

            <div class="biomedicine-section">
                <h3>🦋 THYROID ANATOMY</h3>
                <div class="biomedicine-card">
                    <strong>Location</strong>
                    <p>The thyroid gland is located in the <strong>anterior neck adjacent to the trachea</strong>.</p>
                    <p>It consists primarily of:</p>
                    <ul>
                        <li><strong>Right lobe</strong></li>
                        <li><strong>Left lobe</strong></li>
                        <li><strong>Isthmus</strong></li>
                    </ul>
                </div>
            </div>

            <div class="biomedicine-section">
                <h3>🔬 NORMAL THYROID PHYSIOLOGY</h3>
                <div class="biomedicine-flow">
                    <span>HYPOTHALAMUS</span><b>→</b>
                    <span>TRH</span><b>→</b>
                    <span>PITUITARY</span><b>→</b>
                    <span>TSH</span><b>→</b>
                    <span>THYROID</span><b>→</b>
                    <span>T4 / T3</span>
                </div>
                <div class="biomedicine-card">
                    <p>Thyroid hormones participate in regulation of:</p>
                    <ul>
                        <li>Metabolic rate</li>
                        <li>Heat production</li>
                        <li>Energy utilization</li>
                        <li>Cardiovascular activity</li>
                        <li>Neurologic function</li>
                    </ul>
                    <p><strong>Key concept:</strong> The system operates through <strong>negative feedback</strong>.</p>
                </div>
            </div>

            <div class="biomedicine-board-clue">
                <strong>🎯 BOARD CLUE — WHAT IS A GOITER?</strong>
                <div class="board-clue-big">GOITER = ENLARGEMENT OF THE THYROID GLAND</div>
                <p>A goiter <strong>does not by itself determine thyroid function</strong>.</p>
            </div>

            <div class="biomedicine-section">
                <h3>⚖️ THREE FUNCTIONAL STATES</h3>
                <div class="biomedicine-grid three">
                    <div class="biomedicine-state">
                        <strong>EUTHYROID</strong>
                        <p>Normal thyroid function</p>
                    </div>
                    <div class="biomedicine-state">
                        <strong>HYPERTHYROID</strong>
                        <p>Excess thyroid hormone effect</p>
                    </div>
                    <div class="biomedicine-state">
                        <strong>HYPOTHYROID</strong>
                        <p>Insufficient thyroid hormone effect</p>
                    </div>
                </div>
            </div>

            <div class="biomedicine-section">
                <h3>🔥 HYPERTHYROIDISM — CLINICAL CLUES</h3>
                <div class="biomedicine-grid">
                    <div class="biomedicine-feature">🌡️ Heat intolerance</div>
                    <div class="biomedicine-feature">💦 Excessive sweating</div>
                    <div class="biomedicine-feature">〰️ Tremor</div>
                    <div class="biomedicine-feature">😠 Irritability</div>
                    <div class="biomedicine-feature">❤️ Increased awareness of heartbeat</div>
                    <div class="biomedicine-feature">⚖️ Weight loss</div>
                </div>
            </div>

            <div class="biomedicine-section">
                <h3>❤️ PALPITATION ≠ TACHYCARDIA</h3>
                <div class="biomedicine-grid two">
                    <div class="biomedicine-card">
                        <strong>PALPITATION</strong>
                        <p>Subjective awareness of the heartbeat.</p>
                    </div>
                    <div class="biomedicine-card">
                        <strong>TACHYCARDIA</strong>
                        <p>Objectively rapid heart rate.</p>
                        <p>In an adult at rest, generally <strong>&gt;100 beats/minute</strong>.</p>
                    </div>
                </div>
                <div class="biomedicine-warning">
                    <strong>HIGH-YIELD:</strong> Palpitation does <strong>NOT</strong> automatically mean tachycardia.
                </div>
            </div>

            <div class="biomedicine-board-clue graves">
                <strong>👁️ GRAVES DISEASE</strong>
                <div class="board-clue-big">Diffuse Goiter + Hyperthyroidism + Exophthalmos</div>
                <p>→ <strong>Graves Disease</strong></p>
            </div>

            <div class="biomedicine-section">
                <h3>❄️ HYPOTHYROIDISM — CLINICAL CLUES</h3>
                <div class="biomedicine-grid">
                    <div class="biomedicine-feature">😴 Fatigue</div>
                    <div class="biomedicine-feature">❄️ Cold intolerance</div>
                    <div class="biomedicine-feature">⚖️ Weight gain</div>
                    <div class="biomedicine-feature">🧴 Dry skin</div>
                    <div class="biomedicine-feature">Digestive slowing / constipation</div>
                    <div class="biomedicine-feature">🐢 Slowed activity</div>
                </div>
            </div>

            <div class="biomedicine-section">
                <h3>🧪 LABORATORY REASONING</h3>

                <div class="biomedicine-lab">
                    <div>
                        <strong>TSH</strong>
                        <p>Pituitary hormone that stimulates thyroid hormone production.</p>
                    </div>
                    <div>
                        <strong>Free T4</strong>
                        <p>Circulating unbound thyroxine available to tissues.</p>
                    </div>
                    <div>
                        <strong>T3</strong>
                        <p>Metabolically active thyroid hormone; produced directly and through peripheral conversion from T4.</p>
                    </div>
                </div>

                <div class="biomedicine-grid two lab-patterns">
                    <div class="biomedicine-pattern">
                        <strong>PRIMARY HYPERTHYROIDISM</strong>
                        <div class="lab-arrow">TSH ↓ + Free T4 ↑</div>
                    </div>
                    <div class="biomedicine-pattern">
                        <strong>PRIMARY HYPOTHYROIDISM</strong>
                        <div class="lab-arrow">TSH ↑ + Free T4 ↓</div>
                    </div>
                </div>
            </div>

            <div class="biomedicine-decision">
                <strong>🧭 BIO EXAM REASONING</strong>
                <div class="decision-flow">
                    <span>ANATOMY</span><b>→</b>
                    <span>PHYSIOLOGY</span><b>→</b>
                    <span>PATHOLOGY</span><b>→</b>
                    <span>SIGNS / SYMPTOMS</span><b>→</b>
                    <span>LABORATORY FINDINGS</span><b>→</b>
                    <span>CLINICAL CONDITION</span>
                </div>
            </div>

            <div class="biomedicine-memory">
                <strong>🧠 HIGH-YIELD MEMORY</strong>
                <p><strong>GOITER</strong> → Thyroid is enlarged.</p>
                <p><strong>GOITER ≠ AUTOMATICALLY HYPERTHYROID</strong></p>
                <p><strong>EUTHYROID</strong> → Normal function</p>
                <p><strong>HYPERTHYROID</strong> → Excess thyroid hormone effect</p>
                <p><strong>HYPOTHYROID</strong> → Insufficient thyroid hormone effect</p>
                <p><strong>PRIMARY HYPERTHYROIDISM</strong> → TSH ↓ + Free T4 ↑</p>
                <p><strong>PRIMARY HYPOTHYROIDISM</strong> → TSH ↑ + Free T4 ↓</p>
                <p><strong>GRAVES</strong> → Diffuse Goiter + Hyperthyroidism + Exophthalmos</p>
                <p><strong>PALPITATION</strong> → Subjective awareness of heartbeat</p>
                <p><strong>TACHYCARDIA</strong> → Objective rapid heart rate</p>
            </div>

        </div>
    </details>


    <details class="board-module">
        <summary>
            <span>🌿</span>
            <strong>Chinese Herbology</strong>
            <em>Open when needed</em>
        </summary>

        <div class="herbology-module">
            <?php include __DIR__ . '/goiter_herbology_card_backup.php'; ?>
        </div>
    </details>





<?php endif; ?>

<?php if ($scormid === 562): ?>

<!-- CIRCULATION OF THE 12 PRIMARY CHANNELS -->
<div style="margin:25px 0;padding:22px;background:#ffffff;border:1px solid #e1e6ef;border-radius:14px;">

    <h3 style="margin:0 0 8px;">🌀 Circulation of the 12 Primary Channels</h3>

    <p style="margin:0 0 20px;color:#667085;">
        The 12 primary channels are connected to form a complete circuit around the body.
    </p>

    <!-- 10-SECOND SELF-CHECK -->
    <div style="padding:18px;margin-bottom:20px;background:#f7f9fc;border-radius:12px;">
        <h4 style="margin:0 0 8px;">🧠 10-Second Self-Check</h4>
        <p style="margin:0;">
            Complete the circuit:
            <strong>Chest → Hands → Head → Feet → ______</strong>
        </p>
    </div>

    <!-- FOUR GROUPS -->
    <h4 style="margin:0 0 14px;">🔄 The 4 Groups</h4>

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;">

        <div style="padding:18px;border:1px solid #e1e6ef;border-radius:12px;">
            <div style="font-size:20px;">①</div>
            <strong>3 Yin Channels of the Hands</strong>
            <p style="margin:8px 0 6px;">
                Lung • Pericardium • Heart
            </p>
            <p style="margin:0;color:#667085;">
                Chest → inner aspect of the arms → Hands
            </p>
        </div>

        <div style="padding:18px;border:1px solid #e1e6ef;border-radius:12px;">
            <div style="font-size:20px;">②</div>
            <strong>3 Yang Channels of the Hands</strong>
            <p style="margin:8px 0 6px;">
                Large Intestine • Sanjiao • Small Intestine
            </p>
            <p style="margin:0;color:#667085;">
                Hands → outer aspect of the arms → Head
            </p>
        </div>

        <div style="padding:18px;border:1px solid #e1e6ef;border-radius:12px;">
            <div style="font-size:20px;">③</div>
            <strong>3 Yang Channels of the Foot</strong>
            <p style="margin:8px 0 6px;">
                Stomach • Gall Bladder • Bladder
            </p>
            <p style="margin:0;color:#667085;">
                Head → outer aspect of the legs → Feet
            </p>
        </div>

        <div style="padding:18px;border:1px solid #e1e6ef;border-radius:12px;">
            <div style="font-size:20px;">④</div>
            <strong>3 Yin Channels of the Foot</strong>
            <p style="margin:8px 0 6px;">
                Spleen • Liver • Kidneys
            </p>
            <p style="margin:0;color:#667085;">
                Feet → inner aspect of the legs → Chest
            </p>
        </div>

    </div>

    <!-- MEMORY CIRCUIT -->
    <div style="margin-top:20px;padding:18px;background:#f7f9fc;border-radius:12px;">
        <h4 style="margin:0 0 16px;">🧭 Memory Circuit</h4>

        <div style="margin-bottom:14px;">
            <strong>1️⃣ CHEST → HANDS</strong><br>
            <strong>3 Yin Channels of the Hands</strong><br>
            <span style="color:#667085;">Lung • Pericardium • Heart</span>
        </div>

        <div style="margin-bottom:14px;">
            <strong>2️⃣ HANDS → HEAD</strong><br>
            <strong>3 Yang Channels of the Hands</strong><br>
            <span style="color:#667085;">Large Intestine • Sanjiao • Small Intestine</span>
        </div>

        <div style="margin-bottom:14px;">
            <strong>3️⃣ HEAD → FEET</strong><br>
            <strong>3 Yang Channels of the Foot</strong><br>
            <span style="color:#667085;">Stomach • Gall Bladder • Bladder</span>
        </div>

        <div>
            <strong>4️⃣ FEET → CHEST</strong><br>
            <strong>3 Yin Channels of the Foot</strong><br>
            <span style="color:#667085;">Spleen • Liver • Kidneys</span>
        </div>

        <div style="margin-top:20px;padding:14px;background:#ffffff;border-radius:10px;text-align:center;">
            <strong>🔄 Complete Circuit</strong><br>
            <span style="font-size:16px;font-weight:bold;">
                CHEST → HANDS → HEAD → FEET → CHEST
            </span>
        </div>
    </div>

    <!-- BOARD RECOGNITION -->
    <div style="margin-top:20px;padding:18px;border:1px solid #e1e6ef;border-radius:12px;">
        <h4 style="margin:0 0 12px;">🎯 Board Recognition</h4>

        <p style="margin:6px 0;">
            <strong>Chest → Hands</strong> = 3 Yin Channels of the Hands
        </p>

        <p style="margin:6px 0;">
            <strong>Hands → Head</strong> = 3 Yang Channels of the Hands
        </p>

        <p style="margin:6px 0;">
            <strong>Head → Feet</strong> = 3 Yang Channels of the Foot
        </p>

        <p style="margin:6px 0;">
            <strong>Feet → Chest</strong> = 3 Yin Channels of the Foot
        </p>
    </div>

</div>

<?php endif; ?>

<div style="margin:25px 0;padding:20px;background:#f7f9fc;border:1px solid #e1e6ef;border-radius:12px;">
    <h3 style="margin-top:0;">AI Study Assistant</h3>
    <p style="color:#666;">Ask a question about your study material.</p>

    <textarea id="ai_prompt" style="width:100%;min-height:100px;padding:12px;border:1px solid #ddd;border-radius:10px;" placeholder="Ask the AI a question..."></textarea>

    <div style="margin-top:12px;">
        <button type="button" id="ai_ask_button" style="padding:10px 18px;border:0;border-radius:8px;background:#f47e2c;color:white;font-weight:bold;">
            Ask AI
        </button>
    </div>

    <div id="ai_status" style="margin-top:12px;color:#666;"></div>

    <div id="ai_response" style="display:none;margin-top:15px;padding:15px;background:white;border:1px solid #ddd;border-radius:10px;white-space:pre-wrap;"></div>
</div>

<script>
document.getElementById('ai_ask_button').addEventListener('click', async function() {
    const prompt = document.getElementById('ai_prompt').value.trim();
    const status = document.getElementById('ai_status');
    const responseBox = document.getElementById('ai_response');

    if (!prompt) {
        status.textContent = 'Please enter a question.';
        return;
    }

    this.disabled = true;
    status.textContent = 'AI is thinking...';
    responseBox.style.display = 'none';

    try {
        const params = new URLSearchParams({
            id: <?php echo (int)$scormid; ?>,
            prompt: prompt,
            sesskey: M.cfg.sesskey
        });

        const response = await fetch('<?php echo (new moodle_url('/mod/scorm/studyguide/ai.php'))->out(false); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params.toString()
        });
        const data = await response.json();

        if (data.success) {
            responseBox.textContent = data.response;
            responseBox.style.display = 'block';
            status.textContent = '';
        } else {
            status.textContent = data.error || 'AI request failed.';
        }
    } catch (error) {
        status.textContent = 'Unable to connect to the AI service.';
    } finally {
        this.disabled = false;
    }
});
</script>



<div style="margin:25px 0 12px;">
    <h3 style="margin-bottom:6px;">📝 My Notes</h3>
    <p style="color:#667085;margin-top:0;">
        Add your personal notes and reminders.
    </p>
</div>

<form id="student_notes_form" method="post" action="" enctype="multipart/form-data">
<?php if (!empty($existing) && !empty($existing->imagefile)) { ?><div style="margin:15px 0;text-align:center;"><img src="/mod/scorm/studyguide/uploads/<?php echo htmlspecialchars($existing->imagefile); ?>" style="display:block;max-width:600px;width:100%;height:auto;margin:0 auto;border-radius:10px;"></div><?php } ?>
<input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
<textarea name="note" style="width:100%;min-height:180px;padding:15px;border:1px solid #ddd;border-radius:10px;" placeholder="Write your personal notes here..."><?php echo htmlspecialchars($savednote ?? ""); ?></textarea><div style="margin-top:12px;">
<label style="font-weight:bold;">Add Image</label><br>
<input type="file" name="note_image" accept="image/*">
</div>

<div style="margin-top:10px;">
<button type="submit"
        name="upload_note_image"
        value="1"
        style="padding:10px 18px;border:0;border-radius:8px;background:#4f6bed;color:white;font-weight:bold;">
    Upload Image
</button>
</div>
<?php if (!empty($existing) && !empty($existing->imagefile)) { ?><div style="margin-top:10px;"><button type="submit" name="remove_image" value="1" onclick="return confirm('Remove the current image?' );" style="padding:10px 18px;border:0;border-radius:8px;background:#dc3545;color:white;font-weight:bold;">Remove Image</button></div><?php } ?>
<div style="margin-top:15px;display:flex;gap:10px;flex-wrap:wrap;">
<button type="submit" name="save_note" value="1" style="padding:10px 18px;border:0;border-radius:8px;background:#f47e2c;color:white;font-weight:bold;">Save Note</button>
<?php if (!empty($existing) && !empty($existing->note)) { ?>
<button type="submit" name="clear_note" value="1" onclick="return confirm('Clear your note?');" style="padding:10px 18px;border:0;border-radius:8px;background:#dc3545;color:white;font-weight:bold;">Clear Note</button>
<?php } ?>
</div>
</form>
    </div>

    

    <div style="text-align:center;">
        <a href="/mod/scorm/player.php?scoid=<?php echo (int)$scorm->launch; ?>&cm=<?php echo $cmid; ?>&newattempt=on&display=popup"
           style="display:inline-block;padding:13px 28px;border-radius:9px;background:#f47e2c;color:white;text-decoration:none;font-weight:bold;">
            Start Exam
        </a>
    </div>

</div>

<script>
(function() {
    const scrollKey = 'studyguide_student_notes_scroll';

    const form = document.getElementById('student_notes_form');

    if (form) {
        form.addEventListener('submit', function() {
            sessionStorage.setItem(scrollKey, String(window.scrollY));
        });
    }

    window.addEventListener('load', function() {
        const saved = sessionStorage.getItem(scrollKey);

        if (saved !== null) {
            window.scrollTo(0, parseInt(saved, 10));
            sessionStorage.removeItem(scrollKey);
        }
    });
})();
</script>

<?php

echo $OUTPUT->footer();

