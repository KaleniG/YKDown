<?php
declare(strict_types=1);
session_start();
//if (!isset($_SESSION['email']))
//  header('Location: index.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/stylesheets/homepage.css">
  <title>YKDOWN</title>
</head>
<body>
  <ul>
    <li><a href="index.php">YKDOWN</a></li>
    <li><a href="account.php?logout=1">Log out</a></li>
  </ul>
<?php
  include ('config/database.php');
  $conn = get_mysqli_connection();
  
  $email = null;
  if (isset($_COOKIE['user_email']))
    $email = $_COOKIE['user_email'];
  else if (isset($_SESSION['email']))
    $email = $_SESSION['email'];

  $stmt = $conn->prepare("SELECT UserId FROM Users WHERE Email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0)
  {
    $stmt->bind_result($user_id);
    $stmt->fetch();

    $stmt = $conn->prepare("SELECT Link, Resolution FROM Downloads WHERE UserId = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result)
    {
      echo "<div style='margin:10px 10px;'><table border='1' cellpadding='8'>";
      echo "<tr><th>Link</th><th>Resolution</th></tr>";
      while ($row = $result->fetch_assoc()) 
      {
        echo "<tr>";
        echo "<td><a href=\"" . htmlspecialchars($row['Link']) . "\">" . htmlspecialchars($row['Link']) . "</a></td>";
        echo "<td>" . htmlspecialchars($row['Resolution']) . "</td>";
        echo "</tr>";
      }
      echo("</table></div>");
    }
  }

?>
</body>
</html>