<?php
require_once 'includes/config.session.inc.php';
require_once 'includes/view.php';
require_once 'includes/dbh.inc.php';
require_once 'includes/model.php';
require_once 'includes/control.php';
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$controller->markPendingPayments();
$controller->resetDailyAttendance();
$view = new view();
$total_members = $controller->count('users', ['role' => 2]);
$total_trainers = $controller->count('users', ['role' => 3]);
$total_Revenue = $controller->fetch_records('payment', ['member_amount']);
$total = 0;
foreach ($total_Revenue as $record) {
    if (!empty($record['member_amount'])) {
        $total += (int) $record['member_amount'];
    }
}
$displayTotal = round($total / 1000) . 'K';
// echo '<pre>';
// print_r($total_members);
// echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore Gym — Admin Login</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>

    <!-- ══ LEFT: ART PANEL ══ -->
    <div class="art-panel">
        <div class="art-grid"></div>
        <div class="art-glow"></div>
        <div class="orb orb-a"></div>
        <div class="orb orb-b"></div>

        <!-- Main Logo -->
        <div class="art-brand anim-fade-up">
            <div class="main-logo">IRON<span>CORE</span></div>
            <div class="sub-label">Gym Management Admin Panel</div>
        </div>

        <!-- Barbell SVG -->
        <svg class="barbell-svg anim-fade-up anim-d2" viewBox="0 0 320 80" fill="none">
            <rect x="0" y="28" width="320" height="24" rx="5" fill="white" />
            <rect x="0" y="8" width="56" height="64" rx="9" fill="white" />
            <rect x="264" y="8" width="56" height="64" rx="9" fill="white" />
            <rect x="22" y="0" width="22" height="80" rx="5" fill="white" />
            <rect x="276" y="0" width="22" height="80" rx="5" fill="white" />
        </svg>

        <!-- Quick stats -->
        <div class="art-stats anim-fade-up anim-d3">
            <div class="art-stat">
                <div class="val"><?php echo $total_members; ?></div>
                <div class="lbl">Members</div>
            </div>
            <div class="art-stat">
                <div class="val"><?php echo $total_trainers; ?></div>
                <div class="lbl">Trainers</div>
            </div>
            <div class="art-stat">
                <div class="val"><?php echo $displayTotal; ?></div>
                <div class="lbl">Revenue</div>
            </div>
        </div>

        <!-- Footer rule -->
        <div class="art-footer">
            <div class="rule"></div>
            <span>Power · Strength · Discipline</span>
            <div class="rule flip"></div>
        </div>
    </div>

    <!-- ══ RIGHT: FORM PANEL ══ -->
    <div class="form-panel">

        <div class="secure-badge anim-fade-up">
            <div class="pulse-dot"></div>
            <span>Secure Admin Access</span>
        </div>

        <div class="form-title anim-fade-up anim-d1">SIGN<br />IN.</div>
        <p class="form-desc anim-fade-up anim-d2">
            Welcome back. Enter your credentials<br />to access the admin control center.
        </p>

        <!-- JS Error Bridge container (errors injected here by jsErrorBridge()) -->
        <div id="js-error-zone"></div>

        <!-- Login Form -->
        <form action="./login-script" method="post" id="loginForm" novalidate>

            <!-- Email Field -->
            <div class="form-group anim-fade-up anim-d2">
                <label class="form-label" for="email">Email / Username</label>
                <div class="input-wrap">
                    <span class="input-icon"><i class="fa-regular fa-envelope"></i></span>
                    <input class="form-input" type="text" id="email" placeholder="admin@ironcore.gym" name="email"
                        autocomplete="username" />
                </div>
                <?php $view->showErrors(); ?>
            </div>

            <!-- Password Field -->
            <div class="form-group anim-fade-up anim-d3">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                    <input class="form-input" type="password" id="password" placeholder="Enter your password"
                        style="padding-right: 46px;" name="pwd" autocomplete="current-password" />
                    <span class="input-suffix" id="eyeToggle" title="Show/hide password">👁</span>
                </div>
                <?php $view->showErrors(); ?>
            </div>

            <!-- Remember + Forgot -->
            <div class="form-row anim-fade-up anim-d4">
                <label class="remember">
                    <input type="checkbox" id="rememberMe" checked />
                    <div class="check-box"></div>
                    <span>Keep me signed in</span>
                </label>
                <a href="./forget-password" class="forgot-link">Forgot Password?</a>
            </div>

            <!-- Login Button -->
            <button class="btn-login anim-fade-up anim-d5" id="loginBtn" type="submit">
                <span class="btn-text">ACCESS DASHBOARD</span>
                <span class="btn-spinner" id="btnSpinner" style="display:none;">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </span>
            </button>

        </form>

    </div><!-- /form-panel -->

    <!-- Toast -->
    <div class="toast" id="toast" style="display:none;">
        <span class="toast-icon" id="toastIcon">🔥</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>
</body>
<script src="./js/script.js"></script>

</html>