<?php

namespace App\Http\Controllers;

use App\Models\Report;

class ReportController extends Controller
{
    public function download(Report $report)
    {
        $path = $report->path_file;

        if (empty($path)) {
            abort(404, 'Path file kosong');
        }

        $fullPath = storage_path('app/public/' . ltrim($path, '/'));

        if (! file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan: ' . $fullPath);
        }

        $fileName = ($report->name ?: 'report') . '.pdf';

        return response()->download(
            $fullPath,
            $fileName,
            ['Content-Type' => 'application/pdf']
        );
    }
}
