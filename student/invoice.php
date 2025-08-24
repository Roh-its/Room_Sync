<?php
    session_start();
    include('../includes/dbconn.php');
    include('../includes/check-login.php');
    check_login();
    $cms = $_SESSION['cms'];

    $ret = mysqli_query($conn,"SELECT * FROM student WHERE cms='$cms'");
    $row = mysqli_fetch_array($ret);
    $name = $row['SName'];
    $room = $row['SRNo'];

    // 📌 Filter handling
    $filter = $_GET['status'] ?? 'All';
    $whereClause = "";
    if($filter != "All"){
        $whereClause = " AND Status='$filter'";
    }

    // Get invoice statistics
    $totalSql = "SELECT COUNT(*) as total, SUM(IAmount) as total_amount FROM invoice WHERE cms = '$cms'";
    $totalResult = mysqli_query($conn, $totalSql);
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalInvoices = $totalRow['total'];
    $totalAmount = $totalRow['total_amount'] ?? 0;

    $unpaidSql = "SELECT COUNT(*) as unpaid, SUM(IAmount) as unpaid_amount FROM invoice WHERE cms = '$cms' AND Status = 'Unpaid'";
    $unpaidResult = mysqli_query($conn, $unpaidSql);
    $unpaidRow = mysqli_fetch_assoc($unpaidResult);
    $unpaidInvoices = $unpaidRow['unpaid'];
    $unpaidAmount = $unpaidRow['unpaid_amount'] ?? 0;

    $paidSql = "SELECT COUNT(*) as paid, SUM(IAmount) as paid_amount FROM invoice WHERE cms = '$cms' AND Status = 'Paid'";
    $paidResult = mysqli_query($conn, $paidSql);
    $paidRow = mysqli_fetch_assoc($paidResult);
    $paidInvoices = $paidRow['paid'];
    $paidAmount = $paidRow['paid_amount'] ?? 0;

    $overdueSql = "SELECT COUNT(*) as overdue, SUM(IAmount) as overdue_amount FROM invoice WHERE cms = '$cms' AND Status = 'Overdue'";
    $overdueResult = mysqli_query($conn, $overdueSql);
    $overdueRow = mysqli_fetch_assoc($overdueResult);
    $overdueInvoices = $overdueRow['overdue'];
    $overdueAmount = $overdueRow['overdue_amount'] ?? 0;
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
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-unpaid {
            background: rgba(248, 150, 30, 0.15);
            color: #f8961e;
        }
        
        .badge-paid {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        .badge-overdue {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
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
        
        .filter-btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            margin: 0 5px 5px 0;
            border: 1px solid #e2e8f0;
            background: white;
            transition: all 0.3s;
        }
        
        .filter-btn.active, .filter-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .amount {
            font-weight: 600;
            color: #495057;
        }
        
        .amount.paid {
            color: #28a745;
        }
        
        .amount.unpaid {
            color: #f8961e;
        }
        
        .amount.overdue {
            color: #dc3545;
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
        
        .invoice-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .icon-unpaid {
            background: rgba(248, 150, 30, 0.1);
            color: #f8961e;
        }
        
        .icon-paid {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .icon-overdue {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .due-date {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .due-date.overdue {
            color: #dc3545;
            font-weight: 500;
        }
        
        .payment-btn {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
    </div>

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

        <!-- Page Content -->
        <div class="page-wrapper">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title text-dark font-weight-medium mb-1">Invoice Management</h4>
                                <p class="text-muted mb-0">View and manage your hostel invoices</p>
                            </div>
                            <div>
                                <span class="status-badge badge-unpaid">
                                    <i class="fas fa-user me-1"></i> <?php echo $name; ?> | Room: <?php echo $room; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <h3><?php echo $totalInvoices; ?></h3>
                            <p class="mb-0">Total Invoices</p>
                            <small>Rs. <?php echo number_format($totalAmount, 2); ?></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f8961e 0%, #f3722c 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3><?php echo $unpaidInvoices; ?></h3>
                            <p class="mb-0">Unpaid</p>
                            <small>Rs. <?php echo number_format($unpaidAmount, 2); ?></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43aa8b 0%, #4d908e 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3><?php echo $paidInvoices; ?></h3>
                            <p class="mb-0">Paid</p>
                            <small>Rs. <?php echo number_format($paidAmount, 2); ?></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f94144 0%, #f3722c 100%);">
                            <div class="stats-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3><?php echo $overdueInvoices; ?></h3>
                            <p class="mb-0">Overdue</p>
                            <small>Rs. <?php echo number_format($overdueAmount, 2); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Invoices Table -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="section-title mb-0">My Invoices</h5>

                            <!-- Filter Buttons -->
                            <div class="d-flex flex-wrap">
                                <a href="?status=All" class="filter-btn <?php echo $filter == 'All' ? 'active' : ''; ?>">
                                    <i class="fas fa-layer-group me-1"></i> All
                                </a>
                                <a href="?status=Unpaid" class="filter-btn <?php echo $filter == 'Unpaid' ? 'active' : ''; ?>">
                                    <i class="fas fa-clock me-1"></i> Unpaid
                                </a>
                                <a href="?status=Paid" class="filter-btn <?php echo $filter == 'Paid' ? 'active' : ''; ?>">
                                    <i class="fas fa-check-circle me-1"></i> Paid
                                </a>
                                <a href="?status=Overdue" class="filter-btn <?php echo $filter == 'Overdue' ? 'active' : ''; ?>">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Overdue
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Invoice ID</th>
                                        <th>Details</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT IID, IDueDate, IType, Status, IAmount 
                                                FROM invoice 
                                                WHERE cms = '$cms' $whereClause 
                                                ORDER BY IDueDate DESC";

                                        $result = mysqli_query($conn, $sql);
                                        $rowCount = mysqli_num_rows($result);

                                        if($rowCount > 0){
                                            while($row = mysqli_fetch_array($result)){
                                                $status = $row['Status'];
                                                $badgeClass = "";
                                                $iconClass = "";
                                                $amountClass = "";
                                                $dueDateClass = "";
                                                $icon = "";
                                                
                                                if($status == "Unpaid") {
                                                    $badgeClass = "badge-unpaid";
                                                    $iconClass = "icon-unpaid";
                                                    $amountClass = "unpaid";
                                                    $icon = "fas fa-clock";
                                                } else if($status == "Paid") {
                                                    $badgeClass = "badge-paid";
                                                    $iconClass = "icon-paid";
                                                    $amountClass = "paid";
                                                    $icon = "fas fa-check-circle";
                                                } else {
                                                    $badgeClass = "badge-overdue";
                                                    $iconClass = "icon-overdue";
                                                    $amountClass = "overdue";
                                                    $dueDateClass = "overdue";
                                                    $icon = "fas fa-exclamation-triangle";
                                                }

                                                $isOverdue = ($status == "Overdue");
                                                $dueDate = date('M j, Y', strtotime($row['IDueDate']));
                                                
                                                echo "<tr class='fade-in'>
                                                        <td>#".$row['IID']."</td>
                                                        <td>
                                                            <div class='d-flex align-items-center'>
                                                                <div class='invoice-icon $iconClass'>
                                                                    <i class='$icon'></i>
                                                                </div>
                                                                <div>
                                                                    <div class='fw-medium'>".$row['IType']."</div>
                                                                    <div class='due-date $dueDateClass'>Due: $dueDate</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class='$dueDateClass'>$dueDate</span>
                                                            ".($isOverdue ? "<div class='text-danger small'>Overdue</div>" : "")."
                                                        </td>
                                                        <td>
                                                            <span class='amount $amountClass'>Rs. ".number_format($row['IAmount'], 2)."</span>
                                                        </td>
                                                        <td>
                                                            <span class='status-badge $badgeClass'>
                                                                <i class='$icon me-1'></i>".$status."
                                                            </span>
                                                        </td>
                                                        <td>
                                                            ".($status != "Paid" ? "
                                                            <button class='btn btn-sm btn-primary payment-btn' data-invoice-id='".$row['IID']."'>
                                                                <i class='fas fa-credit-card me-1'></i>Pay
                                                            </button>
                                                            " : "
                                                            <button class='btn btn-sm btn-outline-success payment-btn' disabled>
                                                                <i class='fas fa-check me-1'></i>Paid
                                                            </button>
                                                            ")."
                                                        </td>
                                                      </tr>";
                                            }
                                        } else {
                                            echo "<tr>
                                                    <td colspan='6' class='text-center py-5'>
                                                        <div class='text-muted mb-3'>
                                                            <i class='fas fa-receipt fa-3x'></i>
                                                        </div>
                                                        <h5 class='text-muted'>No invoices found</h5>
                                                        <p class='text-muted'>You don't have any invoices matching your filter criteria.</p>
                                                    </td>
                                                  </tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Info Card -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="section-title">Payment Summary</h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Total Amount:</span>
                                    <span class="fw-bold">Rs. <?php echo number_format($totalAmount, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Paid Amount:</span>
                                    <span class="fw-bold text-success">Rs. <?php echo number_format($paidAmount, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Pending Amount:</span>
                                    <span class="fw-bold text-warning">Rs. <?php echo number_format($unpaidAmount, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Overdue Amount:</span>
                                    <span class="fw-bold text-danger">Rs. <?php echo number_format($overdueAmount, 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="section-title">Payment Instructions</h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Payment Methods:</strong> Visit the hostel office to make payments
                                </div>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Cash payments accepted at office</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Bank transfer details available</li>
                                    <li class="mb-0"><i class="fas fa-check-circle text-success me-2"></i> Receipt will be provided upon payment</li>
                                </ul>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Late payments may incur additional charges
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php include '../includes/footer.php' ?>
        </div>
    </div>

    <!-- JS -->
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
            // Add animation to table rows
            const rows = document.querySelectorAll('tr.fade-in');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                setTimeout(() => {
                    row.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    row.style.opacity = '1';
                }, 100 * index);
            });
            
            // Payment button handler
            const paymentButtons = document.querySelectorAll('.payment-btn:not([disabled])');
            paymentButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const invoiceId = this.getAttribute('data-invoice-id');
                    if(confirm('Are you sure you want to proceed with payment for invoice #' + invoiceId + '?')) {
                        // Simulate payment processing
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
                        this.disabled = true;
                        
                        setTimeout(() => {
                            alert('Payment would be processed here. In a real application, this would redirect to a payment gateway.');
                            this.innerHTML = '<i class="fas fa-check me-1"></i>Paid';
                            this.classList.remove('btn-primary');
                            this.classList.add('btn-outline-success');
                        }, 2000);
                    }
                });
            });
            
            // Add hover effects to filter buttons
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                    }
                });
                
                btn.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>