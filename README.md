# SecureBank - Mini Banking Web App (XAMPP)

## Overview
This is a small PHP/MySQL web app intended for local development with XAMPP (Apache + MySQL).
It implements a basic banking system with Customer and Admin roles.

**Important:** This is an educational demo. Do NOT deploy to production.

## Setup (XAMPP)
1. Start Apache and MySQL from XAMPP Control Panel.
2. Open phpMyAdmin (http://localhost/phpmyadmin) and import `setup.sql`.
3. Place the `securebank` folder into XAMPP `htdocs` (e.g., `C:\xampp\htdocs\securebank`).
4. Visit `http://localhost/securebank/` in your browser.

## Default accounts (from setup.sql)
- Admin: username `admin`, password `admin123`
- Alice: username `alice`, password `alice123`
- Bob: username `bob`, password `bob123`

## Notes
- DB connection is in `db.php`. Default MySQL user is `root` with empty password (XAMPP default).
- Passwords in the seed are plaintext for easy testing. In a real app, always store hashed passwords.