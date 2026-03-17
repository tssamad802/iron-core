<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
require_once './includes/view.php';
$view = new view();
$auth = new auth(['admin']);
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$controller->markPendingPayments();
$id = $_GET['id'];
$join = "
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id
";
$columns = [
    'users.*',
    'role.role AS role_name',
    'status.status AS status_name'
];
$data = $controller->fetch_records('users', $columns, $join, ['users.id' => $id]);
$roles = $controller->fetch_records('role');
$status = $controller->fetch_records('status');
$total_users = $controller->count('users');
// echo "<pre>";
// print_r($data);
// echo "</pre>";
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore Gym — Edit Member</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/theme.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <?php
    require_once 'sidebar.php';
    ?>

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
                    <div class="title">Edit Member</div>
                    <div class="subtitle">
                        Update membership details
                    </div>
                </div>
                <div class="page-actions">
                    <a href="./admin-add-users" class="btn btn-secondary">
                        <i class="fa-solid fa-user-plus"></i>
                        New Users
                    </a>
                    <a href="./admin-users" class="btn btn-secondary">
                        <i class="fa-solid fa-users"></i>
                        All Users
                    </a>
                </div>
            </div>

            <div class="card anim-fade-up anim-d1">
                <form action="./admin-edit-script" method="post" class="member-form">
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <div class="member-form-grid">
                        <h3 class="form-section-title">Edit Member</h3>

                        <div class="form-group">
                            <label class="form-label" for="first_name">Full Name</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-user"></i></span>
                                <input type="text" id="first_name" name="fullname" class="form-input"
                                    placeholder="<?php echo $data[0]['fullname']; ?>"
                                    value="<?php echo $data[0]['fullname']; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="username">Username</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-at"></i></span>
                                <input type="text" id="username" name="username" class="form-input"
                                    placeholder="<?php echo $data[0]['username']; ?>"
                                    value="<?php echo $data[0]['username']; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-input"
                                    placeholder="<?php echo $data[0]['email']; ?>"
                                    value="<?php echo $data[0]['email']; ?>">
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
                            <label class="form-label" for="role">Status</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-user-shield"></i></span>
                                <?php $check_status = $data[0]['status_name']; ?>
                                <select id="role" name="status" class="form-input">
                                    <?php
                                    foreach ($status as $row) { ?>
                                        <option value="<?php echo $row['id']; ?>" <?php if ($row == $check_status)
                                               echo 'selected'; ?>><?php echo $row['status']; ?></option>
                                    <?php } ?>
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="role">Role</label>
                            <div class="input-wrap">
                                <span class="input-icon"><i class="fa-solid fa-user-shield"></i></span>
                                <?php $role = $data[0]['role']; ?>
                                <select id="role" name="role" class="form-input">
                                    <?php
                                    $check_role = $data[0]['role_name'];
                                    foreach ($roles as $role) { ?>
                                        <option value="<?php echo $role['id']; ?>" <?php if ($role['role'] == $check_role)
                                               echo 'selected'; ?>><?php echo $role['role']; ?></option>
                                    <?php } ?>
                                </select>

                            </div>
                        </div>

                    </div>

                    <div class="member-form-footer">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
                <?php $view->showErrors(); ?>
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
    </script>
    <script src="./js/script.js"></script>
    <script src="./js/theme.js"></script>
</body>

</html>