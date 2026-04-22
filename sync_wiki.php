<?php
// Script de Sincronización Masiva del Wiki
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WikiDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

echo "--- Iniciando Sincronización del Wiki ---\n";

// 1. Limpiar registros basura
echo "Limpiando registros antiguos...\n";
WikiDocument::truncate();

// 2. Obtener archivos del FTP
echo "Escaneando FTP (Disco 'ftp', carpeta 'Wiki')...\n";
$files = Storage::disk('ftp')->files('Wiki');
$total = count($files);
echo "Encontrados: $total archivos.\n";

$count = 0;
foreach ($files as $filePath) {
    if (basename($filePath) == '.ftpquota') continue;

    $fileNameWithExt = basename($filePath);
    
    // Limpiar el título (quitar timestamp y extensión)
    // Ejemplo: 1774633148_Como realizar entradas.docx -> Como realizar entradas
    $cleanTitle = $fileNameWithExt;
    
    // Quitar timestamp (si existe patrón 10+ dígitos seguido de _)
    if (preg_match('/^\d{10,}_/', $cleanTitle)) {
        $cleanTitle = preg_replace('/^\d{10,}_/', '', $cleanTitle);
    }
    
    // Quitar extensión
    $cleanTitle = pathinfo($cleanTitle, PATHINFO_FILENAME);
    
    // Crear el registro
    WikiDocument::create([
        'user_id'     => 1,
        'title'       => $cleanTitle,
        'description' => 'Documento importado automáticamente del FTP.',
        'file_path'   => $filePath,
        'categoria'   => 'Manual',
        'estado'      => 'Validado',
        'tags'        => 'Importado, FTP'
    ]);
    
    $count++;
    if ($count % 10 == 0) echo "Procesados $count de $total...\n";
}

echo "--- Sincronización Finalizada: $count documentos creados ---\n";
