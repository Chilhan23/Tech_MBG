<div align="center">

#  Tech MBG
### Digital Attendance & Student Management System

<p>
  <img src="https://img.shields.io/badge/Laravel-^13.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-^8.3-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Filament-^5.0-FDAE4B?style=for-the-badge&logoColor=white"/>
  <img src="https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white"/>
</p>

<p>
  <img src="https://img.shields.io/badge/QR%20Code-Scanner-4CAF50?style=flat-square"/>
  <img src="https://img.shields.io/badge/PDF-Export-E53935?style=flat-square"/>
  <img src="https://img.shields.io/badge/Excel-Import-217346?style=flat-square"/>
  <img src="https://img.shields.io/badge/License-MIT-0969da?style=flat-square"/>
</p>

> A modern, high-performance attendance system using **QR Code scanning** to bridge the gap between physical identification and digital records — built for efficiency, accuracy, and ease of use.

</div>

---

##  Key Features

| Feature | Description |
|---------|-------------|
|  **QR Code Scanner** | Scan via browser camera or external USB/Wireless scanner |
|  **Real-Time Dashboard** | Monitor live attendance stats with interactive widgets |
|  **Student Management** | Add, edit, and manage student profiles and class assignments |
|  **Bulk QR Printing** | Generate and print QR codes for all students at once |
|  **PDF Export** | Produce professional, formatted attendance reports |
|  **Excel Import** | Bulk-import students and users via spreadsheet |
|  **Secure Authentication** | Session-protected admin panel for authorized staff only |

---

##  Folder Structure

```
Tech_MBG/
├── 📁 app/
│   ├── 📁 Filament/
│   │   ├── 📁 Pages/
│   │   │   ├── Auth/
│   │   │   │   └── Login.php                  # Custom login page
│   │   │   ├── Dashboard.php                  # Main dashboard
│   │   │   ├── ScannerKelasAmbil.php          # Class book checkout scanner
│   │   │   ├── ScannerKelasKembali.php        # Class book return scanner
│   │   │   ├── ScannerSiswaAmbil.php          # Student check-in scanner
│   │   │   └── ScannerSiswaKembali.php        # Student check-out scanner
│   │   ├── 📁 Resources/
│   │   │   ├── AbsensiKelas/                  # Class attendance resource
│   │   │   ├── AbsensiSiswas/                 # Student attendance resource
│   │   │   ├── Kelas/                         # Class management resource
│   │   │   │   ├── Schemas/                   # Form & Infolist schemas
│   │   │   │   └── Tables/                    # Table definitions
│   │   │   ├── Students/                      # Student management resource
│   │   │   │   ├── Schemas/                   # Form & Infolist schemas
│   │   │   │   └── Tables/                    # Table definitions
│   │   │   └── Users/                         # User management resource
│   │   │       ├── Schemas/                   # Form & Infolist schemas
│   │   │       └── Tables/                    # Table definitions
│   │   └── 📁 Widgets/
│   │       ├── AbsensiPerKelasWidget.php      # Per-class attendance chart
│   │       └── AbsensiStatsWidget.php         # Overall attendance stats
│   ├── 📁 Http/
│   │   └── Controllers/
│   │       ├── AbsensiController.php          # Attendance export controller
│   │       └── ScannerController.php          # QR scan processing controller
│   ├── 📁 Imports/
│   │   └── UserImport.php                     # Bulk user import from Excel
│   └── 📁 Models/
│       ├── Absensi.php                        # Attendance model
│       ├── Kelas.php                          # Class model
│       ├── KelasLog.php                       # Class log model
│       └── Student.php                        # Student model
├── 📁 database/
│   └── migrations/                            # Database migration files
├── 📁 resources/
│   ├── css/filament/panitia/                  # Custom Filament theme
│   └── views/
│       ├── exports/                           # PDF & QR export templates
│       └── filament/pages/                    # Scanner & dashboard views
├── 📁 routes/
│   └── web.php                                # Application routes
├── .env.example                               # Environment configuration template
├── composer.json                              # PHP dependencies
└── vite.config.js                             # Vite build configuration
```

