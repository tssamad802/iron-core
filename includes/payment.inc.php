<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $member_id = $_POST['member_id'];
    $fee_status = $_POST['fee_status'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    $errors = [];

    if ($controller->is_empty_inputs([$member_id, $fee_status])) {
        $errors[] = "Please fill in all fields";
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
        header('Location: ./admin-entry');
        exit;
    }

    $data = $controller->fetch_records('payment', ['*'], '', ['member_id' => $member_id]);
    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    // exit;
    $get_id = $data[0]['id'];
    $get_amount = $data[0]['amount'];
    $get_month = $data[0]['month'];
    $get_year = $data[0]['year'];
    $insert_history = [
        'payment_id' => $get_id,
        'member_id' => $member_id,
        'amount' => $get_amount,
        'month' => $get_month,
        'year' => $get_year
    ];
    $result = $controller->update('payment', ['payment_status' => $fee_status], 'member_id', $member_id);
    $result1 = $controller->insert_record('history', $insert_history);    
    // echo "<pre>";
    // print_r($result);
    // echo "</pre>";
    // exit;
    if ($result) {
        echo "<script>
        alert('Success!');
        window.location.href = './admin-entry';
        </script>";
        exit;
    }
} else {
    header('Location: ./admin-entry');
    exit;
}
?>