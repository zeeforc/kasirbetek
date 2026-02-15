<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Helpers\TransactionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class PosRental extends Component
{
    use WithPagination;

    public $type = 'chair';

    // public $selected_rental_items = [];
    // public $selected_products = [];
    public array $selected_rental_items = []; // [id => bool]
    public array $rental_qty = [];            // [id => int]

    public array $selected_products = [];     // [id => bool]
    public array $product_qty = [];           // [id => int]


    public $start_at;
    public $end_at;

    public $renter_name;
    public $renter_contact;
    public $deposit = 0;
    public $notes;

    // Search + pagination
    public string $search = '';
    public int|string $perPage = 10;

    public $rentals;
    public $payment_methods;
    public $payment_method_id;

    public $is_cash = true;
    public $cash_received = '';
    public $change = 0;

    public $showCheckoutModal = false;
    public $showConfirmationModal = false;
    public $orderToPrint = null;

    public function mount()
    {
        $this->start_at = now()->format('Y-m-d\TH:i');
        $this->end_at = now()->addDay()->format('Y-m-d\TH:i');

        $this->loadRentals();
        $this->payment_methods = PaymentMethod::all();
    }

    public function updatingSearch(): void
    {
        $this->resetPage('productsPage');
        $this->resetPage('rentalPage');
    }

    public function updatingPerPage(): void
    {
        $this->resetPage('productsPage');
        $this->resetPage('rentalPage');
    }

    public function render()
    {
        $productsQuery = Product::query()
            ->where('is_active', 1)
            ->when($this->search !== '', function ($query) {
                $s = $this->search;
                return $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', '%' . $s . '%')
                        ->orWhere('sku', 'like', '%' . $s . '%');
                });
            });

        $rentalItemsQuery = RentalItem::query()
            ->where('is_active', 1)
            ->when($this->search !== '', function ($query) {
                $s = $this->search;
                return $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', '%' . $s . '%')
                        ->orWhere('type', 'like', '%' . $s . '%');
                });
            });

        return view('livewire.pos-rental', [
            'products' => $productsQuery->paginate($this->perPage, ['*'], 'productsPage'),
            'rental_items' => $rentalItemsQuery->paginate($this->perPage, ['*'], 'rentalPage'),
            'rentals' => $this->rentals,
        ]);
    }

    public function loadRentals()
    {
        $this->rentals = Rental::orderBy('created_at', 'desc')->limit(50)->get();
    }

    public function updatedPaymentMethodId($value)
    {
        $pm = PaymentMethod::find($value);
        $this->is_cash = $pm->is_cash ?? true;

        if (! $this->is_cash) {
            $this->cash_received = $this->getTotal();
            $this->change = 0;
        }
    }

    public function updatedCashReceived($value)
    {
        if ($this->is_cash) {
            $this->cash_received = $value;
            $this->calculateChange();

            if ($this->getCashReceivedNumeric() >= $this->getTotal()) {
                $this->resetErrorBag('cash_received');
            }
        }
    }

    public function calculateChange()
    {
        $clean = str_replace('.', '', $this->cash_received);
        $cash = floatval($clean);
        $total = floatval($this->getTotal());

        $this->change = $cash >= $total ? $cash - $total : 0;
    }

    public function getCashReceivedNumeric()
    {
        return floatval(str_replace('.', '', $this->cash_received));
    }

    public function toggleSelectRentalItem($id)
    {
        if (isset($this->selected_rental_items[$id])) {
            unset($this->selected_rental_items[$id]);
        } else {
            $this->selected_rental_items[$id] = 1;
        }
    }

    public function toggleSelectProduct($id)
    {
        if (isset($this->selected_products[$id])) {
            unset($this->selected_products[$id]);
        } else {
            $this->selected_products[$id] = 1;
        }
    }

    public function setRentalQty($id, $qty): void
    {
        $qty = max(1, (int) $qty);
        $this->rental_qty[$id] = $qty;

        // kalau user isi qty, anggap item otomatis terpilih
        $this->selected_rental_items[$id] = true;
    }

    public function setProductQty($id, $qty): void
    {
        $qty = max(1, (int) $qty);
        $this->product_qty[$id] = $qty;

        $this->selected_products[$id] = true;
    }


    // public function setProductQty($id, $qty)
    // {
    //     $this->selected_products[$id] = max(0, intval($qty));
    //     if ($this->selected_products[$id] === 0) {
    //         unset($this->selected_products[$id]);
    //     }
    // }

    // public function setRentalQty($id, $qty)
    // {
    //     $this->selected_rental_items[$id] = max(0, intval($qty));
    //     if ($this->selected_rental_items[$id] === 0) {
    //         unset($this->selected_rental_items[$id]);
    //     }
    // }

    public function updatedStartAt($value): void
    {
        $dt = $this->parseDateTime($value);
        $this->start_at = $dt ? $dt->format('Y-m-d\TH:i') : null;
    }

    public function updatedEndAt($value): void
    {
        $dt = $this->parseDateTime($value);
        $this->end_at = $dt ? $dt->format('Y-m-d\TH:i') : null;
    }

    private function parseDateTime(?string $value): ?\Carbon\Carbon
    {
        if (! $value) {
            return null;
        }

        $value = trim($value);

        foreach (['Y-m-d\TH:i', 'Y-m-d\TH:i:s'] as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $value);
            } catch (\Throwable $e) {
            }
        }

        foreach (['d/m/Y H:i', 'd/m/Y H:i:s', 'd-m-Y H:i', 'd-m-Y H:i:s'] as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $value);
            } catch (\Throwable $e) {
            }
        }

        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getRentalDays(): int
    {
        $start = $this->parseDateTime($this->start_at);
        $end = $this->parseDateTime($this->end_at);

        if (! $start || ! $end) {
            return 1;
        }

        $start = $start->startOfDay();
        $end = $end->startOfDay();

        $days = $start->diffInDays($end);

        return max(1, (int) $days);
    }

    public function getTotal()
    {
        $total = 0;
        $days = $this->getRentalDays();

        foreach ($this->selected_rental_items as $id => $checked) {
            if (!$checked) continue;

            $qty = (int) ($this->rental_qty[$id] ?? 1);
            $item = RentalItem::find($id);

            if ($item) {
                $total += (float) $item->price * $qty * (int) $days;
            }
        }

        foreach ($this->selected_products as $id => $checked) {
            if (!$checked) continue;

            $qty = (int) ($this->product_qty[$id] ?? 1);
            $p = Product::find($id);

            if ($p) {
                $total += (float) $p->price * $qty;
            }
        }

        return $total;
    }


    // public function getTotal()
    // {
    //     $total = 0;
    //     $days = $this->getRentalDays();

    //     // rental items: ambil dari DB per id supaya tidak tergantung pagination
    //     foreach ($this->selected_rental_items as $id => $qty) {
    //         $item = RentalItem::find($id);
    //         if ($item) {
    //             $total += (float) $item->price * (int) $qty * (int) $days;
    //         }
    //     }

    //     // products tambahan: ambil dari DB per id
    //     foreach ($this->selected_products as $id => $qty) {
    //         $p = Product::find($id);
    //         if ($p) {
    //             $total += (float) $p->price * (int) $qty;
    //         }
    //     }

    //     return $total;
    // }

    public function checkout()
    {
        try {
            // pastikan ada minimal 1 item yang benar-benar dipilih
            $hasRental = collect($this->selected_rental_items)->contains(true);
            $hasProduct = collect($this->selected_products)->contains(true);

            if (! $hasRental && ! $hasProduct) {
                Notification::make()->title('Pilih minimal 1 item')->danger()->send();
                return;
            }

            if (! $this->payment_method_id) {
                Notification::make()->title('Pilih metode pembayaran')->danger()->send();
                return;
            }

            $total = $this->getTotal();
            $cashNumeric = $this->is_cash ? $this->getCashReceivedNumeric() : $total;

            if ($this->is_cash) {
                $this->calculateChange();

                if ($cashNumeric < $total) {
                    $this->addError('cash_received', 'Nominal bayar kurang dari total');
                    return;
                }

                $this->resetErrorBag('cash_received');
            }

            DB::transaction(function () use ($total, $cashNumeric) {
                $order = Transaction::create([
                    'payment_method_id' => $this->payment_method_id,
                    'transaction_number' => TransactionHelper::generateUniqueTrxId(),
                    'name' => $this->renter_name ?? 'Umum',
                    'total' => $total,
                    'cash_received' => $cashNumeric,
                    'change' => $this->change,
                ]);

                $days = $this->getRentalDays();

                // RENTAL
                foreach ($this->selected_rental_items as $id => $checked) {
                    if (! $checked) {
                        continue;
                    }

                    $qty = (int) ($this->rental_qty[$id] ?? 1);
                    if ($qty < 1) {
                        $qty = 1;
                    }

                    $item = RentalItem::find($id);
                    if (! $item) {
                        continue;
                    }

                    Rental::create([
                        'transaction_id' => $order->id,
                        'renter_name' => $this->renter_name ?? 'Umum',
                        'renter_contact' => $this->renter_contact,
                        'type' => $item->type,
                        'resource_id' => $item->id,
                        'start_at' => $this->start_at,
                        'end_at' => $this->end_at,
                        'quantity' => $qty,
                        'duration' => (int) $days,
                        'price' => (int) $item->price,
                        'deposit' => (int) ($this->deposit ?? 0),
                        'status' => 'reserved',
                        'notes' => $this->notes,
                    ]);

                    TransactionItem::create([
                        'transaction_id' => $order->id,
                        'product_id' => null,
                        'quantity' => $qty * (int) $days,
                        'price' => (int) $item->price,
                        'cost_price' => 0,
                        'total_profit' => 0,
                    ]);

                    if ($item->stock !== null) {
                        $item->decrement('stock', $qty);
                    }
                }

                // PRODUK TAMBAHAN
                foreach ($this->selected_products as $id => $checked) {
                    if (! $checked) {
                        continue;
                    }

                    $qty = (int) ($this->product_qty[$id] ?? 1);
                    if ($qty < 1) {
                        $qty = 1;
                    }

                    $p = Product::find($id);
                    if (! $p) {
                        continue;
                    }

                    TransactionItem::create([
                        'transaction_id' => $order->id,
                        'product_id' => $p->id,
                        'quantity' => $qty,
                        'price' => (int) $p->price,
                        'cost_price' => (int) ($p->cost_price ?? 0),
                        'total_profit' => (int) (($p->price - ($p->cost_price ?? 0)) * $qty),
                    ]);
                }

                $this->orderToPrint = $order->id;
            });

            Notification::make()->title('Order berhasil disimpan')->success()->send();
            $this->showConfirmationModal = true;
            $this->showCheckoutModal = false;

            // reset sekali saja, setelah transaksi sukses
            $this->resetForm();
            $this->loadRentals();
        } catch (\Throwable $e) {
            logger()->error('POS RENTAL CHECKOUT ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            Notification::make()
                ->title('POS Rental Error')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw $e;
        }
    }


    public function refreshRentals()
    {
        $this->loadRentals();
    }

    public function printBluetooth()
    {
        $order = Transaction::with([
            'paymentMethod',
            'transactionItems.product',
            'rentals.rentalItem',
        ])->findOrFail($this->orderToPrint);

        $cashierName = Auth::user()?->name ?? 'Kasir';

        $items = collect();

        foreach ($order->rentals as $r) {
            $name = $r->rentalItem?->name ?? 'Rental Item';
            $duration = (int) ($r->duration ?? 1);

            $items->push([
                'name' => $name . ' (' . $duration . ' hari)',
                'quantity' => (int) ($r->quantity ?? 1),
                'price' => (int) ($r->price ?? 0),
            ]);
        }

        foreach ($order->transactionItems as $ti) {
            if (! $ti->product_id) {
                continue;
            }

            $items->push([
                'name' => $ti->product?->name ?? 'Produk',
                'quantity' => (int) $ti->quantity,
                'price' => (int) $ti->price,
            ]);
        }

        $days = $this->getRentalDays();

        $this->dispatch(
            'doPrintReceiptRental',
            store: Setting::first(),
            order: $order,
            items: $items,
            date: $order->created_at->format('d-m-Y H:i:s'),
            cashier: $cashierName,
            days: $days
        );

        $this->showConfirmationModal = false;
        $this->orderToPrint = null;
    }

    public function resetForm(): void
    {
        $this->selected_rental_items = [];
        $this->rental_qty = [];

        $this->selected_products = [];
        $this->product_qty = [];

        $this->renter_name = null;
        $this->renter_contact = null;
        $this->notes = null;
        $this->deposit = 0;

        $this->payment_method_id = null;
        $this->cash_received = '';
        $this->change = 0;
        $this->is_cash = true;

        // reset tanggal ke default (opsional)
        $this->start_at = now()->format('Y-m-d\TH:i');
        $this->end_at = now()->addDay()->format('Y-m-d\TH:i');

        // reset search + pagination biar list balik ke awal
        $this->search = '';
        $this->perPage = 10;

        $this->resetPage('productsPage');
        $this->resetPage('rentalPage');

        $this->resetErrorBag();
        $this->resetValidation();
        $this->dispatch('$refresh');
    }

    public function updatedSelectedRentalItems($value, $key): void
    {
        // $key = id rental
        if ($value) {
            // ketika dicentang dan qty belum di inut,  default 1
            if (!isset($this->rental_qty[$key]) || (int) $this->rental_qty[$key] < 1) {
                $this->rental_qty[$key] = 1;
            }
        } else {
            // ketika di unchecklist, buang qty-nya biar bersih
            unset($this->rental_qty[$key]);
        }
    }

    public function updatedSelectedProducts($value, $key): void
    {
        if ($value) {
            if (!isset($this->product_qty[$key]) || (int) $this->product_qty[$key] < 1) {
                $this->product_qty[$key] = 1;
            }
        } else {
            unset($this->product_qty[$key]);
        }
    }
}
