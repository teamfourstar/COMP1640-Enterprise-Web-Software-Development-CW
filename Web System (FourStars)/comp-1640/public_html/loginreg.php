<?php 
session_start();
include_once 'server.php';
$register = new Database();


if(isset($_SESSION['username'])){
     header("Location: index.php");
}

include_once 'nav_bar.php';

?>



<!DOCTYPE html>
<html>
<head>
<title>Login and register</title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>

<!-- LOGIN AND REG FORM DESKTOP -->
  <div class="form3 col-md-8" id="LoginRegDesk">
    <div class="login-box">
      <div class="row">
        <div class="col-md-6 login-left">
            <h2 align="center"> Login </h2>
           <br>
            <form action="loginreg.php" method="post">
                        <?php 
                            include('logerrors.php');
                        ?><br>
                    
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username1" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                       
					   <button type="submit" name="login_b" class="btn btn-success"> Login </button>
                    </form>
                </div>

        <!-- REGISTRATIONFORM -->
        <div class="col-md-6 login-right">
            <h2 align="center"> Register </h2>
            <br>
             <form action="loginreg.php" method="post">
               <!-- errors -->
                        <?php 
                            include('regerrors.php');
                        ?><br>
                        <!-- errors -->

              <div class="form-group">
                  <label>Username</label>
                  <input type="text" name="username1" class="form-control" required>
              </div>

              <div class="form-group">
                  <label>Email</label>
                  <input type="text" name="email1" class="form-control" required>
              </div>

              <div class="form-group">
                  <label>Password</label>
                  <input type="password" name="password2" class="form-control" required>
              </div>
                   <div class="form-group">
                       <label>Roles</label>
                         <br>
                    <select id='roles' name='role'>
                 <?php
                     
                   $user = $register->getAllRoles();
                       foreach($user as $row){
                        echo  '<option value="'.$row->Name.'">'.$row->Name.'</option>';
                    }
                 ?>
            </select>    
              </div>
                <div class="form-group">
                    <label>Department</label>
                    <br>
                    <select id='department' name='department'>
                 <?php

                   $user = $register->getAllDepartments();
                       foreach($user as $row){
                        echo  '<option value="'.$row->Name.'">'.$row->Name.'</option>';
                    }
                 ?>
            </select>
                  
              </div>
                 
				               <p><input type="checkbox" name="checkterms" value="terms">  Click here to agree to our terms and conditions </p><br>

       
              <button type="submit" name="register_b" class="btn btn-warning"> Register </button>
            </form>

        </div>
      </div>
    </div>
  </div>

  <!-- LOGIN FORM MOBILE -->
<div class="container">

 <div class="login-box">


<div class="form1 col-md-6" id=LogMob>

<h2> Login </h2>
        <br><br>
    <form action="loginreg.php" method="post">
  
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username1" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

<div>
        <p><a href="" id="signUp"> Register </a></p>
</div>
        <br><br><br>
         <button type="submit" name="login_b" class="btn btn-primary"> Login </button>
    </form>
</div>



    <div class="form2 col-md-6" id=RegMob>
 
      <h2>Register </h2>
      <br>    
        <form action="loginreg.php" method="post">
      <div class="form-group">
          <label>Username</label>
          <input type="text" name="username1" class="form-control" required>
      </div>

      <div class="form-group">
          <label>Email</label>
          <input type="text" name="email1" class="form-control" required>
      </div>

      <div class="form-group">
          <label>Password</label>
          <input type="password" name="password2" class="form-control" required>
      </div>
      <div class="form-group">
                     
                    <label>Roles</label>
                    <br>
                    <select id='roles' name="role">
                    <?php

                        $user = $register->getAllRoles();
                        foreach($user as $row){
                            echo  '<option value="'.$row->Name.'" selected>'.$row->Name.'</option>';
                        }
                    ?>
                    </select>    
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <br>
                    <select id='department' name='department'>
                        <?php

                        $user = $register->getAllDepartments();
                        foreach($user as $row){
                            echo  '<option value="'.$row->Name.'" selected>'.$row->Name.'</option>';
                        }
                        ?>
                    </select>    
              </div>
               

<div>
        <p><a href="" id="loginID"> Login </a></p>
		
</div>
<br>

<p><input type="checkbox" name="checkterms" value="terms">  Click here to agree to our terms and conditions </p><br>

        <button type="submit" class="btn btn-primary"> Register </button>
<!-- </form> -->
        </form>
</div>
</div>
</div>


<script>
$(window).resize(function() {
  checkBrowserSize();
});
          
  function checkBrowserSize() 
  {
    var w = window.outerWidth;
    var h = window.outerHeight;

    if (w <= 1225)
    {
      document.getElementById("LoginRegDesk").style.display = "none";
      document.getElementById("LogMob").style.display = "";
    }
    else
    {
      document.getElementById("LoginRegDesk").style.display = "";
      document.getElementById("LogMob").style.display = "none";
      document.getElementById("RegMob").style.display = "none";
    }
              
  }
</script>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
$('.form2').hide();
    $('.form1').hide();
</script>
<script>
$(function(){
  $('#signUp').click(function(e){
    $('.form1').hide();
      $('.form2').show();
      e.preventDefault();
  });
});
</script>

<script>
$(function(){
  $('#loginID').click(function(e){
    $('.form2').hide();
      $('.form1').show();
      e.preventDefault();
  });
});
</script>
            

</body>
</html>