<?php
    session_start();
    include('../includes/dbconn.php');
    include('../includes/check-login.php');
    check_login();
    
    $cms = $_SESSION['cms'];
    
    // Get student info
    $studentQuery = mysqli_query($conn, "SELECT * FROM student WHERE cms='$cms'");
    $studentData = mysqli_fetch_array($studentQuery);
    $name = $studentData['SName'];
    $room = $studentData['SRNo'];
    
    // Get attendance stats
    $attendanceQuery = mysqli_query($conn, "SELECT * FROM student_attendance WHERE cms=$cms");
    $totalAttendance = mysqli_num_rows($attendanceQuery);
    $presentCount = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM student_attendance WHERE cms=$cms AND Status='Present'"));
    $attendancePercentage = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100) : 0;
    
    // Get menu count
    $menuCount = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM menu"));
    
    // Get recent activities
    $recentActivities = [];
    
    // Check for recent complaints
    $complaintsQuery = mysqli_query($conn, "SELECT * FROM complaints WHERE RNo='$room' ORDER BY CDate DESC LIMIT 3");
    while ($row = mysqli_fetch_array($complaintsQuery)) {
        $recentActivities[] = [
            'type' => 'complaint',
            'title' => 'Complaint Submitted',
            'description' => $row['CType'],
            'date' => $row['CDate'],
            'icon' => 'fas fa-exclamation-circle'
        ];
    }
    
    // Check for recent suggestions
    $suggestionsQuery = mysqli_query($conn, "SELECT * FROM suggestions WHERE CMS='$cms' ORDER BY SUGDate DESC LIMIT 3");
    while ($row = mysqli_fetch_array($suggestionsQuery)) {
        $recentActivities[] = [
            'type' => 'suggestion',
            'title' => 'Suggestion Submitted',
            'description' => $row['SUGType'],
            'date' => $row['SUGDate'],
            'icon' => 'fas fa-lightbulb'
        ];
    }
    
    // Sort activities by date
    usort($recentActivities, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // Get only the 3 most recent activities
    $recentActivities = array_slice($recentActivities, 0, 3);
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
    <title>Student Dashboard - Hostel Management System</title>

    <!-- Custom CSS -->
    <link href="../dist/css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
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
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .stats-card.attendance::before { background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); }
        .stats-card.menu::before { background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%); }
        .stats-card.room::before { background: linear-gradient(135deg, #f72585 0%, #b5179e 100%); }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .stats-card.attendance .card-icon { color: #4361ee; background: rgba(67, 97, 238, 0.1); }
        .stats-card.menu .card-icon { color: #4cc9f0; background: rgba(76, 201, 240, 0.1); }
        .stats-card.room .card-icon { color: #f72585; background: rgba(247, 37, 133, 0.1); }
        
        .stats-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        
        .stats-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .stats-card .subtext {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            margin-top: 0.5rem;
        }
        
        .progress-bar {
            border-radius: 4px;
            background: linear-gradient(90deg, #4361ee 0%, #3a0ca3 100%);
        }
        
        .section-header {
            margin-bottom: 1.5rem;
            position: relative;
            padding-left: 15px;
        }
        
        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }
        
        .section-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 25px;
            background: var(--primary);
            border-radius: 10px;
        }
        
        .card-modern {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: none;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .card-modern:hover {
            box-shadow: var(--hover-shadow);
        }
        
        .card-header-modern {
            background: transparent;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark);
        }
        
        .card-body-modern {
            padding: 1.5rem;
        }
        
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-modern th {
            background: #f8f9fa;
            font-weight: 600;
            color: #6c757d;
            padding: 1rem;
            border: none;
        }
        
        .table-modern td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .table-modern tr:last-child td {
            border-bottom: none;
        }
        
        .table-modern tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-present {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        .badge-absent {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            text-decoration: none;
            color: var(--dark);
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: none;
        }
        
        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
            color: var(--primary);
        }
        
        .action-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .action-btn h4 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            text-align: center;
        }
        
        .recent-activities {
            margin-top: 2rem;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 12px;
            background: white;
            margin-bottom: 1rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            transform: translateX(5px);
            box-shadow: var(--hover-shadow);
        }
        
        .activity-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-content h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .activity-content p {
            margin: 0.25rem 0 0 0;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .activity-date {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6"
        data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        <!-- Topbar -->
        <header class="topbar" data-navbarbg="skin6">
            <?php include '../includes/student-navigation.php'?>
        </header>

        <!-- Sidebar -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include '../includes/student-sidebar.php'?>
            </div>
        </aside>

        <!-- Page wrapper -->
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 align-self-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="page-title text-dark font-weight-medium mb-1">Dashboard</h3>
                                <p class="text-muted mb-0">Welcome back, <?php echo $name; ?>! Here's your overview.</p>
                            </div>
                            <div class="status-badge badge-present">
                                <i class="fas fa-user me-1"></i> Room: <?php echo $room; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">

                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="stats-card attendance">
                        <div class="card-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>ATTENDANCE</h3>
                        <div class="number"><?php echo $presentCount; ?> Present</div>
                        <div class="subtext"><?php echo $attendancePercentage; ?>% Attendance Rate</div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $attendancePercentage; ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="stats-card menu">
                        <div class="card-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>MESS MENU</h3>
                        <div class="number"><?php echo $menuCount; ?> Days</div>
                        <div class="subtext">Weekly menu available</div>
                    </div>
                    
                    <div class="stats-card room">
                        <div class="card-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <h3>ROOM INFO</h3>
                        <div class="number">Room <?php echo $room; ?></div>
                        <div class="subtext">Your current accommodation</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Attendance Section -->
                        <div class="card-modern">
                            <div class="card-header-modern">
                                <i class="fas fa-calendar-check me-2"></i>Recent Attendance
                            </div>
                            <div class="card-body-modern">
                                <div class="table-responsive">
                                    <table class="table table-modern">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $recentAttendance = mysqli_query($conn,"SELECT * FROM student_attendance WHERE cms=$cms ORDER BY SADate DESC LIMIT 5");
                                                while ($row = mysqli_fetch_array($recentAttendance)) {
                                                    $badgeClass = $row['Status'] == 'Present' ? 'badge-present' : 'badge-absent';
                                            ?>
                                            <tr>
                                                <td><?php echo date('M j, Y', strtotime($row['SADate'])); ?></td>
                                                <td>
                                                    <span class="status-badge <?php echo $badgeClass; ?>">
                                                        <i class="fas <?php echo $row['Status'] == 'Present' ? 'fa-check' : 'fa-times'; ?> me-1"></i>
                                                        <?php echo $row['Status']; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Mess Menu Section -->
                        <div class="card-modern">
                            <div class="card-header-modern">
                                <i class="fas fa-utensils me-2"></i>Weekly Mess Menu
                            </div>
                            <div class="card-body-modern">
                                <div class="table-responsive">
                                    <table class="table table-modern">
                                        <thead>
                                            <tr>
                                                <th>Day</th>
                                                <th>Breakfast</th>
                                                <th>Lunch</th>
                                                <th>Dinner</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $menuQuery = mysqli_query($conn,"SELECT * FROM menu");
                                                while ($row = mysqli_fetch_array($menuQuery)) {
                                            ?>
                                            <tr>
                                                <td><b><?php echo $row['Day'];?></b></td>
                                                <td><?php echo $row['BREAKFAST'];?></td>
                                                <td><?php echo $row['LUNCH'];?></td>
                                                <td><?php echo $row['DINNER'];?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Recent Activities -->
                        <div class="card-modern">
                            <div class="card-header-modern">
                                <i class="fas fa-history me-2"></i>Recent Activities
                            </div>
                            <div class="card-body-modern">
                                <?php if (!empty($recentActivities)): ?>
                                    <?php foreach ($recentActivities as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="<?php echo $activity['icon']; ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <h4><?php echo $activity['title']; ?></h4>
                                            <p><?php echo $activity['description']; ?></p>
                                            <div class="activity-date">
                                                <?php echo date('M j, Y', strtotime($activity['date'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">No recent activities</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card-modern">
                            <div class="card-header-modern">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </div>
                            <div class="card-body-modern">
                                <div class="quick-actions">
                                    <a href="view-attendance.php" class="action-btn">
                                        <div class="action-icon"><i class="fas fa-calendar-check"></i></div>
                                        <h4>View Attendance</h4>
                                    </a>
                                    <a href="invoice.php" class="action-btn">
                                        <div class="action-icon"><i class="fas fa-wallet"></i></div>
                                        <h4>My Fees</h4>
                                    </a>
                                    <a href="hostel-details.php" class="action-btn">
                                        <div class="action-icon"><i class="fas fa-user"></i></div>
                                        <h4>My Profile</h4>
                                    </a>
                                    <a href="complaint.php" class="action-btn">
                                        <div class="action-icon"><i class="fas fa-exclamation-circle"></i></div>
                                        <h4>Complaints</h4>
                                    </a>
                                </div>
                            </div>
                        </div>
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
        // Add animation to elements
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats cards
            const cards = document.querySelectorAll('.stats-card, .action-btn');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>