# Reusable chapter template

The study guide includes an instructor-only chapter template. Open the **Instructor** preview and expand **Instructor Chapter Template — Create the next chapter**.

## Workflow

1. Replace the editable title, description, reading, learning plans and exam links.
2. Enter each School Level activity using `Exact Moodle activity name | Moodle URL`.
3. Enter one objective per line using `Objective | Subject matter | Teaching method | Mock 10 title | Mock URL | optional legacy title | optional legacy URL`.
4. Add the English and Spanish chapter titles and descriptions.
5. Select **Preview template** and resolve the validation messages. The preview reports how many School, Board and CE activities are connected.
6. Select **Copy assessment config** to copy the ready-to-use `STUDY_GUIDE_ASSESSMENT_CONFIG` object for the chapter's `mock-data.js`.
7. Select **Save draft** to keep a browser-only working copy or **Export JSON** to download the completed chapter specification.

Every chapter hero must also include the visible **Español** language button in the lower-right position used by the established chapter design. On phone-width screens it becomes a full-width control. It must switch the current chapter in place and offer **English** to return; it must never send the learner back to the catalog. Keep official Moodle activity names unchanged in both language views.

## Continuing Education approval record

Each chapter can be prepared with the Form B fields without being advertised as an approved Continuing Education course. Keep **CE approval status** as `preparation` or `submitted` and **Public CE catalog status** as `false` until formal Board approval has been recorded.

- Use **CE requirement group** for the broad internal grouping: General, Biomedical Sciences or Mandatory.
- Use **CE Broker subject area** for the exact reporting category. Mandatory topics must retain their specific subject area, such as Medical Errors or Laws and Rules of the Board.
- Use the exact **CE Broker delivery method**. The current online, asynchronous chapters use `Computer-Based Training`.
- The planning estimate uses 15 minutes per objective by default. It is not an approved credit value.
- Enter **Board-approved CE credit hours** and the approval number only after approval.
- Record the approved provider name and provider number. The certificate location for these courses is `Online`.
- Set the public catalog status and certificate to `true` only after approval is active.

The exported JSON follows [chapter-template.example.json](chapter-template.example.json). It does not publish a chapter or modify Moodle. A developer can use the reviewed specification to create the new chapter while retaining the established responsive layout and block order.

## Shared assessment and certificate template

Use the shared assessment layer for new or standardized chapters:

```html
<link rel="stylesheet" href="../shared/course-assessment.css">
<!-- Chapter content and chapter-specific scripts -->
<script src="mock-data.js"></script>
<script src="../shared/course-assessment.js"></script>
```

The chapter-specific `mock-data.js` is the only file that should need substantial content work. It defines:

- `STUDY_GUIDE_ASSESSMENT_CONFIG`: stable course key, public title, certificate title, planning hours, Moodle Exam History URL and the School/Board/CE activity mappings. The instructor template generates this object automatically.
- `schoolExams`: every original School Level activity as an exact Moodle name and URL.
- `moodleMockLinks`: the Moodle SCORM URL for every `objective-N`; use `ceExamLink` for the Continuing Education Mock 10.
- `STUDY_GUIDE_ASSESSMENT_BANKS`: one 10-question bank for every `objective-N` section plus one `continuing-education` bank.
- The visible objective-exam button must use the bank's complete title in the format **Exam Name (Mock 10)**, never the generic label “Open 10-question mock exam.” The shared template renders this button in orange.
- Question names in the format `COURSE-O01-Q01` through `COURSE-O12-Q10`; use `COURSE-CE-Q01` through `COURSE-CE-Q10` for the CE assessment.
- Each question as single-answer multiple choice with four options, a correct answer, general rationale and specific feedback for all three distractors.

Add one empty exam-directory mount point after the chapter review, or let the shared layer place it after `#quick-check` automatically:

```html
<section data-assessment-exam-directory></section>
```

The shared script automatically adds the following standard blocks from the configuration:

1. A collapsible 10-question practice exam at the end of each objective.
2. The complete School Level directory and Moodle Exam History action.
3. The complete Board Level Mock 10 directory with browser-saved attempt summaries.
4. A visible **Continuing Education begins here** divider.
5. The 10-question formative Continuing Education review with Mock-style feedback.
6. A certificate preview that remains disabled until approval and Moodle completion are configured.
7. The final Continuing Education SCORM exam table.
8. Copy and TXT export controls using the standard authoring format: question name, type, text, A–D options, correct answer, general feedback and distractor feedback.

Do not hand-code duplicate School, Board or CE exam rows. Update the chapter configuration and let `shared/course-assessment.js` generate the six-column tables, orange actions, stable anchors and CE order.

## Exam buttons and Exam History

Apply these rules consistently in every chapter:

