<?php
$host = "ftp.intecsolrd.com";
$user = "spgi@intecsolrd.com";
$pass = "Intecsol00";

$conn = ftp_connect($host);
if (!$conn) die("No connect");
if (!ftp_login($conn, $user, $pass)) die("No login");
ftp_pasv($conn, true);

echo "PWD: " . ftp_pwd($conn) . "\n";
echo "Listing root (.):\n";
$list = ftp_nlist($conn, ".");
if ($list === false) echo "Failed to list .\n";
else print_r($list);

echo "Listing .. (if allowed):\n";
$list2 = ftp_nlist($conn, "..");
if ($list2 === false) echo "Failed to list ..\n";
else print_r($list2);

ftp_close($conn);
