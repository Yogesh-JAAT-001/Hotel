# The Heartland Abode

AI-Enabled Smart Hotel Management System developed for academic evaluation and practical portfolio demonstration.

## Project Summary

The Heartland Abode is a full-stack hotel management platform integrating:
- Smart reservations and room management
- Dining and food operations
- Admin and manager dashboards
- Dynamic pricing logic
- Engineering economics and analytics
- Secure authentication and session handling

This repository is prepared for:
- GitHub submission
- GitHub Pages presentation
- University PBL evaluation

## Student Information

- Name: Yogesh Kumar
- Registration No: 2427030798
- Mentor: Dr. Tapan Kumar
- Email: yogesh.2427030798@muj.manipal.edu
- Department: Computer Science & Engineering
- Institute: Manipal University Jaipur

## Technology Stack

- PHP
- MySQL / MariaDB
- HTML5
- CSS3
- JavaScript (Vanilla)
- Chart.js
- MDB UI Kit

## Repository Entry Points

- Main static showcase: `index.html`
- Static demo walkthrough: `demo/index.html`
- Presentation assets: `assets/`
- Legacy backend source (for reference): `index.php`, `admin/`, `api/`, `user/`

## Core Modules

### User Side
- Room browsing and details
- Booking workflow
- Reservation history
- Dining catalog

### Admin Side
- Reservation and room operations
- Guest and staff management
- Food & dining management
- Analytics dashboards

### Intelligence and Economics
- Dynamic pricing support
- Revenue and occupancy insights
- Cost tracking and profitability indicators
- Decision-focused KPI visualizations

## Optional Local Backend Setup (XAMPP)

1. Place project in htdocs:
   - `/Applications/XAMPP/xamppfiles/htdocs/heartland_abode/`
2. Start Apache and MySQL
3. Create database: `heartland_abode`
4. Import SQL files in order:
   - `database/heartland_abode.sql`
   - `database/fix_admin_login.sql`
5. Create `.env` from `.env.example` and update DB credentials if needed

## Static Preview (Relative Paths)

- Showcase page: `index.html`
- Demo page: `demo/index.html`
- Demo video: `assets/video/demo-video.mp4`

## Admin Credentials

- Email: `yogeshkumar@heartlandabode.com`
- Username: `Yogesh Admin`
- Password: `Admin@123`

## GitHub Pages Compatibility

GitHub Pages cannot execute PHP/MySQL backend directly.

This repository includes a production-ready static presentation build:
- No PHP dependency in `index.html` and `demo/index.html`
- Relative asset paths for all static references
- Repository-hosted video via `assets/video/demo-video.mp4`
- Static-safe navigation and media fallback behavior

## Security and Repo Hygiene

- Environment-driven config via `.env`
- `.env` excluded from git via `.gitignore`
- Runtime and OS junk files removed
- Media assets consolidated under `assets/`
- Default fallback behavior for missing media retained

## License

MIT License (`LICENSE`)
