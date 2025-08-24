<?php
session_start();
include('../includes/dbconn.php');
include('../includes/check-login.php');
check_login();

$cms = $_SESSION['cms'];
$ret = mysqli_query($conn, "SELECT * FROM student WHERE cms='$cms'");
$row = mysqli_fetch_array($ret);
$name = $row['SName'];
$room = $row['SRNo'];

$successMsg = $errorMsg = "";

// ✅ Add new suggestion
if (isset($_POST['submit'])) {
    $type = $_POST['type'];
    $desc = $_POST['desc'];
    $date = $_POST['date'];
    $sql = "INSERT INTO suggestions(SUGDate, SUGType, SUGDescription, CMS) VALUES ('$date','$type','$desc','$cms')";
    $query = $conn->query($sql);
    if ($query) {
        $successMsg = "✅ Suggestion submitted successfully!";
    } else {
        $errorMsg = "❌ Something went wrong. Please try again.";
    }
}

// ✅ Delete suggestion
if (isset($_POST['deleteentry'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM suggestions WHERE id='$id'";
    $query = $conn->query($sql);
    if ($query) {
        $successMsg = "🗑️ Suggestion deleted successfully!";
    } else {
        $errorMsg = "❌ Unable to delete suggestion.";
    }
}

// Get suggestion statistics
$totalSql = "SELECT COUNT(*) as total FROM suggestions WHERE CMS = '$cms'";
$totalResult = mysqli_query($conn, $totalSql);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalSuggestions = $totalRow['total'];

$recentSql = "SELECT COUNT(*) as recent FROM suggestions WHERE CMS = '$cms' AND SUGDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recentResult = mysqli_query($conn, $recentSql);
$recentRow = mysqli_fetch_assoc($recentResult);
$recentSuggestions = $recentRow['recent'];

// Get suggestion types count
$typesSql = "SELECT SUGType, COUNT(*) as count FROM suggestions WHERE CMS = '$cms' GROUP BY SUGType";
$typesResult = mysqli_query($conn, $typesSql);
$suggestionTypes = [];
while($typeRow = mysqli_fetch_assoc($typesResult)) {
    $suggestionTypes[$typeRow['SUGType']] = $typeRow['count'];
}
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <title>Hostel Management System - Suggestions</title>
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
            --info: #4895ef;
            --warning: #f8961e;
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
        
        .badge-info {
            background: rgba(73, 80, 87, 0.15);
            color: #495057;
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
        
        .suggestion-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .icon-general {
            background: rgba(67, 97, 238, 0.1);
            color: #4361ee;
        }
        
        .icon-facility {
            background: rgba(76, 201, 240, 0.1);
            color: #4cc9f0;
        }
        
        .icon-food {
            background: rgba(247, 37, 133, 0.1);
            color: #f72585;
        }
        
        .suggestion-type {
            font-weight: 500;
            color: #495057;
        }
        
        .suggestion-desc {
            color: #6c757d;
            font-size: 0.9rem;
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
        
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: var(--hover-shadow);
        }
        
        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 20px;
        }
        
        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 20px;
        }
        
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
        
        .btn-light {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
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
    <!-- Preloader -->
    <div class="preloader"><div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div></div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6"
        data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        <!-- Header -->
        <header class="topbar" data-navbarbg="skin6">
            <?php include '../includes/student-navigation.php'?>
        </header>

        <!-- Sidebar -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include '../includes/student-sidebar.php'?>
            </div>
        </aside>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title text-dark font-weight-medium mb-1">Suggestions & Feedback</h4>
                                <p class="text-muted mb-0">Share your ideas to improve hostel facilities</p>
                            </div>
                            <div>
                                <span class="status-badge badge-info">
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
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3><?php echo $totalSuggestions; ?></h3>
                            <p class="mb-0">Total Suggestions</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3><?php echo $recentSuggestions; ?></h3>
                            <p class="mb-0">Last 30 Days</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3><?php echo count($suggestionTypes); ?></h3>
                            <p class="mb-0">Categories</p>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if ($successMsg): ?>
                    <div class="alert alert-success fade-in"><?php echo $successMsg; ?></div>
                <?php elseif ($errorMsg): ?>
                    <div class="alert alert-danger fade-in"><?php echo $errorMsg; ?></div>
                <?php endif; ?>

                <!-- Suggestions Table -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="section-title mb-0">My Suggestions</h5>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#newSuggestionModal">
                                <i class="fas fa-plus me-2"></i>New Suggestion
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Suggestion ID</th>
                                        <th>Details</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM suggestions WHERE CMS='$cms' ORDER BY SUGDate DESC";
                                    $result = mysqli_query($conn, $sql);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_array($result)) {
                                            $typeIcon = "icon-general";
                                            $typeIconClass = "fas fa-lightbulb";
                                            
                                            if (stripos($row['SUGType'], 'facility') !== false || stripos($row['SUGType'], 'infra') !== false) {
                                                $typeIcon = "icon-facility";
                                                $typeIconClass = "fas fa-building";
                                            } elseif (stripos($row['SUGType'], 'food') !== false || stripos($row['SUGType'], 'mess') !== false) {
                                                $typeIcon = "icon-food";
                                                $typeIconClass = "fas fa-utensils";
                                            }

                                            echo "<tr class='fade-in'>
                                                <td>#".$row['ID']."</td>
                                                <td>
                                                    <div class='d-flex align-items-center'>
                                                        <div class='suggestion-icon $typeIcon'>
                                                            <i class='$typeIconClass'></i>
                                                        </div>
                                                        <div>
                                                            <div class='suggestion-type'>".$row['SUGType']."</div>
                                                            <div class='suggestion-desc'>".$row['SUGDescription']."</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>".date('M j, Y', strtotime($row['SUGDate']))."</td>
                                                <td>
                                                    <span class='status-badge badge-info'>".$row['SUGType']."</span>
                                                </td>
                                                <td>
                                                    <form method='POST' style='display:inline;'>
                                                        <input type='hidden' name='id' value='".$row['ID']."'>
                                                        <button type='submit' name='deleteentry' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this suggestion?');\">
                                                            <i class='fas fa-trash-alt'></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr>
                                            <td colspan='5' class='text-center py-5'>
                                                <div class='text-muted mb-3'>
                                                    <i class='fas fa-lightbulb fa-3x'></i>
                                                </div>
                                                <h5 class='text-muted'>No Suggestions Yet</h5>
                                                <p class='text-muted'>Share your first suggestion to help improve our hostel!</p>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Suggestion Categories -->
                <?php if (!empty($suggestionTypes)): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="section-title">Suggestion Categories</h5>
                        <div class="row">
                            <?php foreach ($suggestionTypes as $type => $count): 
                                $percentage = ($count / $totalSuggestions) * 100;
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="suggestion-type"><?php echo htmlspecialchars($type); ?></span>
                                    <span class="text-muted"><?php echo $count; ?> suggestion(s)</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%; background: linear-gradient(90deg, #4361ee 0%, #3a0ca3 100%);" 
                                         aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- New Suggestion Modal -->
                <div class="modal fade" id="newSuggestionModal" tabindex="-1" role="dialog" aria-labelledby="newSuggestionLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="newSuggestionLabel">
                                    <i class="fas fa-lightbulb me-2"></i>New Suggestion
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label class="form-label required-field">Suggestion Type</label>
                                        <input type="text" name="type" class="form-control" placeholder="e.g., Facility Improvement, Food Quality, etc." required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label required-field">Date</label>
                                        <input type="date" name="date" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label required-field">Description</label>
                                        <textarea name="desc" rows="4" class="form-control" placeholder="Describe your suggestion in detail..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    <button type="submit" name="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Suggestion
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
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