<?php
$host = "ftp.intecsolrd.com";
$user = "spgi@intecsolrd.com";
$pass = "Intecsol00";

$conn = ftp_connect($host);
if (!$conn) die("No connect");
if (!ftp_login($conn, $user, $pass)) die("No login");
ftp_pasv($conn, true);

$tempfile = "VERIFICACION_ANTIGRAVITY.txt";
$f = fopen('php://memory', 'r+');
fwrite($f, "Test de conexion realizado por Antigravity el " . date('Y-m-d H:i:s'));
rewind($f);

if (ftp_fput($conn, $tempfile, $f, FTP_ASCII)) {
    echo "Archivo de prueba CREADO con éxito: $tempfile\n";
    echo "Contenido actual de la raiz:\n";
    print_r(ftp_nlist($conn, "."));
} else {
    echo "Error al CREAR el archivo de prueba.\n";
}

ftp_close($conn);
