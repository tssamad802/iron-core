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

$join = "
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id
";

$columns = [
    'users.*',
    'role.role AS role_name',
    'status.status AS status_name'
];
$total_users = $controller->count('users');
$members = $controller->fetch_records('users', $columns, $join, ['users.role' => 2]);
$trainers = $controller->fetch_records('users', $columns, $join, ['users.role' => 3]);
$trainer_lookup = [];
foreach ($trainers as $trainer) {
    $trainer_lookup[$trainer['id']] = $trainer['fullname'];
}
foreach ($members as &$member) {
    $tid = $member['trainer_id'];
    $member['trainer_name'] = $tid ? ($trainer_lookup[$tid] ?? 'N/A') : 'N/A';
    unset($member['trainer_id']);
}
$total_members = $controller->count('users', ['role' => 2]);
// echo '<pre>';
// print_r($members);
// echo '</pre>';
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore Gym — Member Management</title>
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
                    <div class="title">Member Management</div>
                    <div class="subtitle">Assign trainers to members and review existing pairings</div>
                </div>
                <div class="page-actions">
                    <button class="btn btn-secondary" onclick="openQuickAssignModal()">
                        <i class="fa-solid fa-user-plus"></i>
                        Quick Assign
                    </button>
                    <a href="./admin-add-users">
                        <button class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i>
                            Add Member
                        </button>
                    </a>
                </div>
            </div>



            <!-- MAIN TABLE CARD -->
            <div class="card anim-fade-up anim-d2">
                <div class="card-header">
                    <div>
                        <div class="card-title">All Members</div>
                        <div class="card-subtitle">Manage trainer assignments for each member</div>
                    </div>
                </div>

                <!-- FILTER BAR -->
                <div class="filter-bar">
                    <div class="filter-search">
                        <span class="s-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" placeholder="Search by name or email…" />
                    </div>
                    <select class="filter-select">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="trial">Trial</option>
                        <option value="expired">Expired</option>
                        <option value="pending">Pending</option>
                    </select>
                    <select class="filter-select">
                        <option value="">All Trainers</option>
                        <?php
                        foreach ($trainers as $row) { ?>
                            <option><?php $row['fullname'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="data-table members-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Trainer Assigned</th>
                                <th>Status</th>
                                <th>Member Since</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($members as $row) { ?>
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
                                    <td>
                                        <div class="trainer-assigned">
                                            <span class="trainer-dot"></span>
                                            <?php
                                            $trainer = $row['trainer_name'];
                                            if (empty($trainer)) {
                                                echo 'unassign';
                                            } else {
                                                echo $trainer;
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-active"><?php echo $row['status_name']; ?></span></td>
                                    <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <?php
                                        $trainer = $row['trainer_name'];
                                        if ($trainer === 'N/A') {
                                            echo '
    <div class="action-cell" id="assign-btn">
        <a class="action-btn assign" onclick="openAssignModal(this)" 
        data-id="' . $row['id'] . '"
        data-name="' . $row['fullname'] . '" 
            <i class="fa-solid fa-user-plus"></i> Assign
        </a> 
    </div>
    ';
                                        } else {
                                            echo '
                                            <div class="action-cell">
                                            <a class="action-btn assign" onclick="openAssignModal(this)"
                                            data-id="' . $row['id'] . '"
                                            data-name="' . $row['fullname'] . '" 
                                            >
                                                <i class="fa-solid fa-user-plus"></i> Reassign
                                            </a>
                                            <a href="./unassign-script?id=' . $row['id'] . '">
                                            <button class="action-btn remove" title="Remove Trainer">
                                                <i class="fa-solid fa-unlink"></i>
                                            </button>
                                            </a>
                                        </div> 
                                            ';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="pagination" id="pagination">
                    <span class="page-info">
                        Showing 1–5 of 20 members
                    </span>
                    <div class="page-btns">
                    </div>
                </div>
            </div>

        </div><!-- /page-area -->
    </div><!-- /main-wrap -->

    <!-- ═══════════════════════════════════
         ASSIGN TRAINER MODAL
    ═══════════════════════════════════ -->
    <form action="./assign-trainer-script" method="post">
        <div class="modal-backdrop" id="assignModal" onclick="handleBackdropClick(event)">
            <input type="hidden" name="user_id" id="userid">
            <div class="modal" id="modalBox">
                <div class="modal-header">
                    <div class="modal-icon">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div class="modal-title-group">
                        <div class="modal-title">Assign Trainer</div>
                        <div class="modal-subtitle">Select a trainer for this member</div>
                    </div>
                    <button class="modal-close" onclick="closeAssignModal()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal-divider"></div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Member</label>
                        <input type="text" name="member" class="form-input" id="member_name" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Assign Trainer</label>
                        <select class="form-select" id="modal-trainer" name="trainer">
                            <option value="">— Choose a trainer —</option>
                            <?php
                            foreach ($trainers as $row) { ?>
                                <option value="<?php echo $row['id'] ?>">
                                    <?php echo $row['fullname'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-modal-cancel" onclick="closeModal()">Cancel</button>
                    <button class="btn-modal-confirm" type="submit">
                        CONFIRM ASSIGNMENT
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- ═══════════════════════════════════
     QUICK ASSIGN MODAL
     Drop this anywhere in your <body>
═══════════════════════════════════ -->

    <form action="./quick-assign-script" method="post">
        <div id="quickAssignModal" onclick="handleQuickAssignBackdrop(event)" style="
      position: fixed;
      inset: 0;
      z-index: 500;
      background: rgba(0,0,0,0.72);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.25s ease;
    ">
            <div id="quickAssignBox" style="
        background: #111318;
        border: 1px solid #242832;
        border-radius: 14px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 24px 80px rgba(0,0,0,0.6), 0 0 40px rgba(255,69,0,0.2);
        transform: translateY(28px) scale(0.97);
        transition: transform 0.3s cubic-bezier(0.34,1.2,0.64,1);
        overflow: hidden;
        position: relative;
      ">
                <div style="
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff4500, #ff7c3a);
      "></div>
                <div style="
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 28px 28px 20px;
        gap: 14px;
      ">
                    <div style="
          width: 44px; height: 44px;
          border-radius: 10px;
          flex-shrink: 0;
          background: rgba(255,69,0,0.12);
          border: 1px solid rgba(255,69,0,0.3);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 20px;
          color: #ff4500;
        ">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            letter-spacing: 2px;
            color: #f0f2f8;
            line-height: 1.1;
          ">Quick Assign</div>
                        <div style="font-size: 13px; color: #8a90a2; margin-top: 4px;">
                            Pair a member with a trainer instantly
                        </div>
                    </div>
                    <button type="button" onclick="closeQuickAssignModal()"
                        onmouseover="this.style.borderColor='#ff4500';this.style.color='#ff4500';"
                        onmouseout="this.style.borderColor='#242832';this.style.color='#555c70';" style="
            width: 32px; height: 32px;
            border-radius: 6px;
            border: 1px solid #242832;
            background: #181b22;
            color: #555c70;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s;
            flex-shrink: 0;
          ">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div style="height: 1px; background: #242832; margin: 0 28px;"></div>
                <div style="padding: 24px 28px 20px; display: flex; flex-direction: column; gap: 20px;">
                    <div>
                        <label style="
            display: block;
            font-family: 'Rajdhani', sans-serif;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #8a90a2;
            margin-bottom: 9px;
          ">Member</label>
                        <select name="member_id" id="qa-member" required
                            onfocus="this.style.borderColor='#ff4500';this.style.boxShadow='0 0 0 3px rgba(255,69,0,0.12), 0 0 20px rgba(255,69,0,0.1)';"
                            onblur="this.style.borderColor='#242832';this.style.boxShadow='none';" style="
              width: 100%;
              background: #181b22;
              border: 1px solid #242832;
              border-radius: 10px;
              padding: 12px 40px 12px 16px;
              font-size: 14px;
              color: #f0f2f8;
              outline: none;
              cursor: pointer;
              appearance: none;
              -webkit-appearance: none;
              background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'8\' viewBox=\'0 0 12 8\'%3E%3Cpath d=\'M1 1l5 5 5-5\' stroke=\'%23555c70\' stroke-width=\'1.5\' fill=\'none\' stroke-linecap=\'round\'/%3E%3C/svg%3E');
              background-repeat: no-repeat;
              background-position: right 14px center;
              transition: border-color 0.25s, box-shadow 0.25s;
              font-family: 'DM Sans', sans-serif;
            ">
                            <option value="" style="background:#181b22;">— Choose a member —</option>
                            <?php foreach ($members as $row): ?>
                                <option value="<?php echo $row['id']; ?>" style="background:#181b22;">
                                    <?php echo htmlspecialchars($row['fullname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="
            display: block;
            font-family: 'Rajdhani', sans-serif;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #8a90a2;
            margin-bottom: 9px;
          ">Assign Trainer</label>
                        <select name="trainer" id="qa-trainer" required
                            onfocus="this.style.borderColor='#ff4500';this.style.boxShadow='0 0 0 3px rgba(255,69,0,0.12), 0 0 20px rgba(255,69,0,0.1)';"
                            onblur="this.style.borderColor='#242832';this.style.boxShadow='none';" style="
              width: 100%;
              background: #181b22;
              border: 1px solid #242832;
              border-radius: 10px;
              padding: 12px 40px 12px 16px;
              font-size: 14px;
              color: #f0f2f8;
              outline: none;
              cursor: pointer;
              appearance: none;
              -webkit-appearance: none;
              background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'8\' viewBox=\'0 0 12 8\'%3E%3Cpath d=\'M1 1l5 5 5-5\' stroke=\'%23555c70\' stroke-width=\'1.5\' fill=\'none\' stroke-linecap=\'round\'/%3E%3C/svg%3E');
              background-repeat: no-repeat;
              background-position: right 14px center;
              transition: border-color 0.25s, box-shadow 0.25s;
              font-family: 'DM Sans', sans-serif;
            ">
                            <option value="" style="background:#181b22;">— Choose a trainer —</option>
                            <?php foreach ($trainers as $row): ?>
                                <option value="<?php echo $row['id']; ?>" style="background:#181b22;">
                                    <?php echo htmlspecialchars($row['fullname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="
        padding: 4px 28px 28px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
      ">
                    <a type="button" onclick="closeQuickAssignModal()"
                        onmouseover="this.style.borderColor='#ff4500';this.style.color='#f0f2f8';"
                        onmouseout="this.style.borderColor='#242832';this.style.color='#8a90a2';" style="
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 500;
            background: #181b22;
            border: 1px solid #242832;
            color: #8a90a2;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.18s;
            font-family: 'DM Sans', sans-serif;
          ">Cancel</a>

                    <button type="submit"
                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 32px rgba(255,69,0,0.5)';"
                        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 24px rgba(255,69,0,0.35)';"
                        style="
            padding: 12px 28px;
            font-family: 'Rajdhani', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #ff4500, #ff7c3a);
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 24px rgba(255,69,0,0.35);
            transition: transform 0.2s, box-shadow 0.2s;
          ">CONFIRM ASSIGNMENT</button>
                </div>

            </div><!-- /modal box -->
        </div><!-- /backdrop -->
    </form>
    <!-- TOAST -->
    <div class="toast" id="toast" style="display:none;">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>

</body>
<script src="./js/script.js"></script>
<script src="./js/member-filter.js"></script>
<script src="./js/theme.js"></script>

</html>