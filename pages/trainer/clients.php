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
$join = "
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id
";

$columns = [
    'users.*',
    'role.role AS role_name',
    'status.status AS status_name'
];

$clients = $controller->fetch_records('users', $columns, $join, ['users.trainer_id' => $user_id]);
// echo "<pre>";
// print_r($clients);
// echo "</pre>";
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Clients | GymFlow</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>
<style>
    /* ══════════════════════════════════════════════
           MY CLIENTS PAGE — TRAINER PANEL
        ══════════════════════════════════════════════ */

    /* ── PAGE HEADER STRIP ── */
    .clients-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 26px;
        flex-wrap: wrap;
    }

    .clients-title-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .clients-eyebrow {
        font-family: "Rajdhani", sans-serif;
        font-size: 11px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: var(--accent);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .clients-eyebrow::before {
        content: "";
        display: inline-block;
        width: 20px;
        height: 1.5px;
        background: var(--accent);
    }

    .clients-title {
        font-family: "Bebas Neue", sans-serif;
        font-size: 42px;
        letter-spacing: 3px;
        line-height: 1;
        color: var(--text-prime);
    }

    .clients-subtitle {
        font-size: 13px;
        color: var(--text-sec);
        margin-top: 2px;
    }

    .clients-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* ── FILTER / TOOLBAR ── */
    .clients-toolbar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .toolbar-search {
        position: relative;
        flex: 1;
        min-width: 200px;
    }

    .toolbar-search .s-icon {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-dim);
        font-size: 13px;
        pointer-events: none;
    }

    .toolbar-search input {
        width: 100%;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 10px 14px 10px 38px;
        font-size: 13px;
        color: var(--text-prime);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: "DM Sans", sans-serif;
    }

    .toolbar-search input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-dim);
    }

    .toolbar-search input::placeholder {
        color: var(--text-dim);
    }

    .toolbar-select {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 10px 34px 10px 14px;
        font-size: 13px;
        color: var(--text-sec);
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
        min-width: 148px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23555c70' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        font-family: "DM Sans", sans-serif;
    }

    .toolbar-select:focus {
        border-color: var(--accent);
        color: var(--text-prime);
    }

    .toolbar-count {
        font-family: "Rajdhani", sans-serif;
        font-size: 12px;
        letter-spacing: 1.5px;
        color: var(--text-dim);
        white-space: nowrap;
        padding: 0 4px;
    }

    /* ── CLIENTS TABLE CARD ── */
    .clients-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .clients-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .clients-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 620px;
    }

    /* Table head */
    .clients-table thead tr {
        background: var(--bg-panel);
        border-bottom: 1px solid var(--border);
    }

    .clients-table thead th {
        padding: 14px 18px;
        text-align: left;
        font-family: "Rajdhani", sans-serif;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--text-dim);
        white-space: nowrap;
        user-select: none;
    }

    .clients-table thead th.sortable {
        cursor: pointer;
        transition: color 0.18s;
    }

    .clients-table thead th.sortable:hover {
        color: var(--accent);
    }

    .clients-table thead th .sort-icon {
        display: inline-block;
        margin-left: 5px;
        font-size: 10px;
        opacity: 0.4;
        transition: opacity 0.18s;
    }

    .clients-table thead th.sortable:hover .sort-icon {
        opacity: 1;
    }

    .clients-table thead th.sorted {
        color: var(--accent);
    }

    .clients-table thead th.sorted .sort-icon {
        opacity: 1;
        color: var(--accent);
    }

    /* Table body */
    .clients-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.16s;
    }

    .clients-table tbody tr:last-child {
        border-bottom: none;
    }

    .clients-table tbody tr:hover {
        background: var(--bg-hover);
    }

    .clients-table tbody td {
        padding: 15px 18px;
        font-size: 13px;
        color: var(--text-sec);
        vertical-align: middle;
    }

    /* ── CLIENT IDENTITY CELL ── */
    .client-identity {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .client-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: "Bebas Neue", sans-serif;
        font-size: 14px;
        letter-spacing: 1px;
        color: #fff;
        flex-shrink: 0;
        position: relative;
    }

    /* Cycling avatar accent colors */
    .client-avatar.av-0 {
        background: linear-gradient(135deg, #ff4500, #c23500);
    }

    .client-avatar.av-1 {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .client-avatar.av-2 {
        background: linear-gradient(135deg, #22c55e, #15803d);
    }

    .client-avatar.av-3 {
        background: linear-gradient(135deg, #f59e0b, #b45309);
    }

    .client-avatar.av-4 {
        background: linear-gradient(135deg, #a855f7, #7e22ce);
    }

    .client-avatar.av-5 {
        background: linear-gradient(135deg, #ec4899, #9d174d);
    }

    .client-avatar.av-6 {
        background: linear-gradient(135deg, #06b6d4, #0e7490);
    }

    .client-avatar.av-7 {
        background: linear-gradient(135deg, #84cc16, #3f6212);
    }

    .client-name-group {}

    .client-fullname {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-prime);
        line-height: 1.3;
    }

    .client-id-tag {
        font-family: "Rajdhani", sans-serif;
        font-size: 11px;
        letter-spacing: 1.5px;
        color: var(--text-dim);
        margin-top: 2px;
    }

    /* ── USERNAME CELL ── */
    .username-cell {
        display: flex;
        align-items: center;
        gap: 7px;
        color: var(--text-sec);
    }

    .username-cell .at-sign {
        color: var(--accent);
        font-size: 12px;
        font-weight: 600;
    }

    /* ── EMAIL CELL ── */
    .email-cell {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-sec);
        font-size: 13px;
    }

    .email-cell i {
        font-size: 12px;
        color: var(--text-dim);
        flex-shrink: 0;
    }

    .email-link {
        color: var(--text-sec);
        text-decoration: none;
        transition: color 0.18s;
    }

    .email-link:hover {
        color: var(--accent);
        text-decoration: underline;
    }

    /* ── STATUS BADGES ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 11px;
        border-radius: 100px;
        font-family: "Rajdhani", sans-serif;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-badge.active {
        background: var(--green-dim);
        color: var(--green);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .status-badge.active .status-dot {
        background: var(--green);
        animation: pulse 2s infinite;
    }

    .status-badge.inactive {
        background: rgba(85, 92, 112, 0.12);
        color: var(--text-dim);
        border: 1px solid rgba(85, 92, 112, 0.25);
    }

    .status-badge.inactive .status-dot {
        background: var(--text-dim);
    }

    .status-badge.trial {
        background: var(--blue-dim);
        color: var(--blue);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .status-badge.trial .status-dot {
        background: var(--blue);
    }

    .status-badge.suspended {
        background: var(--red-dim);
        color: var(--red);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .status-badge.suspended .status-dot {
        background: var(--red);
    }

    /* ── ROW ACTIONS ── */
    .row-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }

    .row-action-btn {
        width: 30px;
        height: 30px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: transparent;
        color: var(--text-dim);
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.18s;
        flex-shrink: 0;
    }

    .row-action-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-dim);
    }

    .row-action-btn.danger:hover {
        border-color: var(--red);
        color: var(--red);
        background: var(--red-dim);
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 64px 24px;
        text-align: center;
        display: none;
    }

    .empty-state.show {
        display: flex;
    }

    .empty-icon-wrap {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--accent-dim);
        border: 1px solid rgba(255, 69, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: var(--accent);
        margin-bottom: 20px;
    }

    .empty-title {
        font-family: "Bebas Neue", sans-serif;
        font-size: 28px;
        letter-spacing: 2px;
        color: var(--text-prime);
        margin-bottom: 8px;
    }

    .empty-desc {
        font-size: 13px;
        color: var(--text-sec);
        max-width: 300px;
        line-height: 1.6;
    }

    /* ── PAGINATION ── */
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-top: 1px solid var(--border);
        gap: 12px;
        flex-wrap: wrap;
    }

    .pagination-info {
        font-size: 12px;
        color: var(--text-dim);
        white-space: nowrap;
    }

    .pagination-info strong {
        color: var(--text-sec);
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .page-btn {
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        border: 1px solid var(--border);
        background: var(--bg-card);
        color: var(--text-sec);
        transition: all 0.18s;
        font-family: "Rajdhani", sans-serif;
        font-weight: 600;
        letter-spacing: 0.5px;
        user-select: none;
    }

    .page-btn:hover:not(:disabled):not(.active) {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-dim);
    }

    .page-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        box-shadow: 0 2px 12px rgba(255, 69, 0, 0.35);
    }

    .page-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }

    .page-btn.nav-btn {
        font-size: 12px;
    }

    .page-ellipsis {
        min-width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: var(--text-dim);
        letter-spacing: 2px;
    }

    .per-page-wrap {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 12px;
        color: var(--text-dim);
    }

    .per-page-select {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 5px 26px 5px 10px;
        font-size: 12px;
        color: var(--text-sec);
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='7' viewBox='0 0 10 7'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%23555c70' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        font-family: "DM Sans", sans-serif;
    }

    .per-page-select:focus {
        border-color: var(--accent);
    }

    /* ── SIDEBAR HEADER LOGO VARIANT ── */
    .sidebar-header {
        padding: 22px 22px 18px;
        border-bottom: 1px solid var(--border);
    }

    .logo {
        font-family: "Bebas Neue", sans-serif;
        font-size: 24px;
        letter-spacing: 3px;
        color: var(--text-prime);
    }

    .logo span {
        color: var(--accent);
    }

    /* ── LOGOUT LINK ── */
    .logout-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: var(--radius-md);
        border: 1px solid rgba(239, 68, 68, 0.2);
        background: var(--red-dim);
        color: var(--red);
        font-size: 13px;
        font-weight: 500;
        width: 100%;
        cursor: pointer;
        transition: all 0.18s;
    }

    .logout-link:hover {
        border-color: var(--red);
        background: rgba(239, 68, 68, 0.18);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .summary-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .clients-header {
            flex-direction: column;
            gap: 16px;
        }

        .clients-toolbar {
            gap: 10px;
        }

        .toolbar-search {
            min-width: 100%;
        }

        .toolbar-select {
            flex: 1;
            min-width: 0;
        }

        .toolbar-count {
            display: none;
        }

        .per-page-wrap {
            display: none;
        }

        .pagination-bar {
            justify-content: center;
        }
    }

    @media (max-width: 560px) {
        .summary-strip {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .clients-title {
            font-size: 32px;
        }

        .pagination-info {
            display: none;
        }
    }

    @media (max-width: 380px) {
        .summary-strip {
            grid-template-columns: 1fr;
        }
    }
</style>

<body class="dashboard-body">

    <!-- ══════════════ SIDEBAR ══════════════ -->
    <?php require_once 'trainer-sidebar.php'; ?>

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ══════════════ MAIN WRAP ══════════════ -->
    <div class="dashboard-container">
        <main class="main-wrap">

            <!-- ── TOPBAR ── -->
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

            <!-- ── CONTENT BODY ── -->
            <div class="content-body anim-fade-up">

                <!-- ── PAGE HEADER ── -->
                <div class="clients-header">
                    <div class="clients-title-block">
                        <span class="clients-eyebrow">Trainer Panel</span>
                        <h2 class="clients-title">My Clients</h2>
                        <p class="clients-subtitle">Manage and monitor all clients assigned to you</p>
                    </div>
                </div>

                <!-- ── TABLE CARD ── -->
                <div class="clients-card">

                    <!-- Toolbar -->
                    <div style="padding: 18px 20px 0;">
                        <div class="clients-toolbar">
                            <div class="toolbar-search">
                                <i class="fas fa-search s-icon"></i>
                                <input type="text" id="clientSearch" placeholder="Search by name, username or email…"
                                    autocomplete="off">
                            </div>
                            <select class="toolbar-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="trial">Trial</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            <span class="toolbar-count" id="rowCount">Showing 10 of 42</span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="clients-table-wrap">
                        <table class="clients-table" id="clientsTable">
                            <thead>
                                <tr>
                                    <th class="sortable sorted" data-col="name">
                                        Full Name <span class="sort-icon"><i class="fas fa-sort-up"></i></span>
                                    </th>
                                    <th class="sortable" data-col="username">
                                        Username <span class="sort-icon"><i class="fas fa-sort"></i></span>
                                    </th>
                                    <th class="sortable" data-col="email">
                                        Email <span class="sort-icon"><i class="fas fa-sort"></i></span>
                                    </th>
                                    <th class="sortable" data-col="status">
                                        Status <span class="sort-icon"><i class="fas fa-sort"></i></span>
                                    </th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="clientsBody">
                            </tbody>
                        </table>

                        <!-- Empty state -->
                        <div class="empty-state" id="emptyState">
                            <div class="empty-icon-wrap"><i class="fas fa-users-slash"></i></div>
                            <div class="empty-title">No Clients Found</div>
                            <p class="empty-desc">No clients match your current search or filter. Try adjusting your
                                criteria.</p>
                        </div>
                    </div>

                    <!-- Pagination Bar -->
                    <div class="pagination-bar" id="paginationBar">
                        <div class="pagination-info" id="paginationInfo">
                            Showing <strong>1–10</strong> of <strong>42</strong> clients
                        </div>
                        <div class="pagination-controls" id="paginationControls">
                            <!-- Rendered by JS -->
                        </div>
                        <div class="per-page-wrap">
                            <span>Rows per page</span>
                            <select class="per-page-select" id="perPage">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                </div><!-- /clients-card -->

            </div><!-- /content-body -->
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
<!-- ══════════════ SCRIPT ══════════════ -->
<script>
    const ALL_CLIENTS = <?php echo json_encode(array_map(function ($c) {
        return [
            "id" => $c["id"],
            "name" => $c["fullname"],
            "username" => $c["username"],
            "email" => $c["email"],
            "status_name" => $c["status_name"]
        ];
    }, $clients)); ?>;

    // console.log(ALL_CLIENTS);


    /* ── STATE ── */
    let currentPage = 1;
    let perPage = 10;
    let searchQuery = '';
    let statusFilter = '';
    let sortCol = 'name';
    let sortDir = 'asc';

    /* ── HELPERS ── */
    function getInitials(name) {
        const parts = name.trim().split(' ');
        return (parts[0][0] + (parts[1] ? parts[1][0] : '')).toUpperCase();
    }

    function statusBadgeHTML(status) {
        const map = {
            active: ['active', 'Active'],
            trial: ['trial', 'Trial'],
            inactive: ['inactive', 'Inactive'],
            suspended: ['suspended', 'Suspended'],
        };
        const [cls, label] = map[status] || ['inactive', status];
        return `<span class="status-badge ${cls}"><span class="status-dot"></span>${label}</span>`;
    }

    /* ── FILTER + SORT ── */
    function getFiltered() {
        let data = [...ALL_CLIENTS];

        if (searchQuery) {
            const q = searchQuery.toLowerCase();
            data = data.filter(c =>
                c.name.toLowerCase().includes(q) ||
                c.username.toLowerCase().includes(q) ||
                c.email.toLowerCase().includes(q)
            );
        }

        if (statusFilter) {
            data = data.filter(c => c.status === statusFilter);
        }

        data.sort((a, b) => {
            let av = a[sortCol] || '', bv = b[sortCol] || '';
            av = av.toLowerCase(); bv = bv.toLowerCase();
            if (av < bv) return sortDir === 'asc' ? -1 : 1;
            if (av > bv) return sortDir === 'asc' ? 1 : -1;
            return 0;
        });

        return data;
    }

    /* ── RENDER TABLE ── */
    function renderTable() {
        const filtered = getFiltered();
        const total = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));

        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * perPage;
        const end = Math.min(start + perPage, total);
        const page = filtered.slice(start, end);

        const tbody = document.getElementById('clientsBody');
        const empty = document.getElementById('emptyState');
        const rowCount = document.getElementById('rowCount');
        const paginationInfo = document.getElementById('paginationInfo');

        rowCount.textContent = `Showing ${total > 0 ? start + 1 : 0}–${end} of ${total}`;
        paginationInfo.innerHTML = total > 0
            ? `Showing <strong>${start + 1}–${end}</strong> of <strong>${total}</strong> clients`
            : `No clients found`;

        if (page.length === 0) {
            tbody.innerHTML = '';
            empty.classList.add('show');
            document.getElementById('paginationBar').style.display = 'none';
        } else {
            empty.classList.remove('show');
            document.getElementById('paginationBar').style.display = '';

            tbody.innerHTML = page.map((c, i) => {
                const av = (start + i) % 8;
                return `
                    <tr>
                        <td>
                            <div class="client-identity">
                                <div class="client-avatar av-${av}">${getInitials(c.name)}</div>
                                <div class="client-name-group">
                                    <div class="client-fullname">${c.name}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="username-cell">
                                <span class="at-sign">@</span>
                                <span>${c.username}</span>
                            </div>
                        </td>
                        <td>
                            <div class="email-cell">
                                <i class="far fa-envelope"></i>
                                <a class="email-link" href="mailto:${c.email}">${c.email}</a>
                            </div>
                        </td>
                        <td>${statusBadgeHTML(c.status_name)}</td>
                        
                    </tr>`;
            }).join('');
        }

        renderPagination(currentPage, totalPages);
    }

    function renderPagination(cur, total) {
        const ctrl = document.getElementById('paginationControls');

        let pages = [];
        if (total <= 7) {
            for (let i = 1; i <= total; i++) pages.push(i);
        } else {
            pages = [1];
            if (cur > 3) pages.push('...');
            for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
            if (cur < total - 2) pages.push('...');
            pages.push(total);
        }

        ctrl.innerHTML = `
                <button class="page-btn nav-btn" onclick="goPage(${cur - 1})" ${cur === 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>
                ${pages.map(p => p === '...'
            ? `<span class="page-ellipsis">…</span>`
            : `<button class="page-btn ${p === cur ? 'active' : ''}" onclick="goPage(${p})">${p}</button>`
        ).join('')}
                <button class="page-btn nav-btn" onclick="goPage(${cur + 1})" ${cur === total ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
    }

    function goPage(n) {
        const filtered = getFiltered();
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (n < 1 || n > totalPages) return;
        currentPage = n;
        renderTable();
    }

    document.querySelectorAll('.clients-table thead th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.col;
            if (sortCol === col) {
                sortDir = sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                sortCol = col;
                sortDir = 'asc';
            }

            document.querySelectorAll('.clients-table thead th.sortable').forEach(h => {
                h.classList.remove('sorted');
                h.querySelector('.sort-icon').innerHTML = '<i class="fas fa-sort"></i>';
            });
            th.classList.add('sorted');
            th.querySelector('.sort-icon').innerHTML = sortDir === 'asc'
                ? '<i class="fas fa-sort-up"></i>'
                : '<i class="fas fa-sort-down"></i>';

            currentPage = 1;
            renderTable();
        });
    });

    document.getElementById('clientSearch').addEventListener('input', function () {
        searchQuery = this.value.trim();
        currentPage = 1;
        renderTable();
    });

    document.getElementById('statusFilter').addEventListener('change', function () {
        statusFilter = this.value;
        currentPage = 1;
        renderTable();
    });

    document.getElementById('perPage').addEventListener('change', function () {
        perPage = parseInt(this.value);
        currentPage = 1;
        renderTable();
    });

    // const sidebar = document.getElementById('sidebar');
    // const overlay = document.getElementById('sidebarOverlay');
    // document.getElementById('menuToggle').addEventListener('click', () => {
    //     sidebar.classList.toggle('open');
    //     overlay.classList.toggle('open');
    // });
    // overlay.addEventListener('click', () => {
    //     sidebar.classList.remove('open');
    //     overlay.classList.remove('open');
    // });

    renderTable();
</script>
<!-- <td>
                            <div class="row-actions">
                                <button class="row-action-btn" title="View Profile"><i class="fas fa-eye"></i></button>
                                <button class="row-action-btn" title="Edit Client"><i class="fas fa-pen"></i></button>
                                <button class="row-action-btn" title="Schedule Session"><i class="fas fa-calendar-plus"></i></button>
                                <button class="row-action-btn danger" title="Remove Client"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td> -->

</html>