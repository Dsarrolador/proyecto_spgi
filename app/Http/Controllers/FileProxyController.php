<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileProxyController extends Controller
{
    /**
     * Stream a file from the FTP disk.
     *
     * @param string $path
     * @return StreamedResponse
     */
    public function stream($path)
    {
        // Decode path in case it's encoded in the URL
        $decodedPath = urldecode($path);

        if (!Storage::disk('public')->exists($decodedPath)) {
            abort(404, 'Archivo no encontrado en el servidor local.');
        }

        $headers = [
            'Content-Type' => Storage::disk('public')->mimeType($decodedPath),
            'Content-Disposition' => 'inline; filename="' . basename($decodedPath) . '"',
        ];

        return Storage::disk('public')->download($decodedPath, basename($decodedPath), $headers);
    }
}
