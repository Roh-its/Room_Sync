<?php
session_start();
include('../includes/dbconn.php');
include('../includes/check-login.php');
check_login();

$cms = $_SESSION['cms'];
$ret = mysqli_query($conn, "SELECT * FROM student WHERE cms='$cms'");
$row = mysqli_fetch_array($ret);
$name = $row['SName'];

$successMsg = $errorMsg = "";

// Insert record
if (isset($_POST['submit'])) {
    $going = $_POST['going'];
    $return = $_POST['return'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO mess_off(cms, MSDate, MEDate, MReason) VALUES ('$cms','$going','$return','$reason')";
    $query = $conn->query($sql);

    if ($query) {
        $successMsg = "✅ Mess off request added successfully.";
    } else {
        $errorMsg = "❌ Something went wrong. Please try again.";
    }
}

// Delete record
if (isset($_POST['deleteentry'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM mess_off WHERE id='$id'";
    $query = $conn->query($sql);

    if ($query) {
        $successMsg = "🗑️ Record deleted successfully.";
    } else {
        $errorMsg = "❌ Unable to delete record.";
    }
}
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hostel Management System</title>
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../dist/css/style.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        body {
            font-family: 'Raleway', sans-serif;
            background-color: #f5f7fb;
            color: #495057;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: var(--hover-shadow);
        }
        
        .page-title {
            font-weight: 700;
            color: var(--dark);
            position: relative;
            padding-bottom: 10px;
        }
        
        .page-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }
        
        .section-title {
            position: relative;
            padding-left: 15px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .section-title:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 20px;
            background: var(--primary);
            border-radius: 10px;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #6c757d;
            background-color: #f8f9fa;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .btn-action {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-active {
            background: rgba(76, 201, 240, 0.15);
            color: #4cc9f0;
        }
        
        .badge-upcoming {
            background: rgba(248, 150, 30, 0.15);
            color: #f8961e;
        }
        
        .badge-completed {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        .badge-pending {
            background: rgba(108, 117, 125, 0.15);
            color: #6c757d;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 8px;
                border: 1px solid #e2e8f0;
            }
        }
        
        /* Custom button styles */
        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }
        
        .btn-success {
            background: var(--success);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-dark {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        /* Animation for new records */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .duration-badge {
            background: rgba(73, 80, 87, 0.1);
            color: #495057;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
    </div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical"
        data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Topbar -->
        <header class="topbar" data-navbarbg="skin6">
            <?php include '../includes/student-navigation.php'?>
        </header>

        <!-- Sidebar -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar"><?php include '../includes/student-sidebar.php'?></div>
        </aside>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title text-dark font-weight-medium mb-1">Mess Off Management</h4>
                                <p class="text-muted mb-0">Request and manage your mess off periods</p>
                            </div>
                            <div>
                                <span class="status-badge badge-active">
                                    <i class="fas fa-user me-1"></i> <?php echo $name; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <?php
                    $totalSql = "SELECT COUNT(*) as total FROM mess_off WHERE cms = '$cms'";
                    $totalResult = mysqli_query($conn, $totalSql);
                    $totalRow = mysqli_fetch_assoc($totalResult);
                    $totalRecords = $totalRow['total'];
                    
                    $activeSql = "SELECT COUNT(*) as active FROM mess_off WHERE cms = '$cms' AND MSDate <= CURDATE() AND MEDate >= CURDATE()";
                    $activeResult = mysqli_query($conn, $activeSql);
                    $activeRow = mysqli_fetch_assoc($activeResult);
                    $activeRecords = $activeRow['active'];
                    
                    $upcomingSql = "SELECT COUNT(*) as upcoming FROM mess_off WHERE cms = '$cms' AND MSDate > CURDATE()";
                    $upcomingResult = mysqli_query($conn, $upcomingSql);
                    $upcomingRow = mysqli_fetch_assoc($upcomingResult);
                    $upcomingRecords = $upcomingRow['upcoming'];
                    ?>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <h3><?php echo $totalRecords; ?></h3>
                            <p class="mb-0">Total Requests</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h3><?php echo $activeRecords; ?></h3>
                            <p class="mb-0">Active Now</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3><?php echo $upcomingRecords; ?></h3>
                            <p class="mb-0">Upcoming</p>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if ($successMsg): ?>
                    <div class="alert alert-success fade-in"><?php echo $successMsg; ?></div>
                <?php elseif ($errorMsg): ?>
                    <div class="alert alert-danger fade-in"><?php echo $errorMsg; ?></div>
                <?php endif; ?>

                <!-- Mess Off Records Table -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="section-title">Request History</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Duration</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql = "SELECT * FROM mess_off WHERE cms = '$cms' ORDER BY MSDate DESC";
                                $result = mysqli_query($conn, $sql);
                                if (mysqli_num_rows($result) > 0) {
                                    $count = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $currentDate = date('Y-m-d');
                                        $startDate = $row['MSDate'];
                                        $endDate = $row['MEDate'];
                                        
                                        // Calculate duration
                                        $datetime1 = new DateTime($startDate);
                                        $datetime2 = new DateTime($endDate);
                                        $interval = $datetime1->diff($datetime2);
                                        $duration = $interval->format('%a days');
                                        
                                        // Determine status
                                        if ($endDate < $currentDate) {
                                            $status = '<span class="status-badge badge-completed"><i class="fas fa-check-circle me-1"></i> Completed</span>';
                                        } else if ($startDate <= $currentDate && $endDate >= $currentDate) {
                                            $status = '<span class="status-badge badge-active"><i class="fas fa-running me-1"></i> Active</span>';
                                        } else {
                                            $status = '<span class="status-badge badge-upcoming"><i class="fas fa-clock me-1"></i> Upcoming</span>';
                                        }
                                        
                                        echo "<tr class='fade-in'>
                                                <td>".$count."</td>
                                                <td>".date('M j, Y', strtotime($row['MSDate']))."</td>
                                                <td>".date('M j, Y', strtotime($row['MEDate']))."</td>
                                                <td><span class='duration-badge'>".$duration."</span></td>
                                                <td>".$row['MReason']."</td>
                                                <td>".$status."</td>
                                                <td>
                                                    <form method='POST' style='display:inline-block;'>
                                                        <input type='hidden' name='id' value='".$row['ID']."'>
                                                        <button type='submit' name='deleteentry' class='btn btn-sm btn-danger btn-action' onclick=\"return confirm('Are you sure you want to delete this mess off request?');\">
                                                            <i class='fas fa-trash-alt'></i>
                                                        </button>
                                                    </form>
                                                </td>
                                              </tr>";
                                        $count++;
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-4'>No mess off requests found. Submit your first request below.</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mess Off Form -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="section-title">New Mess Off Request</h5>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CMS ID</label>
                                    <input type="text" name="cms" value="<?php echo $cms?>" readonly class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" value="<?php echo $name?>" readonly class="form-control">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Reason <span class="text-danger">*</span></label>
                                    <input type="text" name="reason" placeholder="Enter reason for mess off" required class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="going" required class="form-control" id="startDate">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="return" required class="form-control" id="endDate">
                                    <small class="text-muted" id="durationDisplay"></small>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" name="submit" class="btn btn-success px-4 me-2">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Request
                                </button>
                                <button type="reset" class="btn btn-dark px-4">
                                    <i class="fas fa-redo me-2"></i>Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <?php include '../includes/footer.php' ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../dist/js/app-style-switcher.js"></script>
    <script src="../dist/js/feather.min.js"></script>
    <script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../dist/js/sidebarmenu.js"></script>
    <script src="../dist/js/custom.min.js"></script>
    
    <script>
        // Add animation to new elements
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date for start and end fields to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('startDate').setAttribute('min', today);
            document.getElementById('endDate').setAttribute('min', today);
            
            // Automatically set end date minimum based on start date
            document.getElementById('startDate').addEventListener('change', function() {
                const startDate = this.value;
                document.getElementById('endDate').setAttribute('min', startDate);
                calculateDuration();
            });
            
            document.getElementById('endDate').addEventListener('change', calculateDuration);
            
            function calculateDuration() {
                const startDate = new Date(document.getElementById('startDate').value);
                const endDate = new Date(document.getElementById('endDate').value);
                
                if (startDate && endDate && startDate <= endDate) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end days
                    document.getElementById('durationDisplay').textContent = `Duration: ${diffDays} day(s)`;
                } else {
                    document.getElementById('durationDisplay').textContent = '';
                }
            }
            
            // Add animation to alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '1';
                }, 100);
            });
        });
    </script>
</body>
</html>