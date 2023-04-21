<?php

session_start();

//setting the variable to the already assigned session

$username = $_SESSION['username'];

include('database.php');

$lastLog = new Database();

$time = $_SESSION['timeStamp'];

?>





<!DOCTYPE html>

<html>

<head>

<title>Idea Selection</title>



  <!-- Required meta tags -->

  <meta charset="utf-8">

  <!--To allow MS Edge and IE -->  

  <meta http-equiv="X-UA-Compatible" content="IE=Edge">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <style>
        
        .fixing
        {
            background-color: #d2d2d2;
            width: 85%;
            padding:5%;
            margin-left: 7.5%;
            margin-top: 2%;
            height 400px;
            border-radius: 5px;
            border: solid 2px black;
            position: relative;
        }
    </style>

    <!-- ADD ICON LIBRARY -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->

  <link rel="stylesheet" type="text/css" href="styles2.css">   
    <style>
        .display-4 {
            font-size: 50px;
        }
    </style>
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
    <h1> Select an Idea</h1>
    <form action="forum.php" method="post">

        <!-- FIRST FORUM -->
        <div class="pad"> 
            <?php 
            //  while ($res = mysqli_fetch_array($query)) {
            $user = $lastLog->getAllForums();
            foreach($user as $row){ ?>
                <div class="Jumbotroncentral">
                    <div class="jumbotron">
                        <h2 class="display-9"><?php echo $row->Name ?></h2>
                        <p class="display-9"><?php echo $row->Description ?></p>
                        <a href="view_ideas.php?name=<?php echo $row->Name; ?>"><button type="button" class="seeforum">Enter this idea</button></a>
                    </div>
                </div>
                <br>
            <?php } ?>
        </div>
    </form> 
    </body>
</html>
<?php

?>