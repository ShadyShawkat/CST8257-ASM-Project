<?php

$userName = $_SESSION['userName'];
$userId = trim($_SESSION['userID']);

$db = Database::getInstance();
$conn = $db->getConnection();

try
{
    $sql = "
        SELECT
            A.Title,
            A.Date_Updated,
            COUNT(P.Picture_Id),
            Ac.Description
        FROM
            Album AS A
        LEFT JOIN
            Picture AS P ON A.Album_Id = P.Album_Id
        JOIN
            Accessibility AS Ac ON A.Accessibility_Code = Ac.Accessibility_Code
        WHERE A.Owner_Id = ?
        GROUP BY
        A.Album_Id, A.Title, A.Date_Updated, Ac.Description;
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $userId);
    $stmt->execute();

    $result = $stmt->get_result();

    $albums = [];

    foreach ($result as $album)
    {
        array_push($albums, $album);
    }
}
catch (PDOException $e)
{
    echo "ERROR: " . $e->getMessage();
}

?>

<div class="container mt-2">
    <h1 class="h1 text-center">My Albums</h1>

    <?php
    // Display message to logged in user
    if (isset($userName))
    {
        loggedInMsg($userName);
    }

    ?>

    <a href="/addalbum">Create a New Album</a>
    <table class="table">
        <thead>
            <th>Title</th>
            <th>Date Updated</th>
            <th>Number of Pictures</th>
            <th>Accessibility</th>
            <th></th>
        </thead>
        <tbody>
            <?php
            $cols = count($albums[0]);

            for ($row = 0; $row < count($albums); $row++)
            {
                echo "<tr>";

                foreach ($albums[$row] as $key => $value)
                {
                    echo "<td>$value</td>";
                }

                echo "<td>Delete</td>";
                echo "</tr>";
            }

            ?>
        </tbody>
    </table>
</div>