1. The first exam action inside each objective is the new SCORM activity from `moodleMockLinks`.
2. Label that button with the complete bank title in the format **Exam Name (Mock 10)**. Do not use generic labels such as **Open 10-question mock exam**.
3. Render the Mock 10 button in orange. Keep its accessible focus state and a minimum 44-pixel touch target.
4. If an objective also links to an older Moodle question bank, place it after the Mock 10 button and use the activity's exact Moodle name, including its question-count suffix when present. For example: **Points Du Mai Channel Caution or Contraindication MC 15**. Do not replace the activity name with **Open 15-question Moodle bank** or another generic label.
5. Use the secondary purple style for an older Moodle bank when it appears next to the orange Mock 10 button.
6. Do not place an **Exam History** button inside an individual objective. Exam History belongs only in the chapter-level examination area.
7. Provide two separate, adjacent, collapsible chapter-level blocks: **School Level Exam — Course Title** followed by **Board Level Exam — Course Title**.
8. The School Level table lists every older scored Moodle examination with the columns **Exam**, **Attempts**, **Last Attempt**, **Average**, **Grade** and **Action**. Each row has an orange **Start Exam** button, and the block ends with an orange **View Exam History** button connected to the correct Moodle course and section.
9. The Board Level table lists every objective in course order with the same result columns and an orange **Start Mock 10** action connected to that objective's `moodleMockLinks` URL.
10. A learner must be able to start every School Level exam and every objective Mock 10 from these two upper examination blocks without opening or searching inside the objectives. Objective-level buttons remain as contextual secondary access, not the only access.
11. The top **School Level Exam** button links to the stable School Level block anchor, such as `#school-level-exams`. The top **Board Level Exam** button links to the stable Board Level block anchor, such as `#board-level-exams`. Neither top button opens a single SCORM activity directly.
12. Keep browser-saved practice results separate from official Moodle records. State clearly that Moodle attempts, dates and scores are not transmitted to the static guide.
13. Use `ceExamLink` for the Continuing Education SCORM activity and display its complete **Course Name Continuing Education (Mock 10)** title in the separate Continuing Education exam block.
14. When shared JavaScript or CSS changes, update its cache-version query string in the chapter HTML so browsers receive the new table structure, button text and styling.
15. Before the Continuing Education exam access, include **10 Continuing Education Review Questions**. Render them with the same interaction and feedback format as every objective Mock 10: four single-answer choices, **Check answer**, correct-answer identification, general rationale and an explanation for every incorrect option. Label this local practice clearly as review and keep it separate from the official Moodle attempt.
16. End the Continuing Education sequence with a collapsible **Continuing Education Exam — Course Title** block. Its six-column table uses **Exam**, **Attempts**, **Last Attempt**, **Average**, **Grade** and **Action**, contains the complete **Course Name Continuing Education (Mock 10)** activity name, and ends with an orange **Start Exam** button that opens the SCORM activity in a new tab.
17. Never show an exam-like label, card, button or note without a working destination. Remove plain-text placeholders such as **Related Moodle bank: 12 questions** when no link is attached. The shared assessment layer automatically removes legacy `source-note` placeholders beginning with **Related Moodle bank:**; real linked actions remain available in the upper directories and objective action rows.

The Continuing Education blocks must keep the same chapter-wide order:

1. **Continuing Education Learning Plan — Course Title** with the objective table first.
2. **How the Course Chapter Aligns with Continuing Education** with extended reading or case application.
3. **Continuing Education Review Questions (10)** with immediate Mock-style feedback.
4. Certificate and completion status, gated by the approval record and Moodle completion rules.
5. **Continuing Education Exam — Course Title** as the final CE block, with the required SCORM assessment table.

Use separate collapsible cards for the Learning Plan, alignment/application, review questions and final CE exam blocks. The certificate preview may remain a standalone status panel. Do not insert cases, an exam link or a certificate before the Learning Plan table.

When a chapter includes a professional-practice case lab, place it inside block 2 and use the same four-part sequence for every scenario:

1. Case: patient context, relevant content or region, timing and warning cues.
2. Anatomical or clinical error: missed screen, vulnerable structure or broken safety rule.
3. Medical or professional impact: urgency, factual documentation and limits of causal claims.
4. Required prevention or response: current protocol, escalation and documentation within scope.

Have learners complete their own analysis before revealing a model response. Mark reconstructed examples as fictional composites unless a complete, verifiable case source is cited.

Do not place forward-jump links inside objective reading. Navigation can remain in the chapter sidebar, but the instructional body should preserve the reading sequence. Keep one complete post-reading directory for older Moodle question banks; when an older bank is also shown inside its matching objective, place it only in the final objective action row after the orange Mock 10 button. Official results remain in Moodle Exam History.

## Required quality checks

- Use measurable objectives and verified source material.
- Supply descriptive alternative text for every instructional image.
- Add separate School, Board and Continuing Education exam links.
- Include feedback in the Continuing Education review before the scored SCORM activity.
- Confirm that a pending chapter does not display an approved credit value or appear in the professional CE catalog.
- Confirm that approved hours, subject area, delivery method and approval number match the Board/CE Broker record.
- Review the English and Spanish views on desktop and a phone-width screen.
- Confirm that the page has no horizontal scrolling and all touch targets are at least 44 pixels high.

## GitHub publication checklist

1. Create the working branch from the latest `origin/main`. Do not reuse a stale branch when it would reintroduce older shared files.
2. Validate JavaScript syntax, every SCORM mapping, the visible exam titles, the Exam History row count and the orange Mock 10 style.
3. Run `deploy/study-guide/build-release.sh` before publishing whenever the shared assessment layer changes.
4. Push the branch, open a pull request with `main` as the base and merge it before starting production deployment.
5. A commit pushed after its earlier pull request was merged requires another pull request. Pushing to the old branch does not place the new commit in `main` automatically.
6. Start a new **Deploy Study Guide to production** workflow with **Run workflow** on `main`. Do not use **Re-run jobs** for an older release when newer changes are required.
7. Confirm that the workflow SHA is the new merge commit. If the deploy log says **Release already exists**, the workflow is attempting to deploy an immutable commit that was already released; merge the new commit into `main` and start a new workflow instead of rerunning the old job.
8. After deployment succeeds, refresh the chapter without cache and confirm the exact button names, colors, links and Exam History placement in production.
