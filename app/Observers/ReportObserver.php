<?php

namespace App\Observers;

use App\Models\CashFlow;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportObserver
{
    /**
     * Handle the Report "creating" event.
     */
    public function creating(Report $report): void
    {
        $logo = optional(Setting::first())->logo;

        $today = now()->format('Ymd');
        $countToday = Report::whereDate('created_at', today())->count() + 1;

        // nama file PDF
        $fileBase = 'LAPORAN-' . $today . '-' . str_pad($countToday, 2, '0', STR_PAD_LEFT);
        $fileName = $fileBase . '.pdf';

        // path relatif di storage disk public
        $path = 'reports/' . $fileName;

        if ($report->report_type === 'inflow') {
            $data = CashFlow::query()
                ->where('type', 'income')
                ->when($report->start_date, fn ($q) => $q->whereDate('updated_at', '>=', $report->start_date))
                ->when($report->end_date, fn ($q) => $q->whereDate('updated_at', '<=', $report->end_date))
                ->get();

            $pdf = Pdf::loadView('pdf.reports.pemasukan', [
                'fileName' => $fileBase,
                'data' => $data,
                'logo' => $logo,
            ])->setPaper('a4', 'portrait');
        } elseif ($report->report_type === 'outflow') {
            $data = CashFlow::query()
                ->where('type', 'expense')
                ->when($report->start_date, fn ($q) => $q->whereDate('updated_at', '>=', $report->start_date))
                ->when($report->end_date, fn ($q) => $q->whereDate('updated_at', '<=', $report->end_date))
                ->get();

            $pdf = Pdf::loadView('pdf.reports.pengeluaran', [
                'fileName' => $fileBase,
                'data' => $data,
                'logo' => $logo,
            ])->setPaper('a4', 'portrait');
        } else {
            // PENJUALAN: hanya transaksi yang punya item produk (product_id not null)
            $data = Transaction::query()
                ->with([
                    'paymentMethod',
                    'transactionItems.productWithTrashed',
                    'rentals.rentalItem',
                ])
                ->where(function ($q) {
                    $q->whereHas('transactionItems', fn ($t) => $t->whereNotNull('product_id'))
                    ->orWhereHas('rentals');
                })
                ->when($report->start_date, fn ($q) => $q->whereDate('created_at', '>=', $report->start_date))
                ->when($report->end_date, fn ($q) => $q->whereDate('created_at', '<=', $report->end_date))
                ->get();


            $pdf = Pdf::loadView('pdf.reports.penjualan', [
                'fileName' => $fileBase,
                'data' => $data,
                'logo' => $logo,
            ])->setPaper('a4', 'portrait');
        }

        // pastikan folder ada
        $pathDirectory = storage_path('app/public/reports');
        if (! file_exists($pathDirectory)) {
            mkdir($pathDirectory, 0755, true);
        }

        // simpan pdf
        $fullPath = storage_path('app/public/' . $path);
        $pdf->save($fullPath);

        // set field report
        $report->name = $fileBase;
        $report->path_file = $path;
    }

    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
    {
        $logo = optional(Setting::first())->logo;

        // pastikan path_file konsisten
        $fileBase = $report->name;
        $fileName = $fileBase . '.pdf';
        $path = 'reports/' . $fileName;

        if ($report->report_type === 'inflow') {
            $data = CashFlow::query()
                ->where('type', 'income')
                ->when($report->start_date, fn ($q) => $q->whereDate('updated_at', '>=', $report->start_date))
                ->when($report->end_date, fn ($q) => $q->whereDate('updated_at', '<=', $report->end_date))
                ->get();

            $pdf = Pdf::loadView('pdf.reports.pemasukan', [
                'fileName' => $fileBase,
                'data' => $data,
                'logo' => $logo,
            ])->setPaper('a4', 'portrait');
        } elseif ($report->report_type === 'outflow') {
            $data = CashFlow::query()
                ->where('type', 'expense')
                ->when($report->start_date, fn ($q) => $q->whereDate('updated_at', '>=', $report->start_date))
                ->when($report->end_date, fn ($q) => $q->whereDate('updated_at', '<=', $report->end_date))
                ->get();

            $pdf = Pdf::loadView('pdf.reports.pengeluaran', [
                'fileName' => $fileBase,
                'data' => $data,
                'logo' => $logo,
            ])->setPaper('a4', 'portrait');
        } else {
            $data = Transaction::query()
                ->with([
                    'paymentMethod',
                    'transactionItems.productWithTrashed',
                ])
                ->whereHas('transactionItems', function ($q) {
                    $q->whereNotNull('product_id');
                })
                ->when($report->start_date, fn ($q) => $q->whereDate('updated_at', '>=', $report->start_date))
                ->when($report->end_date, fn ($q) => $q->whereDate('updated_at', '<=', $report->end_date))
                ->get();

            $pdf = Pdf::loadView('pdf.reports.penjualan', [
                'fileName' => $fileBase,
                'data' => $data,
                'logo' => $logo,
            ])->setPaper('a4', 'portrait');
        }

        $pathDirectory = storage_path('app/public/reports');
        if (! file_exists($pathDirectory)) {
            mkdir($pathDirectory, 0755, true);
        }

        $fullPath = storage_path('app/public/' . $path);
        $pdf->save($fullPath);

        // update path_file biar download selalu valid
        $report->path_file = $path;
        $report->saveQuietly();
    }

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        if (filled($report->path_file)) {
            $pdfPath = 'public/' . $report->path_file; // contoh: public/reports/LAPORAN-xxx.pdf
            if (Storage::exists($pdfPath)) {
                Storage::delete($pdfPath);
            }
            return;
        }

        // fallback lama kalau path_file kosong
        $pdfPath = 'public/reports/' . $report->name . '.pdf';
        if (Storage::exists($pdfPath)) {
            Storage::delete($pdfPath);
        }
    }
}
