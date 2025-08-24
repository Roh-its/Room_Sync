<?php
session_start();
include('../includes/dbconn.php');
include('../includes/check-login.php');
check_login();

// Get statistics for dashboard
$hid = $_SESSION['hid'];

// Total parcels
$total_query = "SELECT COUNT(*) as total FROM parcel p JOIN student s ON p.cms = s.CMS WHERE s.shid = $hid";
$total_result = $conn->query($total_query);
$total_parcels = $total_result ? $total_result->fetch_assoc()['total'] : 0;

// Parcels received
$received_query = "SELECT COUNT(*) as received FROM parcel p JOIN student s ON p.cms = s.CMS WHERE s.shid = $hid AND p.pstatus = 'Received'";
$received_result = $conn->query($received_query);
$received_parcels = $received_result ? $received_result->fetch_assoc()['received'] : 0;

// Parcels pending
$pending_query = "SELECT COUNT(*) as pending FROM parcel p JOIN student s ON p.cms = s.CMS WHERE s.shid = $hid AND p.pstatus = 'Not Received'";
$pending_result = $conn->query($pending_query);
$pending_parcels = $pending_result ? $pending_result->fetch_assoc()['pending'] : 0;

// Recent parcels - let's debug the column names first
$debug_query = "SELECT p.*, s.SName, s.SRNo 
                 FROM parcel p 
                 JOIN student s ON p.cms = s.CMS 
                 WHERE s.shid = $hid 
                 ORDER BY p.pdate DESC 
                 LIMIT 1";
$debug_result = $conn->query($debug_query);
$debug_columns = array();
if ($debug_result && $debug_result->num_rows > 0) {
    $row = $debug_result->fetch_assoc();
    $debug_columns = array_keys($row);
}

// Now get recent parcels for display
$recent_query = "SELECT p.*, s.SName, s.SRNo 
                 FROM parcel p 
                 JOIN student s ON p.cms = s.CMS 
                 WHERE s.shid = $hid 
                 ORDER BY p.pdate DESC 
                 LIMIT 5";
$recent_result = $conn->query($recent_query);

if (isset($_POST['submit'])) {
    $date = $_POST['date'];
    $status = "Not Received";
    $type = $_POST['type'];
    $cms = $_POST['cms'];
    $sql = mysqli_query($conn, "INSERT INTO parcel(pdate, pstatus, ptype, cms) VALUES('$date', '$status', '$type', $cms)");
    if ($sql) {
        echo "<script>alert('Parcel added successfully');</script>";
        // Refresh the page to update statistics
        echo "<script>window.location.href = 'parcel.php';</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again');</script>";
    }
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
    <title>Hostel Management System - Parcel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <!-- This page plugin CSS -->
    <link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
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
        
        /* Form Styling */
        .parcel-form {
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
            display: flex;
            align-items: center;
            gap: 10px;
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
            background: var(--info);
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
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-received {
            background: #e6f7ee;
            color: #0bb56d;
        }
        
        .status-pending {
            background: #fef5e7;
            color: #f9a825;
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
        
        /* Debug info */
        .debug-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .debug-info h4 {
            margin: 0 0 10px 0;
            color: #1565c0;
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
            <?php include 'includes/navigation.php' ?>
        </header>

        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include 'includes/sidebar.php' ?>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Parcel Management</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Parcel Management</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <!-- Debug Information -->
                <div class="debug-info">
                    <h4>Debug Information:</h4>
                    <p>Columns returned by query: <?php echo implode(', ', $debug_columns); ?></p>
                </div>
                
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <h3>Total Parcels</h3>
                        <div class="number"><?php echo $total_parcels; ?></div>
                    </div>
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Parcels Received</h3>
                        <div class="number"><?php echo $received_parcels; ?></div>
                    </div>
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Parcels Pending</h3>
                        <div class="number"><?php echo $pending_parcels; ?></div>
                    </div>
                </div>

                <!-- Parcel Form -->
                <div class="parcel-form">
                    <div class="section-header">
                        <h2>Add New Parcel</h2>
                    </div>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-card">
                                    <h4><i class="fas fa-id-card"></i> CMS ID</h4>
                                    <div class="form-group">
                                        <input type="text" name="cms" class="form-control" placeholder="Enter Student CMS ID" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-card">
                                    <h4><i class="fas fa-tag"></i> Parcel Type</h4>
                                    <div class="form-group">
                                        <input type="text" name="type" class="form-control" placeholder="Enter Parcel Type" required>
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
                                <i class="fas fa-plus-circle"></i> Add Parcel
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Recent Activity -->
                <div class="recent-activity">
                    <div class="section-header">
                        <h2>Recent Parcels</h2>
                    </div>
                    
                    <ul class="activity-list">
                        <?php if($recent_result && $recent_result->num_rows > 0): ?>
                            <?php while($row = $recent_result->fetch_assoc()): ?>
                                <li class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4>
                                            <?php echo isset($row['SName']) ? htmlspecialchars($row['SName']) : 'Unknown Student'; ?> 
                                            (CMS: <?php echo isset($row['cms']) ? htmlspecialchars($row['cms']) : 'N/A'; ?>)
                                        </h4>
                                        <p>
                                            Type: <?php echo isset($row['ptype']) ? htmlspecialchars($row['ptype']) : 'N/A'; ?> | 
                                            Room: <?php echo isset($row['SRNo']) ? htmlspecialchars($row['SRNo']) : 'N/A'; ?> | 
                                            Status: <span class="status-badge <?php echo (isset($row['pstatus']) && $row['pstatus'] == 'Received') ? 'status-received' : 'status-pending'; ?>">
                                                <?php echo isset($row['pstatus']) ? htmlspecialchars($row['pstatus']) : 'Unknown'; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="activity-time">
                                        <?php 
                                            if (isset($row['pdate'])) {
                                                echo date('M j, g:i A', strtotime($row['pdate']));
                                            } else {
                                                echo 'Date not available';
                                            }
                                        ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-content">
                                    <p>No recent parcels found.</p>
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
                    <a href="view-parcels.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <h4>View All Parcels</h4>
                    </a>
                    
                    <a href="view-students-acc.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Manage Students</h4>
                    </a>
                    
                    <a href="parcel-report.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4>Generate Reports</h4>
                    </a>
                    
                    <a href="bulk-parcel.php" class="action-btn">
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
    <script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>

    <script>
        // Add some interactive effects
        $(document).ready(function() {
            // Add animation to cards on page load
            $('.card').each(function(i) {
                $(this).delay(i * 200).animate({opacity: 1, top: 0}, 500);
            });
            
            // Focus on CMS input field
            $('input[name="cms"]').focus();
        });
    </script>

</body>

</html>