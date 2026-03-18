<div align="center">

<img src="https://readme-typing-svg.demolab.com?font=Bebas+Neue&size=60&duration=3000&pause=1000&color=FF4500&center=true&vCenter=true&width=600&lines=IRON+CORE;GYM+MANAGEMENT+SYSTEM" alt="Iron Core" />

<br/>

<img src="https://img.shields.io/badge/PHP-53.9%25-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
<img src="https://img.shields.io/badge/CSS-37.3%25-1572B6?style=for-the-badge&logo=css3&logoColor=white"/>
<img src="https://img.shields.io/badge/JavaScript-6.9%25-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black"/>
<img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge"/>
<img src="https://img.shields.io/badge/Status-Active-brightgreen?style=for-the-badge"/>

<br/><br/>

> **A powerful, full-featured gym management dashboard for Admins, Members, and Trainers.**  
> Streamline your fitness facility — all from one clean, modern interface.

<br/>

---

</div>

## 🏋️ What is Iron Core?

**Iron Core** is a web-based gym management system built with PHP. It provides a centralized dashboard experience tailored for three distinct user roles — **Admins**, **Members**, and **Trainers** — each with their own dedicated views, controls, and features.

Whether you're managing memberships, scheduling sessions, or tracking fitness progress, Iron Core has you covered.

---

## ✨ Features at a Glance

| 🔑 Feature | 📋 Description |
|---|---|
| 🛡️ **Multi-Role Dashboard** | Separate panels for Admin, Member, and Trainer |
| 👥 **Member Management** | Register, view, edit, and remove gym members |
| 🏃 **Trainer Management** | Assign trainers, manage schedules and profiles |
| 📅 **Session Scheduling** | Plan and organize workout sessions |
| 💳 **Membership Tracking** | Monitor plans, renewals, and payment status |
| 📊 **Admin Analytics** | Get a bird's-eye view of gym operations |
| 📱 **Responsive UI** | Clean, modern design that works across devices |
| 🔐 **Secure Auth** | Role-based login with access control |

---

## 🗂️ Project Structure

```
iron-core/
│
├── 📁 css/            # Stylesheets for the dashboard UI
├── 📁 images/         # Icons, logos, and media assets
├── 📁 includes/       # Reusable PHP components (header, footer, DB config)
├── 📁 js/             # JavaScript for interactivity & UI enhancements
├── 📁 pages/          # Role-specific dashboard pages
│   ├── admin/         # Admin panel pages
│   ├── member/        # Member portal pages
│   └── trainer/       # Trainer dashboard pages
│
├── 🔧 .htaccess       # Apache URL rewriting & access rules
└── 🚀 index.php       # Application entry point & router
```

---

## ⚙️ Getting Started

### Prerequisites

Make sure you have the following installed:

- **PHP** >= 7.4
- **MySQL** / **MariaDB**
- **Apache** web server (with `mod_rewrite` enabled)
- A local server environment like **XAMPP**, **WAMP**, or **Laragon**

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/tssamad802/iron-core.git

# 2. Move the project to your server's root directory
# e.g., for XAMPP:
cp -r iron-core /xampp/htdocs/

# 3. Import the database
# Open phpMyAdmin → Create a new database → Import the provided SQL file

# 4. Configure the database connection
# Edit includes/config.php (or db.php) with your credentials:
#   DB_HOST, DB_USER, DB_PASS, DB_NAME

# 5. Launch in your browser
http://localhost/iron-core
```

---

## 👥 User Roles

<table>
<tr>
<td align="center" width="33%">

### 🔴 Admin
- Full system control
- Manage members & trainers
- View reports & analytics
- Configure gym settings
- Handle memberships & billing

</td>
<td align="center" width="33%">

### 🔵 Trainer
- View assigned members
- Manage training schedules
- Update session details
- Track member attendance

</td>
<td align="center" width="33%">

### 🟢 Member
- View personal profile
- Check membership status
- View assigned trainer
- Track session history

</td>
</tr>
</table>

---

## 🛠️ Built With

<div align="center">

<img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" />
<img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" />
<img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" />
<img src="https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white" />

</div>

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. **Fork** the project
2. Create your feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add some amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. **Open a Pull Request**

---

## 📬 Contact

**Developer:** [@tssamad802](https://github.com/tssamad802)  
**Repository:** [github.com/tssamad802/iron-core](https://github.com/tssamad802/iron-core)

---

<div align="center">

**If you find this project useful, please consider giving it a ⭐ — it means a lot!**

<br/>

*Built with 💪 for fitness enthusiasts and gym owners.*

</div>
