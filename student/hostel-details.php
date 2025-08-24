<?php
    session_start();
    include('../includes/dbconn.php');
    include('../includes/check-login.php');
    check_login();
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Hostel Management System</title>
    
    <!-- Custom CSS -->
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="../dist/css/style.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
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
        }
        
        .card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
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
        
        .info-card {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .info-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-icon {
            width: 40px;
            height: 40px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
            font-size: 18px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 180px;
        }
        
        .detail-value {
            font-weight: 500;
            color: var(--dark);
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
        
        @media (max-width: 768px) {
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .detail-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
        
        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .qr-code {
            width: 120px;
            height: 120px;
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6"
        data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        <!-- ============================================================== -->
        <!-- Topbar header -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin6">
            <?php include '../includes/student-navigation.php'?>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- Left Sidebar -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include '../includes/student-sidebar.php'?>
            </div>
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- Page wrapper -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row align-items-center mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title text-dark font-weight-medium mb-1">My Hostel Details</h4>
                                <p class="text-muted mb-0">Overview of your room and personal details in the hostel.</p>
                            </div>
                            <div>
                                <button class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i>Download ID Card
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    $cms = $_SESSION['cms'];
                    $ret = "Call StudentDetails('$cms')";
                    $res = $conn->query($ret);
                    $row = $res->fetch_object();
                ?>

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <h5>Hostel</h5>
                            <h3><?php echo $row->HName; ?></h3>
                            <p class="mb-0">Room No: <?php echo $row->SRNo; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-card" style="background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <h5>Registration No</h5>
                            <h3><?php echo $row->CMS; ?></h3>
                            <p class="mb-0">Active <i class="fas fa-check-circle ms-1"></i></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-card" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
                            <div class="info-icon">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <h5>Room Type</h5>
                            <h3>
                                <?php 
                                    if($row->RCapacity == 2) echo "Biseater";
                                    else if($row->RCapacity == 3) echo "Triseater";
                                    else echo "Single";
                                ?>
                            </h3>
                            <p class="mb-0">Capacity: <?php echo $row->RCapacity; ?> Person</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-card" style="background: linear-gradient(135deg, #7209b7 0%, #560bad 100%);">
                            <div class="info-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5>Department</h5>
                            <h3><?php echo $row->Dept; ?></h3>
                            <p class="mb-0">Hostel Management System</p>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <!-- Personal Details -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="section-title">Personal Information</h5>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center w-100">
                                        <div class="detail-label">Full Name</div>
                                        <div class="detail-value"><?php echo $row->SName; ?></div>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center w-100">
                                        <div class="detail-label">CNIC Number</div>
                                        <div class="detail-value"><?php echo $row->SCnic; ?></div>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-venus-mars"></i>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center w-100">
                                        <div class="detail-label">Gender</div>
                                        <div class="detail-value"><?php echo $row->SGender; ?></div>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center w-100">
                                        <div class="detail-label">Email Address</div>
                                        <div class="detail-value"><?php echo $row->SEmail; ?></div>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center w-100">
                                        <div class="detail-label">Contact Number</div>
                                        <div class="detail-value"><?php echo $row->SPhone; ?></div>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center w-100">
                                        <div class="detail-label">Current Address</div>
                                        <div class="detail-value"><?php echo $row->SAddress; ?></div>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center w-100">
                                        <div class="detail-label">Guardian Name</div>
                                        <div class="detail-value"><?php echo $row->S_FName; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ID Card & Quick Actions -->
                    <div class="col-md-4">
                        <!-- Profile Card -->
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="position-relative">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row->SName); ?>&background=4361ee&color=fff&size=100" class="profile-image" alt="Profile">
                                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-3 border-white rounded-circle"></span>
                                    </div>
                                </div>
                                <h4><?php echo $row->SName; ?></h4>
                                <p class="text-muted"><?php echo $row->Dept; ?></p>
                                <div class="d-flex justify-content-center mb-3">
                                    <span class="status-badge badge-active">
                                        <i class="fas fa-check-circle me-1"></i> Active Student
                                    </span>
                                </div>
                                
                                <div class="qr-code mx-auto mb-3">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo $cms; ?>" alt="QR Code" class="img-fluid">
                                </div>
                                <p class="small text-muted">Scan this QR code for quick access</p>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="section-title">Quick Actions</h5>
                                
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-start">
                                        <i class="fas fa-key me-3"></i> Request Key Replacement
                                    </button>
                                    <button class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-start">
                                        <i class="fas fa-tools me-3"></i> Maintenance Request
                                    </button>
                                    <button class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-start">
                                        <i class="fas fa-file-alt me-3"></i> Download Documents
                                    </button>
                                    <button class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-start">
                                        <i class="fas fa-info-circle me-3"></i> View Hostel Rules
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- Footer -->
            <!-- ============================================================== -->
            <?php include '../includes/footer.php' ?>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
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
        // Add subtle animations to cards when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
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