<!DOCTYPE php>
<html>
    <head>
        <title>Search</title>
        <link rel="stylesheet" href="style.css">
        <link rel="icon" type="image/png" sizes="96x96" href="img/symbol.png">
    </head>

    <body>
        <h1>Search</h1>
        <p id="psearch">Search without being <a href="#" style="color:#f7a41d">tracked</a>!</p>
        <form action="query.php" method="post">
            <input type="text" name="search_query" id="searchbar" style="height: 40px;" placeholder="Search here...">
            <input type="image" src="img/symbol.png" alt="Submit" id="searchsymbol">
        </form>

        <?php
            $servername = "HOST_NAME";
            $username = "USER_NAME";
            $password = "USER_PASSWORD";
            $database = "DATABASE_NAME";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $database);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $query = $_POST["search_query"];

            $sql = "SELECT * FROM websites WHERE title COLLATE UTF8_GENERAL_CI LIKE '%$query%' UNION SELECT * FROM websites WHERE url COLLATE UTF8_GENERAL_CI LIKE '%$query%' UNION SELECT * FROM websites WHERE keywords COLLATE UTF8_GENERAL_CI LIKE '%$query%'";
            $result = $conn->query($sql);

            $count = 0;

            $result_array = array();

            if ($result->num_rows > 0) {
                
                while($row = $result->fetch_assoc() and $count < 100) {
                    $sql2 = "SELECT to_url, COUNT(to_url) as 'c' FROM connections WHERE to_url = '" . $row["url"] . "' GROUP BY to_url";
                    $result2 = $conn->query($sql2);
                    $row["c"] = $result2->fetch_row()[1];
                    array_push($result_array, $row);
                    $count += 1;
                }
                
                usort($result_array, function($a, $b) {
                    return $b['c'] <=> $a['c'];
                });
                
                foreach ($result_array as $row) {
                    echo "<div>" . $row["title"]. "<br> <a class='result_link' href=http://" . $row["url"] . "> http://" . $row["url"]. "</a></div><br>";
                }

            } else {
                echo "<div>No Results</div>";
            }
        ?>
    </body>
</html>
