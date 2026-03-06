<?php
require_once 'config.session.inc.php';
require_once 'dbh.inc.php';
require_once 'model.php';
require_once 'control.php';

$db = new database();
$conn = $db->connection();
$controller = new controller($conn);

$id = $_GET['id'];

$result = $controller->update('users', ['trainer_id' => 'NULL'], 'id', $id);
if ($result) {
    header("Location: ./admin-members-management");
    exit;
} else {
    echo 'unassign is not working';
}
?>