<?php
require_once 'dbh.inc.php';
require_once 'middleware.php';
require_once 'model.php';
require_once 'control.php';

$db = new database();
$conn = $db->connection();
$auth = new auth(['admin']);
$controller = new controller($conn);

$role = $_GET['role'] ?? '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$join = "
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id
";
$columns = [
    'users.*',
    'role.role AS role_name',
    'status.status AS status_name'
];

$where = [];
if ($role != '') {
    $where['users.role'] = $role;
}

$members = $controller->fetch_records('users', $columns, $join, $where, $limit, $offset);
$total = $controller->count('users', $where);
$total_pages = ceil($total / $limit);

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
        <td><span class="badge badge-active"><?= $row['status_name'] ?></span></td>
        <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
        <td style="text-align:right;">
            <a href="./admin-edit-users?id=<?= $row['id'] ?>" class="table-link">Edit</a>
            <a href="./delete-record?id=<?= $row['id'] ?>" class="table-link">Delete</a>
        </td>
    </tr>
<?php } ?>
