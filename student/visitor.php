<?php
    session_start();
    include('../includes/dbconn.php');
    include('../includes/check-login.php');
    check_login();
    $cms = $_SESSION['cms'];
    $ret=mysqli_query($conn,"select * from student where cms='$cms'");
    $row=mysqli_fetch_array($ret);
    $name = $row['SName'];
    $room = $row['SRNo'];

    $successMsg = $errorMsg = "";

    if(isset($_POST['submit'])){
        $vname = $_POST['vname'];
        $relation = $_POST['relation'];
        $cnic = $_POST['cnic'];
        $phone = $_POST['phone'];
        $reason = $_POST['reason'];
        $date = $_POST['date']; 
        $sql = "INSERT INTO `visitor`(VName, VRelation, VPhone, VCnic, VDate, VReason, CMS) 
                VALUES ('$vname', '$relation', '$phone', '$cnic', '$date', '$reason', '$cms')";
        $query = $conn->query($sql);
        if($query){
            $successMsg = "✅ Visitor record added successfully";
        }else{
            $errorMsg = "❌ Something went wrong. Please try again.";
        }
    }
    elseif(isset($_POST['deleteentry'])){
        $id = $_POST['id'];
        $sql = "DELETE FROM `visitor` WHERE VID='$id'";
        $query = $conn->query($sql);
        if($query){
            $successMsg = "🗑️ Visitor record deleted successfully";
        }else{
            $errorMsg = "❌ Unable to delete record. Please check the ID.";
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
            margin-bottom: 25px;
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
        
        .btn-danger {
            background: var(--danger);
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
        
        .visitor-card {
            border-left: 4px solid var(--primary);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #495057;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
    </div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" 
         data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        <header class="topbar" data-navbarbg="skin6">
            <?php include '../includes/student-navigation.php'?>
        </header>

        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include '../includes/student-sidebar.php'?>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title text-dark font-weight-medium mb-1">Visitor Management</h4>
                                <p class="text-muted mb-0">Manage your visitor records and requests</p>
                            </div>
                            <div>
                                <span class="status-badge badge-active">
                                    <i class="fas fa-user me-1"></i> <?php echo $name; ?> | Room: <?php echo $room; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <?php
                    $totalSql = "SELECT COUNT(*) as total FROM visitor WHERE cms = '$cms'";
                    $totalResult = mysqli_query($conn, $totalSql);
                    $totalRow = mysqli_fetch_assoc($totalResult);
                    $totalRecords = $totalRow['total'];
                    
                    $today = date('Y-m-d');
                    $todaySql = "SELECT COUNT(*) as today FROM visitor WHERE cms = '$cms' AND VDate = '$today'";
                    $todayResult = mysqli_query($conn, $todaySql);
                    $todayRow = mysqli_fetch_assoc($todayResult);
                    $todayRecords = $todayRow['today'];
                    
                    $upcomingSql = "SELECT COUNT(*) as upcoming FROM visitor WHERE cms = '$cms' AND VDate > '$today'";
                    $upcomingResult = mysqli_query($conn, $upcomingSql);
                    $upcomingRow = mysqli_fetch_assoc($upcomingResult);
                    $upcomingRecords = $upcomingRow['upcoming'];
                    ?>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3><?php echo $totalRecords; ?></h3>
                            <p class="mb-0">Total Visitors</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <h3><?php echo $todayRecords; ?></h3>
                            <p class="mb-0">Today's Visitors</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h3><?php echo $upcomingRecords; ?></h3>
                            <p class="mb-0">Upcoming Visits</p>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if ($successMsg): ?>
                    <div class="alert alert-success fade-in"><?php echo $successMsg; ?></div>
                <?php elseif ($errorMsg): ?>
                    <div class="alert alert-danger fade-in"><?php echo $errorMsg; ?></div>
                <?php endif; ?>

                <!-- Visitor Records Table -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="section-title">Visitor History</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Visitor Name</th>
                                        <th>Relation</th>
                                        <th>Phone</th>
                                        <th>CNIC</th>
                                        <th>Date</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM visitor WHERE cms = '$cms' ORDER BY VDate DESC";
                                        $result = mysqli_query($conn, $sql);
                                        $rowCount = mysqli_num_rows($result);
                                        if($rowCount > 0){
                                            while($row = mysqli_fetch_array($result)){
                                                $visitDate = $row['VDate'];
                                                $currentDate = date('Y-m-d');
                                                $status = '';
                                                
                                                if ($visitDate > $currentDate) {
                                                    $status = '<span class="status-badge badge-upcoming"><i class="fas fa-clock me-1"></i> Upcoming</span>';
                                                } else if ($visitDate == $currentDate) {
                                                    $status = '<span class="status-badge badge-active"><i class="fas fa-running me-1"></i> Today</span>';
                                                } else {
                                                    $status = '<span class="status-badge badge-completed"><i class="fas fa-check-circle me-1"></i> Completed</span>';
                                                }
                                                
                                                echo "<tr class='fade-in'>
                                                        <td>".$row['VID']."</td>
                                                        <td>".$row['VName']."</td>
                                                        <td>".$row['VRelation']."</td>
                                                        <td>".$row['VPhone']."</td>
                                                        <td>".$row['VCnic']."</td>
                                                        <td>".date('M j, Y', strtotime($row['VDate']))."</td>
                                                        <td>".$row['VReason']."</td>
                                                        <td>".$status."</td>
                                                      </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center py-4'>No visitor records found. Add your first visitor below.</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Delete Record Form -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="section-title">Delete Visitor Record</h5>
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Visitor ID</label>
                                                <input type="text" id="id" name="id" placeholder="Enter Visitor ID to delete" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type='submit' class='btn btn-danger mt-md-0 mt-2' name='deleteentry' onclick="return confirm('Are you sure you want to delete this visitor record?');">
                                                <i class="fas fa-trash-alt me-2"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="section-title">Quick Info</h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Total Visitors:</span>
                                    <span class="fw-bold"><?php echo $totalRecords; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Today's Visitors:</span>
                                    <span class="fw-bold text-primary"><?php echo $todayRecords; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Upcoming Visits:</span>
                                    <span class="fw-bold text-info"><?php echo $upcomingRecords; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor Form -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="section-title">New Visitor Request</h5>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label class="form-label required-field">CMS ID</label>
                                    <input type="text" name="cms" value="<?php echo $cms?>" readonly class="form-control">
                                </div>

                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label class="form-label required-field">Student Name</label>
                                    <input type="text" name="name" value="<?php echo $name?>" readonly class="form-control">
                                </div>

                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label class="form-label required-field">Room Number</label>
                                    <input type="text" value="<?php echo $room?>" readonly class="form-control">
                                </div>

                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label class="form-label required-field">Date of Visit</label>
                                    <input type="date" name="date" required class="form-control" min="<?php echo date('Y-m-d'); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Visitor Name</label>
                                    <input type="text" name="vname" placeholder="Full name of visitor" required class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Relation</label>
                                    <input type="text" name="relation" placeholder="Relationship with visitor" required class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">CNIC Number</label>
                                    <input type="text" name="cnic" placeholder="XXXXX-XXXXXXX-X" required class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Contact Number</label>
                                    <input type="text" name="phone" placeholder="+91-xxxxxxxxxxx" required class="form-control">
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label required-field">Reason for Visit</label>
                                    <input type="text" name="reason" placeholder="Purpose of the visit" required class="form-control">
                                </div>
                            </div>

                            <div class="form-actions text-center mt-4">
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
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="date"]').setAttribute('min', today);
            
            // Add animation to alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '1';
                }, 100);
            });
            
            // CNIC input formatting
            const cnicInput = document.querySelector('input[name="cnic"]');
            cnicInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 13) value = value.slice(0, 13);
                
                if (value.length > 5) {
                    value = value.slice(0, 5) + '-' + value.slice(5);
                }
                if (value.length > 13) {
                    value = value.slice(0, 13) + '-' + value.slice(13);
                }
                e.target.value = value;
            });
            
            // Phone input formatting
            const phoneInput = document.querySelector('input[name="phone"]');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                
                if (value.length > 4) {
                    value = value.slice(0, 4) + '-' + value.slice(4);
                }
                e.target.value = value;
            });
        });
    </script>
</body>
</html>