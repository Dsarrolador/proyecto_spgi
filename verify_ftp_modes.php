<?php
$host = "ftp.intecsolrd.com";
$user = "spgi@intecsolrd.com";
$pass = "Intecsol00";

$conn = ftp_connect($host);
if (!$conn) die("No connect");
if (!ftp_login($conn, $user, $pass)) die("No login");

echo "--- Testing ACTIVE Mode ---\n";
ftp_pasv($conn, false);
print_r(ftp_nlist($conn, "."));

echo "--- Testing PASSIVE Mode ---\n";
ftp_pasv($conn, true);
print_r(ftp_nlist($conn, "."));

ftp_close($conn);
