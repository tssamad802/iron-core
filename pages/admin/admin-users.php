<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
$auth = new auth(['admin']);
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$controller->markPendingPayments();
$join = "
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id
LEFT JOIN payment ON users.id = payment.member_id";
$columns = [
    'users.*',
    'payment.member_amount',
    'payment.payment_status',
    'payment.month',
    'payment.year',
    'role.role AS role_name',
    'status.status AS status_name'
];
$total_users = $controller->count('users');
$total = $controller->count('users');

$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;
$total_pages = ceil($total / $limit);
$fetching_members = $controller->fetch_records(
    'users',
    $columns,
    $join,
    [],
    $limit,
    $offset
);
$total = $controller->count('users');
$roles = $controller->fetch_records('role');
    // echo '<pre>';
    // print_r($fetching_members);
    // echo '</pre>';
    // exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore Gym — Users</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
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

    <!-- MAIN WRAP -->
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
            <!-- PAGE HEADER -->
            <div class="page-header anim-fade-up">
                <div class="page-title-block">
                    <div class="title">Users</div>
                    <div class="subtitle">Manage all gym users and their plans</div>
                </div>
                <div class="page-actions">
                    <a href="./admin-add-users" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus" style="color: rgba(255, 255, 255, 1.00);"></i>
                        Add User
                    </a>
                </div>
            </div>

            <!-- FILTER BAR -->
            <div class="card anim-fade-up anim-d1" style="margin-bottom:18px;">
                <div class="flex-between" style="gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <div class="form-group" style="margin-bottom:0;min-width:170px;">
                            <label class="form-label" for="filter_status">Role</label>
                            <div class="input-wrap">
                                <select id="filter_role" class="form-input" style="padding-left:16px;">
                                    <?php
                                    foreach ($roles as $role) { ?>
                                        <option value="<?php echo $role['id'] ?>"><?php echo $role['role'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MEMBERS TABLE -->
            <div class="card anim-fade-up anim-d2">
                <div class="card-header">
                    <div>
                        <div class="card-title">Users List</div>
                        <div class="card-subtitle">Static sample data — ready for backend wiring</div>
                    </div>
                    <span class="font-label" style="color:var(--text-sec);">TOTAL · <?php echo $total; ?> USERS</span>
                </div>

                <div class="table-responsive">
                    <table class="data-table members-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="members_body">
                            <?php
                            foreach ($fetching_members as $row) {
                                ?>
                                <tr>
                                    <td>
                                        <div class="member-cell">
                                            <?php
                                            $name = $row['fullname'];
                                            $words = explode(" ", trim($name));
                                            $initials = strtoupper(substr($words[0], 0, 1));
                                            if (count($words) > 1) {
                                                $initials .= strtoupper(substr($words[1], 0, 1));
                                            }
                                            ?>
                                            <div class="avatar avatar-sm"><?php echo $initials; ?></div>

                                            <div>
                                                <div class="member-name"><?php echo $row['username']; ?></div>
                                                <div class="member-email"><?php echo $row['email']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-active"><?php echo $row['status_name']; ?></span></td>
                                    <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                    <td style="text-align:right;">
                                        <a href="./admin-edit-users?id=<?php echo $row['id']; ?>"
                                            class="table-link">Edit</a>
                                        <a href="./delete-record?id=<?php echo $row['id']; ?>" class="table-link">Delete</a>

                                        <?php
                                        if (
                                            strtolower($row['role_name']) !== 'admin' &&
                                            strtolower($row['role_name']) !== 'trainer' &&
                                            strtolower($row['payment_status']) === 'pending'

                                        ) { ?>
                                            <button class="fee-notify-btn">
                                                <i class="fa-solid fa-bell"></i> Notify
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <span class="page-info">
                            Showing <?php echo ($offset + 1) ?>–
                            <?php echo min($offset + $limit, $total) ?>
                            of <?php echo $total ?> users
                        </span>

                        <div class="page-btns">

                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1 ?>">
                                    <button class="page-btn"><i class="fa-solid fa-chevron-left"></i></button>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i ?>">
                                    <button class="page-btn <?php echo $i == $page ? 'active' : '' ?>">
                                        <?php echo $i ?>
                                    </button>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1 ?>">
                                    <button class="page-btn"><i class="fa-solid fa-chevron-right"></i></button>
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
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
    <script src="./js/server.js"></script>
</body>

</html>