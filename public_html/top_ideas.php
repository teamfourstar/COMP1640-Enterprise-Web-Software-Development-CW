<?php 
session_start(); 

include_once 'nav_bar.php';
include_once 'database.php';
$db = new Database();
?>

<HTML>
<head>
<Title>Ideas Section</Title>
	<style>
		.filterTab {
			overflow: hidden;
		}

		.filterTab button {
			background-color: #ece3fa;
			float: left;
			border: none;
			outline: none;
			cursor: pointer;
			padding: 14px 16px;
			transition: 0.3s;
			font-size: 16px;
		}

		.filterTab button:hover {
			background-color: #ece3fa;
		}

		.filterTab button.active {
			background-color: #ece3fa;
		}

		.filterContent {
			display: none;
			padding: 6px 12px;
		}
	</style>

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
</head>

<body>
    
    <div class = "filterTab">
        <button class = "filterlinks" onclick = "displayFilter(event, 'topIdeas')" id = defaultOpen> Top Rated Ideas </button>
        <button class = "filterlinks" onclick = "displayFilter(event, 'mostViewedIdeas')"> Most Viewed Ideas </button>
        <button class = "filterlinks" onclick = "displayFilter(event, 'recentIdeas')"> Recent Ideas </button>
        <button class = "filterlinks" onclick = "displayFilter(event, 'recentComments')"> Recent Comments </button>
    </div>
	
    
    <?php $displayNum = 5; ?>
    
	<div id = "topIdeas" class = "filterContent">
		<h2> TOP RATED IDEAS </h2>
        
        <?php displayIdeas($db->highestRatedIdeas($displayNum)); ?>
	</div>

    
	<div id = "mostViewedIdeas" class = "filterContent">
		<h2> MOST VIEWED IDEAS </h2>
        
        <?php displayIdeas($db->mostViewedIdeas($displayNum)); ?>
	</div>
    

	<div id = "recentIdeas" class = "filterContent">
		<h2> RECENT IDEAS </h2>
        
        <?php displayIdeas($db->latestIdeas($displayNum)); ?>
	</div>
    

	<div id = "recentComments" class = "filterContent">
		<h2> RECENT COMMENTS </h2>

		<?php displayComments($db->latestComments($displayNum)); ?>
	</div>
    
    
    <?php 
        function displayIdeas($ideaList) 
        {
            foreach ($ideaList as $idea) {
                echo '
                <div class="Jumbotroncentral">
                	<div class="jumbotron">
						<p style="float: right;">Posted on: ' . ((new DateTime($idea->DatePosted))->format('jS M Y H:m')) . '</p>
						<h3 class="display-9"> ' . $idea->Title . ' </h3>
						<p class="display-9"> ' . $idea->UserName . ' </p>
						<p class="lead"> ' . $idea->IdeaText . ' </p>
						<button style="font-size:14px"> ' . $idea->Likes . ' <i class="fa fa-thumbs-up"></i></button>
						<button style="font-size:14px">' . $idea->Dislikes . '<i class="fa fa-thumbs-down"></i></button>
					</div>
                </div>';
            }
        }
        
        
        function displayComments($commentList)
        {
			$num = 0;
            foreach ($commentList as $comment) {
                echo '
                <div class="Jumbotroncentral">
					<div class="jumbotron">
						<p style="float: right;">Posted on: ' . ((new DateTime($comment->DatePosted))->format('jS M Y H:m')) . ' </p>
						<h3 class="display-9"> From Idea: ' . $comment->IdeaTitle . ' </h3>
						<p class="display-9">  ' . $comment->UserName . ' </p>
						<p class="lead">Comment: ' . $comment->CommentText . ' </p>
					</div>
                </div>';
                $comment->IdeaDatePosted; // Other variable
            }
        }
    ?>


	<script>
		/* Reference 
		 * All tab code from w3schools
		 * Link: https://www.w3schools.com/howto/howto_js_tabs.asp
		 */
		
		function displayFilter(evt, member) {
			var i, tabcontent, tablinks;
			
			tabcontent = document.getElementsByClassName("filterContent");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}
			
			tablinks = document.getElementsByClassName("filterlinks");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" active", "");
			}
			
			document.getElementById(member).style.display = "block";
			evt.currentTarget.className += " active";
		}
		

		// Get the element with id = "defaultOpen" and click on it
		document.getElementById("defaultOpen").click();
		
		
		// Clicking add or removed 'selected'
		$('.tablinks').on('click', function() {
			$('.tablinks').removeClass('selected');
			$(this).addClass('selected');
		});
	</script>
</body>
</HTML>
