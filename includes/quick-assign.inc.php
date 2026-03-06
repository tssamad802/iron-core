<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $member_id = $_POST['member_id'];
    $trainer = $_POST['trainer'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    $errors = [];

    if ($controller->is_empty_inputs([$member_id, $trainer])) {
        $errors[] = "Please fill in all fields";
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
        header("Location: ./admin-members-management");
        exit;
    }

    // print_r($member_id);
    // exit;

    $result = $controller->update('users', ['trainer_id' => $trainer], 'id', $member_id);
    //  echo '<pre>';
    // print_r($result);
    // echo '</pre>';
    // exit;
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