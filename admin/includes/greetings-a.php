<?php 
 
    date_default_timezone_set('Asia/Karachi');
    //Here we define out main variables 
    $welcome_string="Welcome"; 
    $numeric_date=date("G"); 
    
    //Start conditionals based on military time 
    if($numeric_date>=0&&$numeric_date<=11) 
    $welcome_string="Good Morning,"; 
    else if($numeric_date>=12&&$numeric_date<=17) 
    $welcome_string="Good Afternoon,"; 
    else if($numeric_date>=18&&$numeric_date<=21) 
    $welcome_string="Good Evening,";
    else if($numeric_date>=22&&$numeric_date<=24) 
    $welcome_string="Good Night,"; 
    

        $mid=$_SESSION['mid'];
        $row1 = mysqli_query($mysqli, "Call manager('$mid')");
         while($row = mysqli_fetch_object($row1))
         {
             $_SESSION['Hid']=$row->MHID;
             $_SESSION['Hname']=$row->HName;
             echo "<h3 class='page-title text-truncate text-dark font-weight-medium mb-1'>$welcome_string $row->MName! </h3>";
        }
            // close
            mysqli_free_result($row1);
            mysqli_next_result($mysqli);

 
?>