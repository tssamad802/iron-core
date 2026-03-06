<?php
require_once 'config.session.inc.php';
require_once 'dbh.inc.php';
require_once 'model.php';
require_once 'control.php';

$db = new database();
$conn = $db->connection();
$controller = new controller($conn);

$id = $_GET['id'];
$delete = $controller->delete('users', 'id', $id);
$delete1 = $controller->delete('plan_clients', 'client_id', $id);
if ($delete) {
    header("Location: admin-users");
    exit;
} else {
    header("Location: admin-users");
    exit;
}
?>