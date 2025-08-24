<nav class="navbar top-navbar navbar-expand-md">
    <div class="navbar-header" data-logobg="skin6">
        <!-- This is for the sidebar toggle which is visible on mobile only -->
        <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                class="ti-menu ti-close"></i></a>
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <div class="navbar-brand">
            <!-- Logo icon -->
            <a href="dashboard.php">
                <b class="logo-icon">
                    <!-- Dark Logo icon -->
                    <img src="../assets/images/image.png" alt="homepage" class="dark-logo" />
                    <!-- Light Logo icon -->
                    <!-- <img src="../assets/images/logo-icon-nav.png" alt="homepage" class="light-logo" /> -->
                </b>
                <!--End Logo icon -->
                <!-- Logo text -->
                <span class="logo-text">
                    <img src="" class="light-logo" alt="" />
                    <!-- dark Logo text -->
                    <img src="../assets/images/textnav.png" alt="homepage" class="dark-logo" />
                    <!-- Light Logo text -->
                </span>
            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Toggle which is visible on mobile only -->
        <!-- ============================================================== -->
        <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
            data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
    </div>
    <!-- ============================================================== -->
    <!-- End Logo -->
    <!-- ============================================================== -->
    <div class="navbar-collapse collapse" id="navbarSupportedContent">
        <!-- ============================================================== -->
        <!-- toggle and nav items -->
        <!-- ============================================================== -->
        <ul class="navbar-nav float-left mr-auto ml-3 pl-1">

            <!-- ============================================================== -->
            <!-- create a digital clock along with date in the navbar-->
            <!-- ============================================================== -->
            <li class="nav-item d-none d-md-block">
                <a class="nav-link" href="javascript:void(0)">
                    <div id="clock"
                        style="font-family: Raleway,Rubik,sans-serif;font-size:15px;font-weight:500;text-shadow:0px 0px 1px #fff;color:#212529;">
                    </div>

                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a class="nav-link" href="javascript:void(0)">
                    <div id="date"
                        style=" letter-spacing:3px;font-size:15px;font-weight:500;font-family:Raleway,Rubik,sans-serif;color:#212529;">
                    </div>

                </a>
            </li>
            <!-- ============================================================== -->
            <!-- End create a digital clock along with date in the navbar-->
            <!-- ============================================================== -->


        </ul>
        <!-- ============================================================== -->
        <!-- Right side toggle and nav items -->
        <!-- ============================================================== -->
        <ul class="navbar-nav float-right">

            <!-- ============================================================== -->
            <!-- User profile -->
            <!-- ============================================================== -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <img src="../assets/images/users/user-icn.png" alt="user" class="rounded-circle" width="40">

                    <?php
                    $login = $_SESSION['login'];
                    $ret = "SELECT * FROM  student WHERE SEmail = ? ";

                    $stmt = $conn->prepare($ret); // CHANGED: $mysqli to $conn
                    $stmt->bind_param('s', $login);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    while ($row = $res->fetch_object()) {
                    ?>

                    <span class="ml-2 d-none d-lg-inline-block"><span>Hello,</span> <span class="text-dark"><?php echo $row->SName;
                    } ?></span> <i data-feather="chevron-down" class="svg-icon"></i></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                    <a class="dropdown-item" href="profile.php"><i data-feather="user" class="svg-icon mr-2 ml-1"></i>
                        My Profile</a>


                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="acc-setting.php"><i data-feather="settings"
                            class="svg-icon mr-2 ml-1"></i>
                        Account Setting</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php"><i data-feather="power" class="svg-icon mr-2 ml-1"></i>
                        Logout</a>


                </div>
            </li>
            <!-- ============================================================== -->
            <!-- User profile -->
            <!-- ============================================================== -->
        </ul>
    </div>
</nav>
<script>
    function startTime() {
        var today = new Date();
        var hr = today.getHours();
        var min = today.getMinutes();
        var sec = today.getSeconds();
        ap = (hr < 12) ? "<span>AM</span>" : "<span>PM</span>";
        hr = (hr == 0) ? 12 : hr;
        hr = (hr > 12) ? hr - 12 : hr;
        //Add a zero in front of numbers<10
        hr = checkTime(hr);
        min = checkTime(min);
        sec = checkTime(sec);
        document.getElementById("clock").innerHTML = hr + ":" + min + ":" + sec + " " + ap;

        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var curWeekDay = days[today.getDay()];
        var curDay = today.getDate();
        var curMonth = months[today.getMonth()];
        var curYear = today.getFullYear();
        var date = curWeekDay + ", " + curDay + " " + curMonth + " " + curYear;
        document.getElementById("date").innerHTML = date;

        var time = setTimeout(function () { startTime() }, 500);
    }
    function checkTime(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }
    startTime();
</script>