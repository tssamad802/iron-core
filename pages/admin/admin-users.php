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
    <link rel="stylesheet" href="./css/theme.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
    <?php
    require_once 'sidebar.php';
    ?>

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
    <script>
        (function () {
            /* ══════════════════════════════════════
               ALL ROWS DATA — injected from PHP
               (read-only, no backend logic changed)
            ══════════════════════════════════════ */
            const ALL_USERS = <?php
            // Re-fetch ALL users without limit for JS dataset
            $all_members = $controller->fetch_records('users', $columns, $join, [], 999999, 0);
            $js_data = [];
            foreach ($all_members as $r) {
                $name = $r['fullname'];
                $words = explode(' ', trim($name));
                $inits = strtoupper(substr($words[0], 0, 1));
                if (count($words) > 1)
                    $inits .= strtoupper(substr($words[1], 0, 1));

                $showNotify = (
                    strtolower($r['role_name']) !== 'admin' &&
                    strtolower($r['role_name']) !== 'trainer' &&
                    strtolower($r['payment_status']) === 'pending'
                );

                $js_data[] = [
                    'initials' => $inits,
                    'username' => htmlspecialchars($r['username'], ENT_QUOTES),
                    'email' => htmlspecialchars($r['email'], ENT_QUOTES),
                    'status_name' => htmlspecialchars($r['status_name'], ENT_QUOTES),
                    'created_at' => date('Y-m-d', strtotime($r['created_at'])),
                    'edit_url' => './admin-edit-users?id=' . (int) $r['id'],
                    'delete_url' => './delete-record?id=' . (int) $r['id'],
                    'show_notify' => $showNotify,
                ];
            }
            echo json_encode($js_data, JSON_HEX_TAG | JSON_HEX_QUOT);
            ?>;

            const LIMIT = 5;
            let filtered = [...ALL_USERS];
            let currentPage = 1;

            const tbody = document.getElementById('members_body');
            const paginationEl = document.querySelector('.pagination');

            /* ── INJECT SEARCH INPUT ── */
            const filterCard = document.querySelector('.card.anim-fade-up.anim-d1');
            if (filterCard) {
                const flexDiv = filterCard.querySelector('.flex-between');
                const wrap = document.createElement('div');
                wrap.style.cssText = 'position:relative;min-width:220px;flex:1;max-width:300px;';
                wrap.innerHTML = `
      <i class="fa-solid fa-magnifying-glass"
         style="position:absolute;left:12px;top:50%;transform:translateY(-50%);
                color:var(--text-dim);font-size:13px;pointer-events:none;z-index:1;"></i>
      <input id="js_search" type="text" placeholder="Search by name or email…"
        style="width:100%;background:var(--bg-card);border:1px solid var(--border);
               border-radius:var(--radius-md);padding:9px 14px 9px 36px;font-size:13px;
               color:var(--text-prime);outline:none;font-family:'DM Sans',sans-serif;
               transition:border-color .2s;" autocomplete="off" />`;
                flexDiv.appendChild(wrap);
                const inp = wrap.querySelector('input');
                inp.addEventListener('focus', () => inp.style.borderColor = 'var(--accent)');
                inp.addEventListener('blur', () => inp.style.borderColor = 'var(--border)');
                inp.addEventListener('input', onSearch);
            }

            /* ── RENDER ROWS ── */
            function renderRows() {
                const start = (currentPage - 1) * LIMIT;
                const slice = filtered.slice(start, start + LIMIT);

                tbody.innerHTML = slice.map(u => `
      <tr>
        <td>
          <div class="member-cell">
            <div class="avatar avatar-sm">${u.initials}</div>
            <div>
              <div class="member-name">${u.username}</div>
              <div class="member-email">${u.email}</div>
            </div>
          </div>
        </td>
        <td><span class="badge badge-active">${u.status_name}</span></td>   
        <td>${u.created_at}</td>
        <td style="text-align:right;">
          <a href="${u.edit_url}"   class="table-link">Edit</a>
          <a href="${u.delete_url}" class="table-link">Delete</a>
          ${u.show_notify
                        ? `<button class="fee-notify-btn"><i class="fa-solid fa-bell"></i> Notify</button>`
                        : ''}
        </td>
      </tr>`).join('');
            }

            /* ── RENDER PAGINATION ── */
            function renderPagination() {
                if (!paginationEl) return;
                const total = filtered.length;
                const totalPages = Math.max(1, Math.ceil(total / LIMIT));
                const start = total === 0 ? 0 : (currentPage - 1) * LIMIT + 1;
                const end = Math.min(currentPage * LIMIT, total);

                paginationEl.innerHTML = `
      <span class="page-info">Showing ${start}–${end} of ${total} users</span>
      <div class="page-btns" id="js_btns"></div>`;

                const btnsEl = paginationEl.querySelector('#js_btns');

                /* prev */
                btnsEl.appendChild(makeBtn('<i class="fa-solid fa-chevron-left"></i>',
                    currentPage <= 1, false, () => go(currentPage - 1)));

                /* numbered pages */
                for (let i = 1; i <= totalPages; i++) {
                    btnsEl.appendChild(makeBtn(i, false, i === currentPage, () => go(i)));
                }

                /* next */
                btnsEl.appendChild(makeBtn('<i class="fa-solid fa-chevron-right"></i>',
                    currentPage >= totalPages, false, () => go(currentPage + 1)));
            }

            function makeBtn(html, disabled, active, onClick) {
                const btn = document.createElement('button');
                btn.className = 'page-btn' + (active ? ' active' : '');
                btn.innerHTML = html;
                btn.disabled = disabled;
                if (!disabled && onClick) btn.addEventListener('click', onClick);
                return btn;
            }

            function go(page) {
                const totalPages = Math.max(1, Math.ceil(filtered.length / LIMIT));
                currentPage = Math.min(Math.max(1, page), totalPages);
                renderRows();
                renderPagination();
            }

            /* ── SEARCH ── */
            function onSearch(e) {
                const q = e.target.value.trim().toLowerCase();
                filtered = q
                    ? ALL_USERS.filter(u =>
                        u.username.toLowerCase().includes(q) ||
                        u.email.toLowerCase().includes(q))
                    : [...ALL_USERS];
                currentPage = 1;
                renderRows();
                renderPagination();
            }

            /* ── BOOT ── */
            go(1);

        })();
    </script>
    <script src="./js/script.js"></script>
    <script src="./js/server.js"></script>
    <script src="./js/theme.js"></script>
</body>

</html>