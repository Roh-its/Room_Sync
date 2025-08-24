<?php
session_start();
include('../includes/dbconn.php');
include('../includes/check-login.php');
check_login();

// Get hostel ID from session if you need it later (not used in queries unless your table has a column for it)
$hid = $_SESSION['hid'] ?? null;

// Helper: check if complaints table has Status column
$hasStatus = false;
$colRes = mysqli_query($conn, "SHOW COLUMNS FROM complaints LIKE 'Status'");
if ($colRes && mysqli_num_rows($colRes) > 0) {
    $hasStatus = true;
}

// Handle status update (only if Status column exists)
if (isset($_POST['update_status']) && $hasStatus) {
    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';
    if (!empty($id) && !empty($status)) {
        $stmt = mysqli_prepare($conn, "UPDATE complaints SET Status = ? WHERE ID = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "<div class='alert alert-success'>Status updated successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error updating status: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
        }
        mysqli_stmt_close($stmt);
    }
} elseif (isset($_POST['update_status']) && !$hasStatus) {
    $message = "<div class='alert alert-warning'>Status column not found in complaints table. Update is disabled to avoid altering your schema.</div>";
}

// Handle delete request
if (isset($_POST['delete_complaint'])) {
    $id = $_POST['id'] ?? '';
    if (!empty($id)) {
        $stmt = mysqli_prepare($conn, "DELETE FROM complaints WHERE ID = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "<div class='alert alert-success'>Complaint deleted successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error deleting: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Filter
$filter_status = $_GET['status'] ?? 'all';
$where_clause = "";
if ($hasStatus && $filter_status !== 'all') {
    $safe = mysqli_real_escape_string($conn, $filter_status);
    $where_clause = "WHERE Status = '$safe'";
}

// Stats
$total_count = 0; $pending_count = 0; $inprogress_count = 0; $resolved_count = 0; $unknown_count = 0;

$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM complaints");
if ($res) { $total_count = (int)mysqli_fetch_assoc($res)['c']; }

if ($hasStatus) {
    $sres = mysqli_query($conn, "SELECT Status, COUNT(*) AS c FROM complaints GROUP BY Status");
    while ($sres && $row = mysqli_fetch_assoc($sres)) {
        $status = $row['Status'];
        $count = (int)$row['c'];
        if ($status === 'Pending') $pending_count = $count;
        elseif ($status === 'In Progress') $inprogress_count = $count;
        elseif ($status === 'Resolved') $resolved_count = $count;
        else $unknown_count += $count;
    }
} else {
    // No Status column: treat all as "Pending" for display purposes
    $pending_count = $total_count;
}

// Data queries
$sql = "SELECT * FROM complaints $where_clause ORDER BY CDate DESC";
$result = mysqli_query($conn, $sql);

$recent_query = "SELECT * FROM complaints ORDER BY CDate DESC LIMIT 5";
$recent_result = mysqli_query($conn, $recent_query);
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
    <title>Hostel Management System - Complaints</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../dist/css/style.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee; --secondary: #3f37c9; --success: #4cc9f0; --info: #4895ef;
            --warning: #f72585; --light: #f8f9fa; --dark: #212529; --gray: #6c757d; --light-gray: #e9ecef;
        }
        body { background-color: #f5f8ff; color: #333; line-height: 1.6; }
        .page-wrapper { background-color: #f5f8ff; padding: 20px; }
        .page-breadcrumb { background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,.05); margin-bottom: 30px; }
        .page-title { color: var(--primary); font-size: 1.8rem; margin: 0; font-weight: 600; }

        /* Dashboard Cards */
        .dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,.05); text-align: center; transition: transform .3s ease; }
        .card:hover { transform: translateY(-5px); }
        .card h3 { color: var(--gray); margin-bottom: 10px; font-size: 1rem; font-weight: 500; }
        .card .number { font-size: 2.2rem; font-weight: 700; color: var(--primary); }
        .card .icon { font-size: 2.5rem; margin-bottom: 15px; color: var(--primary); opacity: .8; }

        /* Status Section */
        .status-section { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,.05); padding: 25px; margin-bottom: 30px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--light-gray); }
        .section-header h2 { color: var(--primary); font-size: 1.5rem; font-weight: 600; }
        .date-display { background: var(--light); padding: 8px 15px; border-radius: 20px; font-weight: 500; color: var(--primary); }
        .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 15px; }
        .status-item { background: var(--light); padding: 15px; border-radius: 10px; display: flex; align-items: center; gap: 15px; }
        .status-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
        .pending .status-icon { background: #ffc107; }
        .progress .status-icon { background: #17a2b8; }
        .resolved .status-icon { background: #28a745; }
        .unknown .status-icon { background: var(--gray); }
        .status-info h4 { font-weight: 600; margin-bottom: 5px; }
        .status-info p { color: var(--gray); font-size: .9rem; }

        /* Recent Activity */
        .recent-activity { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,.05); padding: 25px; margin-bottom: 30px; }
        .activity-list { list-style: none; padding: 0; margin: 0; }
        .activity-item { display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--light-gray); }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; background: var(--info); }
        .activity-content { flex: 1; }
        .activity-content h4 { margin: 0 0 5px 0; font-weight: 600; }
        .activity-content p { margin: 0; color: var(--gray); font-size: .9rem; }
        .activity-time { color: var(--gray); font-size: .85rem; }

        /* Filter buttons */
        .filter-buttons .btn { border-radius: 20px; margin: 0 5px 10px 0; }

        /* Table */
        .table-modern { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-modern th { background-color: #f8f9fa; color: #495057; font-weight: 600; padding: 12px 15px; }
        .table-modern td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid #e9ecef; }
        .table-modern tr:last-child td { border-bottom: none; }
        .table-modern tr:hover { background-color: rgba(26, 75, 140, 0.05); }

        .status-badge { padding: 5px 10px; border-radius: 50px; font-size: .75rem; font-weight: 600; }
        .badge-pending { background: rgba(255,193,7,.15); color: #856404; }
        .badge-in-progress { background: rgba(23,162,184,.15); color: #0c5460; }
        .badge-resolved { background: rgba(40,167,69,.15); color: #155724; }
        .badge-unknown { background: rgba(108,117,125,.15); color: #495057; }

        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 15px; margin-top: 20px; }
        .action-btn { background: white; border: 1px solid var(--light-gray); border-radius: 8px; padding: 15px; text-align: center; transition: all .3s ease; cursor: pointer; text-decoration: none; color: var(--dark); }
        .action-btn:hover { transform: translateY(-3px); box-shadow: 0 4px 8px rgba(0,0,0,.1); color: var(--primary); text-decoration: none; }
        .action-icon { font-size: 1.5rem; margin-bottom: 10px; color: var(--primary); }

        @media (max-width: 992px) {
            .dashboard-cards { grid-template-columns: repeat(2, 1fr); }
            .quick-actions { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .dashboard-cards { grid-template-columns: 1fr; }
            .section-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .status-grid { grid-template-columns: 1fr; }
            .quick-actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="preloader">
    <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
</div>
<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
    <header class="topbar" data-navbarbg="skin6">
        <?php include 'includes/navigation.php' ?>
    </header>
    <aside class="left-sidebar" data-sidebarbg="skin6">
        <div class="scroll-sidebar" data-sidebarbg="skin6">
            <?php include 'includes/sidebar.php' ?>
        </div>
    </aside>

    <div class="page-wrapper">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-7 align-self-center">
                    <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Complaints</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0 p-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Complaints</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <?php if (isset($message)) echo $message; ?>

            <!-- Top Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="icon"><i class="fas fa-list"></i></div>
                    <h3>Total Complaints</h3>
                    <div class="number"><?php echo $total_count; ?></div>
                </div>
                <div class="card">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <h3>Pending</h3>
                    <div class="number"><?php echo $pending_count; ?></div>
                </div>
                <div class="card">
                    <div class="icon"><i class="fas fa-spinner"></i></div>
                    <h3>In Progress</h3>
                    <div class="number"><?php echo $inprogress_count; ?></div>
                </div>
                <div class="card">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <h3>Resolved</h3>
                    <div class="number"><?php echo $resolved_count; ?></div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="status-section">
                <div class="section-header">
                    <h2>Complaint Status</h2>
                    <div class="date-display"><i class="fas fa-calendar-alt"></i> <span><?php echo date('F j, Y'); ?></span></div>
                </div>
                <div class="status-grid">
                    <div class="status-item pending">
                        <div class="status-icon"><i class="fas fa-hourglass-half"></i></div>
                        <div class="status-info">
                            <h4>Pending</h4>
                            <p><?php echo $pending_count; ?> complaints</p>
                        </div>
                    </div>
                    <div class="status-item progress">
                        <div class="status-icon"><i class="fas fa-spinner"></i></div>
                        <div class="status-info">
                            <h4>In Progress</h4>
                            <p><?php echo $inprogress_count; ?> complaints</p>
                        </div>
                    </div>
                    <div class="status-item resolved">
                        <div class="status-icon"><i class="fas fa-check"></i></div>
                        <div class="status-info">
                            <h4>Resolved</h4>
                            <p><?php echo $resolved_count; ?> complaints</p>
                        </div>
                    </div>
                    <?php if ($hasStatus && $unknown_count > 0): ?>
                    <div class="status-item unknown">
                        <div class="status-icon"><i class="fas fa-question"></i></div>
                        <div class="status-info">
                            <h4>Unknown</h4>
                            <p><?php echo $unknown_count; ?> complaints</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Complaints -->
            <div class="recent-activity">
                <div class="section-header">
                    <h2>Recent Complaints</h2>
                </div>
                <ul class="activity-list">
                    <?php if ($recent_result && mysqli_num_rows($recent_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($recent_result)): 
                            $status = $hasStatus && isset($row['Status']) ? $row['Status'] : 'Pending';
                            ?>
                            <li class="activity-item">
                                <div class="activity-icon"><i class="fas fa-exclamation-circle"></i></div>
                                <div class="activity-content">
                                    <h4><?php echo htmlspecialchars($row['CType']); ?> (Room: <?php echo htmlspecialchars($row['RNo']); ?>)</h4>
                                    <p>Status: <?php echo htmlspecialchars($status); ?> — <?php echo htmlspecialchars($row['CDescription']); ?></p>
                                </div>
                                <div class="activity-time"><?php echo date('M j, Y', strtotime($row['CDate'])); ?></div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="activity-item"><div class="activity-content"><p>No recent complaints found.</p></div></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Filter -->
            <div class="card status-section">
                <div class="section-header">
                    <h2>Filter Complaints</h2>
                </div>
                <div class="filter-buttons text-center">
                    <a href="complaints.php?status=all" class="btn <?php echo $filter_status == 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">All</a>
                    <a href="complaints.php?status=Pending" class="btn <?php echo $filter_status == 'Pending' ? 'btn-warning' : 'btn-outline-warning'; ?>" <?php echo !$hasStatus ? 'disabled' : ''; ?>>Pending</a>
                    <a href="complaints.php?status=In Progress" class="btn <?php echo $filter_status == 'In Progress' ? 'btn-info' : 'btn-outline-info'; ?>" <?php echo !$hasStatus ? 'disabled' : ''; ?>>In Progress</a>
                    <a href="complaints.php?status=Resolved" class="btn <?php echo $filter_status == 'Resolved' ? 'btn-success' : 'btn-outline-success'; ?>" <?php echo !$hasStatus ? 'disabled' : ''; ?>>Resolved</a>
                </div>
                <?php if (!$hasStatus): ?>
                    <p class="text-center text-muted mb-0">Status filter disabled — <code>Status</code> column not found.</p>
                <?php endif; ?>
            </div>

            <!-- Complaints Table -->
            <div class="status-section">
                <div class="section-header">
                    <h2>Complaint List</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Room No</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th style="min-width:180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                                $status = $hasStatus && isset($row['Status']) ? $row['Status'] : 'Pending';
                                $status_class = 'badge-pending';
                                if ($status === 'In Progress') $status_class = 'badge-in-progress';
                                elseif ($status === 'Resolved') $status_class = 'badge-resolved';
                                elseif ($status !== 'Pending') $status_class = 'badge-unknown';
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID']); ?></td>
                                <td><?php echo htmlspecialchars($row['RNo']); ?></td>
                                <td><?php echo htmlspecialchars($row['CDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['CType']); ?></td>
                                <td><?php echo htmlspecialchars($row['CDescription']); ?></td>
                                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                                <td class="action-buttons">
                                    <!-- Update -->
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $row['ID']; ?>" <?php echo !$hasStatus ? 'disabled' : ''; ?>>
                                        <i class="fas fa-edit"></i> Update
                                    </button>
                                    <!-- Delete -->
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['ID']; ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>

                            <!-- Update Modal -->
                            <div class="modal fade" id="updateModal<?php echo $row['ID']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Complaint Status</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                <?php if ($hasStatus): ?>
                                                <div class="mb-3">
                                                    <label class="form-label">Current Status: <span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($status); ?></span></label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="Pending" <?php echo $status=='Pending'?'selected':''; ?>>Pending</option>
                                                        <option value="In Progress" <?php echo $status=='In Progress'?'selected':''; ?>>In Progress</option>
                                                        <option value="Resolved" <?php echo $status=='Resolved'?'selected':''; ?>>Resolved</option>
                                                    </select>
                                                </div>
                                                <?php else: ?>
                                                    <p class="text-muted mb-0">Status column not found. Updating is disabled.</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="update_status" class="btn btn-primary" <?php echo !$hasStatus ? 'disabled' : ''; ?>>Update Status</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $row['ID']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                <p>Are you sure you want to delete complaint #<?php echo $row['ID']; ?> from Room <?php echo htmlspecialchars($row['RNo']); ?>?</p>
                                                <p class="text-muted"><strong>Type:</strong> <?php echo htmlspecialchars($row['CType']); ?><br>
                                                <strong>Description:</strong> <?php echo htmlspecialchars($row['CDescription']); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="delete_complaint" class="btn btn-danger">Delete Complaint</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php
                            endwhile;
                        else:
                            echo "<tr><td colspan='7' class='text-center'>No complaints found.</td></tr>";
                        endif;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="section-header">
                <h2>Quick Actions</h2>
            </div>
            <div class="quick-actions">
                <a href="complaints.php" class="action-btn">
                    <div class="action-icon"><i class="fas fa-list"></i></div>
                    <h4>View All Complaints</h4>
                </a>
                <a href="view-students-acc.php" class="action-btn">
                    <div class="action-icon"><i class="fas fa-users"></i></div>
                    <h4>Manage Students</h4>
                </a>
                <a href="complaints-report.php" class="action-btn">
                    <div class="action-icon"><i class="fas fa-chart-bar"></i></div>
                    <h4>Generate Reports</h4>
                </a>
                <a href="bulk-complaints.php" class="action-btn">
                    <div class="action-icon"><i class="fas fa-file-import"></i></div>
                    <h4>Bulk Upload</h4>
                </a>
            </div>
        </div>

        <?php include '../includes/footer.php' ?>
    </div>
</div>

<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 5 Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="../dist/js/app-style-switcher.js"></script>
<script src="../dist/js/feather.min.js"></script>
<script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<script src="../dist/js/sidebarmenu.js"></script>
<script src="../dist/js/custom.min.js"></script>
<script src="../assets/extra-libs/c3/d3.min.js"></script>
<script src="../assets/extra-libs/c3/c3.min.js"></script>
<script src="../assets/libs/chartist/dist/chartist.min.js"></script>
<script src="../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
<script>
    // Small UX polish
    setTimeout(function(){ $('.alert').fadeOut('slow'); }, 4000);
    $(document).ready(function(){
        $('.card').each(function(i){ $(this).delay(i*150).animate({opacity:1, top:0}, 400); });
    });
</script>
</body>
</html>
