<?php
// Prevent function redeclaration error
if (!function_exists('check_login')) {
    
    function check_login()
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if session login exists and is not empty
        if(!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0)
        {	
            $host = $_SERVER['HTTP_HOST'];
            $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $extra = "index.php";		
            
            // Clear session if needed
            if(isset($_SESSION["login"])) {
                $_SESSION["login"] = "";
            }
            
            header("Location: http://$host$uri/$extra");
            exit(); // Important: stop script execution after redirect
        }
    }
}
?>