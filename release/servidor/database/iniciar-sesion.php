<?php
/* session_name('colefy_session'); // 👈 único por sistema
session_start(); */

 
require_once '../../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $pass = $_POST['pass'] ?? '';

    $auth = new AuthController();
    $response = $auth->login($username, $pass);
    
    echo json_encode($response);
}
 