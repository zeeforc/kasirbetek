<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\TransactionItem;

class TransactionItemObserver
{
    /**
     * Handle the TransactionItem "created" event.
     */
    public function created(TransactionItem $transactionItem): void
    {
        // Rental item atau item tanpa produk
        if (empty($transactionItem->product_id)) {
            return;
        }

        $product = Product::find($transactionItem->product_id);

        // Produk tidak ditemukan (atau sudah terhapus)
        if (! $product) {
            return;
        }

        $product->decrement('stock', $transactionItem->quantity);
    }

    /**
     * Handle the TransactionItem "updated" event.
     */
    public function updated(TransactionItem $transactionItem): void
    {
        if (empty($transactionItem->product_id)) {
            return;
        }

        $product = Product::find($transactionItem->product_id);

        if (! $product) {
            return;
        }

        $originalQuantity = (int) $transactionItem->getOriginal('quantity');
        $newQuantity = (int) $transactionItem->quantity;

        if ($originalQuantity !== $newQuantity) {
            // balikin stok lama lalu kurangi stok baru
            $product->increment('stock', $originalQuantity);
            $product->decrement('stock', $newQuantity);
        }
    }

    /**
     * Handle the TransactionItem "deleted" event.
     */
    public function deleted(TransactionItem $transactionItem): void
    {
        if (empty($transactionItem->product_id)) {
            return;
        }

        $product = Product::find($transactionItem->product_id);

        if (! $product) {
            return;
        }

        $product->increment('stock', $transactionItem->quantity);
    }
}
