<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$backupFile = 'C:\PROYECTOS\SPGI\spgi_backup.sql';
$handle = fopen($backupFile, 'r');
if (!$handle) die("Could not open backup file\n");

while (($line = fgets($handle)) !== false) {
    if (strpos($line, 'INSERT INTO `cliente_maestro` VALUES') !== false) {
        echo "Found line, executing...\n";
        try {
            Illuminate\Support\Facades\DB::statement($line);
            echo "Success!\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        break;
    }
}
fclose($handle);
