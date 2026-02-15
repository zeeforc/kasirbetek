<?php

namespace App\Http\Controllers;

use App\Models\StockReport;

class StockReportController extends Controller
{
    public function download(StockReport $stock_report)
    {
        $path = $stock_report->path_file;

        if (empty($path)) {
            abort(404, 'Path file kosong');
        }

        $fullPath = storage_path('app/public/' . ltrim($path, '/'));

        if (! file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan');
        }

        $fileName = ($stock_report->name ?: 'stock-report') . '.xlsx';

        return response()->download(
            $fullPath,
            $fileName,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
