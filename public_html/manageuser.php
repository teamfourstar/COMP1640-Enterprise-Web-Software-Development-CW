<?php
session_start();

if(isset($_SESSION['admin']) && ($_SESSION['admin'] != null)){

    if(isset($_SESSION['username'])){
       $username = $_SESSION['username'];
       $admin = $_SESSION['admin'];
    }
    else {
        header("Location: forum.php");
         }
} else
{
     header("Location: loginreg.php");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Users</title>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <!--To allow MS Edge and IE -->
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- ADD ICON LIBRARY -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="styles2.css">
    <link rel="stylesheet" type="text/css" href="manageuserCSS.css">
</head>

<body>

    <!--NAVIGATION BAR-->
    <!-- Image and text -->
<div id="nav-placeholder">

</div>

<script>
$(function(){
  $("#nav-placeholder").load("nav_bar.php");
});
</script>



    <!--PAGE TITLE/HEADER-->
    <h1> Manage users</h1>
    <br>
    <form action="" method="post">
        <!-- form action="" method="post"-->

        <div class="container">
            <table class="table table-bordered" id="table">
                <thead>

                    <?php  //connection

try {
$host = "localhost";
$username = "root";
$password = "";
$database = "mdb_st2645h";
$dbc = mysqli_connect($host, $username, $password, $database) OR die("couldn't connect to database".  mysqli_connect_errno());
} catch (Exception $e){
  $host = "mysql.cms.gre.ac.uk";
  $username = "st2645h";
  $password = "Enterprise94";
  $database = "mdb_st2645h";
  $dbc = mysqli_connect($host, $username, $password, $database) OR die("couldn't connect to database".  mysqli_connect_errno());
}


$result = mysqli_query($dbc, " SELECT UserID, u.UserName,u.Password, d.Name AS dName, Email, r.Name AS rName, Banned, u.Removed  FROM User u LEFT Join Department d ON u.DepartmentID=d.DepartmentID INNER join Role r on u.RoleID=r.RoleID ORDER BY u.UserID ASC");

?>


                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Edit</th>
                    </tr>

                    </thead>
                    <tbody>


                    <?php
                    $i = 0;
                    while($row = mysqli_fetch_array($result)): ?>
                    <tr>

                        <form method="post">
                            <td><?php echo $row['UserName'];?></td>

                            <td><?php echo $row['Email'];?></td>

                            <td><?php echo $row['dName'];?></td>

                            <td><?php echo $row['rName'];?></td>



                            <!-- Can be changed to block or remove -->
                            <td><?php

                               if($row['Removed'] == 1)
                                {
                                 echo '<p1>Deleted</p1>';
                                }
                                else if($row['Banned'] == 1)
                                {
                                echo '<p1>Banned</p1>';
                                }

                                else if($row['Banned'] == 0 && $row['Removed'] == 0){
                                    echo '<p2> Normal</p2>';
                            };
                                ?></td>

                            <input type="hidden" name="id<?php echo $i; ?>" value="<?php echo $row['UserID']; ?>" >


                        <th><button name="Edit<?php echo $i;?>" class="open-form" onclick="openForm()">Edit</button></th>




                       <?php
                    if(isset($_POST["Edit".$i])){

                        echo "<div class='form-popup' id='myForm'>";
                       echo "<form method ='post'> ";
                        echo "<h1>".$row['UserName']."</h1>";

                        echo "<label for='banned'><b>Banned</b></label>";
                        echo "<select id='Banned' name='Banned'>";

                        if($row['Banned'] == 1)
                        {
                            echo "<option value='areBanned' selected>Banned</option>";
                            echo "<option value='notBanned'>Not Banned</option>";
                        }
                        else if($row['Banned'] == 0)
                        {
                            echo "<option value='notBanned' selected>Not Banned</option>";
                            echo "<option value='areBanned'>Banned</option>";
                        };

                        echo "</select>";

echo "<input type='submit' id='".$row['UserID']."' name='pwdReset' value='Reset'  >";
echo    "<input type='submit' id='".$row['UserID']."' name='EditSubmit'".$i." class='btn' />";
echo    "<button type='button' class='btn cancel' onclick='closeForm()'>Close</button>";
echo    "</form>";
echo   "</div>";

}




                            ?>

                        <?php
                         if(isset($_POST["pwdReset"]))
                         {
                            $salt = "randomstringforsalt";
                            $password = md5($salt.'password');
                       echo $_POST["id".$i];
                          $check = "UPDATE User SET Password = '".$password."' WHERE UserID = '".$_POST['id'.$i]."'";

                         if ($dbc->query($check) === TRUE)
                         {
                         echo "<meta http-equiv='refresh' content='0'>";

                         }
                         else
                         {

                        echo "<script> alert('Error updating record: " . $dbc->error . "')</script>";
                         }

                        }

                        ?>


                            <?php
                            if(isset($_POST["EditSubmit"]))
                            {

                               $BannedChecker = $_POST['Banned'];
                                if ($BannedChecker == 'areBanned'){
                                    $BanNum = 1;

                                }
                                if($BannedChecker == 'notBanned'){
                                    $BanNum = 0;

                                }


               $check = "UPDATE User SET Banned = '".$BanNum."' WHERE UserID = '".$_POST['id'.$i]."'";
                      if ($dbc->query($check) === TRUE)
                         {
                         echo "<meta http-equiv='refresh' content='0'>";
                         }
                         else
                         {
                                 echo 'Test';
                 //       echo "<script> alert('Error updating record: " . $dbc->error . "')</script>";
                         }


                                // update user, if banned, unbanned, or deleted, if already deleted then
                               // cannot change

                            }



                            ?>


                    </form>
                    </tr>






                        <?php $i++; endwhile;?>





                </tbody>

            </table>

        </div>

    </form>

<script>
function openForm() {
  document.getElementById("myForm").style.display = "block";
}

function closeForm() {
  document.getElementById("myForm").style.display = "none";
}
</script>

</body>

</html>
