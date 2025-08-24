<?php
session_start();
include('../includes/dbconn.php');
include('../includes/check-login.php');
check_login();

// Debugging: Check what session variables are available
error_log("Session variables: " . print_r($_SESSION, true));

// Mark attendance
if(isset($_POST['submit']))
{
    $enrollment = $_POST['enrollment'];
    $date = $_POST['date'];
    $status = "Present";
    
    // Check if student exists with this enrollment number
    $check_student_query = "SELECT CMS FROM student WHERE CMS = '$enrollment'";
    $student_result = $conn->query($check_student_query);
    
    if($student_result->num_rows == 0) {
        echo "<script>alert('Student with this ID not found');</script>";
    } else {
        // Check if attendance already marked for this student on this date
        $check_query = "SELECT * FROM student_attendance WHERE CMS = '$enrollment' AND SADate = '$date'";
        $check_result = $conn->query($check_query);
        
        if($check_result->num_rows > 0) {
            echo "<script>alert('Attendance already marked for this student today');</script>";
        } else {
            $sql = mysqli_query($conn, "INSERT INTO student_attendance(SADate,Status,CMS) VALUES('$date','$status', '$enrollment')");
            if($sql) {
                echo "<script>alert('Attendance Marked successfully');</script>";
            } else {
                echo "<script>alert('Error marking attendance');</script>";
            }
        }
    }
}

// Get statistics for dashboard
// Check if session variable exists before accessing it
$hid = isset($_SESSION['hid']) ? $_SESSION['hid'] : null;

// Debugging: Check if we have a valid hostel ID
if (!$hid) {
    error_log("Hostel ID not found in session. Available session variables: " . print_r($_SESSION, true));
    
    // Try to get hostel ID from manager table if we have mid
    if (isset($_SESSION['mid'])) {
        $mid = $_SESSION['mid'];
        $manager_query = "SELECT HID FROM manager WHERE MID = '$mid'";
        $manager_result = $conn->query($manager_query);
        
        if ($manager_result && $manager_result->num_rows > 0) {
            $manager_data = $manager_result->fetch_assoc();
            $hid = $manager_data['HID'];
            $_SESSION['hid'] = $hid; // Set it for future use
            error_log("Retrieved HID from database: " . $hid);
        }
    }
}

// Initialize variables with default values
$total_students = 0;
$present_today = 0;
$absent_today = 0;
$unknown_today = 0;

// Total Students - only query if hid is available
if ($hid) {
    $total_query = "SELECT COUNT(*) as total FROM student WHERE SHID = '$hid'";
    $total_result = $conn->query($total_query);
    if ($total_result) {
        $total_students = $total_result->fetch_assoc()['total'];
    }
} else {
    // If no hid in session, show error message but don't prevent page from loading
    echo "<script>
        setTimeout(function() {
            alert('Hostel ID not found in session. Some features may be limited. Please log in again if problems persist.');
        }, 1000);
    </script>";
}

// Present Today
$today = date('Y-m-d');
$present_query = "SELECT COUNT(*) as present FROM student_attendance WHERE SADate = '$today' AND Status = 'Present'";
$present_result = $conn->query($present_query);
if ($present_result) {
    $present_today = $present_result->fetch_assoc()['present'];
}

// Absent Today (assuming students not marked present are absent)
$absent_today = $total_students - $present_today;

// Unknown Status (if any other status exists)
$unknown_query = "SELECT COUNT(*) as unknown FROM student_attendance WHERE SADate = '$today' AND Status NOT IN ('Present', 'Absent')";
$unknown_result = $conn->query($unknown_query);
if ($unknown_result) {
    $unknown_today = $unknown_result->fetch_assoc()['unknown'];
}

