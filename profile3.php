<?php
include 'dbc.php';
session_start();

if (isset($_SESSION['worker_id'])) {
    $worker_id = $_SESSION['worker_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $category = $_POST['category'];
    $qualification= $_POST['qualification'];
    $state= $_POST['state'];
    $district= $_POST['district'];
    $hometwon= $_POST['hometwon'];
    $perhour= $_POST['Rupees'];