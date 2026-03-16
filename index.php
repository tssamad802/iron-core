<?php
require_once 'includes/route.inc.php';
$router = new Router('/iron-core');

$router
    ->add(['/', '/home', '/login'], 'pages/login.php')
    ->add('/dashboard', 'pages/admin/dashboard.php')
    ->add('/forget-password', 'pages/forget_pwd.php')
    ->add('/trainer-dashboard', 'pages/trainer/trainer-dash.php')
    ->add('/trainer-clients', 'pages/trainer/clients.php')
    ->add('/gear-dashboard', 'pages/member/gear-dash.php')
    ->add('/myworkout', 'pages/member/myworkout.php')
    ->add('/diet-plan', 'pages/member/diet-plan.php')
    ->add('/diet-plan-script', 'includes/diet-plan.inc.php')
    ->add('/login-script', 'includes/login-script.php')
    ->add('/logout', 'includes/logout.inc.php')
    ->add('/admin-add-users', 'pages/admin/admin-users-add.php')
    ->add('/admin-plan', 'pages/admin/admin-plans.php')
    ->add('/admin-edit-users', 'pages/admin/admin-users-edit.php')
    ->add('/admin-users', 'pages/admin/admin-users.php')
    ->add('/admin-add-script', 'includes/admin-add.inc.php')
    ->add('/admin-edit-script', 'includes/admin-edit.inc.php')
    ->add('/delete-record', 'includes/delete_record.inc.php')
    ->add('/admin-fetch-script', 'includes/fetch-users.inc.php')
    ->add('/admin-members-management', 'pages/admin/admin-member-management.php')
    ->add('/admin-entry', 'pages/admin/entry.php')
    ->add('/assign-trainer-script', 'includes/assign-trainer.inc.php')
    ->add('/unassign-script', 'includes/unassign.inc.php')
    ->add('/quick-assign-script', 'includes/quick-assign.inc.php')
    ->add('/workout-plan', 'pages/trainer/workout-plan.php')
    ->add('/view-plan', 'pages/trainer/view_plan.php')
    ->add('/plan-script', 'includes/plan.inc.php')
    ->add('/delete-plan', 'includes/delete-plan.inc.php')
    ->add('/assign-plan-clients', 'includes/assign-plan-clients.inc.php')
    ->add('/delete_diet', 'includes/delete_diet.php')
    ->add('/member-plans', 'pages/trainer/member-plans.php')
    ->add('/attendance-script', 'includes/attendance.inc.php')
    ->add('/delete-exercise', 'includes/delete_exercise.inc.php')
    ->add('/remove-plan-client', 'includes/remove_plan_client.inc.php')
    ->add('/payment-script', './includes/payment.inc.php');

$router->dispatch();
?>