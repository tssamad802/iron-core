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
$total_users = $controller->count('users');
$join = "
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id
LEFT JOIN payment ON users.id = payment.member_id";
$history_join = "
INNER JOIN users ON history.member_id = users.id
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id";
$columns = [
  'users.*',
  'payment.member_amount',
  'payment.trainer_amount',
  'payment.payment_status',
  'payment.month',
  'payment.year',
  'role.role AS role_name',
  'status.status AS status_name'
];
$history_columns = [
  'history.*',
  'users.fullname',
  'users.email',
  'role.role AS role_name',
  'status.status AS status_name'
];
$pending_members = $controller->fetch_records('users', $columns, $join, ['payment_status' => 'pending']);
$history = $controller->fetch_records('history', $history_columns, $history_join, [], 5);
// echo '<pre>';
// print_r($history);
// echo '</pre>';
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IronCore Gym — Member Fees</title>
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="dashboard-body">

  <!-- Sidebar overlay (mobile) -->
  <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

  <!-- ══════════════ SIDEBAR ══════════════ -->
  <?php
  require_once 'sidebar.php';
  ?>
  <!-- ══════════════ /SIDEBAR ══════════════ -->


  <!-- ══════════════ MAIN AREA ══════════════ -->
  <div class="main-area">

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

      <!-- Page header -->
      <div class="page-header anim-fade-up">
        <div class="page-title-block">
          <div class="title">Member Fees</div>
          <div class="subtitle" id="todayDate"></div>
        </div>
        <div>
          <button class="btn-export">
            <i class="fa-solid fa-file-csv"></i> Export CSV
          </button>
        </div>
      </div>

      <!-- ═══════════════════════════════════
           FEE FORM CARD
      ═══════════════════════════════════ -->
      <div class="fee-form-card anim-fade-up anim-d1">

        <div class="fee-form-header">
          <div class="fee-form-icon">
            <i class="fa-solid fa-receipt"></i>
          </div>
          <div>
            <div class="fee-form-title">Record Fee Payment</div>
            <div class="fee-form-subtitle">Select a member and mark their payment status</div>
          </div>
          <div class="fee-form-badge">
            <span class="pulse-dot"></span>
            Entry
          </div>
        </div>

        <form class="fee-form-body" id="feeForm" method="POST" action="./payment-script">

          <div class="fee-form-grid">

            <!-- Dropdown 1 — Member -->
            <div class="ffe-group">
              <label class="ffe-label" for="selectMember">
                Member <span class="req">*</span>
              </label>
              <div class="ffe-select-wrap">
                <i class="fa-solid fa-user ffe-icon"></i>
                <select class="ffe-select" id="selectMember" name="member_id">
                  <option value="" disabled selected>— Choose a member —</option>
                  <?php
                  foreach ($pending_members as $row) { ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['fullname']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <span class="ffe-hint">
                <i class="fa-solid fa-circle-info"></i>
                Select the member whose fee is being recorded
              </span>
            </div>

            <!-- Dropdown 2 — Fee Status -->
            <div class="ffe-group">
              <label class="ffe-label" for="selectStatus">
                Fee Status <span class="req">*</span>
              </label>
              <div class="ffe-select-wrap">
                <i class="fa-solid fa-circle-half-stroke ffe-icon"></i>
                <select class="ffe-select" id="selectStatus" name="fee_status">
                  <option value="" disabled selected>— Mark fee as —</option>
                  <option value="received">✔ Received</option>
                </select>
              </div>
              <span class="ffe-hint">
                <i class="fa-solid fa-circle-info"></i>
                Mark whether the fee has been received or is still pending
              </span>
            </div>

          </div><!-- /grid -->

          <div class="ffe-divider">
            <div class="ffe-divider-line"></div>
            <span class="ffe-divider-text">Confirmation</span>
            <div class="ffe-divider-line"></div>
          </div>

        </form>

        <div class="fee-form-footer">
          <div class="fee-form-actions">
            <button type="submit" form="feeForm" class="btn-ffe-submit">
              <i class="fa-solid fa-floppy-disk"></i>
              Save Record
            </button>
          </div>
          <?php
          $view->showErrors();
          ?>
        </div>

      </div><!-- /fee-form-card -->

    </div><!-- /page-area -->

    <!-- ═══════════════════════════════════
     PAYMENT HISTORY SECTION
═══════════════════════════════════ -->
    <!-- Header -->
    <div class="ph-header">
      <div class="ph-header-left">
        <div class="ph-icon">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
          <div class="ph-title">Payment History</div>
          <div class="ph-subtitle">All recorded member fee transactions</div>
        </div>
      </div>

      <div class="ph-header-right">
        <span class="ph-count-badge">12 Records</span>

        <!-- Search -->
        <div class="ph-search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" placeholder="Search member..." />
        </div>

        <!-- Export -->
        <button class="ph-export-btn">
          <i class="fa-solid fa-file-csv"></i> Export
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="ph-table-wrap">
      <table class="ph-table">
        <thead>
          <tr>
            <th>
              <span class="th-inner">
                Member
                <i class="fa-solid fa-chevron-down sort-arrow"></i>
              </span>
            </th>
            <th>
              <span class="th-inner">
                Amount
                <i class="fa-solid fa-chevron-down sort-arrow"></i>
              </span>
            </th>
            <th class="ph-col-period">Period</th>
            <th class="ph-col-date">
              <span class="th-inner">
                Recorded At
                <i class="fa-solid fa-chevron-down sort-arrow"></i>
              </span>
            </th>
          </tr>
        </thead>
        <tbody>

          <!-- Row 1 – Received -->
          <?php
          foreach ($history as $row) { ?>
            <tr>
              <td>
                <div class="ph-member-cell">
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
                    <div class="ph-member-name"><?php echo $row['fullname']; ?></div>
                    <div class="ph-member-id">#MBR-0012</div>
                  </div>
                </div>
              </td>
              <td><span class="ph-amount received"><?php echo $row['member_amount']; ?></span></td>
              <td class="ph-col-period">
                <span class="ph-period">
                  <i class="fa-regular fa-calendar" style="font-size:9px;"></i>
                  <?php echo date('M Y', strtotime($row['created_at'])); ?>
                </span>
              </td>
              <td class="ph-col-date">
                <div class="ph-date">
                  <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                  <div class="ph-date-time"><?php echo date('h:i A', strtotime($row['created_at'])); ?></div>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div class="ph-pagination">
      <div class="ph-pag-info">
        Showing <strong>1–7</strong> of <strong>12</strong> records
      </div>

      <div class="ph-pag-controls">
        <!-- Prev -->
        <button class="ph-pag-btn disabled" title="Previous page">
          <i class="fa-solid fa-chevron-left" style="font-size:10px;"></i>
        </button>

        <!-- Page numbers -->
        <button class="ph-pag-btn active">1</button>
        <button class="ph-pag-btn">2</button>
        <span class="ph-pag-dots">···</span>
        <button class="ph-pag-btn">5</button>

        <!-- Next -->
        <button class="ph-pag-btn" title="Next page">
          <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
        </button>
      </div>
    </div>

  </div><!-- /ph-card -->
  </section>

  </div><!-- /main-area -->
  <!-- TOAST -->
  <div class="toast" id="toast" style="display:none;">
    <span class="toast-icon" id="toastIcon">⚡</span>
    <div>
      <div class="toast-title" id="toastTitle"></div>
      <div class="toast-sub" id="toastSub"></div>
    </div>
  </div>
  <script>
    /**
 * IronCore Gym — Member Fees Page
 * Features: Pagination, Amount Formatting, Real-time Search, Dynamic Record Count
 */

    (function () {
      "use strict";

      /* ─────────────────────────────────────────
         CONFIG
      ───────────────────────────────────────── */
      const ROWS_PER_PAGE = 7;

      /* ─────────────────────────────────────────
         DOM REFERENCES
      ───────────────────────────────────────── */
      const tbody = document.querySelector(".ph-table tbody");
      const searchInput = document.querySelector(".ph-search input");
      const countBadge = document.querySelector(".ph-count-badge");
      const pagInfo = document.querySelector(".ph-pag-info");
      const pagControls = document.querySelector(".ph-pag-controls");

      if (!tbody) return; // guard: page not ready

      /* ─────────────────────────────────────────
         1. AMOUNT FORMATTING
         100 → "100" | 1000 → "1K" | 15000 → "15K" | 1500000 → "1.5M"
      ───────────────────────────────────────── */
      function formatAmount(raw) {
        // Strip non-numeric chars (e.g. currency symbols, commas) but keep decimals
        const num = parseFloat(String(raw).replace(/[^0-9.]/g, ""));
        if (isNaN(num)) return raw;

        if (num >= 1_000_000) {
          const val = num / 1_000_000;
          return (val % 1 === 0 ? val : val.toFixed(1)) + "M";
        }
        if (num >= 1_000) {
          const val = num / 1_000;
          return (val % 1 === 0 ? val : val.toFixed(1)) + "K";
        }
        return String(num % 1 === 0 ? num : num.toFixed(2));
      }

      // Apply formatting to every amount cell on load
      document.querySelectorAll(".ph-amount").forEach((cell) => {
        cell.textContent = formatAmount(cell.textContent.trim());
      });

      /* ─────────────────────────────────────────
         2. SNAPSHOT ALL ROWS (after formatting)
      ───────────────────────────────────────── */
      // Store original rows so search never loses data
      const allRows = Array.from(tbody.querySelectorAll("tr"));

      /* ─────────────────────────────────────────
         STATE
      ───────────────────────────────────────── */
      let filteredRows = [...allRows];
      let currentPage = 1;

      /* ─────────────────────────────────────────
         3. RENDER — shows the right page slice
      ───────────────────────────────────────── */
      function render() {
        const total = filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));

        // Clamp currentPage
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * ROWS_PER_PAGE; // 0-based
        const end = Math.min(start + ROWS_PER_PAGE, total);

        // Hide/show rows
        allRows.forEach((row) => (row.style.display = "none"));
        filteredRows.slice(start, end).forEach((row) => (row.style.display = ""));

        // ── Dynamic record count badge ──
        countBadge.textContent = total + " Record" + (total !== 1 ? "s" : "");

        // ── Pagination info text ──
        if (total === 0) {
          pagInfo.innerHTML = "No records found";
        } else {
          pagInfo.innerHTML =
            `Showing <strong>${start + 1}–${end}</strong> of <strong>${total}</strong> record${total !== 1 ? "s" : ""}`;
        }

        // ── Pagination buttons ──
        buildPagination(totalPages);
      }

      /* ─────────────────────────────────────────
         4. BUILD PAGINATION CONTROLS
      ───────────────────────────────────────── */
      function buildPagination(totalPages) {
        pagControls.innerHTML = ""; // clear

        function btn(label, page, extra = "") {
          const b = document.createElement("button");
          b.className = "ph-pag-btn" + extra;
          b.innerHTML = label;
          if (page !== null) {
            b.addEventListener("click", () => {
              currentPage = page;
              render();
            });
          }
          return b;
        }

        // ← Prev
        const prev = btn('<i class="fa-solid fa-chevron-left" style="font-size:10px;"></i>', currentPage - 1);
        if (currentPage === 1) prev.classList.add("disabled"), prev.setAttribute("disabled", "");
        pagControls.appendChild(prev);

        // Page numbers with ellipsis logic
        const pages = getPageRange(currentPage, totalPages);
        pages.forEach((p) => {
          if (p === "…") {
            const dots = document.createElement("span");
            dots.className = "ph-pag-dots";
            dots.textContent = "···";
            pagControls.appendChild(dots);
          } else {
            const active = p === currentPage ? " active" : "";
            pagControls.appendChild(btn(p, p, active));
          }
        });

        // → Next
        const next = btn('<i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>', currentPage + 1);
        if (currentPage === totalPages) next.classList.add("disabled"), next.setAttribute("disabled", "");
        pagControls.appendChild(next);
      }

      function getPageRange(current, total) {
        if (total <= 5) return Array.from({ length: total }, (_, i) => i + 1);

        const range = new Set([1, total, current]);
        if (current > 1) range.add(current - 1);
        if (current < total) range.add(current + 1);

        const sorted = [...range].sort((a, b) => a - b);
        const result = [];
        sorted.forEach((p, i) => {
          if (i > 0 && p - sorted[i - 1] > 1) result.push("…");
          result.push(p);
        });
        return result;
      }

      /* ─────────────────────────────────────────
         5. REAL-TIME SEARCH
      ───────────────────────────────────────── */
      if (searchInput) {
        searchInput.addEventListener("input", () => {
          const q = searchInput.value.trim().toLowerCase();

          filteredRows = q
            ? allRows.filter((row) => {
              return row.textContent.toLowerCase().includes(q);
            })
            : [...allRows];

          currentPage = 1;
          render();
        });
      }

      /* ─────────────────────────────────────────
         INIT
      ───────────────────────────────────────── */
      render();
    })();
    // Date in subtitle
    document.getElementById('todayDate').textContent =
      new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

    // Sidebar toggle (mobile)
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('overlay').classList.toggle('open');
    }
    function closeSidebar() {
      document.getElementById('sidebar').classList.remove('open');
      document.getElementById('overlay').classList.remove('open');
    }
  </script>

</body>
<script src="./js/script.js"></script>

</html>