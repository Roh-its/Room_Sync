<?php
include('../includes/dbconn.php');

if (!empty($_POST["email"])) {
    $email = $_POST["email"];
    $query = mysqli_query($mysqli, "SELECT SEmail FROM student WHERE SEmail='$email'");

    if (mysqli_num_rows($query) > 0) {
        echo "<span style='color:red;'>❌ Email already registered</span>";
    } else {
        echo "<span style='color:green;'>✅ Email available</span>";
    }
}
?>
