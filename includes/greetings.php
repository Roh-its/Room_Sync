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
    else if($numeric_date>=18&&$numeric_date<=23) 
    $welcome_string="Good Evening,"; 

        $login=$_SESSION['login'];
        $ret="select * from student where SEmail=?";
         $stmt= $conn->prepare($ret) ; // CHANGED: $mysqli to $conn
         $stmt->bind_param('s',$login);
         $stmt->execute();
         $res=$stmt->get_result();
                                        
         while($row=$res->fetch_object())
         {
             echo "<h3 class='page-title text-truncate text-dark font-weight-medium mb-1'>$welcome_string $row->SName! </h3>"; }
    
 
?>