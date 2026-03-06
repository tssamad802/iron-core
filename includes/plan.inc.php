<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $trainer_id = $_POST['trainer_id'];
    $plan_name = $_POST['plan_name'];
    $category = $_POST['category'];
    $duration = $_POST['duration'];
    $days = $_POST['days'] ?? '';
    $days_string = implode(',', $days);
    $exercise_name = $_POST['exercise_name'];
    $sets = $_POST['sets'];
    $reps = $_POST['reps'];
    $rest = $_POST['rest'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    $errors = [];


    if ($controller->is_empty_inputs([$plan_name, $category, $duration, $days, $exercise_name, $sets, $reps, $rest])) {
        $errors[] = "Please fill in all fields";
    }
    if ($controller->check_record('plan', ['plan_name' => $plan_name])) {
        $errors[] = "Plan name already exists.";
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
        header("Location: ./workout-plan");
        exit;
    }

    $plan_inserts_records = [
        'plan_name' => $plan_name,
        'category' => $category,
        'days' => $days_string,
        'duration' => $duration,
        'trainer_id' => $trainer_id
    ];

    $insert_plan = $controller->insert_record('plan', $plan_inserts_records);
    $plan_id = $insert_plan;
    if ($insert_plan) {
        for ($i = 0; $i < count($exercise_name); $i++) {
            if (!empty($exercise_name[$i])) {
                $exercise_data = [
                    'plan_id' => $plan_id,
                    'exercise_name' => $exercise_name[$i],
                    'sets' => $sets[$i],
                    'reps' => $reps[$i],
                    'rest' => $rest[$i],
                    'trainer_id' => $trainer_id
                ];

                $controller->insert_record('exercise', $exercise_data);
            }
        }
        echo "<script>alert('Success!');
                window.location.href = './workout-plan';
                </script>";
                exit;
        // echo '<pre>';
        // echo $insert_plan;
        // echo '</pre>';
        // exit;
    } else {
        echo 'something went wrong';
    }
    exit;
} else {
    header("Location: ./workout-plan");
    exit;
}
?>