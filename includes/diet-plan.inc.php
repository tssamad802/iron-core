<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $diet_name = $_POST['diet_name'];
    $member_id = $_POST['member_id'];
    $goal = $_POST['goal'];
    $calories = $_POST['calories'];
    $duration = $_POST['duration'];
    $breakfast = $_POST['breakfast'];
    $lunch = $_POST['lunch'];
    $dinner = $_POST['dinner'];
    $notes = $_POST['notes'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    $errors = [];

    if ($controller->is_empty_inputs([$diet_name, $goal, $calories, $duration, $notes])) {
        $errors[] = "Please fill in all fields";
    }
    if ($controller->check_record('diet', ['diet_name' => $diet_name])) {
        $errors[] = "Diet name already exists.";
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
        header("Location: ./diet-plan");
        exit;
    }

    $insert_records = [
        'diet_name' => $diet_name,
        'goal' => $goal,
        'calories' => $calories,
        'duration' => $duration,
        'breakfast' => $breakfast,
        'lunch' => $lunch,
        'dinner' => $dinner,
        'notes' => $notes,
        'member_id' => $member_id
    ];

    $result = $controller->insert_record('diet', $insert_records);
    if ($result) {
        echo "<script>
        alert('Success!');
        window.location.href = './diet-plan';
        </script>";
        exit;
    } else {
        $_SESSION['errors'] = 'record is not inserting...';
        header("Location: ./diet-plan");
        exit;
    }
} else {
    header('Location: ./diet-plan');
    exit;
}
?>