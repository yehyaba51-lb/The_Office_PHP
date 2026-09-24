# The Office — Backend (PHP)

Backend API for The Office e-learning platform. Plain PHP, no framework — talks to a MariaDB database and serves JSON to the React frontend.

## Folder structure

This repo's contents need to end up inside a `backend/` folder, sitting alongside a `React` frontend, inside your Apache `htdocs`:

```
C:\xampp\htdocs\The_Office_PHP\
├── backend\        ← this repo goes here
│   ├── api\
│   ├── classes\
│   ├── config\
│   └── ...
└── (React frontend, cloned separately, lives outside htdocs)
```

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
cd C:\xampp\htdocs\The_Office_PHP
git clone https://github.com/yehyaba51-lb/backend.git backend
```

### 5. Install PHP dependencies
`vendor/` isn't included in this repo (regenerated from `composer.json`/`composer.lock`, same reason `node_modules` isn't committed in JS projects). Run as **administrator** if you hit permission errors:
```
cd backend
composer install
```

### 6. Create your `.env` file
Also not included (holds real credentials). Copy `.env.example` to `.env` in the `backend/` folder, then fill in:
```
DB_HOST=localhost
DB_NAME=e_learning
DB_USER=root
DB_PASS=
```

### 7. Create the database
1. Open `http://localhost/phpmyadmin`
2. Create a new database named `e_learning`
3. Run the table-creation SQL script (in this repo) against it
4. Run the seed data script if you want sample data to test with

### 8. Test it
Open `http://localhost/The_Office_PHP/backend/api/cours.php` in your browser. You should see JSON, not an error.

## What's NOT in this repo, and why

| Missing | Why | How to get it |
|---|---|---|
| `vendor/` | Regenerated from `composer.lock`, not meant to be committed | `composer install` |
| `.env` | Holds real database credentials | Copy `.env.example`, fill in your own values |
| The database itself | MySQL data doesn't travel with git | Run the SQL scripts in phpMyAdmin |

## Connecting the frontend

The React app's `.env` needs `VITE_SERVER_URL` pointing at this backend's `api/` folder:
```
VITE_SERVER_URL=http://localhost/The_Office_PHP/backend/api
```