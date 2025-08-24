<?php
session_start();
include('../includes/dbconn.php');
if (!isset($_SESSION['hid'])) {
    die("❌ HID missing in session.");
}
$hid = $_SESSION['hid'];
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Hostel Management System Dashboard">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Hostel Management System - Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../dist/css/style.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #495057;
        }
        
        .dashboard-header {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 0;
            margin-bottom: 25px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.1);
        }
        
        .card-modern {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 24px;
            overflow: hidden;
        }
        
        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-header-modern {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            color: white;
            font-weight: 600;
            padding: 15px 20px;
            border-bottom: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        
        .card-header-modern i {
            transition: transform 0.3s;
        }
        
        .card-header-modern[aria-expanded="true"] i {
            transform: rotate(180deg);
        }
        
        .card-header-success {
            background: linear-gradient(120deg, #38b000, #38b000);
        }
        
        .card-header-warning {
            background: linear-gradient(120deg, #f48c06, #dc2f02);
        }
        
        .card-header-info {
            background: linear-gradient(120deg, #00b4d8, #0077b6);
        }
        
        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        .table-modern th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 12px 15px;
            position: sticky;
            top: 0;
        }
        
        .table-modern td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-modern tr:last-child td {
            border-bottom: none;
        }
        
        .table-modern tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-in {
            background-color: rgba(56, 176, 0, 0.15);
            color: #38b000;
        }
        
        .badge-out {
            background-color: rgba(230, 57, 70, 0.15);
            color: #e63946;
        }
        
        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            color: white;
            margin-bottom: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stats-card h2 {
            font-size: 2rem;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .stats-card p {
            margin-bottom: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .stats-card-primary {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
        }
        
        .stats-card-success {
            background: linear-gradient(120deg, #38b000, #2d8c00);
        }
        
        .stats-card-warning {
            background: linear-gradient(120deg, #f48c06, #dc2f02);
        }
        
        .stats-card-info {
            background: linear-gradient(120deg, #00b4d8, #0077b6);
        }
        
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-box input {
            border-radius: 50px;
            padding-left: 45px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }
        
        .search-box i {
            position: absolute;
            left: 20px;
            top: 12px;
            color: #6c757d;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            border-radius: 3px;
        }
        
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 15px;
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

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6"
         data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed"
         data-boxed-layout="full">
        
        <header class="topbar" data-navbarbg="skin6">
            <?php include('includes/navigation.php'); ?>
        </header>

        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include('includes/sidebar.php'); ?>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Dashboard</h4>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="dashboard-header">
                    <div class="container">
                        <h1 class="text-center mb-3"><i class="fas fa-home me-2"></i>MU HOSTEL DASHBOARD</h1>
                        
                        <!-- Quick Stats -->
                        <div class="row">
                            <?php
                            // Fetch stats for cards
                            $in_out_count = 0;
                            $query = "CALL in_out('$hid')";
                            if ($result = mysqli_query($conn, $query)) {
                                $in_out_count = mysqli_num_rows($result);
                                mysqli_free_result($result);
                                mysqli_next_result($conn);
                            }
                            
                            $mess_off_count = 0;
                            $query = "CALL mess_off('$hid')";
                            if ($result = mysqli_query($conn, $query)) {
                                $mess_off_count = mysqli_num_rows($result);
                                mysqli_free_result($result);
                                mysqli_next_result($conn);
                            }
                            
                            $complaints_count = 0;
                            $query = "SELECT COUNT(*) as count FROM complaints";
                            if ($result = mysqli_query($conn, $query)) {
                                $row = mysqli_fetch_assoc($result);
                                $complaints_count = $row['count'];
                                mysqli_free_result($result);
                            }
                            
                            $visitors_count = 0;
                            $query = "CALL get_visitors('$hid')";
                            if ($result = mysqli_query($conn, $query)) {
                                $visitors_count = mysqli_num_rows($result);
                                mysqli_free_result($result);
                                mysqli_next_result($conn);
                            }
                            ?>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="stats-card stats-card-primary">
                                    <i class="fas fa-door-open"></i>
                                    <h2><?php echo $in_out_count; ?></h2>
                                    <p>Students IN/OUT</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="stats-card stats-card-success">
                                    <i class="fas fa-utensils"></i>
                                    <h2><?php echo $mess_off_count; ?></h2>
                                    <p>Mess Off Requests</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="stats-card stats-card-warning">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <h2><?php echo $complaints_count; ?></h2>
                                    <p>Active Complaints</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="stats-card stats-card-info">
                                    <i class="fas fa-users"></i>
                                    <h2><?php echo $visitors_count; ?></h2>
                                    <p>Visitors Today</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="row mb-4">
                    <div class="col-md-6 mx-auto">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" placeholder="Search across all sections..." id="globalSearch">
                        </div>
                    </div>
                </div>

                <!-- Students IN/OUT -->
                <div class="card-modern">
                    <div class="card-header-modern" data-toggle="collapse" data-target="#inOutTable">
                        <span><i class="fas fa-door-open me-2"></i>Students IN/OUT</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div id="inOutTable" class="collapse show">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Room No</th>
                                            <th>Department</th>
                                            <th>Going Date</th>
                                            <th>Return Date</th>
                                            <th>City</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "CALL in_out('$hid')";
                                        if ($result = mysqli_query($conn, $query)) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $status = strtotime($row['ReturnDate']) > time() ? 'OUT' : 'IN';
                                                echo "<tr>
                                                        <td>{$row['SName']}</td>
                                                        <td>{$row['SRNo']}</td>
                                                        <td>{$row['Dept']}</td>
                                                        <td>{$row['LeaveDate']}</td>
                                                        <td>{$row['ReturnDate']}</td>
                                                        <td>{$row['City']}</td>
                                                        <td><span class='status-badge " . ($status == 'OUT' ? 'badge-out' : 'badge-in') . "'>$status</span></td>
                                                      </tr>";
                                            }
                                            mysqli_free_result($result);
                                            mysqli_next_result($conn);
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>❌ Query Failed: " . mysqli_error($conn) . "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Mess Off -->
                <div class="card-modern">
                    <div class="card-header-modern card-header-success" data-toggle="collapse" data-target="#messOffTable">
                        <span><i class="fas fa-utensils me-2"></i>Students Mess Off</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div id="messOffTable" class="collapse">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Room No</th>
                                            <th>CMS</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Days</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "CALL mess_off('$hid')";
                                        if ($result = mysqli_query($conn, $query)) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $days = ceil((strtotime($row['MEDate']) - strtotime($row['MSDate'])) / (60 * 60 * 24));
                                                echo "<tr>
                                                        <td>{$row['SName']}</td>
                                                        <td>{$row['SRNo']}</td>
                                                        <td>{$row['CMS']}</td>
                                                        <td>{$row['MSDate']}</td>
                                                        <td>{$row['MEDate']}</td>
                                                        <td>{$days} days</td>
                                                      </tr>";
                                            }
                                            mysqli_free_result($result);
                                            mysqli_next_result($conn);
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complaints -->
                <div class="card-modern">
                    <div class="card-header-modern card-header-warning" data-toggle="collapse" data-target="#complaintsTable">
                        <span><i class="fas fa-exclamation-circle me-2"></i>Complaints</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div id="complaintsTable" class="collapse">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Room No</th>
                                            <th>Date</th>
                                            <th>Complain Type</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT RNo, CDate, CType, CDescription FROM complaints ORDER BY CDate DESC";
                                        if ($result = mysqli_query($conn, $query)) {
                                            if (mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                            <td>{$row['RNo']}</td>
                                                            <td>{$row['CDate']}</td>
                                                            <td>{$row['CType']}</td>
                                                            <td>{$row['CDescription']}</td>
                                                            <td><span class='status-badge badge-warning'>Pending</span></td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5' class='text-center'>No complaints found.</td></tr>";
                                            }
                                            mysqli_free_result($result);
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center'>❌ Query Failed: " . mysqli_error($conn) . "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Visitors -->
                <div class="card-modern">
                    <div class="card-header-modern card-header-info" data-toggle="collapse" data-target="#visitorsTable">
                        <span><i class="fas fa-users me-2"></i>Students Visitors</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div id="visitorsTable" class="collapse">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Room No</th>
                                            <th>Visitor</th>
                                            <th>Relation</th>
                                            <th>Date</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "CALL get_visitors('$hid')";
                                        if ($result = mysqli_query($conn, $query)) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>
                                                        <td>{$row['SName']}</td>
                                                        <td>{$row['SRNo']}</td>
                                                        <td>{$row['VName']}</td>
                                                        <td>{$row['VRelation']}</td>
                                                        <td>{$row['VDate']}</td>
                                                        <td>{$row['VReason']}</td>
                                                      </tr>";
                                            }
                                            mysqli_free_result($result);
                                            mysqli_next_result($conn);
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('../includes/footer.php'); ?>
        </div>
    </div>

    <!-- JavaScript -->
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
            // Global search functionality
            $('#globalSearch').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                
                // Open all collapsible sections when searching
                $('.collapse').collapse('show');
                
                // Search across all tables
                $('.table-modern tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            
            // Add smooth scrolling to page elements
            $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(event) {
                if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        event.preventDefault();
                        $('html, body').animate({
                            scrollTop: target.offset().top - 70
                        }, 1000);
                    }
                }
            });
            
            // Add animation to stats cards on scroll
            function animateStats() {
                $('.stats-card').each(function() {
                    var position = $(this).offset().top;
                    var scroll = $(window).scrollTop();
                    var windowHeight = $(window).height();
                    
                    if (scroll > position - windowHeight + 100) {
                        $(this).addClass('animated');
                    }
                });
            }
            
            // Initial call and on scroll
            animateStats();
            $(window).scroll(animateStats);
        });
    </script>
</body>
</html>