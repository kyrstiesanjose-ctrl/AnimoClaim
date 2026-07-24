# AnimoClaim

AnimoClaim is a PHP web application for managing student event claims at DLSU — students reserve time slots for event giveaways/kits and pick them up using a QR-coded ticket, while organizers manage events, approve claims, and monitor crowd density in real time.

## Features

**Student**
- Browse active events and reserve a time slot
- View reserved claims as QR-coded tickets (`student/claim.php`)
- Track ticket history (`student/tickets.php`)
- View campus map / event locations (`student/map.php`)
- Manage profile (`student/profile.php`)

**Organizer**
- Create and manage events, track inventory and reservation capacity (`organizer/dashboard.php`)
- Approve claims by scanning/entering a ticket's QR hash (`organizer/terminal.php`)
- Live crowd monitoring dashboard fed by an overhead camera (`organizer/crowd.php`, `organizer/vision.php`)
- Audit logs of claim activity (`organizer/audits.php`)

**Crowd monitoring bridge**
- `yolo_traffic_bridge.py` is a standalone Python/Flask service using OpenCV + YOLOv8 (Ultralytics) to count people crossing a line via webcam, and posts headcount/density data to `api/update_traffic.php` for the organizer dashboard to poll.

## Tech Stack

- PHP + PDO (MySQL)
- Tailwind CSS, Material Symbols
- Vanilla JS (fetch-based API calls)
- Python (OpenCV, Ultralytics YOLO, Flask) for the optional crowd-counting bridge

## Project Structure

```
AnimoClaim/
├── index.php                  # Login page
├── forgot_password.php
├── config/
│   ├── database.php            # DB connection, session start, requireLogin(), CSRF token
│   ├── logout.php
│   └── reset_passwords.php
├── includes/
│   ├── header.php               # Shared layout/nav (sets $base_url)
│   └── footer.php
├── components/
│   ├── sidebar_student.php
│   └── bottom_nav_student.php
├── student/                    # Student-facing pages
│   ├── index.php, event.php, event_details.php
│   ├── claim.php, tickets.php, map.php, profile.php
├── organizer/                   # Organizer-facing pages
│   ├── dashboard.php, terminal.php, vision.php, crowd.php, audits.php
├── api/                        # JSON endpoints consumed by the front end
│   ├── get_claims.php, book_slot.php, approve_claim.php
│   ├── get_traffic.php, update_traffic.php
│   ├── get_map_logs.php, manage_strikes.php
├── tools/
│   └── rehash.php               # One-off utility to rehash stored passwords
└── yolo_traffic_bridge.py       # Python crowd-counting service (optional)
```

## Setup (XAMPP)

1. Clone this repo directly into `htdocs/claim` — the app expects to be served at `localhost/claim/...` (see `$base_url` in `includes/header.php`).
2. Start Apache and MySQL in XAMPP.
3. Create a database named `animo_claim` and import your schema (tables include `events`, `event_time_slots`, `inventory`, `reservations`, and `crowd_traffic_logs`).
4. Default DB credentials are set in `config/database.php` for local XAMPP (`root` / no password) — update if yours differ.
5. Visit `http://localhost/claim/` to log in.

### Optional: crowd monitoring bridge

```
pip install opencv-python flask flask-cors ultralytics requests
python yolo_traffic_bridge.py
```
This runs a local video stream and posts headcount data to `api/update_traffic.php`, which `organizer/crowd.php` and `organizer/vision.php` poll and display.

## Notes

- Passwords are hashed with `password_hash()` / verified with `password_verify()`. Use `tools/rehash.php` if you need to rehash existing plaintext or legacy hashes in the database.
- CSRF tokens are generated per session in `config/database.php` and expected on state-changing API calls (e.g. `book_slot.php`).
