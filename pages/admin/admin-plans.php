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
$join = "
INNER JOIN users AS trainer ON trainer.id = plan.trainer_id
INNER JOIN role ON trainer.role = role.id
INNER JOIN status ON plan.plan_status = status.id
";
$columns = [
    'plan.*',
    'trainer.fullname AS trainer_name',
    'trainer.id AS trainer_id',
    'role.role AS role_name',
    'status.status AS status_name',
    '(SELECT COUNT(*) FROM plan_clients WHERE plan_clients.plan_id = plan.id) AS total_clients'
];

$plan_fetching = $controller->fetch_records('plan', $columns, $join);
// echo '<pre>';
// print_r($plan_fetching);
// echo '<pre>';
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore Gym — Plans</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>

    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-area">

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
                    <div class="title">Workout Plans</div>
                    <div class="subtitle" id="todayDate">Loading date...</div>
                </div>
            </div>

            <div class="plans-toolbar anim-fade-up anim-d1">
                <div class="plans-toolbar-left">
                    <div class="tab-group">
                        <button class="tab active" onclick="filterTab(this,'all')">All</button>
                        <button class="tab" onclick="filterTab(this,'admin')">Admin Plans</button>
                        <button class="tab" onclick="filterTab(this,'trainer')">Trainer Plans</button>
                    </div>
                </div>
                <div class="plans-toolbar-right">
                    <div class="plans-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="planSearch" placeholder="Search plans..."
                            oninput="searchPlans(this.value)" />
                    </div>
                    <select class="filter-select" onchange="filterCategory(this.value)">
                        <option value="">All Categories</option>
                        <option value="strength">Strength</option>
                        <option value="cardio">Cardio</option>
                        <option value="hybrid">Hybrid</option>
                        <option value="rehab">Rehab</option>
                        <option value="flexibility">Flexibility</option>
                    </select>
                    <select class="filter-select" onchange="filterStatus(this.value)">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="plans-card anim-fade-up anim-d2">
                <div class="plans-card-header">
                    <div>
                        <div class="plans-card-title"><i class="fa-solid fa-list-check"></i> Plans Overview</div>
                        <div class="plans-card-subtitle">All admin and trainer workout plans</div>
                    </div>
                    <span class="record-count-badge" id="recordCount">10 Records</span>
                </div>

                <div class="table-responsive">
                    <table class="plans-table" id="plansTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Plan Name</th>
                                <th class="col-cat">Category</th>
                                <th class="col-role">Created By</th>
                                <th>Assigned Members</th>
                                <th class="col-status">Status</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody id="plansBody">

                            <!-- <tr data-role="admin" data-cat="strength" data-status="active">
                                <td><span
                                        style="font-family:'Rajdhani',sans-serif;font-size:12px;color:var(--text-dim);font-weight:700;">01</span>
                                </td>
                                <td>
                                    <div class="plan-name-cell">
                                        <div class="plan-icon-box orange"><i class="fa-solid fa-fire-flame-curved"></i>
                                        </div>
                                        <div>
                                            <div class="plan-name-text">Iron Crusher Pro</div>
                                            <div class="plan-name-sub">Advanced strength training</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-cat"><span class="cat-badge cat-strength">Strength</span></td>
                                <td class="col-role"><span class="role-tag role-admin"><i
                                            class="fa-solid fa-shield-halved" style="font-size:9px;"></i> Admin</span>
                                </td>
                                <td>
                                    <div class="members-cell">
                                        <div class="mini-avatars">
                                            <div class="mini-av">AK</div>
                                            <div class="mini-av">BR</div>
                                            <div class="mini-av">ZA</div>
                                            <div class="mini-av-more">+</div>
                                        </div><span class="members-count">47</span>
                                    </div>
                                </td>
                                <td class="col-status"><span class="status-badge status-active">Active</span></td>
                                <td><span style="font-size:13px;color:var(--text-sec);">12 Weeks</span></td>
                            </tr> -->

                            <?php foreach ($plan_fetching as $row) { ?>
                                <tr data-role="trainer" data-cat="cardio" data-status="active">
                                    <td><span
                                            style="font-family:'Rajdhani',sans-serif;font-size:12px;color:var(--text-dim);font-weight:700;">
                                        <?php echo $row['id']; ?></span>
                                    </td>
                                    <td>
                                        <div class="plan-name-cell">
                                            <div class="plan-icon-box blue"><i class="fa-solid fa-person-running"></i></div>
                                            <div>
                                                <div class="plan-name-text"><?php echo $row['plan_name']; ?></div>
                                                <div class="plan-name-sub">High-intensity interval training</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-cat"><span
                                            class="cat-badge cat-cardio"><?php echo $row['category']; ?></span></td>
                                    <td class="col-role"><span class="role-tag role-trainer"><i class="fa-solid fa-dumbbell"
                                                style="font-size:9px;"></i> Trainer
                                            <?php echo $row['trainer_name']; ?></span></td>
                                    <td>
                                        <div class="members-cell">
                                            <div class="mini-avatars">
                                                <div class="mini-av">OR</div>
                                                <div class="mini-av">SA</div>
                                                <div class="mini-av-more">+</div>
                                            </div><span class="members-count"><?php echo $row['total_clients']; ?></span>
                                        </div>
                                    </td>
                                    <td class="col-status"><span
                                            class="status-badge status-active"><?php echo $row['status_name']; ?></span>
                                    </td>
                                    <td><span style="font-size:13px;color:var(--text-sec);"><?php echo $row['duration']; ?>
                                            Weeks</span></td>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="plans-empty" id="plansEmpty" style="display:none;">
                    <i class="fa-solid fa-list-check"></i>
                    <h3>No Plans Found</h3>
                    <p>Try adjusting your search or filter criteria.</p>
                </div>

                <div class="plans-pagination">
                    <div class="pag-info">Showing <span>1–10</span> of <span>18</span> plans</div>
                    <div class="pag-controls">
                        <button class="pag-btn" disabled><i class="fa-solid fa-chevron-left"
                                style="font-size:10px;"></i></button>
                        <button class="pag-btn active">1</button>
                        <button class="pag-btn">2</button>
                        <button class="pag-btn"><i class="fa-solid fa-chevron-right"
                                style="font-size:10px;"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Toast -->
    <div class="toast" id="toast" style="display:none;">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>
    <script>
        document.getElementById('todayDate').textContent = new Date().toLocaleDateString('en-US', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('open');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        }

        let currentRole = 'all', currentCat = '', currentStatus = '', currentSearch = '';

        function filterTab(el, role) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            currentRole = role;
            applyFilters();
        }
        function filterCategory(val) { currentCat = val; applyFilters(); }
        function filterStatus(val) { currentStatus = val; applyFilters(); }
        function searchPlans(val) { currentSearch = val.toLowerCase(); applyFilters(); }

        function applyFilters() {
            const rows = document.querySelectorAll('#plansBody tr');
            let visible = 0;
            rows.forEach(row => {
                const show =
                    (currentRole === 'all' || row.dataset.role === currentRole) &&
                    (!currentCat || row.dataset.cat === currentCat) &&
                    (!currentStatus || row.dataset.status === currentStatus) &&
                    (!currentSearch || row.textContent.toLowerCase().includes(currentSearch));
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            document.getElementById('plansEmpty').style.display = visible === 0 ? 'block' : 'none';
            document.getElementById('recordCount').textContent = visible + ' Record' + (visible !== 1 ? 's' : '');
        }

        document.querySelectorAll('.pag-btn:not([disabled])').forEach(btn => {
            btn.addEventListener('click', function () {
                if (!this.querySelector('i')) {
                    document.querySelectorAll('.pag-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
        const rows = document.querySelectorAll("#plansBody tr");
        const rowsPerPage = 5;
        const pagButtons = document.querySelectorAll(".pag-btn");
        let currentPage = 1;

        function showPage(page) {
            currentPage = page;

            let start = (page - 1) * rowsPerPage;
            let end = start + rowsPerPage;

            rows.forEach((row, index) => {
                if (index >= start && index < end) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });

            updateButtons();
        }

        function updateButtons() {
            document.querySelectorAll(".pag-btn").forEach(btn => btn.classList.remove("active"));

            let pageBtns = document.querySelectorAll(".pag-btn:not(:first-child):not(:last-child)");
            if (pageBtns[currentPage - 1]) {
                pageBtns[currentPage - 1].classList.add("active");
            }
        }

        pagButtons.forEach(btn => {
            btn.addEventListener("click", function () {

                if (this.innerText === "1") showPage(1);
                if (this.innerText === "2") showPage(2);

                if (this.querySelector(".fa-chevron-left")) {
                    if (currentPage > 1) showPage(currentPage - 1);
                }

                if (this.querySelector(".fa-chevron-right")) {
                    let totalPages = Math.ceil(rows.length / rowsPerPage);
                    if (currentPage < totalPages) showPage(currentPage + 1);
                }
            });
        });

        showPage(1);
    </script>
    <script src="./js/script.js"></script>
</body>

</html>