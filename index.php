<?php
declare(strict_types=1);
ini_set('MAX_EXECUTION_TIME', 360000);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <form action="<?php echo(htmlspecialchars($_SERVER['PHP_SELF'])) ?>" method="post">
    <label>Link:</label>
    <input type="url" name="url" required>
    <input type="submit" name="submit" value="Download">
  </form>

<?php
require __DIR__ . '/vendor/autoload.php';

use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

$collection = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) 
{
  $pathToYTDLPBinary = __DIR__ .'/assets/bin/yt-dlp.exe';
  $pathToFFMPEG = __DIR__ .'/assets/bin/';
  $yt = new YoutubeDl();
  $yt->setBinPath($pathToYTDLPBinary);

  $collection = $yt->download(Options::create()
    ->downloadPath(__DIR__ . '/temp')
    ->url($_POST['url'])
    ->format('bestvideo[ext=mp4]+bestaudio[ext=m4a]/best')
    ->ffmpegLocation($pathToFFMPEG)
    ->mergeOutputFormat('mp4')
    ->output('%(title)s.%(ext)s')
  );

  foreach ($collection->getVideos() as $video) 
  {
    if ($video->getError() !== null) {
      echo("❌ Error: " . $video->getError());
    } else {
      echo("✅ Downloaded: " . $video->getTitle());

      $file_url = $video->getFilename();
      if (file_exists($file_url)) 
      {
        if (ob_get_level()) 
        {
          ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_url) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_url));
        flush();
        readfile($file_url);
        unlink($file_url);
        exit;
      }
    }
  }
}

?>
</body>
</html>