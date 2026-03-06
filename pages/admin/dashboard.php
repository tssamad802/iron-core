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
$total_users = $controller->count('users');
$total_trainers = $controller->count('users', ['role' => 3]);
$total_Revenue = $controller->fetch_records('payment', ['member_amount']);
$total = 0;
foreach ($total_Revenue as $record) {
    if (!empty($record['member_amount'])) {
        $total += (int) $record['member_amount'];
    }
}
$displayTotal = round($total / 1000) . 'K';
$payments = $controller->fetch_records('payment', ['member_amount', 'created_at']);
$currentMonth = date('m');
$currentYear = date('Y');
$totalCollectedThisMonth = 0;
$totalCollectedAllTime = 0;
foreach ($payments as $p) {
    $amount = !empty($p['member_amount']) ? (int) $p['member_amount'] : 0;
    $totalCollectedAllTime += $amount;
    $paymentMonth = date('m', strtotime($p['created_at']));
    $paymentYear = date('Y', strtotime($p['created_at']));
    if ($paymentMonth == $currentMonth && $paymentYear == $currentYear) {
        $totalCollectedThisMonth += $amount;
    }
}
$percentage = $totalCollectedAllTime > 0 ? ($totalCollectedThisMonth / $totalCollectedAllTime) * 100 : 0;
$percentage = round($percentage, 1);

$this_month_total = $controller->count('users', [
    'MONTH(created_at)' => date('m'),
    'YEAR(created_at)' => date('Y')
]);
$last_month_total = $controller->count('users', [
    'MONTH(created_at)' => date('m', strtotime('-1 month')),
    'YEAR(created_at)' => date('Y', strtotime('-1 month'))
]);
if ($last_month_total == 0) {
    $growth = $this_month_total > 0 ? 100 : 0;
} else {
    $growth = (($this_month_total - $last_month_total) / $last_month_total) * 100;
}
$growth = number_format($growth, 1);
$sign = $growth >= 0 ? '↑' : '↓';
$this_week_trainers = $controller->count('users', [
    'role' => 3,
    'WEEK(created_at, 1)' => date('W'),
    'YEAR(created_at)' => date('Y')
]);
$last_week_trainers = $controller->count('users', [
    'role' => 3,
    'WEEK(created_at, 1)' => date('W', strtotime('-1 week')),
    'YEAR(created_at)' => date('Y', strtotime('-1 week'))
]);
$growth = $this_week_trainers - $last_week_trainers;
$sign = $growth >= 0 ? '↑' : '↓';
$recordsThisMonth = $controller->fetch_records(
    'users',
    ['fee'],
    '',
    ['role' => 2]
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore Gym — Admin Dashboard</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>
    <!-- ══ SIDEBAR OVERLAY (mobile) ══ -->
    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- ══ SIDEBAR ══ -->
    <?php
    require_once 'sidebar.php';
    ?>
    <!-- ══ MAIN WRAP ══ -->
    <div class="main-wrap">

        <!-- TOPBAR -->
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

        <!-- PAGE AREA -->
        <div class="page-area">

            <!-- Page Header -->
            <div class="page-header anim-fade-up">
                <div class="page-title-block">
                    <div class="title">Admin Dashboard</div>
                    <div class="subtitle" id="todayDate">Loading...</div>
                </div>
                <div class="page-actions">
                    <a href="./admin-add-users" class="btn btn-primary">
                        <i class="fa-solid fa-plus" style="color: rgba(255, 255, 255, 1.00);"></i>
                        Add Member
                    </a>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div class="stats-row">
                <div class="stat-card orange anim-fade-up anim-d1">
                    <div class="stat-top">
                        <div class="stat-lbl">Total Users</div>
                        <div class="stat-icon"><i class="fa-solid fa-user-group"
                                style="color: rgba(255, 255, 255, 1.00);"></i></div>
                    </div>
                    <div class="stat-val"><?php echo $total_users; ?></div>
                    <div class="stat-pill pill-up"> <?php echo $sign . ' ' . abs($growth) . '% this month'; ?></div>
                </div>

                <div class="stat-card green anim-fade-up anim-d2">
                    <div class="stat-top">
                        <div class="stat-lbl">Active Trainers</div>
                        <div class="stat-icon"><i class="fa-solid fa-dumbbell"></i></div>
                    </div>
                    <div class="stat-val"><?php echo $total_trainers; ?></div>
                    <div class="stat-pill pill-up"><?php echo $sign . ' ' . abs($growth) . ' new this week'; ?></div>
                </div>

                <div class="stat-card blue anim-fade-up anim-d3">
                    <div class="stat-top">
                        <div class="stat-lbl">Monthly Revenue</div>
                        <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="stat-val"><?php echo $displayTotal; ?></div>
                    <div class="stat-pill pill-up"><?php echo $percentage; ?>%</div>
                </div>

                <div class="stat-card yellow anim-fade-up anim-d4">
                    <div class="stat-top">
                        <div class="stat-lbl">New Registrations</div>
                        <div class="stat-icon"><i class="fa-solid fa-user-plus"></i>
                        </div>
                    </div>
                    <div class="stat-val">134</div>
                    <div class="stat-pill pill-down">↓ 3.2% this week</div>
                </div>
            </div>

        </div><!-- /page-area -->
    </div><!-- /main-wrap -->

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