<?php
session_start();
include('../includes/dbconn.php');

// ✅ Backend logic unchanged
if (isset($_POST['submit'])) {
    $CMS    = $_POST['cms'];
    $SName  = $_POST['name'];
    $S_FName= $_POST['fname'];
    $SAddress= $_POST['address'];
    $SEmail = $_POST['email'];
    $SPhone = $_POST['phone'];
    $SCnic  = $_POST['cnic'];
    $SGender= $_POST['gender'];
    $Dept   = $_POST['dept'];
    $SRNo   = $_POST['room_no'];
    $SHID   = $_SESSION['hid']; 
    $Password = md5($_POST['password']);

    $check = $conn->query("SELECT CMS FROM student WHERE CMS='$CMS' OR SEmail='$SEmail' OR SCnic='$SCnic'");
    if ($check && $check->num_rows > 0) {
        echo "<script>alert('⚠️ Student is already registered!'); window.location.href='register-student.php';</script>";
        exit();
    }

    $query = "INSERT INTO student 
        (CMS, SName, S_FName, SAddress, SEmail, SPhone, SCnic, SGender, Dept, SHID, SRNo) 
        VALUES 
        ('$CMS', '$SName', '$S_FName', '$SAddress', '$SEmail', '$SPhone', '$SCnic', '$SGender', '$Dept', '$SHID', '$SRNo')";

    if ($conn->query($query)) {
        $loginQuery = "INSERT INTO studentlogin (email, PASSWORD, CMS) 
                       VALUES ('$SEmail', '$Password', '$CMS')";
        if ($conn->query($loginQuery)) {
            echo "<script>alert('✅ Student has been Registered!'); window.location.href='register-student.php';</script>";
        } else {
            echo "❌ Error inserting into studentlogin: " . $conn->error;
        }
    } else {
        echo "❌ Error inserting into student: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Hostel Management System - Register Student</title>
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.css">

    <link href="../dist/css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #eef2ff 100%);
            color: #333;
            line-height: 1.6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-wrapper {
            padding: 20px;
        }
        
        .page-breadcrumb {
            background: white;
            padding: 18px 25px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            border-left: 4px solid var(--primary);
        }
        
        .page-title {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        
        .breadcrumb-item a {
            color: var(--gray);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .breadcrumb-item a:hover {
            color: var(--primary);
        }
        
        .registration-container {
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .form-header {
            background: linear-gradient(120deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .form-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .form-header h2 i {
            margin-right: 12px;
            font-size: 1.8rem;
        }
        
        .step-indicator {
            display: flex;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 5px;
            margin-top: 10px;
        }
        
        .step {
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            margin: 0 4px;
        }
        
        .step.active {
            background: white;
            width: 20px;
            border-radius: 10px;
        }
        
        .form-body {
            padding: 30px;
        }
        
        .form-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .form-section.active {
            display: block;
        }
        
        .section-title {
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-gray);
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 1.4rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 15px;
            transition: var(--transition);
            background-color: #fafbff;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            background-color: #fff;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .input-icon .form-control {
            padding-left: 45px;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray);
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }
        
        .btn-prev, .btn-next {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .btn-prev {
            background: var(--gray);
        }
        
        .btn-prev:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-next:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-submit {
            background: var(--success);
            color: white;
            border: none;
            padding: 14px 35px;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(76, 201, 240, 0.3);
        }
        
        .btn-submit:hover {
            background: #3aafd9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 201, 240, 0.4);
        }
        
        .btn-prev i, .btn-next i, .btn-submit i {
            margin-right: 8px;
        }
        
        .progress-bar {
            height: 6px;
            background: var(--light-gray);
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .progress {
            height: 100%;
            background: linear-gradient(120deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
            width: 33%;
            transition: width 0.5s ease;
        }
        
        /* Form validation styles */
        .form-control.error {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }
        
        .form-control.error + .error-message {
            display: block;
        }
        
        /* Success message */
        .success-message {
            background: #2ecc71;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .form-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .step-indicator {
                margin-top: 15px;
                align-self: center;
            }
            
            .form-body {
                padding: 20px;
            }
            
            .form-footer {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-prev, .btn-next, .btn-submit {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        <header class="topbar" data-navbarbg="skin6">
            <?php include 'includes/navigation.php'?>
        </header>
        
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include 'includes/sidebar.php'?>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="page-title">Student Registration</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Register Student</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-3 text-right">
                        <button class="btn btn-sm btn-outline-primary" id="helpBtn">
                            <i class="fas fa-question-circle"></i> Help
                        </button>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="registration-container">
                    <div class="form-header">
                        <h2><i class="fas fa-user-graduate"></i> Register New Student</h2>
                        <div class="step-indicator">
                            <div class="step active"></div>
                            <div class="step"></div>
                            <div class="step"></div>
                        </div>
                    </div>
                    
                    <div class="form-body">
                        <div class="progress-bar">
                            <div class="progress" id="formProgress"></div>
                        </div>
                        
                        <form method="POST" action="register-student.php" id="registrationForm">
                            <!-- Personal Information Section -->
                            <div class="form-section active" id="section1">
                                <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="cms">CMS ID</label>
                                        <div class="input-icon">
                                            <i class="fas fa-id-card"></i>
                                            <input type="text" id="cms" name="cms" class="form-control" placeholder="Enter CMS ID" required>
                                        </div>
                                        <div class="error-message">Please enter a valid CMS ID</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="name">Full Name</label>
                                        <div class="input-icon">
                                            <i class="fas fa-signature"></i>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter full name" required>
                                        </div>
                                        <div class="error-message">Please enter the student's full name</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="fname">Father's Name</label>
                                        <div class="input-icon">
                                            <i class="fas fa-user-friends"></i>
                                            <input type="text" id="fname" name="fname" class="form-control" placeholder="Enter father's name" required>
                                        </div>
                                        <div class="error-message">Please enter father's name</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="cnic">CNIC</label>
                                        <div class="input-icon">
                                            <i class="fas fa-address-card"></i>
                                            <input type="text" id="cnic" name="cnic" class="form-control" placeholder="Enter CNIC without dashes" required>
                                        </div>
                                        <div class="error-message">Please enter a valid CNIC</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <div class="input-icon">
                                            <i class="fas fa-venus-mars"></i>
                                            <select id="gender" name="gender" class="form-control" required>
                                                <option value="">--Select Gender--</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="error-message">Please select a gender</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="dept">Department</label>
                                        <div class="input-icon">
                                            <i class="fas fa-building"></i>
                                            <input type="text" id="dept" name="dept" class="form-control" placeholder="Enter department" required>
                                        </div>
                                        <div class="error-message">Please enter department</div>
                                    </div>
                                </div>
                                
                                <div class="form-footer">
                                    <div></div> <!-- Empty div for alignment -->
                                    <button type="button" class="btn-next" id="nextToSection2">
                                        <i class="fas fa-arrow-right"></i> Next
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Contact Information Section -->
                            <div class="form-section" id="section2">
                                <h3 class="section-title"><i class="fas fa-address-book"></i> Contact Information</h3>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <div class="input-icon">
                                            <i class="fas fa-envelope"></i>
                                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter email address" required>
                                        </div>
                                        <div class="error-message">Please enter a valid email address</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <div class="input-icon">
                                            <i class="fas fa-phone"></i>
                                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter phone number" required>
                                        </div>
                                        <div class="error-message">Please enter a valid phone number</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <div class="input-icon">
                                            <i class="fas fa-home"></i>
                                            <input type="text" id="address" name="address" class="form-control" placeholder="Enter complete address" required>
                                        </div>
                                        <div class="error-message">Please enter address</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="room_no">Room Number</label>
                                        <div class="input-icon">
                                            <i class="fas fa-door-open"></i>
                                            <input type="text" id="room_no" name="room_no" class="form-control" placeholder="Enter room number" required>
                                        </div>
                                        <div class="error-message">Please enter room number</div>
                                    </div>
                                </div>
                                
                                <div class="form-footer">
                                    <button type="button" class="btn-prev" id="backToSection1">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                    <button type="button" class="btn-next" id="nextToSection3">
                                        <i class="fas fa-arrow-right"></i> Next
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Account Information Section -->
                            <div class="form-section" id="section3">
                                <h3 class="section-title"><i class="fas fa-lock"></i> Account Information</h3>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <div class="input-icon">
                                            <i class="fas fa-key"></i>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Create password" required>
                                            <span class="password-toggle" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="error-message">Password must be at least 8 characters</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm Password</label>
                                        <div class="input-icon">
                                            <i class="fas fa-key"></i>
                                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                                            <span class="password-toggle" id="toggleConfirmPassword">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="error-message">Passwords do not match</div>
                                    </div>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input type="checkbox" class="form-check-input" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" class="text-primary">Terms and Conditions</a>
                                    </label>
                                </div>
                                
                                <div class="form-footer">
                                    <button type="button" class="btn-prev" id="backToSection2">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                    <button type="submit" name="submit" class="btn-submit">
                                        <i class="fas fa-user-plus"></i> Register Student
                                    </button>
                                </div>
                                
                                <div class="success-message" id="successMessage">
                                    <i class="fas fa-check-circle"></i> Student registered successfully!
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php include '../includes/footer.php' ?>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../dist/js/app-style-switcher.js"></script>
    <script src="../dist/js/feather.min.js"></script>
    <script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../dist/js/sidebarmenu.js"></script>
    <script src="../dist/js/custom.min.js"></script>

    <script>
        $(document).ready(function() {
            // Multi-step form navigation
            $('#nextToSection2').click(function() {
                if (validateSection('#section1')) {
                    $('#section1').removeClass('active');
                    $('#section2').addClass('active');
                    updateProgress(66);
                    updateStepIndicator(1);
                }
            });
            
            $('#nextToSection3').click(function() {
                if (validateSection('#section2')) {
                    $('#section2').removeClass('active');
                    $('#section3').addClass('active');
                    updateProgress(100);
                    updateStepIndicator(2);
                }
            });
            
            $('#backToSection1').click(function() {
                $('#section2').removeClass('active');
                $('#section1').addClass('active');
                updateProgress(33);
                updateStepIndicator(0);
            });
            
            $('#backToSection2').click(function() {
                $('#section3').removeClass('active');
                $('#section2').addClass('active');
                updateProgress(66);
                updateStepIndicator(1);
            });
            
            // Password visibility toggle
            $('#togglePassword').click(function() {
                const passwordInput = $('#password');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });
            
            $('#toggleConfirmPassword').click(function() {
                const confirmInput = $('#confirm_password');
                const type = confirmInput.attr('type') === 'password' ? 'text' : 'password';
                confirmInput.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });
            
            // Form validation
            function validateSection(sectionId) {
                let isValid = true;
                $(sectionId + ' .form-control').each(function() {
                    if ($(this).prop('required') && !$(this).val()) {
                        $(this).addClass('error');
                        isValid = false;
                    } else {
                        $(this).removeClass('error');
                    }
                });
                
                // Additional validation for specific fields
                if (sectionId === '#section1') {
                    const cnic = $('#cnic').val();
                    if (cnic && !/^\d{13}$/.test(cnic)) {
                        $('#cnic').addClass('error');
                        isValid = false;
                    }
                }
                
                if (sectionId === '#section2') {
                    const email = $('#email').val();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (email && !emailRegex.test(email)) {
                        $('#email').addClass('error');
                        isValid = false;
                    }
                }
                
                return isValid;
            }
            
            // Update progress bar
            function updateProgress(percentage) {
                $('#formProgress').css('width', percentage + '%');
            }
            
            // Update step indicator
            function updateStepIndicator(activeIndex) {
                $('.step').removeClass('active');
                $('.step').eq(activeIndex).addClass('active');
            }
            
            // Form submission validation
            $('#registrationForm').on('submit', function(e) {
                const password = $('#password').val();
                const confirmPassword = $('#confirm_password').val();
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    $('#confirm_password').addClass('error');
                    $('#confirm_password').siblings('.error-message').text('Passwords do not match').show();
                    
                    // Scroll to the error
                    $('html, body').animate({
                        scrollTop: $('#confirm_password').offset().top - 100
                    }, 500);
                }
                
                if (! $('#terms').is(':checked')) {
                    e.preventDefault();
                    alert('Please agree to the terms and conditions');
                }
            });
            
            // Real-time validation
            $('.form-control').on('input', function() {
                $(this).removeClass('error');
                $(this).siblings('.error-message').hide();
            });
            
            // Help button
            $('#helpBtn').click(function() {
                alert('Need help? Contact system administrator for assistance with student registration.');
            });
        });
    </script>
</body>
</html>