# The Office — Backend (PHP)

Backend API for The Office e-learning platform. Plain PHP, no framework — talks to a MariaDB database and serves JSON to the React frontend.

## Folder structure

Clone this repo directly into `htdocs`. It contains everything the backend needs, plus the `uploads` folder:

```
C:\xampp\htdocs
└── The_Office_PHP
    ├── backend
    │   ├── api
    │   ├── classes
    │   ├── config
    │   ├── composer.json
    │   └── ...
    └── uploads
        ├── pdfs
        ├── soumissions
        ├── thumbnails
        └── videos
```

The React frontend is cloned separately and lives outside `htdocs`.

## Setup, from scratch

### 1. XAMPP
Install from [apachefriends.org](https://www.apachefriends.org/). Start **Apache** and **MySQL** in the XAMPP Control Panel.

### 2. Composer
Check if it's already installed:
```
composer --version
```
If not: download **Composer-Setup.exe** from [getcomposer.org/download](https://getcomposer.org/download/), point it at `C:\xampp\php\php.exe` when asked, leave the proxy field blank.

**After installing, close and reopen your terminal** — a window already open won't see the update.

### 3. Enable the `zip` PHP extension
Composer needs it to install packages.
1. Open `C:\xampp\php\php.ini`
2. Find `;extension=zip`
3. Remove the leading `;`
4. Save, restart your terminal

### 4. Clone this repo into the right spot
```
cd C:\xampp\htdocs
git clone https://github.com/yehyaba51-lb/The_Office_PHP.git The_Office_PHP
```

### 5. Install PHP dependencies
`vendor/` isn't included in this repo (regenerated from `composer.json`/`composer.lock`, same reason `node_modules` isn't committed in JS projects). Run as **administrator** if you hit permission errors:
```
cd The_Office_PHP/backend
composer install
```

### 6. Create your `.env` file
Also not included (holds real credentials). Copy `backend/.env.example` to `backend/.env`, then fill in:
```
DB_HOST=localhost
DB_NAME=the_office_database
DB_USER=root
DB_PASS=

FRONTEND_URL=http://localhost:3000
```

### 7. Create the database
1. Open `http://localhost/phpmyadmin`
2. Create a new database named `the_office_database`
3. Run `backend/SQL Script.sql` against it — this creates the tables
4. Run `backend/SQL Seed.sql` if you want sample data to test with

### 8. Test it
Open `http://localhost/The_Office_PHP/backend/api/cours.php` in your browser. You should see JSON, not an error.

## Uploads folder

`uploads/` holds files written at runtime — images uploaded by formateurs, PDFs, videos, student submissions. The folder structure is committed (via `.gitkeep` files), but the files themselves are gitignored.

If a subfolder is missing on your machine, the backend recreates it on the next upload.

## What's NOT in this repo, and why

| Missing | Why | How to get it |
|---|---|---|
| `backend/vendor/` | Regenerated from `composer.lock`, not meant to be committed | `composer install` |
| `backend/.env` | Holds real database credentials | Copy `.env.example`, fill in your own values |
| The database itself | MySQL data doesn't travel with git | Run the SQL scripts in phpMyAdmin |
| Uploaded files | Runtime content, not source code | Created as users upload |

## Connecting the frontend

The React app's `.env` needs `VITE_SERVER_URL` pointing at this backend's `api/` folder and `VITE_UPLOADS_URL` pointing at the the backend's `uploads` folder:
```
VITE_SERVER_URL=http://localhost/The_Office_PHP/backend/api
VITE_UPLOADS_URL=http://localhost/The_Office_PHP/
```