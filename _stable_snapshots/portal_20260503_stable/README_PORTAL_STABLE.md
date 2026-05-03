# Portal Stable Snapshot — 2026-05-03

Status: Working / stable.

Confirmed:
- Portal dashboard opens.
- Profile dropdown opens by click.
- Dropdown shows Dashboard / Notifications / Logout.
- Files removed from dropdown.
- Profile photo replaces initials when Personal Photo exists.
- Email fallback exists through sfPortalAccountEmailSource.
- Notifications open by click and appear above nav pills.
- Salary slips / files / travel tickets routes exist.
- Dashboard shows job title.

Important:
Do not refactor resources/views/portal/layouts/app.blade.php immediately unless doing a dedicated cleanup batch.
The file contains many layered patches, but the UI is currently working.
