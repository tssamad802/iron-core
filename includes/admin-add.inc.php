<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    $role = $_POST['role'];
    $fee = $_POST['fee'];
    $trainer_salary = $_POST['trainer_salary'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    $errors = [];

    if ($controller->is_empty_inputs([$fullname, $username, $email, $pwd, $role])) {
        $errors[] = "Please fill in all fields";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is invalid";
    }
    if ($controller->check_record('users', ['username' => $username, 'email' => $email, 'pwd' => $pwd])) {
        $errors[] = "username or email or pwd already saved";
    }

    // echo '<pre>';
    // print_r($controller->check_record('users', ['username' => $username, 'email' => $email, 'pwd' => $pwd]));
    // echo '</pre>';
    // exit;
    if ($errors) {
        $_SESSION['errors'] = $errors;
        header("Location: ./admin-add-users");
        exit;
    }
    $insert_records = [
        'fullname' => $fullname,
        'username' => $username,
        'email' => $email,
        'pwd' => $pwd,
        'role' => $role,
        'fee' => $fee,
        'trainerPay' => $trainer_salary
    ];

    $result = $controller->insert_record('users', $insert_records);
    $insert_payment = [
        'member_id' => $result,
        'member_amount' => $fee,
        'trainer_amount' => $trainer_salary,
        'month' => date('m'),
        'year' => date('y'),
    ];
    $result1 = $controller->insert_record('payment', $insert_payment);
    // print_r($result);
    // exit;
    if ($result && $result1) {
        echo "<script>
        alert('Success!');
        window.location.href = './admin-users';
        </script>";
        exit;
        // header("Location: ./admin-users");
        // exit;
    } else {
        header("Location: ./admin-add-users");
        exit;
    }
} else {
    header("Location: ./admin-add-users");
    exit;
}
?>