<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('database.php');
$admin = new Database();
$username = $_SESSION['username'];

if (isset($_SESSION['username'])){
  $str_user_role = $admin->getRole($username);
  // echo $str_user_role;
}


?>

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

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #762ffa;">
  <a href="/">
  <img border="0" alt="Idea Submission System" src="/images/FourStar.png" width="112.35" height="55.5">
  <a class="navbar-brand" href="/"></a>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent" >
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
          <form class="form-inline my-2 my-lg-0" action="forum.php">
            <button class="btn btn-light my-2 my-sm-0"  type="submit">Ideas</button>
          </form>
      </li>
    </ul>

<?php
  //Checks if user is an admin


  if(isset($_SESSION['username'])){
  echo '<a class="navbar-brand" href="#">Welcome, '.$username.'.</a>';
 

  $user = $admin->isAdmin($username);

  if($user){
      $_SESSION['admin'] = $user;
    echo '<form class="form-inline my-2 my-lg-0" action="admin_panel.php">';
    echo '<button style="margin:40px;" class="btn btn-light my-2 my-sm-0" type="Submit">Admin area</button>';
    echo '</form>';

    echo '<form class="form-inline my-2 my-lg-0" action="qa_management.php">';
    echo '<button style="margin:40px;" class="btn btn-light my-2 my-sm-0" type="Submit">QA Management Panel</button>';
    echo '</form>';
    }
  else {
       $_SESSION['admin'] = null;
    }
  }

  switch ($str_user_role) {
    case "Quality Assurance Manager":
        if (!$user){
            echo '<form class="form-inline my-2 my-lg-0" action="qa_management.php">';
            echo '<button style="margin:40px;" class="btn btn-light my-2 my-sm-0" type="Submit">QA Management Panel</button>';
            echo '</form>';
        }
        break;
    case "Quality Assurance Coordinator":
        break;
    case "Academic":
        break;
    case "Support":
        break;
}

  if(isset($_SESSION['username'])){
    echo '<form class="form-inline my-2 my-lg-0" action="reports.php">';
    echo '<button style="margin:40px;" class="btn btn-light my-2 my-sm-0" type="Submit">Reports</button>';
    echo '</form>';

     echo '<form class="form-inline my-2 my-lg-0" action="logout.php">';
     echo '<button class="btn btn-light my-2 my-sm-0" type="submit">Sign out</button>';
     echo '</form>';
  }
  else {
     echo '<form class="form-inline my-2 my-lg-0" action="loginreg.php">';
     echo '<button class="btn btn-light my-2 my-sm-0" type="submit">Sign in</button>';
     echo '</form>';
  }
?>
  </div>
</nav>

