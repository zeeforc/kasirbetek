<?php
namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Collection;
class StockReportService
{
    public function getStockData(?string $reportDate = null): Collection
    {
        $products = Product::query()
            ->where('is_active', true)
            ->select('id', 'name', 'stock', 'cost_price', 'price')
            ->orderBy('name', 'asc')
            ->get();
        return $products->map(function (Product $product) {
            $stock = $product->stock ?? 0;
            $costPrice = $product->cost_price ?? 0;
            $sellingPrice = $product->price ?? 0;
            return [
                'name' => $product->name,
                'stock' => $stock,
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'total_cost_value' => $stock * $costPrice,
                'total_selling_value' => $stock * $sellingPrice,
            ];
        });
    }
    public function calculateTotals(Collection $stockData): array
    {
        $totalItems = $stockData->count();
        $totalCostValue = $stockData->sum('total_cost_value');
        $totalSellingValue = $stockData->sum('total_selling_value');
        return compact('totalItems', 'totalCostValue', 'totalSellingValue');
    }
    public function formatForExport(Collection $stockData, array $totals): array
    {
        $formatted = $stockData->map(function (array $item) {
            return [
                'Nama Produk' => $item['name'],
                'Sisa Stok' => $item['stock'],
                'Harga Modal' => 'Rp ' . number_format($item['cost_price'], 0, ',', '.'),
                'Harga Jual' => 'Rp ' . number_format($item['selling_price'], 0, ',', '.'),
                'Total Harga Modal' => 'Rp ' . number_format($item['total_cost_value'], 0, ',', '.'),
                'Total Harga Jual' => 'Rp ' . number_format($item['total_selling_value'], 0, ',', '.'),
            ];
        })->toArray();
        $formatted[] = ['Nama Produk' => ''];
        $formatted[] = [
            'Nama Produk' => 'TOTAL',
            'Sisa Stok' => $stockData->sum('stock'),
            'Harga Modal' => '',
            'Harga Jual' => '',
            'Total Harga Modal' => 'Rp ' . number_format($totals['totalCostValue'], 0, ',', '.'),
            'Total Harga Jual' => 'Rp ' . number_format($totals['totalSellingValue'], 0, ',', '.'),
        ];
        return $formatted;
    }
}