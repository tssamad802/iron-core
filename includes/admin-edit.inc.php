<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    $errors = [];

    if ($controller->is_empty_inputs([$fullname, $username, $email, $role, $status])) {
        $errors[] = "Please fill in all fields";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is invalid";
    }
    if ($controller->check_record('users', ['username' => $username, 'email' => $email], $id)) {
        $errors[] = "username or email already saved";
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
        header("Location: ./admin-add-member");
        exit;
    }

    $update_records = [
        'fullname' => $fullname,
        'username' => $username,
        'email' => $email,
        'pwd' => $pwd,
        'status' => $status,
        'role' => $role
    ];

    $result = $controller->update('users', $update_records, 'id', $id);
    // print_r($result);
    // exit;
    if ($result) {
        header("Location: ./admin-users");
        exit;
    } else {
        header("Location: ./admin-edit-users?id=$id");
        exit;
    }
} else {
    header("Location: ./admin-edit-users?id=$id");
    exit;
}
?>