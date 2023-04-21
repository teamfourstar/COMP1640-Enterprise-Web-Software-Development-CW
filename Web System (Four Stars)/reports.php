<?php 
session_start();

if(!isset($_SESSION['username'])){
     header("Location: loginreg.php");
}

include_once 'nav_bar.php';




include_once 'database.php';
$db = new Database();

    

$department =  $db->getDepartment($_SESSION['username']);
// echo $department;
if ($department == "None"){
    $department = null;
}

// echo $department;

?>

<HTML>
<head>
<title> Reports </title>

<style>
	.tab {
		overflow: hidden;
	}

	.tab button {
		background-color: inherit;
		float: left;
		border: none;
		outline: none;
		cursor: pointer;
		padding: 14px 16px;
		transition: 0.3s;
		font-size: 16px;
	}

	.tab button:hover {
		background-color: #ddd;
	}

	.tab button.active {
		background-color: #ccc;
	}

	.content {
		display: none;
		padding: 6px 12px;
	}
</style>    
</head>

<body>

    
    
    

    <!-- Tab list -->

    <div class="Jumbotroncentral">
        <div class="jumbotron">
		 <!-- Test selectors -->
<!--         <form method="GET">
            <p> User: 
                <select name="user" onchange="this.form.submit()">
        
                    <?php
                        for ($i = 0; $i < count($users); $i++) {
                            if ($users[$i] == $_GET['user']) {
                                echo "<option value='$users[$i]' selected>";
                                    $i++;
                                    echo $users[$i] ;
                                echo "</option>";
                            }
                            else {
                                echo "<option value='$users[$i]'>";
                                    $i++;
                                    echo $users[$i] ;
                                echo "</option>";
                            }
                        }
                    ?>
                </select>
            </p>
        </form>  -->           
            

            <div class = "tab">
<table class="table table-responsive">
  <tr>
    <th colspan="3"><h1 align="center">Statistics</h1></th>
    <th colspan="3"><h1 align="center">Exception Reports</h1></th>
  </tr>
  <tr>
    <td><button class = "links" onclick = "displayTab(event, 'number')" id = defaultOpen> Ideas per Department </button></td>
    <td><button class = "links" onclick = "displayTab(event, 'percent')"> Percentage per Department </button></td>
    <td><button class = "links" onclick = "displayTab(event, 'contributor')"> Contributors per Department </button></td>
    <td><button class = "links" onclick = "displayTab(event, 'noComment')"> Ideas Without Comments </button></td>
    <td><button class = "links" onclick = "displayTab(event, 'anonymousIdeas')"> Anonymous Ideas </button></td>
    <td><button class = "links" onclick = "displayTab(event, 'anonymousComments')"> Anonymous Comments </button></td>     
  </tr>
