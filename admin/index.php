<?php
session_start();
include('../includes/dbconn.php'); // this defines $conn

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password = md5($password);

    // Check adminlogin with manager join
    $query = mysqli_query($conn, "
        SELECT a.MID, m.MHID 
        FROM adminlogin a 
        JOIN manager m ON a.MID = m.MID 
        WHERE a.username='$email' AND a.password='$password'
    ");

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        // Store session variables
        $_SESSION['login'] = $email;
        $_SESSION['mid'] = $row['MID'];
        $_SESSION['hid'] = $row['MHID'];  // store hostel id

        header("location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System - Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Raleway', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a4b8c 0%, #2a5da6 50%, #1a4b8c 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            width: 1000px;
            max-width: 95%;
            display: flex;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }
        
        .image-section {
            flex: 1;
            background: linear-gradient(rgba(26, 75, 140, 0.7), rgba(42, 93, 166, 0.7)), 
                        url('https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1016&q=80') no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: white;
            text-align: center;
        }
        
        .image-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .image-content p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 400px;
        }
        
        .features {
            list-style: none;
            text-align: left;
            max-width: 400px;
        }
        
        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .features i {
            margin-right: 10px;
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .form-section {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
            position: relative;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            color: #2a5da6;
            font-weight: 700;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-title h2 {
            font-size: 1.8rem;
            color: #2a5da6;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #444;
            font-size: 1.1rem;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            font-size: 1.1rem;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 16px 15px 16px 50px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .input-with-icon input:focus {
            border-color: #2a5da6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 93, 166, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: #2a5da6;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(26, 75, 140, 0.2);
        }
        
        .btn-login:hover {
            background: #1a4b8c;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(26, 75, 140, 0.3);
        }
        
        .go-back {
            text-align: center;
            margin-top: 25px;
        }
        
        .go-back a {
            color: #2a5da6;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            font-size: 1.1rem;
        }
        
        .go-back a i {
            margin-right: 10px;
            transition: transform 0.3s;
        }
        
        .go-back a:hover {
            color: #1a4b8c;
        }
        
        .go-back a:hover i {
            transform: translateX(-5px);
        }
        
        .alert {
            padding: 12px 15px;
            background: #ffdddd;
            color: #ff0000;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
            font-weight: 500;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
            }
            
            .image-section {
                padding: 30px 20px;
            }
            
            .image-content h1 {
                font-size: 2rem;
            }
            
            .form-section {
                padding: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .form-section {
                padding: 25px;
            }
            
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .form-title h2 {
                font-size: 1.5rem;
            }
            
            .form-group label {
                font-size: 1rem;
            }
            
            .input-with-icon input {
                padding: 14px 15px 14px 45px;
                font-size: 1rem;
            }
            
            .btn-login {
                padding: 14px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="image-section">
            <div class="image-content">
                <h1>Hostel Management System</h1>
                <p>Efficiently manage your hostel operations with our comprehensive management system</p>
                
                <ul class="features">
                    <li><i class="fas fa-check"></i> Student Registration & Management</li>
                    <li><i class="fas fa-check"></i> Room Allocation & Inventory</li>
                    <li><i class="fas fa-check"></i> Attendance Tracking</li>
                    <li><i class="fas fa-check"></i> Fee Management & Payments</li>
                    <li><i class="fas fa-check"></i> Complaints & Maintenance</li>
                </ul>
            </div>
        </div>
        
        <div class="form-section">
            <div class="logo">
                <img src="../assets/images/big/icon.png" alt="Hostel Management System">
                <h1>Hostel Management System</h1>
            </div>
            
            <div class="alert" id="errorAlert"></div>
            
            <div class="form-title">
                <h2>Admin Login</h2>
            </div>
            
            <form class="login-form" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <button type="submit" name="login" class="btn-login">LOGIN</button>
            </form>
            
            <div class="go-back">
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Go Back to Home</a>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission with basic validation
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;
            let errorAlert = document.getElementById('errorAlert');
            
            // Basic validation
            if (!email || !password) {
                e.preventDefault();
                errorAlert.textContent = 'Please fill in all fields';
                errorAlert.style.display = 'block';
                
                // Hide alert after 3 seconds
                setTimeout(function() {
                    errorAlert.style.display = 'none';
                }, 3000);
            }
        });
        
        // Show error message from PHP if exists
        <?php if (isset($error) && $error): ?>
            document.getElementById('errorAlert').textContent = '<?php echo $error; ?>';
            document.getElementById('errorAlert').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>