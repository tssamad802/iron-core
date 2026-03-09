<?php
require_once 'config.session.inc.php';
require_once 'dbh.inc.php';
require_once 'model.php';
require_once 'control.php';

$db = new database();
$conn = $db->connection();
$controller = new controller($conn);

$delete_id = $_GET['id'];

$plan = $controller->delete('diet', 'id', $delete_id);

header('Location: ./diet-plan');
exit;
?>