<?php

namespace App\Observers;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Log;

class InventoryItemObserver
{
    private function resolveStockTarget(InventoryItem $item)
    {
        // Jika rental_item_id terisi, stok rental item yang berubah
        if (!empty($item->rental_item_id)) {
            return $item->rentalItem;
        }

        // Prioritas: jika product_id terisi, stok produk yang berubah
        if (!empty($item->product_id)) {
            return $item->product;
        }

        return null;
    }

    public function created(InventoryItem $item): void
    {
        $inventory = $item->inventory;
        if (! $inventory) {
            return;
        }

        $target = $this->resolveStockTarget($item);
        if (! $target) {
            return;
        }

        $qty = (int) $item->quantity;

        if ($inventory->type === 'in') {
            $target->increment('stock', $qty);
            return;
        }

        if ($inventory->type === 'out') {
            $target->decrement('stock', $qty);
            return;
        }

        if ($inventory->type === 'adjustment') {
            $target->update(['stock' => $qty]);
            return;
        }
    }

    public function updated(InventoryItem $item): void
    {
        $inventory = $item->inventory;
        if (! $inventory) {
            return;
        }

        $target = $this->resolveStockTarget($item);
        if (! $target) {
            return;
        }

        $originalQty = (int) $item->getOriginal('quantity');
        $newQty = (int) $item->quantity;

        // kalau qty tidak berubah, tidak perlu apa apa
        if ($originalQty === $newQty) {
            return;
        }

        if ($inventory->type === 'in') {
            $target->increment('stock', $newQty - $originalQty);
            return;
        }

        if ($inventory->type === 'out') {
            $target->decrement('stock', $newQty - $originalQty);
            return;
        }

        if ($inventory->type === 'adjustment') {
            $target->update(['stock' => $newQty]);
            return;
        }
    }

    public function deleted(InventoryItem $item): void
    {
        $inventory = $item->inventory;
        if (! $inventory) {
            return;
        }

        $target = $this->resolveStockTarget($item);
        if (! $target) {
            return;
        }

        $qty = (int) $item->quantity;

        if ($inventory->type === 'in') {
            $target->decrement('stock', $qty);
            return;
        }

        if ($inventory->type === 'out') {
            $target->increment('stock', $qty);
            return;
        }

        if ($inventory->type === 'adjustment') {
            Log::warning('Inventory adjustment item dihapus, stok tidak dikembalikan otomatis.');
            return;
        }
    }
}
