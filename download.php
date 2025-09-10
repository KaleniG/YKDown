<?php
declare(strict_types=1);
session_start();
ini_set('max_execution_time', '3600');

$folder = __DIR__ . '/temp';

$files = glob($folder . '/*');
foreach ($files as $file) 
{
  if (is_file($file)) 
  {
    unlink($file); 
  }
}

require __DIR__ . '/vendor/autoload.php';

use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['url'])) 
  {
    exit('❌ No URL provided.');
}

$url = $_POST['url'];
$res = $_POST['res'];

$yt = new YoutubeDl();
$yt->setBinPath(__DIR__ . '/assets/bin/yt-dlp.exe');

$collection = $yt->download(
    Options::create()
        ->downloadPath(__DIR__ . '/temp')
        ->url($url)
        ->format("bestvideo[ext=mp4][height<={$res}]+bestaudio[ext=m4a]/best")
        ->ffmpegLocation(__DIR__ . '/assets/bin/ffmpeg.exe')
        ->mergeOutputFormat('mp4')
        ->output('%(title)s.%(ext)s')
);

foreach ($collection->getVideos() as $video) 
{
  if ($video->getError() !== null) {
    exit('❌ Error: ' . $video->getError());
  }
  $file = $video->getFilename();
  if (!file_exists($file)) {
      exit('❌ File not found.');
  }

  $email = null;
  if (isset($_COOKIE['user_email']))
    $email = $_COOKIE['user_email'];
  else if (isset($_SESSION['email']))
    $email = $_SESSION['email'];

  if (isset($email))
  {
    include ('config/database.php');

    $conn = get_mysqli_connection();

    $stmt = $conn->prepare("SELECT UserId FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0)
    {
      $stmt->bind_result($user_id);
      $stmt->fetch();

      $resolution = $res . 'p';
      $stmt = $conn->prepare("INSERT INTO Downloads (UserID, Link, Resolution) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $user_id, $url, $resolution);
      $stmt->execute();
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    unlink($file);
  }
  exit;
}
