# 🚀 Tech_MBG: Digital Attendance & Student Management

A modern, high-performance attendance system using **QR Code scanning** to bridge the gap between physical identification and digital records. Built for efficiency, accuracy, and ease of use.

---

## 📖 User Guide

Follow these steps to manage students and track attendance effectively through the **Panitia Panel**.

### 1. 🔐 Accessing the Command Center
The entire system is managed via a secure administrative portal.
* **Portal Entry**: Simply visit the root domain; you will be automatically redirected to the `/panitia` portal.
* **Authentication**: Sign in with your authorized credentials to unlock the management tools.

### 2. 📊 Real-Time Monitoring
Once logged in, the **Dashboard** provides an instant overview of your operations:
* **Attendance Stats**: Track live check-in numbers through the `AbsensiStatsWidget`.
* **Class Analysis**: View a breakdown of attendance per class via the `AbsensiPerKelasWidget`.

### 3. 👨‍🎓 Student & QR Management
Manage your student database and prepare physical identification cards:
* **Manage Students**: Add, edit, or view student profiles through the **Students** resource menu.
* **Single QR Print**: Generate and print a QR code for a specific student from their profile page.
* **Bulk QR Generation**: Use the **Bulk Print** feature to generate QR codes for the entire student body in one go.

### 4. 📲 Recording Presence (Scanning)
The core of the system is the scanning interface:
* **Internal Scanner**: Navigate to the **Scanner** page within the panel to activate the camera.
* **Instant Scan**: Point the camera at a student's QR code. The system will automatically validate the identity and record the timestamp.

### 5. 📄 Reporting & Data Export
Convert digital logs into official documentation:
* **Review Logs**: Access the **Absensis** menu to see every recorded entry.
* **PDF Export**: Generate professional, formatted attendance reports using the **Export PDF** tool for archival or administrative submission.

---

> [!IMPORTANT]
> **Security Protocol**: All administrative and scanning functions are protected. Only panitia members with valid sessions can access the `/panitia` routes or perform scans.
