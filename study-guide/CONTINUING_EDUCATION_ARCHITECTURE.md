# Continuing Education course architecture

## One course, two entry routes

The same reviewed chapter content supports two distinct learner contexts:

1. **Board preparation route** — available from the Board-preparation dashboard. It does not award, claim or report Continuing Education credit.
2. **Continuing Education route** — available from the separate professional catalog only when the course is formally approved and active.

The routes may reuse reading, images and practice activities, but CE assessment, completion, reporting and certificate records remain separate from Board-preparation progress.

## Two synchronized catalogs

The Board-preparation catalog lists every configured chapter available through the monthly membership. The professional Continuing Education catalog reads the same course registry but lists only courses that pass the approval gate below. Shared titles, descriptions and chapter URLs are maintained once in `continuing-education-courses.js`; the two screens filter that common record for their respective audiences.

The optional `?preview=approved` catalog mode is a visual demonstration only. It must remain clearly labelled and never changes a course approval record.

## Membership and course-specific payment

The monthly AcupunctureTests membership provides the Board-preparation route. Continuing Education examination, official completion record and certificate require a separate enrollment for the specific approved course.

The learner may review the shared chapter before enrollment. The CE Start Exam control must verify the Moodle enrollment on the server and direct an unpaid learner to the course-specific payment process. Hiding a link in browser JavaScript is not an access control. Payment for one CE course must not unlock another CE course.

The standard price is **$10 USD for each approved CE credit hour**. One approved instructional hour equals one credit for pricing purposes. The public price is calculated as:

`approved CE credit hours × $10`

Planning estimates do not create a public price. The calculation becomes authoritative only after the Board-approved credit hours are recorded. For example, 2.25 approved credit hours cost $22.50 and 6.5 approved credit hours cost $65.00.

## Approval gate

A Form B-ready chapter is not automatically a Board-approved CE course. A course is listed publicly in the professional CE catalog only when all of these values are present:

- `ceApprovalStatus` is `approved`;
- `cePubliclyActive` is `true`;
- `approvedCeCreditHours` is greater than zero; and
- `ceApprovalNumber` is recorded.

Before that point, the learner-facing message must say that approval or credit is pending. Planning estimates must never be presented as approved credit.

## Time and credit fields

The reusable template defaults to 15 planning minutes per objective. Internal estimated hours are calculated as:

`objective count × minutes per objective ÷ 60`

This produces preliminary planning estimates of 2.25 hours for Channels and Collaterals (9 objectives) and 6.5 hours for Point Categories (26 objectives). The public course and certificate use only `approvedCeCreditHours` after approval.

## Classification

Store both levels:

- **Requirement group** — an internal filter: General, Biomedical Sciences or Mandatory.
- **CE Broker subject area** — the exact reporting category, such as General, Biomedical Sciences, Medical Errors or Laws and Rules of the Board.

“Mandatory” is not used as a substitute for the exact CE Broker subject area. The current Channels and Collaterals and Point Categories drafts are classified as General / General.

## Delivery method

The current online, asynchronous, self-paced chapters use the CE Broker delivery method `Computer-Based Training`. The learner-facing delivery description may add “Online, asynchronous and self-paced,” but it does not replace the official method field.

## Certificate gate

`ceCertificateEnabled` remains `false` until the course is approved and publicly active. A future certificate must use the approved course title, approved credit hours, completion date, provider information and approval data from the course record.
