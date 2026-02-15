<?php

namespace App\Observers;

use App\Models\StockReport;
use App\Helpers\ExcelExportHelper;

class StockReportObserver
{
    public function creating(StockReport $report): void
    {
        $stockReportService = app(\App\Services\StockReportService::class);

        $today = now()->format('Ymd');
        $countToday = StockReport::whereDate('created_at', today())->count() + 1;
        $fileBase = 'STOK-' . $today . '-' . str_pad($countToday, 2, '0', STR_PAD_LEFT);

        $stockData = $stockReportService->getStockData();
        $totals = $stockReportService->calculateTotals($stockData);
        $exportData = $stockReportService->formatForExport($stockData, $totals);

        // wrap excel generation to avoid breaking DB write if something wrong with writer
        try {
            $path = ExcelExportHelper::generateExcel($exportData, $fileBase);
        } catch (\Throwable $e) {
            // log and continue — we still want DB row to be created
            report($e);
            $path = null;
        }

        $report->name = $fileBase;
        $report->total_items = $totals['totalItems'] ?? 0;
        $report->total_cost_value = $totals['totalCostValue'] ?? 0;
        $report->total_selling_value = $totals['totalSellingValue'] ?? 0;
        $report->path_file = $path;
    }

    public function updated(StockReport $report): void
    {
        $stockReportService = app(\App\Services\StockReportService::class);

        if ($report->getOriginal('path_file')) {
            $oldPath = storage_path('app/public/' . $report->getOriginal('path_file'));
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $fileBase = $report->name;

        $stockData = $stockReportService->getStockData();
        $totals = $stockReportService->calculateTotals($stockData);
        $exportData = $stockReportService->formatForExport($stockData, $totals);

        try {
            $path = ExcelExportHelper::generateExcel($exportData, $fileBase);
        } catch (\Throwable $e) {
            report($e);
            $path = $report->path_file;
        }

        $report->total_items = $totals['totalItems'] ?? 0;
        $report->total_cost_value = $totals['totalCostValue'] ?? 0;
        $report->total_selling_value = $totals['totalSellingValue'] ?? 0;
        $report->path_file = $path;
    }

    public function deleted(StockReport $report): void
    {
        if ($report->path_file) {
            $path = storage_path('app/public/' . $report->path_file);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
