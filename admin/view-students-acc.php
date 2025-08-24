<?php
session_start();
include('../includes/dbconn.php');
include('../includes/check-login.php');
check_login();

// Delete student if requested
if (isset($_GET['del'])) {
    $cms = intval($_GET['del']);
    $adn = "CALL deletestudent($cms)";
    $stmt = $conn->prepare($adn);
    if ($stmt) {
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Record has been deleted'); window.location.href='view-students-acc.php';</script>";
        exit();
    } else {
        die("❌ Delete failed: " . $conn->error);
    }
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
    <link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../dist/css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2962FF;
            --secondary: #6272a4;
            --success: #10d876;
            --info: #17a2b8;
            --warning: #FFC107;
            --danger: #ea5455;
            --light: #f8f9fa;
            --dark: #1e2742;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .table th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
        }
        
        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .delete-btn {
            color: var(--danger);
            background-color: rgba(234, 84, 85, 0.1);
        }
        
        .delete-btn:hover {
            background-color: var(--danger);
            color: white;
            transform: scale(1.1);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .search-container {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-input {
            padding-left: 40px;
            border-radius: 25px;
            border: 1px solid #ddd;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 10px;
            color: #aaa;
        }
        
        .page-title {
            position: relative;
            padding-left: 15px;
        }
        
        .page-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 24px;
            width: 4px;
            background-color: var(--primary);
            border-radius: 2px;
        }
        
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 60px;
            margin-bottom: 15px;
            color: #dee2e6;
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 15px;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
            
            .action-buttons {
                white-space: nowrap;
            }
        }
    </style>

    <script language="javascript" type="text/javascript">
    var popUpWin=0;
    function popUpWindow(URLStr, left, top, width, height){
        if(popUpWin) {
            if(!popUpWin.closed) popUpWin.close();
        }
        popUpWin = open(URLStr,'popUpWin', 
            'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+510+',height='+430+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
    }
    
    // Search functionality
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('studentTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName('td');
            let show = false;
            
            for (let j = 0; j < td.length; j++) {
                if (td[j] && td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                    show = true;
                    break;
                }
            }
            
            tr[i].style.display = show ? '' : 'none';
        }
    }
    </script>
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical" 
         data-navbarbg="skin6" data-sidebartype="full"
         data-sidebar-position="fixed" data-header-position="fixed" 
         data-boxed-layout="full">

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
                <div class="row align-items-center">
                    <div class="col-6">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Student Accounts</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Students</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-6 text-right">
                        <a href="register-student.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Student
                        </a>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="card-subtitle">All registered student accounts in your hostel</h6>
                                    <div class="search-container" style="max-width: 300px;">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" id="searchInput" class="form-control search-input" 
                                               placeholder="Search students..." onkeyup="filterTable()">
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table id="studentTable" class="table table-striped table-hover no-wrap">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>CMS ID</th>
                                                <th>Gender</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Room #</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php	
                                        $hid = $_SESSION['hid'];
                                        $ret = "SELECT * FROM student WHERE shid = $hid";
                                        $stmt = $conn->prepare($ret);
                                        $studentCount = 0;
                                        if ($stmt) {
                                            $stmt->execute();
                                            $res = $stmt->get_result();
                                            $studentCount = $res->num_rows;
                                            if ($res->num_rows > 0) {
                                                while ($row = $res->fetch_object()) {
                                                    $initial = substr($row->SName, 0, 1);
                                                    // Check if SProgram property exists to avoid undefined property error
                                                    $program = property_exists($row, 'SProgram') ? $row->SProgram : 'Not Specified';
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="student-avatar mr-3">
                                                                    <?php echo $initial; ?>
                                                                </div>
                                                                <div class="d-flex flex-column">
                                                                    <span class="font-weight-bold"><?php echo $row->SName; ?></span>
                                                                    <small class="text-muted"><?php echo $program; ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $row->CMS; ?></td>
                                                        <td>
                                                            <?php 
                                                            if ($row->SGender == 'Male') {
                                                                echo '<span class="badge status-badge bg-info">Male</span>';
                                                            } else {
                                                                echo '<span class="badge status-badge bg-warning text-dark">Female</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $row->SPhone; ?></td>
                                                        <td><?php echo $row->SEmail; ?></td>
                                                        <td>
                                                            <span class="badge status-badge bg-light text-dark">
                                                                <?php echo $row->SRNo; ?>
                                                            </span>
                                                        </td>
                                                        <td class="action-buttons">
                                                            <a href="edit-student.php?id=<?php echo $row->CMS; ?>" 
                                                               class="action-btn mr-2 text-info" title="Edit">
                                                               <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="view-students-acc.php?del=<?php echo $row->CMS; ?>" 
                                                               class="action-btn delete-btn" title="Delete" 
                                                               onclick="return confirm('Are you sure you want to delete this student record?');">
                                                               <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="7">
                                                    <div class="empty-state">
                                                        <i class="fas fa-user-graduate"></i>
                                                        <h5>No Students Found</h5>
                                                        <p>There are no students registered in your hostel yet.</p>
                                                        <a href="add-student.php" class="btn btn-primary mt-2">
                                                            <i class="fas fa-plus"></i> Add New Student
                                                        </a>
                                                    </div>
                                                </td></tr>';
                                            }
                                            $stmt->close();
                                        } else {
                                            echo "<tr><td colspan='7'>
                                                <div class='alert alert-danger'>❌ Query failed: " . $conn->error . "</div>
                                            </td></tr>";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php
                                if ($studentCount > 0) {
                                    echo '<div class="d-flex justify-content-between align-items-center mt-4">
                                        <div class="text-muted">Showing ' . $studentCount . ' student accounts</div>
                                        <div>
                                            <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV(\'students.csv\')">
                                                <i class="fas fa-download"></i> Export
                                            </button>
                                        </div>
                                    </div>';
                                }
                                ?>
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
    <script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable only if not already initialized
            if (!$.fn.DataTable.isDataTable('#studentTable')) {
                $('#studentTable').DataTable({
                    "pageLength": 10,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search records...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "infoEmpty": "Showing 0 to 0 of 0 entries",
                        "paginate": {
                            "previous": "<i class='fas fa-chevron-left'></i>",
                            "next": "<i class='fas fa-chevron-right'></i>"
                        }
                    },
                    "dom": '<"top"<"float-right"f>>rt<"bottom"lip><"clear">',
                    "drawCallback": function() {
                        $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                    }
                });
                
                // Add styling to search input
                $('.dataTables_filter input').addClass('form-control form-control-sm');
            }
        });
        
        // Function to export table data to CSV
        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;
            
            // CSV file
            csvFile = new Blob([csv], {type: "text/csv"});
            
            // Download link
            downloadLink = document.createElement("a");
            
            // File name
            downloadLink.download = filename;
            
            // Create a link to the file
            downloadLink.href = window.URL.createObjectURL(csvFile);
            
            // Hide download link
            downloadLink.style.display = "none";
            
            // Add the link to DOM
            document.body.appendChild(downloadLink);
            
            // Click download link
            downloadLink.click();
        }
        
        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("#studentTable tr");
            
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (var j = 0; j < cols.length; j++) {
                    // Skip action columns
                    if (j === cols.length - 1) continue;
                    
                    // Clean inner text of cells
                    let text = cols[j].innerText.replace(/\s+/g, ' ').trim();
                    text = text.replace(/"/g, '""');
                    row.push('"' + text + '"');
                }
                
                csv.push(row.join(","));        
            }
            
            // Download CSV file
            downloadCSV(csv.join("\n"), filename);
        }
    </script>
</body>
</html>