<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Models\Transaction;
use Filament\Actions;
use App\Models\Setting;
use App\Models\TransactionItem;
use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function printStruk($order, $items)
    {
        $cashierName = null;
        $authUser = Auth::user();

        if ($authUser instanceof \App\Models\User) {
            try {
                if ($authUser->hasRole('kasir')) {
                    $cashierName = $authUser->name;
                }
            } catch (\Throwable $e) {
                // ignore
            }

            if (!$cashierName && !empty($authUser->name)) {
                $cashierName = $authUser->name;
            }
        } elseif (is_object($authUser) && isset($authUser->name)) {
            $cashierName = $authUser->name;
        }

        if (!$cashierName) {
            try {
                $kasirUser = \App\Models\User::role('kasir')->first();
                $cashierName = $kasirUser->name ?? 'Kasir';
            } catch (\Throwable $e) {
                $cashierName = 'Kasir';
            }
        }

        $this->dispatch(
            'doPrintReceipt',
            store: Setting::first(),
            order: $order,
            items: $items,
            date: $order->created_at->format('d-m-Y H:i:s'),
            cashier: $cashierName
        );
    }
}
