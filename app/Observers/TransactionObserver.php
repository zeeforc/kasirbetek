<?php

namespace App\Observers;

use App\Models\CashFlow;
use App\Models\Transaction;
use App\Helpers\TransactionHelper;

class TransactionObserver
{
    public function creating(Transaction $transaction): void
    {
        // Jangan overwrite kalau sudah ada (misal dibuat manual dari tempat lain)
        if (empty($transaction->transaction_number)) {
            $transaction->transaction_number = TransactionHelper::generateUniqueTrxId();
        }
    }

    public function created(Transaction $transaction): void
    {
        $isRental = $transaction->rentals()->exists();

        CashFlow::create([
            'date'   => now(),
            'type'   => 'income',
            'source' => $isRental ? 'rental' : 'sales',
            'amount' => $transaction->total,
            'notes'  => ($isRental ? 'Pemasukan dari rental #' : 'Pemasukan dari transaksi #') . $transaction->transaction_number,
        ]);
    }

    public function updated(Transaction $transaction): void
    {
        if (! $transaction->isDirty('total')) {
            return;
        }

        CashFlow::where('notes', 'like', "%#{$transaction->transaction_number}%")
            ->where('type', 'income')
            ->update([
                'amount' => $transaction->total,
            ]);
    }

    public function deleted(Transaction $transaction): void
    {
        $isRental = $transaction->rentals()->exists();

        CashFlow::create([
            'date'   => now(),
            'type'   => 'expense',
            'source' => 'refund',
            'amount' => $transaction->total,
            'notes'  => ($isRental ? 'Pembatalan rental #' : 'Pembatalan transaksi #') . $transaction->transaction_number,
        ]);

        // Kembalikan stok produk saja (abaikan item rental yg product_id null)
        foreach ($transaction->transactionItems as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = $item->product;
            if (! $product) {
                continue;
            }

            $product->stock += (int) $item->quantity;
            $product->save();
        }
    }

    public function restored(Transaction $transaction): void
    {
        $isRental = $transaction->rentals()->exists();

        CashFlow::create([
            'date'   => now(),
            'type'   => 'income',
            'source' => $isRental ? 'restored_rental' : 'restored_sales',
            'amount' => $transaction->total,
            'notes'  => ($isRental ? 'Restore rental #' : 'Restore transaksi #') . $transaction->transaction_number,
        ]);

        // Kurangi lagi stok produk saja (abaikan item rental yg product_id null)
        foreach ($transaction->transactionItems as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = $item->product;
            if (! $product) {
                continue;
            }

            $product->stock -= (int) $item->quantity;
            $product->save();
        }
    }

    public function forceDeleting(Transaction $transaction): void
    {
        // Kalau belum soft deleted, balikin stok produk (abaikan item rental)
        if (! $transaction->trashed()) {
            foreach ($transaction->transactionItems as $item) {
                if (! $item->product_id) {
                    continue;
                }

                $product = $item->product;
                if (! $product) {
                    continue;
                }

                $product->stock += (int) $item->quantity;
                $product->save();
            }
        }
    }

    public function forceDeleted(Transaction $transaction): void
    {
        // Hapus semua cashflow terkait transaksi ini (sales, rental, refund, restored)
        CashFlow::where('notes', 'like', "%#{$transaction->transaction_number}%")->delete();
    }
}
