<?php
declare(strict_types=1);

session_start();
if (isset($_SESSION["name"]))
  header("Location: home.php");
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
  </ul>
  <div>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" style="display: grid; grid-template-columns: 150px 200px; gap: 10px 5px; width: max-content; margin-top: 10px">
      <label for="email">E-mail:</label>
      <input type="email" id="email" name="email" autocomplete=”off” required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" autocomplete=”off” required>

      <div>
        <label>Remember me</label>
        <input type="checkbox" name="rememberme[]" value="yes" style="flex: 1;" autocomplete=”off”>
      </div>
      <div style="display: flex; gap: 10px;">
        <button type="button" onclick="window.location.href='register.php'" style="flex: 1;">&lt Register</button>
        <input type="submit" name="login" value="Login" style="flex: 1;">
      </div>
    </form>
  </div>
</body>
</html>

<?php
if (isset($_POST['login']))
{
  include ('config/database.php');

  $conn = get_mysqli_connection();
  
  $email = $_POST["email"];
  $password = $_POST["password"];

  $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ? AND Password = ?");
  $stmt->bind_param("ss", $email, $password);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0)
  {
    if (!empty($_POST["rememberme"]))
    {
      setcookie("user_email", $email, time() + (10 * 365 * 24 * 60 * 60));
      $_SESSION["email"] = $email;
    }
    else
    {
      if (isset($_COOKIE["user_login"]))
        setcookie("user_login", "");
    }
    header("Location: home.php");
  }
  else
    echo("Invalid user credentials");

  $stmt->close();
  $conn->close();
}
?>