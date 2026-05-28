<?php
/* session_name('colefy_session'); // 👈 único por sistema
session_start(); */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
require_once '../../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $pass = $_POST['pass'] ?? '';

    $auth = new AuthController();
    $response = $auth->login($username, $pass);
    
    echo json_encode($response);
}
 