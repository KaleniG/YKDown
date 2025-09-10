<?php
declare(strict_types=1);
ini_set('max_execution_time', '3600');
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
    <li><a href="account.php">Account</a></li>
  </ul>

  <div>
    <img src="assets/images/Logo.png" class="logo">
  </div>
  <div>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
      <input type="url" name="url" required placeholder="Paste YouTube link">
      <input type="submit" value="Search">
    </form>
  </div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['url'])) {
  $url = trim($_POST['url']);

  if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/', $url, $matches)) 
  {
    $videoId = $matches[1];
    $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";

    echo <<<HTML
      <div class="thumbnail">
        <img src="{$thumbnailUrl}" class="thumbnail">
      </div>
      <div>
        <form action="download.php" method="post" class="download">
          <input type="hidden" name="url" value="{$url}">
          <select name="res">
            <option value="1080">1080p</option>
            <option value="720" select>720p</option>
            <option value="480">480p</option>
            <option value="360">360p</option>
          </select>
          <input type="submit" value="Download">
        </form>
      </div>
    HTML;
  } 
  else 
  {
    echo "<p style='color:red;'>❌ Invalid YouTube URL.</p>";
  }
}
?>

</body>
</html>