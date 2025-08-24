<?php
session_start();
include('../includes/dbconn.php');

if (!isset($_SESSION['cms'])) {
    die("Student not logged in");
}

$cms = $_SESSION['cms'];

// ✅ get student info
$sql = "SELECT SRNo, SName FROM student WHERE CMS = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cms);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $roomNo = $row['SRNo'];
    $name   = $row['SName'];
} else {
    die("Student not found in database");
}

$successMsg = $errorMsg = "";

// ✅ Handle delete request
if (isset($_POST['deleteentry'])) {
    $id = $_POST['id'] ?? '';
    if (!empty($id)) {
        $del = $conn->prepare("DELETE FROM complaints WHERE ID = ? AND RNo = ?");
        $del->bind_param("ii", $id, $roomNo);
        if ($del->execute()) {
            $successMsg = "🗑️ Complaint deleted successfully.";
        } else {
            $errorMsg = "❌ Error deleting complaint: " . $conn->error;
        }
    }
}

// ✅ Handle complaint submission
if (isset($_POST['submit'])) {
    $ctype = $_POST['type'] ?? '';
    $cdesc = $_POST['desc'] ?? '';
    $cdate = $_POST['date'] ?? '';

    if (!empty($ctype) && !empty($cdesc) && !empty($cdate)) {
        $insert = $conn->prepare("INSERT INTO complaints (CDate, CType, CDescription, RNo) VALUES (?, ?, ?, ?)");
        $insert->bind_param("sssi", $cdate, $ctype, $cdesc, $roomNo);
        if ($insert->execute()) {
            $successMsg = "✅ Complaint submitted successfully!";
        } else {
            $errorMsg = "❌ Error: " . $conn->error;
        }
    } else {
        $errorMsg = "⚠️ Please fill all fields.";
    }
}

// Get complaint statistics (removed CStatus references)
$totalSql = "SELECT COUNT(*) as total FROM complaints WHERE RNo = ?";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param("i", $roomNo);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalComplaints = $totalRow['total'];

// Since we don't have status, we'll show all as pending
$pendingComplaints = $totalComplaints;
$resolvedComplaints = 0;
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-pending {
            background: rgba(248, 150, 30, 0.15);
            color: #f8961e;
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
        
        .complaint-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .icon-electric {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .icon-cleaning {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }
        
        .icon-furniture {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .complaint-type {
            font-weight: 500;
            color: #495057;
        }
        
        .complaint-desc {
            color: #6c757d;
            font-size: 0.9rem;
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
                                <h4 class="page-title text-dark font-weight-medium mb-1">Complaint Management</h4>
                                <p class="text-muted mb-0">Submit and track your hostel complaints</p>
                            </div>
                            <div>
                                <span class="status-badge badge-pending">
                                    <i class="fas fa-user me-1"></i> <?php echo $name; ?> | Room: <?php echo $roomNo; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <h3><?php echo $totalComplaints; ?></h3>
                            <p class="mb-0">Total Complaints</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f8961e 0%, #f3722c 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3><?php echo $pendingComplaints; ?></h3>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43aa8b 0%, #4d908e 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3><?php echo $resolvedComplaints; ?></h3>
                            <p class="mb-0">Resolved</p>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if ($successMsg): ?>
                    <div class="alert alert-success fade-in"><?php echo $successMsg; ?></div>
                <?php elseif ($errorMsg): ?>
                    <div class="alert alert-danger fade-in"><?php echo $errorMsg; ?></div>
                <?php endif; ?>

                <!-- Complaint List -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="section-title">My Complaints</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Complaint ID</th>
                                        <th>Details</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM complaints WHERE RNo = ? ORDER BY CDate DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $roomNo);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            // All complaints are considered pending since we don't have status column
                                            $badgeClass = "badge-pending";
                                            $icon = "fas fa-clock";
                                            
                                            $typeIcon = "icon-electric";
                                            $typeIconClass = "fas fa-bolt";
                                            
                                            if ($row['CType'] == "Cleaning") {
                                                $typeIcon = "icon-cleaning";
                                                $typeIconClass = "fas fa-broom";
                                            } elseif ($row['CType'] == "Furniture") {
                                                $typeIcon = "icon-furniture";
                                                $typeIconClass = "fas fa-couch";
                                            }

                                            echo "<tr class='fade-in'>
                                                <td>#{$row['ID']}</td>
                                                <td>
                                                    <div class='d-flex align-items-center'>
                                                        <div class='complaint-icon $typeIcon'>
                                                            <i class='$typeIconClass'></i>
                                                        </div>
                                                        <div>
                                                            <div class='complaint-type'>{$row['CType']}</div>
                                                            <div class='complaint-desc'>{$row['CDescription']}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>".date('M j, Y', strtotime($row['CDate']))."</td>
                                                <td>{$row['CType']}</td>
                                                <td>
                                                    <span class='status-badge $badgeClass'>
                                                        <i class='$icon me-1'></i>Pending
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
                                                <h5 class='text-muted'>No Complaints Found</h5>
                                                <p class='text-muted'>You haven't submitted any complaints yet.</p>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Delete Form -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="section-title">Delete Complaint</h5>
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Complaint ID</label>
                                                <input type="text" id="id" name="id" placeholder="Enter Complaint ID to delete" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type='submit' class='btn btn-danger mt-md-0 mt-2' name='deleteentry' onclick="return confirm('Are you sure you want to delete this complaint?');">
                                                <i class="fas fa-trash-alt me-2"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="section-title">Complaint Info</h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Total Complaints:</span>
                                    <span class="fw-bold"><?php echo $totalComplaints; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Pending Resolution:</span>
                                    <span class="fw-bold text-warning"><?php echo $pendingComplaints; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Resolved:</span>
                                    <span class="fw-bold text-success"><?php echo $resolvedComplaints; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complaint Form -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="section-title">New Complaint</h5>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label class="form-label required-field">CMS ID</label>
                                    <input type="text" id="cms" name="cms" value="<?php echo $cms?>" readonly class="form-control">
                                </div>

                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label class="form-label required-field">Full Name</label>
                                    <input type="text" id="name" name="name" value="<?php echo $name?>" readonly class="form-control">
                                </div>

                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label class="form-label required-field">Room Number</label>
                                    <input type="text" value="<?php echo $roomNo?>" readonly class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Complaint Type</label>
                                    <select class="form-control" name="type" id="type" required>
                                        <option value="Electric">⚡ Electric</option>
                                        <option value="Cleaning">🧹 Cleaning</option>
                                        <option value="Furniture">🛋️ Furniture</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Date</label>
                                    <input type="date" name="date" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label required-field">Description</label>
                                    <textarea name="desc" placeholder="Please describe your complaint in detail..." class="form-control" rows="3" required></textarea>
                                </div>
                            </div>

                            <div class="form-actions text-center mt-4">
                                <button type="submit" name="submit" class="btn btn-success px-4 me-2">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Complaint
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
            // Set maximum date to today
            document.querySelector('input[name="date"]').setAttribute('max', new Date().toISOString().split('T')[0]);
            
            // Add animation to alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '1';
                }, 100);
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