<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
require_once 'includes/view.php';
$view = new view();
$auth = new auth(['admin']);
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$controller->markPendingPayments();
$roles = $controller->fetch_records('role');
$total_users = $controller->count('users');
// echo '<pre>';
// print_r($roles);
// echo '</pre>';
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore Gym — Add User</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/theme.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon"><img src="./images/logo.png" alt="IronCore"></div>
            <div>
                <div class="logo-text">IronCore</div>
                <div class="logo-sub">Admin Panel</div>
            </div>
        </div>
        <?php
        require_once 'sidebar.php';
        ?>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <?php
                $name = $auth->show_name();
                $names = explode(' ', $name);
                $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                ?>
                <div class="avatar avatar-sm" style="width:36px;height:36px;font-size:13px;border-radius:8px;">
                    <?php echo $initials; ?>
                </div>
                <div class="user-meta">
                    <div class="name"><?php echo $auth->show_name(); ?></div>
                    <div class="role">Super Admin</div>
                </div>
                <span class="user-arrow"></span>
            </div>
        </div>
    </aside>

    <div class="main-wrap">
       <header class="topbar">
            <div class="topbar-left">
                <button class="menu-btn" onclick="toggleSidebar()">☰</button>
            </div>

            <div class="topbar-right">

                <?php
                $name = $auth->show_name();
                $names = explode(' ', $name);
                $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                ?>
                <div class="topbar-profile">
                    <div class="avatar avatar-sm" style="width:28px;height:28px;font-size:11px;border-radius:6px;">
                        <?php echo $initials; ?>
                    </div>
                    <span class="tp-name"><?php echo $name; ?></span>
                </div>

                <button class="logout-btn" onclick="doLogout()">
                    <i class="fa-solid fa-arrow-right-from-bracket" style="color: rgba(255, 255, 255, 1.00);"></i>
                    <span>Logout</span>
                </button>
            </div>
        </header>

        <div class="page-area">
            <div class="page-header anim-fade-up">
                <div class="page-title-block">
                    <div class="title">Add User</div>
                    <div class="subtitle">Create a new gym membership profile</div>
                </div>
                <div class="page-actions">
                    <a href="./admin-users" class="btn btn-secondary">
                        <i class="fa-solid fa-users"></i>
                        All Users
                    </a>
                </div>
            </div>

            <div class="card anim-fade-up anim-d1">
                <form action="./admin-add-script" method="post" class="member-form">
                    <div class="member-form-grid">
                        <h3 class="form-section-title">Add User</h3>

                        <div class="form-group">
                            <label class="form-label" for="first_name">Full Name</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-user"></i></span>
                                <input type="text" id="first_name" name="fullname" class="form-input"
                                    placeholder="Ahmed">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="username">Username</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-at"></i></span>
                                <input type="text" id="username" name="username" class="form-input"
                                    placeholder="ahmedkhan">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-input"
                                    placeholder="name@example.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="pwd">Password</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" id="pwd" name="pwd" class="form-input" placeholder="********">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="role">Role</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-user-shield"></i></span>
                                <select id="role" name="role" class="form-input">
                                    <?php
                                    foreach ($roles as $role) { ?>
                                        <option value="<?php echo $role['id']; ?>"><?php echo $role['role']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="feeField" style="display:none;">
                            <label class="form-label">Fee</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-crown"></i></span>
                                <input type="text" name="fee" class="form-input" placeholder="membership..">
                            </div>
                        </div>

                        <div class="form-group" id="salaryTrainerField" style="display:none;">
                            <label class="form-label">Trainer Salary</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-money-bill-wave"></i></span>
                                <input type="text" name="trainer_salary" class="form-input"
                                    placeholder="Trainer Salary...">
                            </div>
                        </div>
                    </div>

                    <div class="member-form-footer">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fa-solid fa-arrow-left"></i>
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-check"></i>
                            Save User
                        </button>
                    </div>
                    <?php $view->showErrors(); ?>
                </form>
            </div>
        </div>
    </div>

    <div class="toast" id="toast" style="display:none;">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('open');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        }
        const roleSelect = document.getElementById("role");
        const feeField = document.getElementById("feeField");
        const salaryTrainerField = document.getElementById("salaryTrainerField");

        roleSelect.addEventListener("change", function () {
            const selectedText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

            feeField.style.display = "none";
            salaryTrainerField.style.display = "none";

            if (selectedText === "member") {
                feeField.style.display = "block";
            } else if (selectedText === "trainer") {
                salaryTrainerField.style.display = "block";
            }
        });
    </script>
    <script src="./js/script.js"></script>
    <script src="./js/theme.js"></script>
</body>

</html>