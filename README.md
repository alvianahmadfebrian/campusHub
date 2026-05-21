# CampusHub

## Clone project

```bash
git clone https://github.com/alvianahmadfebrian/campusHub.git
cd campusHub
```

---

## Install dependency

Backend:

```bash
composer install
```

Frontend:

```bash
npm install
```

---

## Setup environment

File `.env` sudah disediakan di folder:

```text
seed/.env
```

Copy file tersebut ke root project:

```bash
cp seed/.env .env
```

Lalu generate app key:

```bash
php artisan key:generate
```

---

## Jalankan migration

```bash
php artisan migrate
```

---

## Jalankan project

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Akses:

```text
http://127.0.0.1:8000
```

---

## Update project (pull)

```bash
git pull origin main
```

---

## Upload perubahan (push)

```bash
git status
git add .
git commit -m "update project"
git push origin main
```
