<?php
require_once(__DIR__ . "/../../config.php");
require_login();

$PAGE->set_url(new moodle_url("/blocks/myprogress/herbology.php"));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Chinese Herbology");
$PAGE->set_heading("Chinese Herbology");

echo $OUTPUT->header();
?>
<style>
.herbology-wrap{max-width:1000px;margin:30px auto}
.herbology-intro{margin-bottom:28px}
.herbology-intro h2{font-size:30px;margin:0 0 8px}
.herbology-intro p{color:#64748b;font-size:16px;margin:0}
.herbology-grid{display:grid;grid-template-columns:repeat(2,minmax(280px,1fr));gap:20px}
.herbology-card{background:#fff;border:1px solid #e2e6ea;border-radius:14px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
.herbology-card h3{margin:0 0 10px;font-size:21px}
.herbology-card p{color:#64748b;margin:0 0 18px}
.herbology-button{display:inline-block;padding:11px 16px;background:#f1f5f9;color:#1e293b;text-decoration:none;border-radius:9px;font-weight:600}
.herbology-button:hover{text-decoration:none;background:#e2e8f0}
.herbology-shared{margin-top:28px}
.herbology-shared h3{font-size:22px;margin-bottom:16px}
@media(max-width:700px){.herbology-grid{grid-template-columns:1fr}}
</style>
<div class="herbology-wrap">
  <div class="herbology-intro">
    <h2>Chinese Herbology</h2>
    <p>Study Single Herbs and Formulas, then review your progress with the shared resources.</p>
  </div>
  <div class="herbology-grid">
    <div class="herbology-card">
      <h3>Single Herbs</h3>
      <p>Study and practice individual Chinese herbs.</p>
      <a class="herbology-button" href="<?php echo new moodle_url("/course/view.php", ["id"=>45]); ?>">Open Single Herbs →</a>
    </div>
    <div class="herbology-card">
      <h3>Formulas</h3>
      <p>Study and practice Chinese herbal formulas.</p>
      <a class="herbology-button" href="<?php echo new moodle_url("/course/view.php", ["id"=>117]); ?>">Open Formulas →</a>
    </div>
  </div>
  <div class="herbology-shared">
    <h3>Shared Resources</h3>
    <div class="herbology-grid">
      <div class="herbology-card">
        <h3>Simulation</h3>
        <p>Test your Chinese Herbology knowledge with a combined simulation.</p>
        <a class="herbology-button" href="<?php echo new moodle_url("/course/view.php", ["id"=>41]); ?>">Start Simulation →</a>
      </div>
      <div class="herbology-card">
        <h3>Notes</h3>
        <p>Review Chinese Herbology notes and study material.</p>
        <a class="herbology-button" href="<?php echo new moodle_url("/course/view.php", ["id"=>92]); ?>">Review Notes →</a>
      </div>
    </div>
  </div>
</div>
<?php
echo $OUTPUT->footer();
?>