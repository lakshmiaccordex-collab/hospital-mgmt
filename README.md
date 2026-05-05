# 🏥 Hospital Patient Management System

A full-featured **Hospital Patient Management System** built with **PHP 8** and **MySQL** — featuring role-based access control, patient records, medical history, file uploads, CSV export, full-text search, and pagination.

---

## 🚀 Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.1+ |
| Database | MySQL 8.0+ |
| Auth | Session-based with Role Control |
| File Handling | Native PHP Upload |
| Server | Apache with mod_rewrite |
| Frontend | Vanilla HTML/CSS/JS |

---

## ✨ Features

- 🔐 **Role-Based Access** — Admin, Doctor, Receptionist with different permissions
- 🛏️ **Patient Management** — Full CRUD with auto-generated Patient IDs
- 🩺 **Medical Records** — Doctors can add diagnoses, prescriptions, lab reports
- 📁 **File Upload** — Patient photos + medical report files (PDF/images)
- 🔍 **Full-Text Search** — Search by name, patient ID, phone, city
- 📄 **Pagination** — All list views paginated with filters
- 🔎 **Advanced Filtering** — Filter by status, department, gender, blood group, doctor
- 📊 **CSV Export** — Export patient data to Excel-compatible CSV
- 📈 **Dashboard** — Live stats, department breakdown, activity log
- 🚨 **Status Tracking** — Active, Critical, Under Observation, Discharged

---

## 👥 Role Permissions

| Feature | Admin | Doctor | Receptionist |
|---|---|---|---|
| View Patients | ✅ All | ✅ Own patients | ✅ All |
| Admit Patient | ✅ | ✅ | ✅ |
| Edit Patient | ✅ | ✅ | ✅ |
| Delete Patient | ✅ | ❌ | ❌ |
| Add Medical Records | ✅ | ✅ | ❌ |
| Export CSV | ✅ | ✅ | ❌ |

---

## 📁 Project Structure

```
hospital-mgmt/
├── config/
│   ├── database.php       # PDO singleton connection
│   └── app.php            # Session, constants, helpers
├── controllers/
│   ├── AuthController.php # Login, logout
│   └── PatientController.php # Full CRUD + export + medical records
├── middleware/
│   └── Auth.php           # Session auth + role checks
├── models/
│   └── Patient.php        # DB queries, search, pagination
├── views/
│   ├── auth/login.php
│   ├── dashboard.php
│   ├── patients/
│   │   ├── index.php      # List with search/filter/pagination
│   │   ├── create.php     # Admit new patient form
│   │   ├── edit.php       # Edit patient form
│   │   └── show.php       # Patient detail + medical records
│   └── partials/
│       ├── header.php     # Sidebar + nav + flash messages
│       └── footer.php
├── uploads/               # Patient photos + report files
├── exports/               # Temporary CSV exports
├── database.sql           # Schema + seed data
├── .env.example           # Environment template
├── .htaccess              # Apache routing
└── index.php              # Main router
```

---

## ⚙️ Setup & Installation

### Prerequisites
- PHP >= 8.1
- MySQL >= 8.0
- Apache with `mod_rewrite` enabled

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/hospital-mgmt.git
cd hospital-mgmt

# 2. Create environment file
cp .env.example .env

# 3. Update .env with your database credentials
nano .env

# 4. Import database
mysql -u root -p < database.sql

# 5. Set permissions
chmod 755 uploads/ exports/

# 6. Place in Apache htdocs and access
# http://localhost/hospital-mgmt
```

---

## 🔑 Demo Credentials

| Role | Email | Password |
|---|---|---|
| Admin | admin@hospital.com | password |
| Doctor | ramesh@hospital.com | password |
| Receptionist | mary@hospital.com | password |

---

## 📡 Application Routes

| Method | URL | Description | Role |
|---|---|---|---|
| GET/POST | `/login` | Login page | Public |
| GET | `/dashboard` | Dashboard with stats | All |
| GET | `/patients` | Patient list + search | All |
| GET | `/patients/create` | Admit new patient | All |
| POST | `/patients/store` | Save new patient | All |
| GET | `/patients/:id` | Patient details + records | All |
| GET | `/patients/:id/edit` | Edit patient | All |
| POST | `/patients/:id/update` | Update patient | All |
| POST | `/patients/:id/delete` | Delete patient | Admin |
| POST | `/patients/:id/records` | Add medical record | Admin/Doctor |
| GET | `/patients/export` | Export CSV | Admin/Doctor |

---

## 🔒 Security Features

- Password hashing with **bcrypt**
- **Role-based** access control on every route
- **Session regeneration** on login
- SQL Injection prevention via **PDO prepared statements**
- File upload **MIME type validation**
- XSS prevention via **htmlspecialchars**

---

## 👩‍💻 Author

**R.S. Lakshmi** — Senior Full Stack Developer
📧 lakshmiaccordex@gmail.com
🔗 [LinkedIn](https://www.linkedin.com/in/lakshmi-r-s-48367238b/)

---

## 📄 License

MIT License — free to use for learning and portfolio purposes.
