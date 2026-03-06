<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_id = $_POST['user_id'];
    $member = $_POST['member'];
    $trainer = $_POST['trainer'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    $errors = [];

    if ($controller->is_empty_inputs([$member, $trainer])) {
        $errors[] = "Please fill in all fields";
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
        header("Location: ./admin-members-management");
        exit;
    }

    // print_r(value: $user_id);
    // exit;

    $result = $controller->update('users', ['trainer_id' => $trainer], 'id', $user_id);
    if ($result) {
        echo "<script>
        alert('Success!');
        window.location.href = './admin-members-management';
        </script>";
        exit;
    } else {
        echo 'record is not inserting...';
    }
    // echo '<pre>';
    // print_r($result);
    // echo '</pre>';
    // exit;
} else {
    header('Location: ./admin-members-management');
    exit;
}
?>