// Get recent attendance records - only if hid is available
if ($hid) {
    $recent_query = "SELECT sa.*, s.SName, s.SRNo 
                     FROM student_attendance sa 
                     JOIN student s ON sa.CMS = s.CMS 
                     WHERE s.SHID = '$hid' 
                     ORDER BY sa.SADate DESC 
                     LIMIT 5";
    $recent_result = $conn->query($recent_query);
} else {
    // If no hid, get recent records for all hostels (limited functionality)
    $recent_query = "SELECT sa.*, s.SName, s.SRNo 
                     FROM student_attendance sa 
                     JOIN student s ON sa.CMS = s.CMS 
                     ORDER BY sa.SADate DESC 
                     LIMIT 5";
    $recent_result = $conn->query($recent_query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Hostel Management System - Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../dist/css/style.min.css" rel="stylesheet">

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
        }
        
        body {
            background-color: #f5f8ff;
            color: #333;
            line-height: 1.6;
        }
        
        .page-wrapper {
            background-color: #f5f8ff;
            padding: 20px;
        }
        
        .page-breadcrumb {
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .page-title {
            color: var(--primary);
            font-size: 1.8rem;
            margin: 0;
            font-weight: 600;
        }
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card h3 {
            color: var(--gray);
            margin-bottom: 10px;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .card .number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .card .icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
            opacity: 0.8;
        }
        
        /* Status Section */
        .status-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .section-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .date-display {
            background: var(--light);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
            color: var(--primary);
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .status-item {
            background: var(--light);
            padding: 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .status-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .present .status-icon {
            background: var(--success);
        }
        
        .absent .status-icon {
            background: var(--warning);
        }
        
        .unknown .status-icon {
            background: var(--gray);
        }
        
        .status-info h4 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .status-info p {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        /* Form Styling */
        .attendance-form {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .form-card {
            background: var(--light);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-card h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .form-control {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .btn-success {
            background: var(--success);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: #3aafd9;
            transform: translateY(-2px);
        }
        
        /* Recent Activity */
        .recent-activity {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            background: var(--success);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-content h4 {
            margin: 0 0 5px 0;
            font-weight: 600;
        }
        
        .activity-content p {
            margin: 0;
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .activity-time {
            color: var(--gray);
            font-size: 0.85rem;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .action-btn {
            background: white;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--dark);
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: var(--primary);
            text-decoration: none;
        }
        
        .action-icon {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .status-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
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
                <div class="row">
                    <div class="col-7 align-self-center">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Students Attendance</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Attendance</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Total Students</h3>
                        <div class="number"><?php echo $total_students; ?></div>
                    </div>
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h3>Present Today</h3>
                        <div class="number"><?php echo $present_today; ?></div>
                    </div>
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <h3>Absent Today</h3>
                        <div class="number"><?php echo $absent_today; ?></div>
                    </div>
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h3>Unknown Status</h3>
                        <div class="number"><?php echo $unknown_today; ?></div>
                    </div>
                </div>
                
                <!-- Status Section -->
                <div class="status-section">
                    <div class="section-header">
                        <h2>Attendance Status</h2>
                        <div class="date-display">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?php echo date('F j, Y'); ?></span>
                        </div>
                    </div>
                    
                    <div class="status-grid">
                        <div class="status-item present">
                            <div class="status-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="status-info">
                                <h4>Present</h4>
                                <p><?php echo $present_today; ?> students</p>
                            </div>
                        </div>
                        
                        <div class="status-item absent">
                            <div class="status-icon">
                                <i class="fas fa-times"></i>
                            </div>
                            <div class="status-info">
                                <h4>Absent</h4>
                                <p><?php echo $absent_today; ?> students</p>
                            </div>
                        </div>
                        
                        <div class="status-item unknown">
                            <div class="status-icon">
                                <i class="fas fa-question"></i>
                            </div>
                            <div class="status-info">
                                <h4>Unknown</h4>
                                <p><?php echo $unknown_today; ?> students</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Form -->
                <div class="attendance-form">
                    <div class="section-header">
                        <h2>Mark Attendance</h2>
                    </div>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-card">
                                    <h4><i class="fas fa-id-card"></i> STUDENT ID</h4>
                                    <div class="form-group">
                                        <input type="text" name="enrollment" class="form-control" placeholder="Enter Student ID" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-card">
                                    <h4><i class="fas fa-calendar"></i> Date</h4>
                                    <div class="form-group">
                                        <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d')?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions text-center mt-4">
                            <button type="submit" name="submit" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Mark Present
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Recent Activity -->
                <div class="recent-activity">
                    <div class="section-header">
                        <h2>Recent Attendance Records</h2>
                    </div>
                    
                    <ul class="activity-list">
                        <?php if($recent_result && $recent_result->num_rows > 0): ?>
                            <?php while($row = $recent_result->fetch_assoc()): ?>
                                <li class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4><?php echo htmlspecialchars($row['SName']); ?> (ID: <?php echo htmlspecialchars($row['CMS']); ?>)</h4>
                                        <p>Room No: <?php echo htmlspecialchars($row['SRNo']); ?> | Status: <?php echo htmlspecialchars($row['Status']); ?></p>
                                    </div>
                                    <div class="activity-time">
                                        <?php echo date('M j, g:i A', strtotime($row['SADate'])); ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-content">
                                    <p>No recent attendance records found.</p>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Quick Actions -->
                <div class="section-header">
                    <h2>Quick Actions</h2>
                </div>
                
                <div class="quick-actions">
                    <a href="view-attendance.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <h4>View All Records</h4>
                    </a>
                    
                    <a href="view-students-acc.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Manage Students</h4>
                    </a>
                    
                    <a href="attendance-report.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4>Generate Reports</h4>
                    </a>
                    
                    <a href="bulk-attendance.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <h4>Bulk Upload</h4>
                    </a>
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
    <script src="../assets/extra-libs/c3/d3.min.js"></script>
    <script src="../assets/extra-libs/c3/c3.min.js"></script>
    <script src="../assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="../dist/js/pages/dashboards/dashboard1.min.js"></script>

    <script>
        // Add some interactive effects
        $(document).ready(function() {
            // Add animation to cards on page load
            $('.card').each(function(i) {
                $(this).delay(i * 200).animate({opacity: 1, top: 0}, 500);
            });
            
            // Focus on Student ID input field
            $('input[name="enrollment"]').focus();
        });
    </script>

</body>

</html>