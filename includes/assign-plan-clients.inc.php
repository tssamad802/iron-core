<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $clients = $_POST['clients'];
    $plan_id = $_POST['plan_id'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    // echo '<pre>';
    // print_r($clients);
    // echo '</pre>';
    // exit;

    $errors = [];

    if ($controller->is_empty_inputs([$clients])) {
        $errors[] = "Please fill in all fields";
    }
    foreach ($clients as $client) {
        if ($controller->check_record('plan_clients', ['plan_id' => $plan_id, 'client_id' => $client])) {
            $errors[] = "client already selected";
        }
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
        header('Location: ./view-plan?plan_id=' . $plan_id);
        exit;
    }

    for ($i = 0; $i < count($clients); $i++) {
        if (!empty($clients[$i])) {
            $clients_data = [
                'plan_id' => $plan_id,
                'client_id' => $clients[$i]
            ];

            $controller->insert_record('plan_clients', $clients_data);
        }
    }
    $_SESSION['success'] = ['Clients assigned successfully'];
    header('Location: ./view-plan?plan_id=' . $plan_id);
    exit;

} else {
    header('Location: ./view-plan?plan_id=' . $plan_id);
    exit;
}
?>