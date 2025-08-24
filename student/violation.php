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
    
    // Get violation statistics
    $totalSql = "SELECT COUNT(*) as total FROM student_violation WHERE cms = '$cms'";
    $totalResult = mysqli_query($conn, $totalSql);
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalViolations = $totalRow['total'];

    $recentSql = "SELECT COUNT(*) as recent FROM student_violation WHERE cms = '$cms' AND VDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $recentResult = mysqli_query($conn, $recentSql);
    $recentRow = mysqli_fetch_assoc($recentResult);
    $recentViolations = $recentRow['recent'];

    // Get violation types count
    $typesSql = "SELECT VType, COUNT(*) as count FROM student_violation WHERE cms = '$cms' GROUP BY VType";
    $typesResult = mysqli_query($conn, $typesSql);
    $violationTypes = [];
    while($typeRow = mysqli_fetch_assoc($typesResult)) {
        $violationTypes[$typeRow['VType']] = $typeRow['count'];
    }
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Hostel Management System</title>
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
            --warning: #f8961e;
            --danger: #f72585;
            --success: #4cc9f0;
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
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-warning {
            background: rgba(248, 150, 30, 0.15);
            color: #f8961e;
        }
        
        .badge-danger {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        
        .badge-info {
            background: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }
        
        .stats-card {
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            height: 100%;
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
        
        /* Animation for new records */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .violation-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .icon-warning {
            background: rgba(248, 150, 30, 0.1);
            color: #f8961e;
        }
        
        .icon-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .icon-info {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }
        
        .violation-type {
            font-weight: 500;
            color: #495057;
        }
        
        .violation-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .severity-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .severity-high {
            background-color: #dc3545;
        }
        
        .severity-medium {
            background-color: #f8961e;
        }
        
        .severity-low {
            background-color: #17a2b8;
        }
        
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .progress-warning {
            background: linear-gradient(90deg, #f8961e 0%, #f3722c 100%);
        }
        
        .progress-danger {
            background: linear-gradient(90deg, #f94144 0%, #f3722c 100%);
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
                                <h4 class="page-title text-dark font-weight-medium mb-1">Violation Records</h4>
                                <p class="text-muted mb-0">View your hostel rule violation history</p>
                            </div>
                            <div>
                                <span class="status-badge badge-warning">
                                    <i class="fas fa-user me-1"></i> <?php echo $name; ?> | Room: <?php echo $room; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3><?php echo $totalViolations; ?></h3>
                            <p class="mb-0">Total Violations</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f8961e 0%, #f3722c 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3><?php echo $recentViolations; ?></h3>
                            <p class="mb-0">Last 30 Days</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43aa8b 0%, #4d908e 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3><?php echo count($violationTypes); ?></h3>
                            <p class="mb-0">Violation Types</p>
                        </div>
                    </div>
                </div>

                <!-- Violation Types Overview -->
                <?php if (!empty($violationTypes)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="section-title">Violation Types Overview</h5>
                        <div class="row">
                            <?php foreach ($violationTypes as $type => $count): 
                                $percentage = ($count / $totalViolations) * 100;
                                $severity = 'warning';
                                if ($percentage > 50) $severity = 'danger';
                                if ($percentage < 20) $severity = 'info';
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="violation-type">
                                        <span class="severity-indicator severity-<?php echo $severity; ?>"></span>
                                        <?php echo htmlspecialchars($type); ?>
                                    </span>
                                    <span class="text-muted"><?php echo $count; ?> violation(s)</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-<?php echo $severity; ?>" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Violations Table -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="section-title">Violation History</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Violation ID</th>
                                        <th>Details</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Severity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM student_violation WHERE cms = '$cms' ORDER BY VDate DESC";
                                        $result = mysqli_query($conn, $sql);
                                        $rowCount = mysqli_num_rows($result);
                                        
                                        if($rowCount > 0){
                                            while($row = mysqli_fetch_array($result)){
                                                $violationType = $row['VType'];
                                                $severity = 'warning';
                                                $iconClass = "icon-warning";
                                                $badgeClass = "badge-warning";
                                                $icon = "fas fa-exclamation-triangle";
                                                
                                                // Determine severity based on type (this is a simple example)
                                                if (stripos($violationType, 'serious') !== false || stripos($violationType, 'major') !== false) {
                                                    $severity = 'danger';
                                                    $iconClass = "icon-danger";
                                                    $badgeClass = "badge-danger";
                                                    $icon = "fas fa-times-circle";
                                                } elseif (stripos($violationType, 'minor') !== false || stripos($violationType, 'warning') !== false) {
                                                    $severity = 'info';
                                                    $iconClass = "icon-info";
                                                    $badgeClass = "badge-info";
                                                    $icon = "fas fa-info-circle";
                                                }

                                                echo "<tr class='fade-in'>
                                                        <td>#".$row['VID']."</td>
                                                        <td>
                                                            <div class='d-flex align-items-center'>
                                                                <div class='violation-icon $iconClass'>
                                                                    <i class='$icon'></i>
                                                                </div>
                                                                <div>
                                                                    <div class='violation-type'>".$row['VType']."</div>
                                                                    <div class='violation-date'>CMS: ".$cms."</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>".date('M j, Y', strtotime($row['VDate']))."</td>
                                                        <td>".$row['VType']."</td>
                                                        <td>
                                                            <span class='status-badge $badgeClass'>
                                                                <i class='$icon me-1'></i>".ucfirst($severity)."
                                                            </span>
                                                        </td>
                                                      </tr>";
                                            }
                                        } else {
                                            echo "<tr>
                                                    <td colspan='5' class='text-center py-5'>
                                                        <div class='text-muted mb-3'>
                                                            <i class='fas fa-check-circle fa-3x text-success'></i>
                                                        </div>
                                                        <h5 class='text-muted'>No Violations Found</h5>
                                                        <p class='text-muted'>Great job! You have no violation records.</p>
                                                    </td>
                                                  </tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Guidelines Card -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="section-title">Hostel Guidelines</h5>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong> Please adhere to hostel rules to avoid violations
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-medium mb-3"><i class="fas fa-ban text-danger me-2"></i>Prohibited Actions</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Noise after 11 PM</li>
                                    <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Unauthorized guests</li>
                                    <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Damage to property</li>
                                    <li class="mb-0"><i class="fas fa-times text-danger me-2"></i>Smoking in rooms</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-medium mb-3"><i class="fas fa-check-circle text-success me-2"></i>Expected Behavior</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Maintain cleanliness</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Follow curfew times</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Respect other residents</li>
                                    <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Report issues promptly</li>
                                </ul>
                            </div>
                        </div>
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
    <script src="../assets/extra-libs/c3/d3.min.js"></script>
    <script src="../assets/extra-libs/c3/c3.min.js"></script>
    <script src="../assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="../dist/js/pages/dashboards/dashboard1.min.js"></script>

    <script>
        // Add animation to new elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to table rows
            const rows = document.querySelectorAll('tr.fade-in');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                setTimeout(() => {
                    row.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    row.style.opacity = '1';
                }, 100 * index);
            });
            
            // Add hover effects to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>

</body>
</html>