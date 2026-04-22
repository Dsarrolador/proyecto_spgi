<?php
$host = 'ftp.intecsolrd.com';
$user = 'spgi@intecsolrd.com';
$pass = 'Intecsol00';

$conn = ftp_connect($host);
if (!$conn) die("No connection");
if (!ftp_login($conn, $user, $pass)) die("No login");
ftp_pasv($conn, true);

echo "PWD: " . ftp_pwd($conn) . "\n";

echo "LIST /:\n";
print_r(ftp_nlist($conn, "."));

echo "\nSCANNING COMMON PATHS:\n";
$paths = ['/spgi', '/public_html', '/public_ftp', '/www', '../', '../../'];
foreach ($paths as $p) {
    echo "Testing $p: ";
    $list = @ftp_nlist($conn, $p);
    if ($list === false) {
        echo "NOT FOUND\n";
    } else {
        echo "FOUND (" . count($list) . " items)\n";
        print_r($list);
    }
}

ftp_close($conn);
