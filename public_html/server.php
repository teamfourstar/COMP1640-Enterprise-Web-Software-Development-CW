
<?php
    include_once('database.php');
    if (session_status() === PHP_SESSION_NONE) session_start();

    $funObj = new Database();
    $logerrors = array();
    $errors = array();


    // if the register button is clicked



    if (isset($_POST['register_b'])){

        $termsCheck = is_null($_POST['checkterms']) ? false : true;

        if ($termsCheck)
        {
            $username = $_POST['username1'];
            $email = $_POST['email1'];
            $password1 = $_POST['password2'];
            $salt = "randomstringforsalt";

            $password1 = md5($salt.$password1);

            $role = $_POST['role'];
            $department = $_POST['department'];

            // ensure that form fields are filled properly


            $user = $funObj->usernameTaken($username);
            if ($user){
                array_push($errors,"user already exists");
            }

            if(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
                array_push($errors,"invalid email");
            }

            // if there are no errors, save user to database

            if (count($errors) == 0) {

                $register = $funObj->createUser($username, $password1, $email, $role, $department);

                if(!$register){
                    array_push($errors,"Registration Successful");
                }else{
                    array_push($errors,"Registration Not Successful");
                }
            }
        }
        else
        {
            array_push($errors,"Terms & Conditions not accepted");
        }
    }




    // login

    if (isset($_POST['login_b'])){

        $username = $_POST['username1'];
        $password = $_POST['password'];
        $salt = "randomstringforsalt";
        //$password = md5($salt.$password);

        $user = $funObj->checkLogin($username, $password);
        if ($user) {

            // login Success
            array_push($logerrors,"login successful");

            //session for logged in user
            session_start();
            $_SESSION['username'] = $username;
            $time = $funObj->getLastLogin($username);
            $_SESSION['timeStamp'] = $time;
            // This will need to change to
            header("Location: forum.php");

        }
        else {
            array_push($logerrors,"Wrong password or ID");
        }
    }




    // submit idea

    if (isset($_POST['sub_idea'])){

         //initialise variables
        $username = $_SESSION['username'];
        $title = $_POST['ideatitle3'];
        $category = [$_POST['category']];
        $description = $_POST['description'];
        $anonymous = (is_null($_POST['ann'])) ? false : true;
        $forum = $_SESSION['forum_name'];
        $file = $_FILES['fileUpload']['tmp_name'] == "" ? [] : [$_FILES['fileUpload']];

        //calling the function createIdea from the class database
        $funObj->createIdea($description, $title, $forum, $username, $anonymous, $category, $file);

        header("Location: view_ideas.php");

    }
?>
