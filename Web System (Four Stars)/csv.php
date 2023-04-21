<?php 
    //$_GET['forum'] = "Computing";
    $forum = $_GET['forum'];

    $host = "localhost";
    $username = "jsmarchant97";
    $password = "enterpriseCW";
    $database = "jsmarcha_enterprisecw";
    $connect = "mysql:host=" . $host . ";dbname=" . $database . ";charset=utf8";
    $dbc = new PDO($connect, $username, $password);
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // zip file
    $zipname = "$forum.zip";
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);


    // Forum 
    $sql_Forum = "SELECT f.* FROM Forum f WHERE f.Name = ?";
    AddFile($zip, "Forum", $sql_Forum, [$forum]);


    // Idea 
    $sql_Idea = "SELECT i.* FROM Forum f 
    INNER JOIN Idea i ON f.ForumID = i.ForumID 
                 WHERE f.Name = ?";
    AddFile($zip, "Idea", $sql_Idea, [$forum]);


    // IdeaCategory 
    $sql_IdeaCat = "SELECT ic.* FROM Forum f 
                    INNER JOIN Idea i ON f.ForumID = i.ForumID 
                    INNER JOIN IdeaCategory ic ON i.IdeaID = ic.IdeaID 
                    WHERE f.Name = ?";
    AddFile($zip, "IdeaCategory", $sql_IdeaCat, [$forum]);


    // Category 
    $sql_Category = "SELECT DISTINCT c.* FROM Forum f 
                     INNER JOIN Idea i ON f.ForumID = i.ForumID 
                     INNER JOIN IdeaCategory ic ON i.IdeaID = ic.IdeaID 
                     INNER JOIN Category c ON ic.CategoryID = c.CategoryID
                     WHERE f.Name = ?";
    AddFile($zip, "Category", $sql_Category, [$forum]);


    // Comment 
    $sql_Comment = "SELECT c.* FROM Forum f 
                    INNER JOIN Idea i ON f.ForumID = i.ForumID 
                    INNER JOIN Comment c ON i.IdeaID = c.IdeaID 
                    WHERE f.Name = ?";
    AddFile($zip, "Comment", $sql_Comment, [$forum]);
    

    // Rate 
    $sql_Rate = "SELECT r.* FROM Forum f 
                 INNER JOIN Idea i ON f.ForumID = i.ForumID 
                 INNER JOIN Rate r ON i.IdeaID = r.IdeaID 
                 WHERE f.Name = ?";
    $sql_Rate = "SELECT * FROM Rate r";
    AddFile($zip, "Rate", $sql_Rate, [$forum]);


    // User 
    $sql_User = "SELECT DISTINCT u.* FROM Forum f 
                 INNER JOIN Idea i ON f.ForumID = i.ForumID 
                 INNER JOIN User u ON i.UserID = u.UserID 
                 WHERE f.Name = ?";
    AddFile($zip, "User", $sql_User, [$forum]);
    

    function AddFile($zip, $table, $sqlRows, $fields = [])
    {
        global $dbc;

        $fd = null;
        

        $sql_Describe = "Describe $table";
        $query = $dbc->prepare($sql_Describe);
        $query->execute();
        $colTitles = $query->fetchAll(PDO::FETCH_COLUMN);

        $sql_Rows = $sqlRows;
        $query = $dbc->prepare($sql_Rows);
        $query->execute($fields);
        $rows = $query->fetchALl(PDO::FETCH_ASSOC);


        // temporary file
        $fd = fopen('php://temp/maxmemory:1048576', 'w+');
        if (false === $fd) {
            die('Failed to create temporary file');
        }


        // data to csv
        fputcsv($fd, $colTitles);
        foreach($rows as $row) 
        {
            fputcsv($fd, $row);
        }


        // start of the stream
        rewind($fd);


        
        $zip->addFromString("$table.csv", stream_get_contents($fd));


        //close the file
        fclose($fd);
    }


    $sql_Document = "SELECT d.* FROM Forum f
                     INNER JOIN Idea i ON f.ForumID = i.ForumID
                     INNER JOIN Document d ON i.IdeaID = d.IdeaID
                     WHERE f.Name = ?";
    AddDocument($zip, "Document", $sql_Document, [$forum]);

    function AddDocument($zip, $table, $sqlRows, $fields = [])
    {
        global $dbc;

        //$fd = null;
        
        
        $sql_Describe = "Describe $table";
        $query = $dbc->prepare($sql_Describe);
        $query->execute();
        $colTitles = $query->fetchAll(PDO::FETCH_COLUMN);
        
        $sql_Rows = $sqlRows;
        $query = $dbc->prepare($sql_Rows);
        $query->execute($fields);
        $rows = $query->fetchALl(PDO::FETCH_ASSOC);


        // temporary file
        $fd = fopen('php://temp/maxmemory:1048576', 'w+');
        if (false === $fd) {
            die('Failed to create temporary file');
        }


        
        
        $column = [];
        foreach ($colTitles as $col)
        {
            if ($col != "Document")
            {
                $column[] = $col;
            }
        }
        
        
        // data to csv
        fputcsv($fd, $column);
        foreach($rows as $row) 
        {
            //  column fields
            $newRow = [];
            foreach ($row as $key => $field)
            {
                if ($key != "Document")
                    $newRow[] = $field;
            }

            fputcsv($fd, $newRow);
        }
        

        
        rewind($fd);


        // add the in-memory file to the archive, giving a name
        $zip->addFromString("$table.csv", stream_get_contents($fd));
        

        //close the file
        fclose($fd);

        
        $zip->addEmptyDir('Documents');
        foreach($rows as $row) 
        {
            $zip->addFromString("Documents/".$row['Name'], $row['Document']);
        }
    }


    // close the archive
    $zip->close();


    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename='.$zipname);
    header('Content-Length: ' . filesize($zipname));
    readfile($zipname);

    // remove the zip archive
  
    unlink($zipname);
?>