<?php
if (!function_exists('check_admin_login')) {
    function check_admin_login() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Ensure admin session is valid
        if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
            header("Location: index.php"); // Redirect to admin login page
            exit();
        }
    }
}
?>
