<div align="center">

# 🏋️ Iron Core

**A full-featured gym management dashboard for Admins, Members, and Trainers**

![PHP](https://img.shields.io/badge/PHP-53.9%25-777BB4?style=for-the-badge&logo=php&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-37.3%25-264DE4?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-6.9%25-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

[![GitHub Stars](https://img.shields.io/github/stars/tssamad802/iron-core?style=flat-square)](https://github.com/tssamad802/iron-core/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/tssamad802/iron-core?style=flat-square)](https://github.com/tssamad802/iron-core/network/members)
[![GitHub Issues](https://img.shields.io/github/issues/tssamad802/iron-core?style=flat-square)](https://github.com/tssamad802/iron-core/issues)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)

</div>

---

## 📖 Overview

**Iron Core** is a comprehensive gym management system built with PHP, CSS, and JavaScript. It provides dedicated dashboards for three types of users — **Admins**, **Members**, and **Trainers** — making it easy to manage every aspect of a gym from a single platform.

Whether you're running a small fitness studio or a large gym chain, Iron Core gives you the tools to streamline operations, track memberships, manage schedules, and keep your community connected.

---

## ✨ Features

### 🔐 Role-Based Access
| Role | Capabilities |
|------|-------------|
| **Admin** | Full control — manage members, trainers, schedules, and reports |
| **Trainer** | View assigned members, manage sessions, track progress |
| **Member** | View membership details, schedule, and progress |

### 🗂️ Core Modules
- **Dashboard Overview** — At-a-glance stats for the whole gym
- **Member Management** — Register, update, and track gym members
- **Trainer Management** — Assign trainers and manage their profiles
- **Schedule Management** — Organize classes and personal training sessions
- **User Authentication** — Secure login system for all user roles
- **Responsive UI** — Clean, mobile-friendly interface styled with custom CSS

---

## 📁 Project Structure

```
iron-core/
├── css/              # Stylesheets and UI themes
├── images/           # Icons, logos, and media assets
├── includes/         # Reusable PHP components (header, footer, DB config)
├── js/               # JavaScript for interactivity and UI logic
├── pages/            # Individual dashboard pages per role
├── index.php         # Application entry point
└── .htaccess         # Apache configuration and URL routing
```

---

## 🚀 Getting Started

### Prerequisites

Make sure you have the following installed:

- **PHP** >= 7.4
- **MySQL** or **MariaDB**
- **Apache** web server (with `mod_rewrite` enabled)
- Or use a local stack like **XAMPP**, **WAMP**, or **LAMP**

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/tssamad802/iron-core.git
   cd iron-core
   ```

2. **Move to your web server's root directory**
   ```bash
   # For XAMPP on Windows:
   cp -r iron-core/ C:/xampp/htdocs/iron-core

   # For LAMP on Linux:
   cp -r iron-core/ /var/www/html/iron-core
   ```

3. **Set up the database**
   - Open **phpMyAdmin** or your preferred MySQL client
   - Create a new database (e.g., `iron_core_db`)
   - Import the SQL file from the `includes/` directory (if provided)

4. **Configure the database connection**
   - Open `includes/config.php` (or similar)
   - Update your DB credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'iron_core_db');
   ```

5. **Launch the app**
   - Start Apache and MySQL via your server panel
   - Visit `http://localhost/iron-core` in your browser

---

## 🖥️ Usage

Once the app is running, log in using your role credentials:

| Role | Default Login |
|------|--------------|
| Admin | Set during setup |
| Trainer | Created by Admin |
| Member | Registered via Admin |

Each role will be redirected to their own dedicated dashboard upon login.

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP (server-side logic & routing) |
| Frontend | HTML5, CSS3, JavaScript |
| Database | MySQL / MariaDB |
| Server | Apache with `.htaccess` rewrite rules |

---

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. **Fork** this repository
2. **Create** a feature branch: `git checkout -b feature/your-feature-name`
3. **Commit** your changes: `git commit -m "Add your feature"`
4. **Push** to the branch: `git push origin feature/your-feature-name`
5. **Open** a Pull Request

Please make sure your code is clean, well-commented, and tested before submitting.

---

## 🐛 Bug Reports & Feature Requests

Found a bug or have an idea? Open an [issue](https://github.com/tssamad802/iron-core/issues) and describe it in detail. All feedback is appreciated!

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

<div align="center">

Made with 💪 by [tssamad802](https://github.com/tssamad802)

*Forge your core. Build your legacy.*

</div>
