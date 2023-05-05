<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

    /** Connects to the database and runs SQL commands */
    class Database
    {
        private $dbc; // Database connection


        public function __construct()
        {
            $this->connection();
        }


        /** Connection to the database */
        private function connection()
        {
            // Testing connection
            try {
            	$host = "localhost";
            	$username = "jsmarchant97";
            	$password = "enterpriseCW";
            	$database = "jsmarcha_enterprisecw";
            	$connect = "mysql:host=" . $host . ";dbname=" . $database . ";charset=utf8";
                $this->dbc = new PDO($connect, $username, $password);
                $this->dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) {
		try {
            		$host = "localhost";
            		$username = "root";
            		$password = "";
            		$database = "mdb_st2645h";
            		$connect = "mysql:host=" . $host . ";dbname=" . $database . ";charset=utf8";
                	$this->dbc = new PDO($connect, $username, $password);
                	$this->dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
                	echo "<script> alert('ERROR \nConnection failed: " . $e->getMessage() . "') </script>";
		}
            }
        }




	
        public function createUser(string $username, string $password, string $email, string $role, string $department) // TESTED
        {
            // Clear excess whitespace
            $username = trim($username);
            $password = trim($password);
            $email = trim($email);
            $role = trim($role);
            $department = trim($department);


            // Parameter validation
            $e1 = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);
            $e2 = $this->strValidation($password, "password", $this->letterNum, __FUNCTION__);
            $e3 = $this->typeValidation($email, "email", __FUNCTION__, FILTER_VALIDATE_EMAIL);
            $e4 = $this->strValidation($role, "role", $this->letters, __FUNCTION__);
            $e5 = $this->strValidation($department, "department", $this->letters, __FUNCTION__);


            // haven't thrown an error
            if (!isset($e1) && !isset($e2) && !isset($e3) && !isset($e4) && !isset($e5)) {

                $department_Sub = "SELECT IF (r.NoDepartment = 1, NULL, (SELECT d.DepartmentID FROM Department d WHERE d.Name = ?))";

                $sql = "INSERT INTO User (DepartmentID, RoleID, UserName, Password, Email)
                        SELECT ($department_Sub), r.RoleID, ?, ?, ? FROM Role r
                        WHERE r.Name = ?";

                $this->doesExistSQL($sql, [$department, $username, $password, $email, $role]);
            }
            else
                $this->errorMessage([$e1, $e2, $e3, $e4, $e5]);
        }


        
        public function usernameTaken(string $username) // TESTED
        {
            // Clear excess whitespace
            $username = trim($username);


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            // If parameter hasn't thrown an error
            if (!isset($e)) {
                $sql = "SELECT UserName FROM User WHERE UserName = ?";

                return $this->doesExistSQL($sql, [$username]);
            }
            else
                $this->errorMessage([$e]);

            return true;
        }


        
        public function checkLogin(string $username, string $password) 
        {
            // Clear excess whitespace
            $username = trim($username);
            $password = trim($password);


            // Parameter validation
            $e1 = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);
            $e2 = $this->strValidation($password, "password", $this->letterNum, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {
                $sql = "SELECT UserName FROM User WHERE UserName = ? AND Password = ? AND Banned = '0' AND Removed = '0'";

                return $this->doesExistSQL($sql, [$username, $password]);
            }
            else
                $this->errorMessage([$e1, $e2]);

            return false;
        }


        
        public function getLastLogin(string $username): ?string
        {
            // Clear excess whitespace
            $username = trim($username);


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            if (!isset($e)) {
                $sql = "SELECT LastLogin FROM User WHERE UserName = ?";

                return $this->getFieldSQL($sql, "LastLogin", [$username]);
            }
            else
                $this->errorMessage([$e]);


            return null;
        }


        
        public function setLastLogin(string $username): void
        {
            // Clear excess whitespace
            $username = trim($username);


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            if (!isset($e)) {
                $date = (new DateTime())->format('Y-m-d H:i:s');

                $sql = "UPDATE User SET LastLogin = ? WHERE UserName = ?";

                $this->runSQL($sql, [$date, $username]);
            }
            else
                $this->errorMessage([$e]);
        }




        
         
        public function getAllDepartments(): ?array 
        {
            $sql = "SELECT Name, Description FROM Department WHERE Removed = '0' ORDER BY Name";

            return $this->getArrayObjectsSQL($sql);
        }


        
        public function getDepartment(string $username): ?string 
        {
            // Clear excess whitespace
            $username = trim($username);


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            // If parameter hasn't thrown an error
            if (!isset($e)) {
                $name_Sub = "IF (r.NoDepartment = 1, 'None', d.Name)";
                $desc_Sub = "IF (r.NoDepartment = 1, 'None', d.Description)";

                $sql = "SELECT $name_Sub AS Name, $desc_Sub AS Description FROM Department d
                        RIGHT JOIN User u ON d.DepartmentID = u.DepartmentID
                        INNER JOIN Role r ON u.RoleID = r.RoleID
                        WHERE u.UserName = '".$username."' AND u.Banned = '0' AND u.Removed = '0' AND  (d.Removed = 0 OR d.Removed IS NULL)";
                return $this->dbc->query($sql)->fetch()["Name"];
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        public function getAllRoles(): ?array // TESTED
        {
            $sql = "SELECT Name, Type, Description FROM Role WHERE Removed = '0'";

            return $this->getArrayObjectsSQL($sql);
        }


       
        public function getRole(string $username): ?string // TESTED
        {
            // Clear excess whitespace
            $username = trim($username);


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            // If parameter hasn't thrown an error
            if (!isset($e)) {
                $sql = "SELECT r.Name, r.Type, r.Description FROM Role r
                        INNER JOIN User u ON r.RoleID = u.RoleID
                        WHERE u.UserName = '".$username."' AND u.Removed = '0' AND r.Removed = '0'";

                return $this->dbc->query($sql)->fetch()["Name"];
            }
            else
                $this->errorMessage([$e]);

            return null;
        }




        
        public function getAllForums(): ?array // TESTED
        {
            $sql = "SELECT Name, Description, Closure, FinalClosure FROM Forum WHERE Removed = '0' ORDER BY Name ASC";

            return $this->getArrayObjectsSQL($sql);
        }


        
        public function createForum(string $name, string $description, string $closureDate): void // TESTED
        {
            // Clear excess whitespace
            $name = trim($name);
            $description = trim($description);
            $closureDate = trim($closureDate);


            // Parameter validation
            $e1 = $this->strValidation($name, "name", $this->letters, __FUNCTION__);
            $e2 = $this->strValidation($description, "description", $this->letterNum, __FUNCTION__);
            $e3 = $this->strValidation($closureDate, "closureDate", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2) && !isset($e3)) {

                $closure = (new DateTime($closureDate))->format('Y-m-d H:i:s');
                $finalClosure = (date_modify(new DateTime($closureDate), "+30 days"))->format('Y-m-d H:i:s');

                $sql = "INSERT INTO Forum (Name, Description, Closure, FinalClosure) VALUES (?, ?, ?, ?)";

                $this->runSQL($sql, [$name, $description, $closure, $finalClosure]);
            }
            else
                $this->errorMessage([$e1, $e2, $e3]);
        }


		
		public function deleteForum(string $forum): void // TESTED
		{
			// Clear excess whitespace
            $forum = trim($forum);


            // Parameter validation
            $e = $this->strValidation($forum, "forum", $this->letters, __FUNCTION__);


            // If parameter hasn't thrown an error
            if (!isset($e)) {
                $sql = "UPDATE Forum SET Removed = '1' WHERE Name = ?";

				$this->runSQL($sql, [$forum]);
            }
            else
                $this->errorMessage([$e]);
		}




		
        public function getForum(string $forum): ?object // TESTED
        {
            // Clear excess whitespace
            $forum = trim($forum);


            // Parameter validation
            $e = $this->strValidation($forum, "forum", $this->letters, __FUNCTION__);


            // If parameter hasn't thrown an error
            if (!isset($e)) {
                $sql = "SELECT Name, Description, Closure, FinalClosure FROM Forum WHERE Name = ? AND Removed = '0'";

				return $this->getObjectSQL($sql, [$forum]);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        
        public function getAllIdeas(string $forum, int $page, int $amount): ?array // TESTED
        {
            // Clear excess whitespace
            $forum = trim($forum);


            // Parameter validation
            $e1 = $this->strValidation($forum, "forum", $this->letters, __FUNCTION__);
            $e2 = $this->typeValidation($page, "page", __FUNCTION__);
            $e3 = $this->typeValidation($amount, "amount", __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2) && !isset($e3)) {
                $startFrom = ($page - 1) * $amount;

                $username_Sub = "CASE WHEN u.Removed = '1' THEN 'Deleted' WHEN i.Anonymous = '1' THEN 'Anonymous' ELSE u.UserName END";

                $likes_Sub = "SELECT COUNT(ThumbUp) FROM Rate rl WHERE rl.IdeaID = r.IdeaID AND rl.ThumbUp = '1'";
                $dislikes_Sub = "SELECT COUNT(ThumbDown) FROM Rate rd WHERE rd.IdeaID = r.IdeaID AND rd.ThumbDown = '1'";

                $sql = "SELECT DISTINCT ($username_Sub) AS UserName, i.Title, i.IdeaText, i.DatePosted, ($likes_Sub) AS Likes, ($dislikes_Sub) AS Dislikes FROM Idea i
                        INNER JOIN User u ON i.UserID = u.UserID
                        INNER JOIN Rate r ON i.IdeaID = r.IdeaID
                        INNER JOIN Forum f ON i.ForumID = f.ForumID
                        WHERE f.Name = ? AND i.Removed = '0'
                        ORDER By i.DatePosted DESC
                        LIMIT $startFrom, $amount";

                return $this->getArrayObjectsSQL($sql, [$forum]);
            }
            else
                $this->errorMessage([$e1, $e2, $e3]);

            return null;
        }


        
        public function createIdea(string $idea, string $title, string $forum, string $username, bool $anonymous, array $categories = [], array $files = []): void // TESTED
        {
            // Clear excess whitespace
            $idea = trim($idea);
            $title = trim($title);
            $forum = trim($forum);
            $username = trim($username);

			$anonymous = ($anonymous) ? 1 : 0;

            // Parameter validation
            $e1 = $this->strValidation($forum, "forum", $this->letters, __FUNCTION__);
            $e2 = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {

				$date = (new DateTime())->format('Y-m-d H:i:s');

                $user_Sub = "SELECT UserID FROM User WHERE UserName = ?";
                $forum_Sub = "SELECT ForumID FROM Forum WHERE Name = ?";

                $sql = "INSERT INTO Idea (UserID, ForumID, Title, IdeaText, DatePosted, Anonymous)
                        VALUES (($user_Sub), ($forum_Sub), ?, ?, ?, ?)";

                $this->runSQL($sql, [$username, $forum, $title, $idea, $date, $anonymous]);


				// Loop through all categories
				foreach ($categories as $category) {
					$this->setIdeaCategory($category, $title, $date);
				}


				// Loop through all files
				foreach ($files as $file) {
					$this->uploadDocument($file, $title, $date);
				}
            }
            else
                $this->errorMessage([$e1, $e2]);
        }


       
		public function deleteIdea(string $title, string $datePosted): void // TESTED
		{
			// Clear excess whitespace
            $title = trim($title);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $sql = "UPDATE Idea SET Removed = '1' WHERE Title = ? AND DatePosted = ?";

                $this->runSQL($sql, [$title, $date]);
            }
            else
                $this->errorMessage([$e]);
		}


       
        public function getCoordinatorEmail(string $department): ?string // TESTED
        {
            // Clear excess whitespace
            $department = trim($department);


            // Parameter validation
            $e = $this->strValidation($department, "department", $this->letters, __FUNCTION__);


            // If parameter hasn't thrown an error
            if (!isset($e)) {
                $sql = "SELECT u.Email FROM User u
                        INNER JOIN Department d ON u.DepartmentID = d.DepartmentID
                        INNER JOIN Role r ON u.RoleID = r.RoleID
                        WHERE d.Name = ? AND r.Type = 'Coordinator' AND u.Removed = '0'";

                return $this->getFieldSQL($sql, "Email", [$department]);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        
        public function increaseViewCount(string $ideaTitle, string $datePosted): void // TESTED
        {
            // Clear excess whitespace
            $ideaTitle = trim($ideaTitle);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $sql = "UPDATE Idea SET ViewCounter = (ViewCounter + 1) WHERE Title = ? AND DatePosted = ?";

                $this->runSQL($sql, [$ideaTitle, $date]);
            }
            else
                $this->errorMessage([$e]);
        }

		public function insertCommentForJake(string $p_post_id, string $p_user_name, string $p_comment_text)// UNTESTED
        {

			try //server attempts to run query
			{
				$sql = 'INSERT INTO Comment SET
					IdeaID = :Ideaid,
					UserID = (SELECT UserID FROM User WHERE UserName = :UserName),
					CommentText = :Commenttext,
					Anonymous = :Anonymous,
					DatePosted = CURDATE(),
					Removed = :Removed';

				$s = $this->dbc->prepare($sql);
				$s->bindValue(':Ideaid', $p_post_id);
				$s->bindValue(':UserName', $p_user_name);
				$s->bindValue(':Commenttext', $p_comment_text);
				$s->bindValue(':Anonymous', 0);
				$s->bindValue(':Removed', 0);
				$s->execute();
			}catch (PDOException $e)//opens error page if query fails
			{
				$this->errorMessage([$e]);
				//$output = 'Error fetching ideas:' .  $e->getMessage();
			}
        }

        public function setThumbsUpDownForJake(string $p_post_id, string $p_user_name, int $p_thumb_position)// UNTESTED
        {
			$thumb_down = 0;
			if ($p_thumb_position == 0){
				$thumb_down = 1;
			}

			try //server attempts to run query
			{

				$sql = 'INSERT INTO Rate SET
					IdeaID = :Ideaid,
					UserID = (SELECT UserID FROM User WHERE UserName = :UserName),
					ThumbUp = :Thumbup,
					ThumbDown = :Thumbdown';
				$s = $this->dbc->prepare($sql);
				$s->bindValue(':Ideaid', $p_post_id);
				$s->bindValue(':UserName', $p_user_name);
				$s->bindValue(':Thumbup', $p_thumb_position);
				$s->bindValue(':Thumbdown', $thumb_down);
				$s->execute();
				}
			catch (PDOException $e)//opens error page if query fails
			{
				$this->errorMessage([$e]);
				//$output = 'Error fetching ideas:' .  $e->getMessage();
			}
        }


        public function getCommentsForJake(int $p_idea_id)// UNTESTED
        {

			try //server attempts to run query
			{
				$result = $this->dbc->query('SELECT C.CommentID, C.IdeaID, U.UserName, C.CommentText, C.Anonymous, C.DatePosted, U.Banned
          FROM Comment C
          JOIN User as U ON U.UserID = C.UserID
          JOIN Idea as I ON I.IdeaID = C.IdeaID
          WHERE C.IdeaID = ' . $p_idea_id);
				return $result;
			}
			catch (PDOException $e)//opens error page if query fails
			{
				$this->errorMessage([$e]);
				//$output = 'Error fetching ideas:' .  $e->getMessage();
			}
        }

        public function getIdeaCount()// UNTESTED
        {

			try //server attempts to run query
			{
				$result = $this->dbc->query('SELECT COUNT(*) AS record_count FROM Idea I LEFT JOIN Forum F ON F.ForumID = I.ForumID WHERE F.Name = "' . $_SESSION['forum_name'] . '"')->fetch()["record_count"];
				return $result;
			}
			catch (PDOException $e)//opens error page if query fails
			{
				$this->errorMessage([$e]);
				//$output = 'Error fetching ideas:' .  $e->getMessage();
			}
        }

        
        public function getIdeasWithPagination(string $p_pagination_step_from, string $p_pagination_step_to)// UNTESTED
        {
			$q = '
					SELECT
						Idea.IdeaID,
						Title,
						IdeaText,
						Anonymous,
						DatePosted,
						UserName,
						IF (ThumbUpSum IS NULL, 0 ,ThumbUpSum) AS Likes,
						IF (ThumbDownSum IS NULL, 0 ,ThumbDownSum) AS Dislikes,
						IF (commentCount IS NULL, 0, commentCount) AS commentCount,
						Banned
					FROM
						Idea
					LEFT JOIN
						(SELECT IdeaID, SUM(ThumbUp) AS ThumbUpSum, SUM(ThumbDown) AS ThumbDownSum FROM Rate GROUP BY IdeaID) AS RateAggregated
						ON
						RateAggregated.IdeaID = Idea.IdeaID
					LEFT JOIN
						(SELECT IdeaID, COUNT(CommentID) AS commentCount FROM `Comment` GROUP BY IdeaID) AS CommentAggregated
						ON
						CommentAggregated.IdeaID = Idea.IdeaID
					  LEFT JOIN
						`User`
						ON
						Idea.UserID = `User`.`UserID`
                         LEFT JOIN
						`Forum`
						ON
						Idea.ForumID = `Forum`.`ForumID`
                        WHERE `Forum`.Name LIKE "' . $_SESSION['forum_name'] . '"
					ORDER BY
						IdeaID DESC
					LIMIT '.$p_pagination_step_from.', '.$p_pagination_step_to;
			try //server attempts to run query
			{
				$result = $this->dbc->query($q);
				return $result;
			}
			catch (PDOException $e)//opens error page if query fails
			{
				$this->errorMessage([$e]);
				//$output = 'Error fetching ideas:' .  $e->getMessage();
			}
        }


        
        public function getIdea(string $title, string $datePosted): ?object // TESTED
        {
            // Clear excess whitespace
            $title = trim($title);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $username_Sub = "CASE WHEN u.Removed = '1' THEN 'Deleted' WHEN i.Anonymous = '1' THEN 'Anonymous' ELSE u.UserName END";

                $likes_Sub = "SELECT COUNT(ThumbUp) FROM Rate rl WHERE rl.IdeaID = r.IdeaID AND rl.ThumbUp = '1'";
                $dislikes_Sub = "SELECT COUNT(ThumbDown) FROM Rate rd WHERE rd.IdeaID = r.IdeaID AND rd.ThumbDown = '1'";

                $sql = "SELECT ($username_Sub) AS UserName, i.Title, i.IdeaText, i.DatePosted, ($likes_Sub) AS Likes, ($dislikes_Sub) AS Dislikes FROM Idea i
                        INNER JOIN User u ON i.UserID = u.UserID
                        INNER JOIN Rate r ON i.IdeaID = r.IdeaID
                        WHERE i.Title = ? AND i.DatePosted = ? AND i.Removed = '0'";

                return $this->getObjectSQL($sql, [$title, $date]);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


		
        public function getComments(string $ideaTitle, string $datePosted, int $amount = null): ?array // TESTED
        {
            // Clear excess whitespace
            $ideaTitle = trim($ideaTitle);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {
                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');
                $limit= "";

                $username_Sub = "CASE WHEN u.Removed = '1' THEN 'Deleted' WHEN c.Anonymous = '1' THEN 'Anonymous' ELSE u.UserName END";

                $sql = "SELECT ($username_Sub) AS UserName, c.CommentText, c.DatePosted FROM Comment c
                        INNER JOIN User u ON c.UserID = u.UserID
                        INNER JOIN Idea i ON c.IdeaID = i.IdeaID
                        WHERE i.Title = ? AND i.DatePosted = ? AND c.Removed = '0'
                        ORDER BY c.DatePosted ASC
                        $limit";

                return $this->getArrayObjectsSQL($sql, [$ideaTitle, $date]);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        
        public function createComment(string $comment, string $ideaTitle, string $datePosted, string $username, bool $anonymous): void // TESTED
        {
            // Clear excess whitespace
            $comment = trim($comment);
            $ideaTitle = trim($ideaTitle);
            $datePosted = trim($datePosted);
            $username = trim($username);

			$anonymous = ($anonymous) ? 1 : 0;

            // Parameter validation
            $e1 = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);
            $e2 = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $user_Sub = "SELECT u.UserID FROM User u WHERE u.UserName = ?";

                $sql = "INSERT INTO Comment (IdeaID, UserID, CommentText, Anonymous)
                        SELECT i.IdeaID, ($user_Sub), ?, ? FROM Idea i
                        WHERE i.Title = ? AND i.DatePosted = ?";

                $this->runSQL($sql, [$username, $comment, $anonymous, $ideaTitle, $date]);
            }
            else
                $this->errorMessage([$e1, $e2]);
        }


		
		public function deleteComment(string $comment, string $datePosted): void // TESTED
		{
			// Clear excess whitespace
            $comment = trim($comment);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {
                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $sql = "UPDATE Comment SET Removed = '1' WHERE CommentText = ? AND DatePosted = ?";

                $this->runSQL($sql, [$comment, $date]);
            }
            else
                $this->errorMessage([$e]);
		}


        
        public function getIdeaDocuments(string $ideaTitle, string $datePosted): ?array // TESTED
        {
            // Clear excess whitespace
            $ideaTitle = trim($ideaTitle);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {

                // Can only one document be uploaded per idea?
                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $sql = "SELECT d.Name, d.Type, d.Document, d.Size, d.Removed FROM Document d
						INNER JOIN Idea i ON d.IdeaID = i.IdeaID
						WHERE i.Title = ? AND i.DatePosted = ?";

                return $this->getArrayObjectsSQL($sql, [$ideaTitle, $date]);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        
        public function uploadDocument($file, string $ideaTitle, string $datePosted) // TESTED
        {
            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


			if (!isset($e)) {
				$name = $file['name'];
				$type = $file['type'];
				$size = $file['size'];
				$tmp_name = $file['tmp_name'];

				$data = file_get_contents($tmp_name);

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');


				try {
                    // If file name longer than 100 char
                    if (strlen($name) > 100)
                        throw new Exception("File name too long");


					// If file size is zero
					if ($size == 0)
						throw new Exception("Select a file to upload");


					// If file larger than 1 MB
					if ($size > 1000000)
						throw new Exception("File too large, max size 1 MB");



					// Allowed file types
					$allowedTypes = ['image/png',
									 'image/jpeg',
									 'image/gif',
									 'application/pdf',
									 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Microsoft Word
									];
					$accept = false;

					// Loop through file types
					foreach ($allowedTypes as $ext) {
						if ($type == $ext) {
							$accept = true;
							break;
						}
					}

					// If type not accepted
					if (!$accept)
						throw new Exception("File type is not accepted");



					$sql = "SELECT d.Name FROM Document d
							INNER JOIN Idea i ON d.IdeaID = i.IdeaID
							WHERE i.Title = ? AND i.DatePosted = ? AND d.Name = ?";

					// If name taken
					if ($this->doesExistSQL($sql, [$ideaTitle, $date, $name]))
						throw new Exception("File already uploaded with that name");
				}
				catch (Exception $e) {
					echo "<script> alert('" . $e->getMessage() . "') </script>";

					return false;
				}


				$idea_Sub = "SELECT i.IdeaID FROM Idea i WHERE i.Title = ? AND i.DatePosted = ?";

				$sql = "INSERT INTO Document (IdeaID, Name, Type, Document, Size) VALUES (($idea_Sub), ?, ?, ?, ?)";

				$this->runSQL($sql, [$ideaTitle, $date, $name, $type, $data, $size]);

				return true;
			}
			else
                $this->errorMessage([$e]);


			return false;
        }


		
		public function downloadDocument(string $name, string $ideaTitle, string $datePosted): void // TESTED
		{
            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


			if (!isset($e)) {
                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

				$sql = "SELECT d.Name, d.Type, d.Document FROM Document d
						INNER JOIN Idea i ON d.IdeaID = i.IdeaID
						WHERE i.Title = ? AND i.DatePosted = ? AND d.Name = ?";

				$file = $this->getObjectSQL($sql, [$ideaTitle, $date, $name]);

				session_start();
				$_SESSION['fileName'] = $file->Name;
				$_SESSION['fileType'] = $file->Type;
				$_SESSION['fileDocument'] = $file->Document;

				echo "<script> window.open('downloadFile.php', '_newtab'); </script>";
			}
			else
                $this->errorMessage([$e]);
		}


       
        public function getUserEmail(string $username): ?string // TESTED
        {
            // Clear excess whitespace
            $username = trim($username);


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {
                $sql = "SELECT Email FROM User WHERE UserName = ?";

                return $this->getFieldSQL($sql, "Email", [$username]);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }




        
        public function liked(string $username, string $ideaTitle, string $datePosted) // TESTED
        {
            return $this->Rated($username, $ideaTitle, $datePosted, true, __FUNCTION__);
        }


       
        public function disliked(string $username, string $ideaTitle, string $datePosted) // TESTED
        {
            return $this->Rated($username, $ideaTitle, $datePosted, false, __FUNCTION__);
        }


        /** Checks if a user has liked or disliked an idea */
        private function rated(string $username, string $ideaTitle, string $datePosted, bool $liked, $function)
        {
            // Clear excess whitespace
            $username = trim($username);
            $ideaTitle = trim($ideaTitle);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e1 = $this->strValidation($username, "username", $this->letterNum, $function);
            $e2 = $this->strValidation($datePosted, "datePosted", $this->dateTime, $function);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {
                $thumb = ($liked) ? "ThumbUp" : "ThumbDown";

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $sql = "SELECT r.ThumbUp FROM Rate r
                        INNER JOIN Idea i ON r.IdeaID = i.IdeaID
                        INNER JOIN User u ON r.UserID = u.UserID
                        WHERE r.$thumb = '1' AND u.UserName = ? AND i.Title = ? AND i.DatePosted = ?";

                return $this->doesExistSQL($sql, [$username, $ideaTitle, $date]);
            }

            return null;
        }


        
        public function getNumLikes(string $ideaTitle, string $datePosted): int // TESTED
        {
            return $this->getRatings($ideaTitle, $datePosted, true, __FUNCTION__);
        }


       
        public function getNumDislikes(string $ideaTitle, string $datePosted): int // TESTED
        {
            return $this->getRatings($ideaTitle, $datePosted, false, __FUNCTION__);
        }


        /** Counts the number of likes or dislikes for an idea */
        private function getRatings(string $ideaTitle, string $datePosted, bool $likes, $function)
        {
            // Clear excess whitespace
            $ideaTitle = trim($ideaTitle);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e = $this->strValidation($datePosted, "datePosted", $this->dateTime, $function);


            // If parameters haven't thrown an error
            if (!isset($e)) {
                $thumb = ($likes) ? "ThumbUp" : "ThumbDown";
                $alias = ($likes) ? "Likes" : "Dislikes";

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $sql = "SELECT COUNT(r.$thumb) AS $alias FROM Rate r
                        INNER JOIN Idea i ON r.IdeaID = i.IdeaID
                        WHERE r.$thumb = '1' AND i.Title = ? AND i.DatePosted = ?";

                return $this->getFieldSQL($sql, $alias, [$ideaTitle, $date]);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        public function setLike(string $username, string $ideaTitle, string $datePosted): void // TESTED
        {
            $this->setRatings($username, $ideaTitle, $datePosted, true, true, __FUNCTION__);
        }


        
        public function setDislike(string $username, string $ideaTitle, string $datePosted): void // TESTED
        {
            $this->setRatings($username, $ideaTitle, $datePosted, false, true, __FUNCTION__);
        }


        
        public function unsetLike(string $username, string $ideaTitle, string $datePosted): void // TESTED
        {
            $this->setRatings($username, $ideaTitle, $datePosted, true, false, __FUNCTION__);
        }


        
        public function unsetDislike(string $username, string $ideaTitle, string $datePosted): void // TESTED
        {
            $this->setRatings($username, $ideaTitle, $datePosted, false, false, __FUNCTION__);
        }


        private function setRatings(string $username, string $ideaTitle, string $datePosted, bool $like, bool $set, $function): void
        {
            // Clear excess whitespace
            $username = trim($username);
            $ideaTitle = trim($ideaTitle);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e1 = $this->strValidation($username, "username", $this->letterNum, $function);
            $e2 = $this->strValidation($datePosted, "datePosted", $this->dateTime, $function);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {

                // Set rating variables
                if ($like && $set) {
                    $thumbUp = 1;
                    $thumbDown = 0;
                }
                else if (!$like && $set) {
                    $thumbUp = 0;
                    $thumbDown = 1;
                }
                else {
                    $thumbUp = 0;
                    $thumbDown = 0;
                }

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');


                $rate_Sub = "SELECT r.ThumbUp FROM Rate r
                             INNER JOIN User u ON r.UserID = u.UserID
                             INNER JOIN Idea i ON r.IdeaID = i.IdeaID
                             WHERE u.UserName = ? AND i.Title = ? AND i.DatePosted = ?";


                // If row exist update, else insert
                if ($this->doesExistSQL($rate_Sub, [$username, $ideaTitle, $date])) {
					$sql = "UPDATE Rate r
							INNER JOIN User u ON r.UserID = u.UserID
							INNER JOIN Idea i ON r.IdeaID = i.IdeaID
							SET r.ThumbUp = ?, r.ThumbDown = ?
							WHERE u.UserName = ? AND i.Title = ? AND i.DatePosted = ?";

                    $this->runSQL($sql, [$thumbUp, $thumbDown, $username, $ideaTitle, $date]);
                }
                else {
                    $idea_Sub = "SELECT IdeaID FROM Idea WHERE Title = ? AND DatePosted = ?";

                    $sql = "INSERT INTO Rate (IdeaID, UserID, ThumbUp, ThumbDown)
                            SELECT ($idea_Sub), UserID, ?, ? FROM User
                            WHERE UserName = ?";

                    $this->runSQL($sql, [$ideaTitle, $datePosted, $thumbUp, $thumbDown, $username]);
                }
            }
            else
                $this->errorMessage([$e1, $e2]);
        }




        
        public function getAllCategories(): ?array // TESTED
        {
            $status_Sub = "SELECT DISTINCT ic.CategoryID FROM IdeaCategory ic WHERE ic.CategoryID = c.CategoryID";

            $sql = "SELECT c.Name, c.Description, IF (c.CategoryID IN ($status_Sub), true, false) AS Status FROM Category c WHERE Removed = '0' ORDER BY Name ASC";

            return $this->getArrayObjectsSQL($sql);
        }


        
        public function getCategory(string $category): ?object // TESTED
        {
            // Clear excess whitespace
            $category = trim($category);


            // Parameter validation
            $e = $this->strValidation($category, "category", $this->letterNum, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {
                $sql = "SELECT Name, Description FROM Category WHERE Name = ? AND Removed = '0'";

                return $this->getObjectSQL($sql, [$category]);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        
        public function createCategory(string $category, string $description = null): void // TESTED
        {
            // Clear excess whitespace
            $category = trim($category);


            // Parameter validation
            $e = $this->strValidation($category, "category", $this->letterNum, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {

                // Only manager

                $sql = "INSERT INTO Category (Name, Description) VALUES (?, ?)";

                $this->runSQL($sql, [$category, $description]);
            }
            else
                $this->errorMessage([$e]);
        }


        
        public function deleteCategory(string $category) // TESTED
        {
            // Clear excess whitespace
            $category = trim($category);


            // Parameter validation
            $e = $this->strValidation($category, "category", $this->letters, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {
                $catIdea_Sub = "SELECT CategoryID FROM IdeaCategory";

                $sql = "UPDATE Category SET Removed = '1' WHERE Name = ? AND CategoryID NOT IN ($catIdea_Sub)";

				// If delete successful
                if ($this->runSQL($sql, [$category], true))
                    return true;
            }
            else
                $this->errorMessage([$e]);


            return false;
        }


        
        public function setIdeaCategory(string $category, string $title, string $datePosted): void // TESTED
        {
            // Clear excess whitespace
            $category = trim($category);
            $title = trim($title);
            $datePosted = trim($datePosted);


            // Parameter validation
            $e1 = $this->strValidation($category, "category", $this->letters, __FUNCTION__);
            $e2 = $this->strValidation($datePosted, "datePosted", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {

                $date = (new DateTime($datePosted))->format('Y-m-d H:i:s');

                $category_Sub = "SELECT c.CategoryID FROM Category c WHERE c.Name = ?";

                $sql = "INSERT INTO IdeaCategory (IdeaID, CategoryID)
                        SELECT i.IdeaID, ($category_Sub) FROM Idea i
                        WHERE i.Title = ? AND i.DatePosted = ?";

                $this->runSQL($sql, [$category, $title, $date]);
            }
            else
                $this->errorMessage([$e1, $e2]);
        }




        
        public function highestRatedIdeas(int $amount = null): ?array // TESTED
        {
            // Parameter validation
            if (!is_null($amount)) {
                $e = $this->typeValidation($amount, "amount", __FUNCTION__);
                $amount = "LIMIT $amount";
            }


            // If parameters haven't thrown an error
            if (!isset($e)) {
                $username_Sub = "CASE WHEN u.Removed = '1' THEN 'Deleted' WHEN i.Anonymous = '1' THEN 'Anonymous' ELSE u.UserName END";

                $likes_Sub = "SELECT COUNT(ThumbUp) FROM Rate rl WHERE rl.IdeaID = r.IdeaID AND rl.ThumbUp = '1'";
                $dislikes_Sub = "SELECT COUNT(ThumbDown) FROM Rate rd WHERE rd.IdeaID = r.IdeaID AND rd.ThumbDown = '1'";

                $sql = "SELECT DISTINCT ($username_Sub) AS UserName, i.Title, i.IdeaText, i.DatePosted, ($likes_Sub) AS Likes, ($dislikes_Sub) AS Dislikes FROM Idea i
                        INNER JOIN User u ON i.UserID = u.UserID
                        INNER JOIN Rate r ON i.IdeaID = r.IdeaID
                        WHERE i.Removed = '0'
                        ORDER By (Likes - Dislikes) DESC
                        $amount";

                return $this->getArrayObjectsSQL($sql);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        
        public function mostViewedIdeas(int $amount = null): ?array // TESTED
        {
            // Parameter validation
            if (!is_null($amount)) {
                $e = $this->typeValidation($amount, "amount", __FUNCTION__);
                $amount = "LIMIT $amount";
            }


            // If parameters haven't thrown an error
            if (!isset($e)) {
				$username_Sub = "CASE WHEN u.Removed = '1' THEN 'Deleted' WHEN i.Anonymous = '1' THEN 'Anonymous' ELSE u.UserName END";

                $likes_Sub = "SELECT COUNT(ThumbUp) FROM Rate rl WHERE rl.IdeaID = r.IdeaID AND rl.ThumbUp = '1'";
                $dislikes_Sub = "SELECT COUNT(ThumbDown) FROM Rate rd WHERE rd.IdeaID = r.IdeaID AND rd.ThumbDown = '1'";

                $sql = "SELECT DISTINCT ($username_Sub) AS UserName, i.Title, i.IdeaText, i.DatePosted, ($likes_Sub) AS Likes, ($dislikes_Sub) AS Dislikes, i.ViewCounter FROM Idea i
                        INNER JOIN User u ON i.UserID = u.UserID
                        INNER JOIN Rate r ON i.IdeaID = r.IdeaID
                        WHERE i.Removed = '0'
                        ORDER By i.ViewCounter DESC
                        $amount";

				return $this->getArrayObjectsSQL($sql);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }


        
        public function latestIdeas(int $amount = null): ?array // TESTED
        {
            // Parameter validation
            if (!is_null($amount)) {
                $e = $this->typeValidation($amount, "amount", __FUNCTION__);
                $amount = "LIMIT $amount";
            }


            // If parameters haven't thrown an error
            if (!isset($e)) {
				$username_Sub = "CASE WHEN u.Removed = '1' THEN 'Deleted' WHEN i.Anonymous = '1' THEN 'Anonymous' ELSE u.UserName END";

                $likes_Sub = "SELECT COUNT(ThumbUp) FROM Rate rl WHERE rl.IdeaID = r.IdeaID AND rl.ThumbUp = '1'";
                $dislikes_Sub = "SELECT COUNT(ThumbDown) FROM Rate rd WHERE rd.IdeaID = r.IdeaID AND rd.ThumbDown = '1'";

                $sql = "SELECT DISTINCT ($username_Sub) AS UserName, i.Title, i.IdeaText, i.DatePosted, ($likes_Sub) AS Likes, ($dislikes_Sub) AS Dislikes FROM Idea i
                        INNER JOIN User u ON i.UserID = u.UserID
                        INNER JOIN Rate r ON i.IdeaID = r.IdeaID
                        WHERE i.Removed = '0'
                        ORDER By i.DatePosted DESC
                        $amount";

				return $this->getArrayObjectsSQL($sql);
            }
            else
                $this->errorMessage([$e]);


            return null;
        }


        
        public function latestComments(int $amount = null): ?array // TESTED
        {
            // Parameter validation
            if (!is_null($amount)) {
                $e = $this->typeValidation($amount, "amount", __FUNCTION__);
                $amount = "LIMIT $amount";
            }


            // If parameters haven't thrown an error
            if (!isset($e)) {
				$username_Sub = "CASE WHEN u.Removed = '1' THEN 'Deleted' WHEN c.Anonymous = '1' THEN 'Anonymous' ELSE u.UserName END";

                $sql = "SELECT DISTINCT ($username_Sub) AS UserName, c.CommentText, c.DatePosted, i.Title AS IdeaTitle, i.DatePosted AS IdeaDatePosted FROM Comment c
						INNER JOIN User u ON c.UserID = u.UserID
						INNER JOIN Idea i ON c.IdeaID = i.IdeaID
						WHERE c.Removed = '0'
						ORDER BY c.DatePosted DESC
						$amount";

                return $this->getArrayObjectsSQL($sql);
            }
            else
                $this->errorMessage([$e]);

            return null;
        }




        
        public function getDepartmentIdeas(string $department = null): ?array // TESTED
        {
            // Empty array
            $fields = [];


            // If not null
            if (!is_null($department)) {
				// Parameter validation
				$e = $this->strValidation($department, "category", $this->letters, __FUNCTION__);

                $departmentSQL .= "WHERE d.Name = ?";
                $fields[] = $department;
            }


			// If parameters haven't thrown an error
            if (!isset($e)) {
				$department_Sub = "SELECT COUNT(ds.Name) FROM Department ds
								   INNER JOIN User u ON ds.DepartmentID = u.DepartmentID
								   INNER JOIN Idea i ON u.UserID = i.UserID
								   INNER JOIN Forum f ON i.ForumID = f.ForumID
								   WHERE ds.DepartmentID IN (d.DepartmentID)
								   GROUP BY ds.DepartmentID";

				$sql = "SELECT d.Name, ($department_Sub) AS IdeaCount FROM Department d $departmentSQL";

				return $this->getArrayObjectsSQL($sql, $fields);
			}
			else
                $this->errorMessage([$e]);


			return null;
        }


       
        public function getPercentageIdeas(string $department = null): ?array
        {
            // Empty array
            $fields = [];


            // If not null
            if (!is_null($department)) {
				// Parameter validation
				$e = $this->strValidation($department, "category", $this->letters, __FUNCTION__);

                $departmentSQL = "WHERE d.Name = ?";
                $fields[] = $department;
            }


			// If parameters haven't thrown an error
            if (!isset($e)) {
				$percent_Sub = "(COUNT(ds.Name)) / (SELECT COUNT(it.Title) FROM Idea it) * 100";

				$department_Sub = "SELECT ($percent_Sub) FROM Department ds
								   INNER JOIN User u ON ds.DepartmentID = u.DepartmentID
								   INNER JOIN Idea i ON u.UserID = i.UserID
								   INNER JOIN Forum f ON i.ForumID = f.ForumID
								   WHERE ds.DepartmentID IN (d.DepartmentID)
								   GROUP BY ds.DepartmentID";

				$sql = "SELECT d.Name, ($department_Sub) AS IdeaPercent FROM Department d $departmentSQL";

				return $this->getArrayObjectsSQL($sql, $fields);
			}
			else
                $this->errorMessage([$e]);


			return null;
        }


        
        public function getDepartmentContributors(string $department = null): ?array
        {
            // Empty array
            $fields = [];


            // If not null
            if (!is_null($department)) {
				// Parameter validation
				$e = $this->strValidation($department, "category", $this->letters, __FUNCTION__);

                $departmentSQL .= "WHERE d.Name = ?";
                $fields[] = $department;
            }


			// If parameters haven't thrown an error
            if (!isset($e)) {
				$department_Sub = "SELECT COUNT(DISTINCT u.UserName) FROM Department ds
								   INNER JOIN User u ON ds.DepartmentID = u.DepartmentID
								   INNER JOIN Idea i ON u.UserID = i.UserID
								   INNER JOIN Forum f ON i.ForumID = f.ForumID
								   WHERE ds.DepartmentID IN (d.DepartmentID)
								   GROUP BY ds.DepartmentID";

				$sql = "SELECT d.Name, ($department_Sub) AS UserCount FROM Department d $departmentSQL";

				return $this->getArrayObjectsSQL($sql, $fields);
			}
			else
                $this->errorMessage([$e]);


			return null;
        }


        
        public function getIdeasWithNoComments(string $department = null): ?array
        {
            // Empty array
            $fields = [];


            // If not null
            if (!is_null($department)) {
				// Parameter validation
				$e = $this->strValidation($department, "category", $this->letters, __FUNCTION__);

                $departmentSQL = "d.Name = ? AND";
                $fields[] = $department;;
            }


			// If parameters haven't thrown an error
            if (!isset($e)) {
				$sql = "SELECT u.UserName, i.Title, i.DatePosted FROM Idea i
						INNER JOIN User u ON i.UserID = u.UserID
						INNER JOIN Department d ON u.DepartmentID = d.DepartmentID
						INNER JOIN Forum f ON i.ForumID = f.ForumID
						WHERE $departmentSQL i.IdeaID NOT IN (SELECT c.IdeaID FROM Comment c)
						ORDER BY i.DatePosted DESC";

				return $this->getArrayObjectsSQL($sql, $fields);
			}
			else
                $this->errorMessage([$e]);


			return null;
        }


        
        public function getAnonymousIdeas(string $department = null): ?array
        {

            // Empty array
            $fields = [];


            // If not null
            if (!is_null($department)) {
				// Parameter validation
				$e = $this->strValidation($department, "category", $this->letters, __FUNCTION__);

                $departmentSQL .= "d.Name = ? AND";
                $fields[] = $department;
            }


			// If parameters haven't thrown an error
            if (!isset($e)) {
				$sql = "SELECT u.UserName, i.Title, i.IdeaText, i.DatePosted, i.ViewCounter, i.Removed FROM Idea i
						INNER JOIN User u ON i.UserID = u.UserID
						INNER JOIN Department d ON u.DepartmentID = d.DepartmentID
						INNER JOIN Forum f ON i.ForumID = f.ForumID
						WHERE $departmentSQL i.Anonymous = '1'
						ORDER BY i.DatePosted DESC";

				return $this->getArrayObjectsSQL($sql, $fields);
			}
			else
                $this->errorMessage([$e]);


			return null;
        }


        public function getAnonymousComments(string $department = null): ?array
        {
            // Empty array
            $fields = [];


            // If not null
            if (!is_null($department)) {
				// Parameter validation
				$e = $this->strValidation($department, "category", $this->letters, __FUNCTION__);

                $departmentSQL .= "d.Name = ? AND";
                $fields[] = $department;
            }


			// If parameters haven't thrown an error
			if (!isset($e)) {
				$sql = "SELECT u.UserName, i.Title AS IdeaTitle, i.DatePosted AS IdeaDatePosted, c.CommentText, c.DatePosted, c.Removed FROM Comment c
						INNER JOIN User u ON c.UserID = u.UserID
						INNER JOIN Idea i ON c.IdeaID = i.IdeaID
						INNER JOIN Department d ON u.DepartmentID = d.DepartmentID
						INNER JOIN Forum f ON i.ForumID = f.ForumID
						WHERE $departmentSQL c.Anonymous = '1'
						ORDER BY i.DatePosted DESC";

				return $this->getArrayObjectsSQL($sql, $fields);
			}
			else
                $this->errorMessage([$e]);


			return null;
        }




        
        public function isAdmin(string $username) // TESTED
        {
            // Clear excess whitespace
            $username = trim($username);


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e)) {
                $sql = "SELECT Admin FROM User WHERE UserName = ?";

                return ($this->getFieldSQL($sql, "Admin", [$username])) ? true : false;
            }
            else
                $this->errorMessage([$e]);

            return false;
        }


        
        public function editUser(string $username, string $newUsername, string $newEmail, bool $admin = false): void // TESTED
        {
            // Clear excess whitespace
            $username = trim($username);
            $newUsername = trim($newUsername);
            $newEmail = trim($newEmail);

			$admin = ($admin) ? 1 : 0;

            // Parameter validation
            $e1 = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);
            $e2 = $this->strValidation($newUsername, "newUsername", $this->letterNum, __FUNCTION__);
            $e3 = $this->typeValidation($newEmail, "newEmail", __FUNCTION__, FILTER_VALIDATE_EMAIL);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2) && !isset($e3)) {
                // Only admin

                $sql = "UPDATE User SET UserName = ?, Email = ?, Admin = ? WHERE UserName = ?";

                $this->runSQL($sql, [$newUsername, $newEmail, $admin, $username]);
            }
            else
                $this->errorMessage([$e1, $e2, $e3]);
        }


       
        public function editAccount(string $username, string $newUsername, string $newEmail): void // TESTED
        {
            // Clear excess whitespace
            $username = trim($username);
            $newUsername = trim($newUsername);
            $newEmail = trim($newEmail);


            // Parameter validation
            $e1 = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);
            $e2 = $this->strValidation($newUsername, "newUsername", $this->letterNum, __FUNCTION__);
            $e3 = $this->typeValidation($newEmail, "newEmail", __FUNCTION__, FILTER_VALIDATE_EMAIL);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2) && !isset($e3)) {
                $sql = "UPDATE User SET UserName = ?, Email = ? WHERE UserName = ?";

                $this->runSQL($sql, [$newUsername, $newEmail, $username]);
            }
            else
                $this->errorMessage([$e1, $e2, $e3]);
        }


        
        public function setPassword(string $username, string $password): void // TESTED
        {
            // Clear excess whitespace
            $username = trim($username);
            $password = trim($password);


            // Parameter validation
            $e1 = $this->strValidation($username, "username", $this->letterNum, __FUNCTION__);
            $e2 = $this->strValidation($password, "password", $this->letterNum, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {
                $sql = "UPDATE User SET Password = ? WHERE UserName = ?";

                $this->runSQL($sql, [$password, $username]);
            }
            else
                $this->errorMessage([$e1, $e2]);
        }


		
		public function hideUser(string $username): void
		{
			$this->hiding($username, true, __FUNCTION__);
		}


		/**
		 * Show a user's ideas and comments
		 *
		 * @param string $username Name of the user
		 *
		 * @return void
		 */
		public function unhideUser(string $username): void
		{
			$this->hiding($username, false, __FUNCTION__);
		}


		/**  */
		private function hiding(string $username, bool $hide, $function): void
		{
			// Clear excess whitespace
            $username = trim($username);

			$hide = ($hide) ? 1 : 0;


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, $function);


            // If parameters haven't thrown an error
            if (!isset($e)) {
				$sql = "UPDATE User SET Hidden = ? WHERE UserName = ?";

				$this->runSQL($sql, [$hide, $username]);
            }
            else
                $this->errorMessage([$e]);
		}


		
		public function banUser(string $username): void // TESTED
		{
			$this->banning($username, true, __FUNCTION__);
		}


		
		public function unbanUser(string $username): void
		{
			$this->banning($username, false, __FUNCTION__);
		}


		/**  */
		private function banning(string $username, bool $ban, $function): void
		{
			// Clear excess whitespace
            $username = trim($username);

			$ban = ($ban) ? 1 : 0;


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, $function);


            // If parameters haven't thrown an error
            if (!isset($e)) {
				$sql = "UPDATE User SET Banned = ? WHERE UserName = ?";

				$this->runSQL($sql, [$ban, $username]);
            }
            else
                $this->errorMessage([$e]);
		}


		
		public function deleteUser(string $username): void // TESTED
		{
			$this->removing($username, true, __FUNCTION__);
		}


		
		public function recoverUser(string $username): void // TESTED
		{
			$this->removing($username, false, __FUNCTION__);
		}


		/**  */
		private function removing(string $username, bool $remove, $function): void
		{
			// Clear excess whitespace
            $username = trim($username);

			$remove = ($remove) ? 1 : 0;


            // Parameter validation
            $e = $this->strValidation($username, "username", $this->letterNum, $function);


            // If parameters haven't thrown an error
            if (!isset($e)) {
				$sql = "UPDATE User SET Removed = ? WHERE UserName = ?";

				$this->runSQL($sql, [$remove, $username]);
            }
            else
                $this->errorMessage([$e]);
		}


        
        public function editClosureDate(string $forum_id, string $closure): void // TESTED
        {
            // Clear excess whitespace
            $forum_id = trim($forum_id);
            $closure = trim($closure);


            // Parameter validation
            $e1 = $this->strValidation($forum_id, "forum", $this->letterNum, __FUNCTION__);
            $e2 = $this->strValidation($closure, "closure", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {
                // Only admin

                $date = (new DateTime($closure))->format('Y-m-d H:i:s');

                $sql = "UPDATE Forum SET Closure = ? WHERE ForumID = ?";

                $this->runSQL($sql, [$date, $forum_id]);
            }
            else
                $this->errorMessage([$e1, $e2]);
        }


       
        public function editFinalClosureDate(string $forum_id, string $closure): void // TESTED
        {
            // Clear excess whitespace
            $forum_id = trim($forum_id);
            $closure = trim($closure);


            // Parameter validation
            $e1 = $this->strValidation($forum_id, "forum", $this->letterNum, __FUNCTION__);
            $e2 = $this->strValidation($closure, "closure", $this->dateTime, __FUNCTION__);


            // If parameters haven't thrown an error
            if (!isset($e1) && !isset($e2)) {
                // Only admin

                $date = (new DateTime($closure))->format('Y-m-d H:i:s');

                $sql = "UPDATE Forum SET FinalClosure = ? WHERE ForumID = ?";

                $this->runSQL($sql, [$date, $forum_id]);
            }
            else
                $this->errorMessage([$e1, $e2]);
        }




       
        public function search(string $search): ?array
        {
            return null;
        }




		
        public function downloadDatabase()
        {

        }





        private function getArrayObjectsSQL(string $sql, array $fields = []): ?array
        {
            try {
                $query = $this->dbc->prepare($sql);
                $query->execute($fields);
                $rows = $query->fetchAll(PDO::FETCH_OBJ);

                // If no rows found
                return ($rows == false) ? null : $rows;

            } catch (Exception $e) {
                $this->errorMessage([$e]);

                return null;
            }
        }


        private function getObjectSQL($sql, array $fields = []): ?object
        {
            try {
                $query = $this->dbc->prepare($sql);
                $query->execute($fields);
                $row = $query->fetch(PDO::FETCH_OBJ);

                // If no row row found
                return ($row == false) ? null : $row;

            } catch (Exception $e) {
                $this->errorMessage([$e]);

                return null;
            }
        }


        /** Runs SQL and return a field */
        private function getFieldSQL(string $sql, string $getField, array $fields = []): ?string
        {
            try {
                $query = $this->dbc->prepare($sql);
                $query->execute($fields);
                $row = $query->fetch(PDO::FETCH_OBJ);

                return $row->{$getField};

            } catch (Exception $e) {
                $this->errorMessage([$e]);

                return null;
            }
        }


        /** Runs SQL and check if returned a result */
        private function doesExistSQL(string $sql, array $fields = [])
        {
            try {
                $query = $this->dbc->prepare($sql);
                $row = $query->execute($fields);

                return ($query->rowCount()) ? true : false;

            } catch (Exception $e) {
                $this->errorMessage([$e]);

                return null;
            }
        }


        /** Runs SQL */
        private function runSQL(string $sql, array $fields = [], bool $countRows = false)
        {
            try {
                $query = $this->dbc->prepare($sql);
                $query->execute($fields);

                if ($countRows)
                    return $query->rowCount();

            } catch (Exception $e) {
                $this->errorMessage([$e]);
            }
        }




        
        private function strValidation(string $var, string $varName, string $charType, $function)
        {
            try {
                // Check variable is a string
                if (!is_string($var)) {
                    throw new Exception("Function {$function} parameter \${$varName} is attempting to validate \'{$var}\', it should be in a string format");
                }


                // Set character types allow for string
                if ($charType == $this->letters) {
                    $allowedChar = "a-z ";
                    $shouldContain = "contain only letters";
                }
                else if ($charType == $this->letterNum) {
                    $allowedChar = "0-9a-z ";
                    $shouldContain = "contain only letters and numbers";
                }
                else if ($charType == $this->text) {
                    $allowedChar = "0-9a-z .,!";
                    $shouldContain = "contain only letters and numbers";
                }
                else if ($charType == $this->dateTime) {
                    try {
                        new DateTime($var); // Check if can be in DateTime format

                        $allowedChar = "0-9 :-";
                        $shouldContain = "be a DateTime";
                    }
                    catch (Exception $ee) {
                        throw new Exception("Function {$function}() parameter \${$varName} is attempting to pass '{$var}', it should {$shouldContain}");
                    }
                }
                else
                    throw new Exception("Validation failed!  No character type selected");


                // Error if parameter contain incorrect information
                if (preg_match("/[^$allowedChar]/i", $var))
                    throw new Exception("Function {$function}() parameter \${$varName} is attempting to pass '{$var}', it should {$shouldContain}");

                return null;
            }
            catch (Exception $e) {
                return $e;
            }
        }


        /** Validates if variable is the correct data type */
        private function typeValidation($var, string $varName, $function, string $correctType = null)
        {
            try {
				$escape = true;

                // Set type comparison
                if (is_int($var)) {
                    $validType = "an integer";
                    $correctType = FILTER_VALIDATE_INT;

					// If $var is 0 escape error message
					$escape = ($var == 0) ? false : true;
                }
                else if (is_bool($var)) {
					$var = ($var) ? true : false;
                    $validType = "an boolean";
                    $correctType = FILTER_VALIDATE_BOOLEAN;
                }
                else if ($correctType == FILTER_VALIDATE_EMAIL)
                    $validType = "a valid email address";
                else
                    throw new Exception("Validation failed!  No variable type selected");


                // Error if parameter contain incorrect data type
                if (!filter_var($var, $correctType) && $escape)
                    throw new Exception("Function {$function}() parameter \${$varName} is attempting to pass '{$var}', it should contain {$validType}");


                return null;
            }
            catch (Exception $e) {
                return $e;
            }
        }


        private $letters = "letters";
        private $letterNum = "letterNum";
        private $text = "text";
        private $dateTime = "DateTime";



        private function errorMessage(array $errorMessage): void
        {
            $message = 'ERRORS';

            foreach ($errorMessage as $e) {
                if (!is_null($e))
                    $message = $message . '\nCaught exception: ' . $e->getMessage();
            }

            echo '<script> alert("'.$message.'") </script>';
        }
    }
?>
