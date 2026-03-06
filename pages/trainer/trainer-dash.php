<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
$auth = new auth(['trainer']);
$user_id = $auth->get_id();
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$total_clients = $controller->count('users', ['users.trainer_id' => $user_id]);
// echo "<pre>";
// print_r($total_clients);
// echo "</pre>";
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GymFlow</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body class="dashboard-body">

   <?php require_once 'trainer-sidebar.php'; ?>

    <div class="dashboard-container">
        <main class="main-wrap">
            <header class="topbar">
                <button class="menu-btn" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar-left">
                    <h1 class="font-heading" style="font-size: 20px;">TRAINER DASHBOARD</h1>
                </div>
                <div class="topbar-right">
                    <div class="topbar-icon-btn">
                    </div>
                    <div class="avatar avatar-sm"
                        style="width:28px;height:28px;border-radius:6px;background:linear-gradient(135deg,var(--accent),#c23500);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">
                        <?php
                        $name = $auth->show_name();
                        $names = explode(' ', $name);
                        echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                        ?>
                    </div>
                </div>
            </header>


            <div class="content-body anim-fade-up">

                <div class="stats-grid">
                    <div class="card stat-card">
                        <div class="card-header">
                            <div class="card-title">Total Clients</div>
                            <i class="fas fa-users text-accent"></i>
                        </div>
                        <div class="stat-value font-display"><?php echo $total_clients; ?></div>
                        <div class="stat-change text-green"><i class="fas fa-caret-up"></i> +4 this month</div>
                    </div>

                    <div class="card stat-card">
                        <div class="card-header">
                            <div class="card-title">Today's Sessions</div>
                            <i class="fas fa-calendar-check text-blue"></i>
                        </div>
                        <div class="stat-value font-display">08</div>
                        <div class="stat-change text-dim">Next: 04:00 PM</div>
                    </div>

                    <div class="card stat-card">
                        <div class="card-header">
                            <div class="card-title">Hours Tracked</div>
                            <i class="fas fa-clock text-yellow"></i>
                        </div>
                        <div class="stat-value font-display">156</div>
                        <div class="stat-change text-sec">Target: 200h</div>
                    </div>
                </div>

            </div>
        </main>
    </div>
        <!-- Toast -->
    <div class="toast" id="toast" style="display:none;">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>
</body>
<script src="./js/script.js"></script>

</html>