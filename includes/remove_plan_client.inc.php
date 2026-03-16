<?php
require_once 'config.session.inc.php';
require_once 'dbh.inc.php';
require_once 'model.php';
require_once 'control.php';

$db = new database();
$conn = $db->connection();
$controller = new controller($conn);

$client_id = $_GET['client_id'];
$plan_id = $_GET['plan_id'];

$controller->delete('plan_clients', 'client_id', $client_id);
header('Location: ./view-plan?plan_id=' . $plan_id);
exit;
?>