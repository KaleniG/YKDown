<?php
declare(strict_types=1);

session_start();
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

  <label for="password" >Password:</label>
  <input type="password" id="password" name="password" autocomplete=”off” required>
  <label for="password_repeat">Repeat Password:</label>
  <input type="password" id="password_repeat" name="password_repeat" autocomplete=”off” required>

  <label></label>
  <div style="display: flex; gap: 10px;">
    <button type="button" onclick="window.location.href='account.php'" style="flex: 1;">&lt Log In</button>
    <input type="submit" name="register" value="Register" style="flex: 1;">
  </div>

  <?php 

if (isset($_POST['register'])) 
{
  include ('config/database.php');

  $conn = get_mysqli_connection();
  
  $email = $_POST["email"];
  $password = $_POST["password"];
  $password_repeat = $_POST["password_repeat"];

  if ($password !== $password_repeat)
  {
    echo('Passwords do not match');
    exit;
  }

  $stmt = $conn->prepare("SELECT UserId FROM Users WHERE Email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0)
  {
    echo('Account with this email already exists');
    exit;
  }
  else
  {
    $stmt = $conn->prepare("INSERT INTO Users (Email, Password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $_SESSION["email"] = $email;
    header('Location: home.php');
  }
  $stmt->close();
  $conn->close();
}
?>
    </form>
  </div>
</body>
</html>