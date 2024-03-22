<?php
// Redirect to the absolute URL with the HTTP protocol
$url = 'https://www.example.com'; // replace with your desired URL
header('Location: ' . $url, true, 302);
exit;
?>