</table>          
            </div>
        </div>
    </div>


    <!-- Number of ideas per departent -->
    <div id = "number" class = "content">
        <?php 
            // Loop through all ideas
            foreach ($db->getDepartmentIdeas($department) as $idea) {

                // If IdeaCount returns a value, save value or set as zero 
                $ideaCount = ($idea->IdeaCount) ? $idea->IdeaCount : 0;

                echo @"
                    <form>          
                        <div class=\"Jumbotroncentral\">
                            <div class=\"jumbotron\">
                                <h3 class=\"display-9\">Department: ".$idea->Name."</h3>
                                <p class=\"display-9\">No. of ideas: ".$ideaCount."</p>    
                            </div>        
                        </div>
                    </form>
                ";

            }
        ?>
    </div>


    <!-- Percentage of ideas per departent -->
    <div id = "percent" class = "content">
        <?php 
            // Loop through all ideas
            foreach ($db->getPercentageIdeas($department, $forums) as $idea) {

                // If IdeaPercent returns a value, save value or set as zero 
                $ideaPercent = ($idea->IdeaPercent) ? $idea->IdeaPercent : 0;

                echo @"
                    <form>          
                        <div class=\"Jumbotroncentral\">
                            <div class=\"jumbotron\">
                                <h3 class=\"display-9\">Department: ".$idea->Name."</h3>
                                <p class=\"display-9\">Ideas Percent: ".number_format($ideaPercent, 2)." %</p>    
                            </div>        
                        </div>
                    </form>
                ";                
            }
        ?>
    </div>


    <!-- Number of contributors per department -->
    <div id = "contributor" class = "content">
        <?php 
            // Loop through all ideas
            foreach ($db->getDepartmentContributors($department) as $idea) {

                // If UserCount returns a value, save value or set as zero 
                $userCount = ($idea->UserCount) ? $idea->UserCount : 0;

                echo @"
                    <form>          
                        <div class=\"Jumbotroncentral\">
                            <div class=\"jumbotron\">
                                <h3 class=\"display-9\">Department: ".$idea->Name."</h3>
                                <p class=\"display-9\">Contributors: ".$userCount."</p>    
                            </div>        
                        </div>
                    </form>
                ";                
            }
        ?>
    </div>

    <!-- Ideas without a comment -->
    <div id = "noComment" class = "content">
        <?php 
            // If ideas are found
            if ($ideaArr = $db->getIdeasWithNoComments($department)) {

                // Loop through all ideas
                foreach ($ideaArr as $idea) {

                    // Date format
                    $date = ((new DateTime($idea->DatePosted))->format('jS M Y H:m'));

                    echo @"
                        <form>          
                            <div class=\"Jumbotroncentral\">
                                <div class=\"jumbotron\">
                                    <h3 class=\"display-9\">Title: ".$idea->Title."</h3>
                                    <p class=\"display-9\">Posted by: ".$idea->UserName."</p>    
                                    <p class=\"display-9\">Posted at: ".$date."</p>    
                                </div>        
                            </div>
                        </form>
                    ";                         
                }
            }
            else {
                echo "<p> No ideas found </p>";
            }
        ?>
    </div>


    <!-- Anonymously posted ideas -->
    <div id = "anonymousIdeas" class = "content">
        <?php 
            // If ideas are found
            if ($ideaArr = $db->getAnonymousIdeas($department)) { 

                // Loop through all ideas
                foreach ($ideaArr as $idea) { 

                    // If removed is true
                    $removed = ($idea->Removed) ? 'true' : 'false';

                    // Date format
                    $date = ((new DateTime($idea->DatePosted))->format('jS M Y H:m'));

                    $idea->IdeaText; // Other variable

                    echo @"
                        <form>          
                            <div class=\"Jumbotroncentral\">
                                <div class=\"jumbotron\">
                                    <h3 class=\"display-9\">Title: ".$idea->Title."</h3>
                                    <p class=\"display-9\">Posted by: ".$idea->UserName."</p>    
                                    <p class=\"display-9\">Posted at: ".$date."</p>    
                                    <p class=\"display-9\">Views count: ".$idea->ViewCounter."</p>    
                                    <p class=\"display-9\">Deleted: ".$removed."</p>    
                                </div>        
                            </div>
                        </form>
                    ";                           
                }
            }
            else {
                echo "<p> No ideas found </p>";
            }
        ?>
    </div>


    <!-- Anonymously posted comments -->
    <div id = "anonymousComments" class = "content">
        <?php 
            // If comments are found
            if ($commentArr = $db->getAnonymousComments($department)) {

                // Loop through all comments
                foreach ($commentArr as $comment) {

                    // If removed is true
                    $removed = ($comment->Removed) ? 'true' : 'false';

                    // Date format
                    $date = ((new DateTime($comment->DatePosted))->format('jS M Y H:m'));
                    $ideaDate = ((new DateTime($comment->IdeaDatePosted))->format('jS M Y H:m'));

                    echo @"
                        <form>          
                            <div class=\"Jumbotroncentral\">
                                <div class=\"jumbotron\">
                                    <h3 class=\"display-9\">Idea: ".$comment->IdeaTitle."</h3>
                                    <p class=\"display-9\">Idea posted at: ".$comment->IdeaDatePosted."</p>    
                                    <p class=\"display-9\">Author: ".$comment->UserName."</p>    
                                    <p class=\"display-9\">Text: ".$comment->CommentText."</p>    
                                    <p class=\"display-9\">Comment posted at: ".$date."</p>    
                                    <p class=\"display-9\">Deleted: ".$removed."</p>    
                                </div>        
                            </div>
                        </form>
                    "; 

                }
            }
            else {
                echo "<p> No comments found </p>";
            }
        ?>
    </div>
    
    <script>
        /* Reference 
         * Link: https://www.w3schools.com/howto/howto_js_tabs.asp
         */
        
        function displayTab(evt, member) {
			var i, tabcontent, tablinks;
			
			tabcontent = document.getElementsByClassName("content");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}
			
			tablinks = document.getElementsByClassName("links");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" active", "");
			}
			
			document.getElementById(member).style.display = "block";
			evt.currentTarget.className += " active";
        }
        

		// Get the element with id = "defaultOpen" and click on it
        document.getElementById("defaultOpen").click();
        
        
        // Clicking add or removed 'selected'
        $('.links').on('click', function() {
    		$('.tablinks').removeClass('selected');
    		$(this).addClass('selected');
		});
	</script>
</body>
</HTML>
