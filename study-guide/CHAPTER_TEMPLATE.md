# Reusable chapter template

The study guide includes an instructor-only chapter template. Open the **Instructor** preview and expand **Instructor Chapter Template — Create the next chapter**.

## Workflow

1. Replace the editable title, description, reading, learning plans and exam links.
2. Enter one objective per line using `Objective | Subject matter | Teaching method`.
3. Add the English and Spanish chapter titles and descriptions.
4. Select **Preview template** and resolve the validation messages.
5. Select **Save draft** to keep a browser-only working copy.
6. Select **Export JSON** to download the completed chapter specification.

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

- `STUDY_GUIDE_ASSESSMENT_CONFIG`: stable course key, public title, certificate title and planning hours.
- `STUDY_GUIDE_ASSESSMENT_BANKS`: one 10-question bank for every `objective-N` section plus one `continuing-education` bank.
- Question names in the format `COURSE-O01-Q01` through `COURSE-O12-Q10`; use `COURSE-CE-Q01` through `COURSE-CE-Q10` for the CE assessment.
- Each question as single-answer multiple choice with four options, a correct answer, general rationale and specific feedback for all three distractors.

The shared script automatically adds the following standard blocks:

1. A collapsible 10-question practice exam at the end of each objective.
2. Browser-saved Exam History with latest, best, average and in-progress results.
3. A post-reading objective exam directory.
4. A visible **Continuing Education begins here** divider.
5. The 10-question formative Continuing Education assessment.
6. A certificate preview that remains disabled until approval and Moodle completion are configured.
7. Copy and TXT export controls using the standard authoring format: question name, type, text, A–D options, correct answer, general feedback and distractor feedback.

Do not place forward-jump links inside objective reading. Navigation can remain in the chapter sidebar, but the instructional body should preserve the reading sequence. Existing Moodle question banks belong in one separate post-reading directory, and official results remain in Moodle Exam History.

## Required quality checks

- Use measurable objectives and verified source material.
- Supply descriptive alternative text for every instructional image.
- Add separate School, Board and Continuing Education exam links.
- Include feedback in the Continuing Education review before the scored SCORM activity.
- Confirm that a pending chapter does not display an approved credit value or appear in the professional CE catalog.
- Confirm that approved hours, subject area, delivery method and approval number match the Board/CE Broker record.
- Review the English and Spanish views on desktop and a phone-width screen.
- Confirm that the page has no horizontal scrolling and all touch targets are at least 44 pixels high.
