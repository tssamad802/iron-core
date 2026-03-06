<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);


    $errors = [];

    if ($controller->is_empty_inputs([$email, $pwd])) {
        $errors[] = "Please fill the field";
    }

    if ($errors) {
        $_SESSION['errors'] = $errors;
        header("Location: ./login");
        exit;
    }


    // $insert_records = [
    //     'fullname' => 'admin',
    //     'username' => 'admin1234',
    //     'email' => $email,
    //     'pwd' => $pwd
    // ];

    $join = "INNER JOIN role ON users.role = role.id LEFT JOIN payment ON users.id = payment.member_id";
    $result = $controller->check_record(
        'users',
        ['email' => $email, 'pwd' => $pwd],
        null,
        'users.id',
        $join,
        'users.*, role.role AS role_name, payment.id AS payment_id, payment.member_id, payment.member_amount, payment.trainer_amount, payment.payment_status, payment.month, payment.year'
    );

    //$result = $controller->insert_record('users', $insert_records);
    if ($result) {
        // echo '<pre>';
        // print_r($result);
        // echo '</pre>';
        // exit;
        $_SESSION['user_id'] = $result['id'];
        $role = $result['role_name'];
        $payment = $result['payment_status'];
        $_SESSION['username'] = $result['username'];
        $_SESSION[$role] = $role;

        if ($role === 'trainer') {
            header("Location: ./trainer-dashboard");
        } elseif ($role === 'member') {
            if ($payment === 'pending') {
                $_SESSION['errors'] = ['Your membership has ended. Please renew to access your dashboard.'];
                header("Location: ./login");
                exit;
            } else {
                header("Location: ./gear-dashboard");
                exit;
            }
        } elseif ($role === 'admin') {
            header("Location: ./dashboard");
        } else {
            header("Location: ./login");
        }
        exit;
    } else {
        $_SESSION['errors'] = ['Invalid email or password'];
        header("Location: ./login");
        exit;
    }

} else {
    header("Location: ./login");
    exit;
}
?>