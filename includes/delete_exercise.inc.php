<?php
require_once 'config.session.inc.php';
require_once 'dbh.inc.php';
require_once 'model.php';
require_once 'control.php';

$db = new database();
$conn = $db->connection();
$controller = new controller($conn);

$exercise_id = $_GET['exercise_id'];
$plan_id = $_GET['plan_id'];

$controller->delete('exercise', 'id', $exercise_id);
header('Location: ./view-plan?plan_id=' . $plan_id);
exit;
?>