---

## ⚙️ Installation

### Prerequisites

Make sure you have the following installed before getting started:

- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**
- **MariaDB** / MySQL >= 10.x
- **Git**

---

### Step 1 — Clone the Repository

```bash
git clone https://github.com/Chilhan23/Tech_MBG.git
cd Tech_MBG
```

### Step 2 — Install PHP Dependencies

```bash
composer install
```

### Step 3 — Install Node Dependencies

```bash
npm install
```

### Step 4 — Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Then open `.env` and update the database settings:

```env
APP_NAME=Tech_MBG
APP_URL=http://localhost

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Tech_MBG
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 5 — Run Database Migrations

```bash
php artisan migrate
```

### Step 6 — Create Storage Symlink

```bash
php artisan storage:link
```

### Step 7 — Build Frontend Assets

```bash
npm run build
```

### Step 8 — Create Admin User (Filament)

```bash
php artisan make:filament-user
```

Follow the prompts to set your admin name, email, and password.

### Step 9 — Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` — you will be automatically redirected to the `/panitia` admin panel.

---

##  Usage Guide

### 1.  Accessing the Admin Panel

Navigate to your root domain — you will be redirected to `/panitia` automatically. Sign in with your authorized admin credentials to unlock all management tools.

---

### 2.  Real-Time Dashboard

Once logged in, the **Dashboard** gives you an instant overview of operations:

- **Attendance Stats** — Track live check-in numbers via the `AbsensiStatsWidget`.
- **Class Analysis** — View a per-class attendance breakdown via the `AbsensiPerKelasWidget`.

---

### 3.  Managing Students & QR Codes

- **Add / Edit Students** — Use the **Students** resource menu to manage student profiles.
- **Print Single QR** — Open a student's profile and generate their individual QR code.
- **Bulk QR Print** — Use the **Bulk Print** feature to generate QR codes for the entire student body at once.

---

### 4.  Recording Attendance (Scanning)

The QR scanner is the core of the system:

- Navigate to the **Scanner** page inside the panel to activate your device's camera.
- Point the camera at a student's QR code — the system automatically validates identity and records a timestamp.
- Alternatively, connect an **external USB/Wireless QR scanner**; the system processes text input automatically.

> [!IMPORTANT]
> **Security Protocol:** All admin and scanning functions are session-protected. Only authenticated panitia members can access `/panitia` routes or perform scans.

---

### 5.  Reports & Data Export

- **View Logs** — Open the **Absensis** menu to see every recorded attendance entry.
- **Export PDF** — Generate a formatted, professional attendance report using the **Export PDF** tool, ready for archival or administrative submission.

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Framework** | Laravel 13 |
| **Admin Panel** | Filament 5 |
| **Language** | PHP 8.3 |
| **Database** | MariaDB / MySQL |
| **Frontend Build** | Vite |
| **QR Code** | SimpleSoftwareIO/simple-qrcode |
| **PDF Export** | barryvdh/laravel-dompdf |
| **Excel Import** | Maatwebsite/Laravel-Excel |

---

##  Contributors

Thanks to everyone who has contributed to this project! 🎉

| Avatar | Name | GitHub | Role |
|--------|------|--------|---------|
| <img src="https://github.com/Chilhan23.png" width="48" style="border-radius:50%"/> | **Chilhan23** | [@Chilhan23](https://github.com/Chilhan23) | Backend Developer |
| <img src="https://github.com/qannn0607.png" width="48" style="border-radius:50%"/> | **qannn0607** | [@qannn0607](https://github.com/qannn0607) | UI/UX Designer |
| <img src="https://github.com/rXonee.png" width="48" style="border-radius:50%"/> | **rXonee** | [@rXonee](https://github.com/rXonee) | Frontend Developer |

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

<div align="center">

Made  by the **LastSeenIn2027**

</div>
