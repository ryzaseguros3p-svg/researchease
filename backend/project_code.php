<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../db_connect.php';
require_once '../classes/ResearchProject.php';

$project = new ResearchProject($db);

if (isset($_POST['add_project'])) {
    $userId = $_SESSION['user_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $status = $_POST['status'];
    $date = date('Y-m-d');

    $project->addResearchProject($userId, $title, $desc, $status, $date);
    header("Location: ../index.php"); 
    exit();
}

if (isset($_POST['update_project'])) {
    $id = $_POST['project_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $status = $_POST['status'];

    $project->updateResearchProject($id, $title, $desc, $status);
    header("Location: ../index.php"); 
    exit();
}

if (isset($_POST['delete_project_confirm'])) {
    $id = $_POST['project_id'];
    $project->deleteResearchProject($id);
    header("Location: ../index.php");
    exit();
}
?>