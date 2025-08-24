<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Hostel Management System - Student Fee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../dist/css/style.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }
        
        body {
            background-color: #f5f8ff;
            color: #333;
            line-height: 1.6;
        }
        
        .page-wrapper {
            background-color: #f5f8ff;
            padding: 20px;
        }
        
        .page-breadcrumb {
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .page-title {
            color: var(--primary);
            font-size: 1.8rem;
            margin: 0;
            font-weight: 600;
        }
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card h3 {
            color: var(--gray);
            margin-bottom: 10px;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .card .number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .card .icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
            opacity: 0.8;
        }
        
        /* Status Section */
        .status-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .section-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .date-display {
            background: var(--light);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
            color: var(--primary);
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .status-item {
            background: var(--light);
            padding: 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .status-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .total .status-icon {
            background: var(--primary);
        }
        
        .paid .status-icon {
            background: var(--success);
        }
        
        .unpaid .status-icon {
            background: var(--warning);
        }
        
        .status-info h4 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .status-info p {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        /* Form Styling */
        .fee-form {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .form-card {
            background: var(--light);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .form-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
        }
        
        .form-card h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-control {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 1rem;
            height: auto;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        /* Custom Select Styling */
        .select-container {
            position: relative;
        }
        
        .select-container::after {
            content: "\f078";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--gray);
        }
        
        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: white;
            cursor: pointer;
        }
        
        select.form-control option {
            padding: 10px;
        }
        
        select.form-control:focus {
            background-color: #fff;
        }
        
        /* Fee Type Specific Styling */
        .form-card.fee-type-card {
            border-left-color: var(--info);
        }
        
        .form-card.fee-type-card:hover {
            border-left-color: var(--primary);
        }
        
        .fee-type-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .fee-type-option {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid var(--light-gray);
        }
        
        .fee-type-option:hover {
            background: #f0f4ff;
            border-color: var(--primary);
        }
        
        .fee-type-option.selected {
            background: #e6eeff;
            border-color: var(--primary);
        }
        
        .fee-type-option input[type="radio"] {
            margin-right: 10px;
            accent-color: var(--primary);
        }
        
        .fee-type-icon {
            margin-right: 10px;
            width: 24px;
            text-align: center;
            color: var(--primary);
        }
        
        .btn-success {
            background: var(--success);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: #3aafd9;
            transform: translateY(-2px);
        }
        
        /* Recent Activity */
        .recent-activity {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
        }
        
        .activity-icon.hostel {
            background: var(--primary);
        }
        
        .activity-icon.mess {
            background: var(--success);
        }
        
        .activity-icon.other {
            background: var(--warning);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-content h4 {
            margin: 0 0 5px 0;
            font-weight: 600;
        }
        
        .activity-content p {
            margin: 0;
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .activity-time {
            color: var(--gray);
            font-size: 0.85rem;
        }
        
        .fee-amount {
            font-weight: 600;
            color: var(--primary);
            margin-right: 10px;
        }
        
        .fee-status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-paid {
            background: #e7f6e9;
            color: #2e7d32;
        }
        
        .status-unpaid {
            background: #ffebee;
            color: #c62828;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .action-btn {
            background: white;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--dark);
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: var(--primary);
            text-decoration: none;
        }
        
        .action-icon {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .status-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .fee-type-options {
                flex-direction: column;
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

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        <header class="topbar" data-navbarbg="skin6">
            <?php include 'includes/navigation.php'?>
        </header>

        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include 'includes/sidebar.php'?>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Student Fee Management</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Fee Management</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Total Students</h3>
                        <div class="number"><?php echo $total_students; ?></div>
                    </div>
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h3>Total Fees Generated</h3>
                        <div class="number"><?php echo $total_fees; ?></div>
                    </div>
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Paid Fees</h3>
                        <div class="number"><?php echo $paid_fees; ?></div>
                    </div>
                    <div class="card">
                        <div class="icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <h3>Unpaid Fees</h3>
                        <div class="number"><?php echo $unpaid_fees; ?></div>
                    </div>
                </div>
                
                <!-- Status Section -->
                <div class="status-section">
                    <div class="section-header">
                        <h2>Fee Status Overview</h2>
                        <div class="date-display">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?php echo date('F j, Y'); ?></span>
                        </div>
                    </div>
                    
                    <div class="status-grid">
                        <div class="status-item total">
                            <div class="status-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="status-info">
                                <h4>Total Fees</h4>
                                <p><?php echo $total_fees; ?> invoices generated</p>
                            </div>
                        </div>
                        
                        <div class="status-item paid">
                            <div class="status-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="status-info">
                                <h4>Paid</h4>
                                <p><?php echo $paid_fees; ?> invoices paid</p>
                            </div>
                        </div>
                        
                        <div class="status-item unpaid">
                            <div class="status-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="status-info">
                                <h4>Unpaid</h4>
                                <p><?php echo $unpaid_fees; ?> invoices pending</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fee Form -->
                <div class="fee-form">
                    <div class="section-header">
                        <h2>Generate Student Fees</h2>
                    </div>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-card">
                                    <h4><i class="fas fa-money-bill-wave"></i> Fee Amount</h4>
                                    <div class="form-group">
                                        <input type="text" name="fee" class="form-control" placeholder="Enter fee amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-card fee-type-card">
                                    <h4><i class="fas fa-tag"></i> Fee Type</h4>
                                    <div class="form-group">
                                        <div class="select-container">
                                            <select name="type" class="form-control" required>
                                                <option value="">Select fee type</option>
                                                <option value="Hostel">Hostel Fee</option>
                                                <option value="Mess">Mess Fee</option>
                                                <option value="Other">Other Fee</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Alternative radio button option (uncomment to use) -->
                                    <!--
                                    <div class="fee-type-options">
                                        <label class="fee-type-option">
                                            <input type="radio" name="type" value="Hostel" required>
                                            <span class="fee-type-icon"><i class="fas fa-building"></i></span>
                                            <span>Hostel Fee</span>
                                        </label>
                                        <label class="fee-type-option">
                                            <input type="radio" name="type" value="Mess">
                                            <span class="fee-type-icon"><i class="fas fa-utensils"></i></span>
                                            <span>Mess Fee</span>
                                        </label>
                                        <label class="fee-type-option">
                                            <input type="radio" name="type" value="Other">
                                            <span class="fee-type-icon"><i class="fas fa-file-invoice"></i></span>
                                            <span>Other Fee</span>
                                        </label>
                                    </div>
                                    -->
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-card">
                                    <h4><i class="fas fa-calendar-day"></i> Due Date</h4>
                                    <div class="form-group">
                                        <input type="date" name="due" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions text-center mt-4">
                            <button type="submit" name="submit" class="btn btn-success">
                                <i class="fas fa-cog"></i> Generate Fees
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Recent Activity -->
                <div class="recent-activity">
                    <div class="section-header">
                        <h2>Recent Fee Records</h2>
                    </div>
                    
                    <ul class="activity-list">
                        <?php if($has_recent_records): ?>
                            <?php while($row = $recent_result->fetch_assoc()): ?>
                                <li class="activity-item">
                                    <div class="activity-icon <?php echo isset($row['itype']) ? strtolower($row['itype']) : 'other'; ?>">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4>
                                            <?php echo isset($row['SName']) ? htmlspecialchars($row['SName']) : 'Unknown Student'; ?> 
                                            (Room: <?php echo isset($row['SRNo']) ? htmlspecialchars($row['SRNo']) : 'N/A'; ?>)
                                        </h4>
                                        <p>
                                            <?php if(isset($row['iamount'])): ?>
                                                <span class="fee-amount">₹<?php echo htmlspecialchars($row['iamount']); ?></span> •
                                            <?php endif; ?>
                                            
                                            <?php echo isset($row['itype']) ? htmlspecialchars($row['itype']) : 'Unknown'; ?> Fee
                                            
                                            <?php if(isset($row['iduedate'])): ?>
                                                • Due: <?php echo date('M j, Y', strtotime($row['iduedate'])); ?>
                                            <?php endif; ?>
                                            
                                            <?php if(isset($row['status'])): ?>
                                                <span class="fee-status status-<?php echo strtolower($row['status']); ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="activity-time">
                                        <?php echo isset($row['iduedate']) ? date('M j', strtotime($row['iduedate'])) : 'Unknown'; ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-content">
                                    <p>No recent fee records found.</p>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Quick Actions -->
                <div class="section-header">
                    <h2>Quick Actions</h2>
                </div>
                
                <div class="quick-actions">
                    <a href="view-invoices.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <h4>View All Invoices</h4>
                    </a>
                    
                    <a href="payment-records.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h4>Payment Records</h4>
                    </a>
                    
                    <a href="fee-reports.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4>Generate Reports</h4>
                    </a>
                    
                    <a href="bulk-fees.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <h4>Bulk Operations</h4>
                    </a>
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
        // Add some interactive effects
        $(document).ready(function() {
            // Add animation to cards on page load
            $('.card').each(function(i) {
                $(this).delay(i * 200).animate({opacity: 1, top: 0}, 500);
            });
            
            // Set default due date to today + 7 days
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            const formattedDate = nextWeek.toISOString().split('T')[0];
            $('input[name="due"]').val(formattedDate);
            
            // Focus on fee amount field
            $('input[name="fee"]').focus();
            
            // Enhanced select styling
            $('select.form-control').on('change', function() {
                if($(this).val() !== '') {
                    $(this).css('color', '#212529');
                } else {
                    $(this).css('color', '#6c757d');
                }
            });
            
            // For the radio button alternative (if used)
            $('.fee-type-option').click(function() {
                $('.fee-type-option').removeClass('selected');
                $(this).addClass('selected');
                $(this).find('input[type="radio"]').prop('checked', true);
            });
        });
    </script>

</body>

</html>