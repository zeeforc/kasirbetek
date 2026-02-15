<div class="min-h-screen p-2 lg:p-6" style="font-family: 'Poppins'">
    <div class="max-w-[1600px] mx-auto">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Left --}}
            <div class="xl:col-span-2">
                {{-- Header controls --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ url()->previous() }}"
                                class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-white dark:hover:bg-gray-600 text-gray-700 dark:text-white flex items-center justify-center transition">
                                <span class="text-2xl leading-none">‹</span>
                            </a>
                            <div>
                                <div class="text-lg font-bold text-gray-800 dark:text-white">Halaman Rental</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Buat sewa dan kelola transaksi
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-filament::button id="connect-button"
                                class="px-4 py-3 bg-blue-500 hover:bg-blue-400 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                </svg>
                            </x-filament::button>
                        </div>
                    </div>

                    {{-- Rental basic fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Start At</label>
                            <input type="datetime-local" wire:model.live="start_at"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">End At</label>
                            <input type="datetime-local" wire:model.live="end_at"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Nama
                                Penyewa</label>
                            <input type="text" wire:model="renter_name" placeholder="Nama penyewa"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Kontak</label>
                            <input type="text" wire:model="renter_contact" placeholder="Nomor HP"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                        </div>

                        <div class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Deposit</label>
                            <input type="number" step="0.01" wire:model="deposit" placeholder="0"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Catatan <span
                                    style="font-size: 12px">(opsional)</span> </label>
                            <textarea wire:model="notes" rows="3" placeholder="Tambahkan catatan jika perlu"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Rental items --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Pilih Rental Items</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Durasi: {{ $this->getRentalDays() }} hari
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($rental_items as $ri)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-3 hover:shadow-md transition">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" wire:model.live="selected_rental_items.{{ $ri->id }}"
                                    class="w-5 h-5">

                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white">
                                        {{ $ri->name }}
                                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $ri->type }})</span>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Rp {{ number_format($ri->price,0,',','.') }} / hari • Tersedia {{ $ri->stock }}
                                    </div>
                                </div>

                                <input type="number" min="1" value="{{ $rental_qty[$ri->id] ?? 1 }}"
                                    wire:input.debounce.300ms="setRentalQty({{ $ri->id }}, $event.target.value)"
                                    class="w-20 px-3 py-2 rounded-lg bg-white dark:bg-gray-600 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-500"
                                    @disabled(empty($selected_rental_items[$ri->id]))
                                >
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Addon products --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Pilih Produk Tambahan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 mb-4">
                        <div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Cari nama, tipe rental, atau SKU produk"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                        </div>

                        <div class="flex items-center gap-3 justify-end">
                            <select wire:model.live="perPage"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($products as $p)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-3 hover:shadow-md transition">
                            <div class="flex items-center gap-3">
                                {{-- <input type="checkbox" wire:click="toggleSelectProduct({{ $p->id }})"
                                    @if(isset($selected_products[$p->id])) checked @endif
                                class="w-5 h-5"> --}}
                                <input type="checkbox" wire:model.live="selected_products.{{ $p->id }}" class="w-5 h-5">

                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white">
                                        {{ $p->name }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Rp {{ number_format($p->price,0,',','.') }} • Stok {{ $p->stock }}
                                    </div>
                                </div>

                                <input type="number" min="1" value="{{ $selected_products[$p->id] ?? 1 }}"
                                    wire:input.debounce.300ms="setProductQty({{ $p->id }}, $event.target.value)"
                                    class="w-20 px-3 py-2 rounded-lg bg-white dark:bg-gray-600 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-500">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <x-filament::pagination :paginator="$products" />
                    </div>
                </div>
            </div>

            {{-- Right cart --}}
            <div class="xl:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Ringkasan</h2>
                        <button wire:click="resetForm"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Reset
                        </button>
                    </div>

                    {{-- Summary list --}}
                    <div class="space-y-3">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Durasi Rental</div>
                            <div class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $this->getRentalDays() }} hari
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                            <div class="text-2xl font-bold text-gray-800 dark:text-white">
                                Rp {{ number_format($this->getTotal(),0,',','.') }}
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Item Dipilih</div>
                            <div class="text-sm text-gray-800 dark:text-white">
                                Rental {{ count($selected_rental_items) }} item,
                                Produk {{ count($selected_products) }} item
                            </div>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showCheckoutModal', true)"
                        class="w-full mt-6 py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200
                        {{ (count($selected_rental_items)===0 && count($selected_products)===0) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ (count($selected_rental_items)===0 &&
                        count($selected_products)===0) ? 'disabled' : '' }}>
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Checkout
                        </div>
                    </button>

                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Rental Terbaru</div>
                        <div class="space-y-2 max-h-[360px] overflow-y-auto pr-2">
                            @forelse($rentals as $r)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-3">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-gray-800 dark:text-white line-clamp-1">
                                            {{ $r->renter_name ?? 'Umum' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            #{{ $r->id }} • {{ $r->created_at?->format('d M Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $r->status ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data rental
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Checkout Modal --}}
    @if ($showCheckoutModal)
    <div wire:ignore.self x-data="{
                init() {
                    if (@this.payment_method_id) {
                        @this.updatedPaymentMethodId(@this.payment_method_id);
                    }
                }
            }" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 p-4">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl transform scale-95 animate-modal-appear">
            <div class="p-6">
                <form wire:submit.prevent="checkout">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 mb-6 text-white">
                                <div class="text-sm opacity-90">Total</div>
                                <div class="text-3xl font-bold">
                                    Rp {{ number_format($this->getTotal(), 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                    Nama Penyewa
                                </label>
                                <input type="text" wire:model="renter_name"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Nama penyewa">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                    Metode Pembayaran
                                </label>
                                <select wire:model.live="payment_method_id"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    required>
                                    <option value="">Pilih metode pembayaran</option>
                                    @foreach ($payment_methods as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 mb-6 text-white">
                                <div class="text-sm opacity-90">Kembalian</div>
                                <div class="text-3xl font-bold">
                                    Rp {{ number_format($change, 0, ',', '.') }}
                                </div>
                            </div>

                            @if ($is_cash)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                    Nominal Bayar
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-9 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">
                                        Rp
                                    </span>
                                    <input type="text" wire:model.live="cash_received"
                                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 text-lg font-medium"
                                        placeholder="0" autocomplete="off">
                                    @error('cash_received')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @else
                            <div class="mb-4">
                                <div class="rounded-xl p-4">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <span class="text-sm text-amber-700 dark:text-amber-500">
                                            Pembayaran non-tunai akan diproses sesuai nominal total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <button type="button" wire:click="$set('showCheckoutModal', false)"
                            class="flex-1 py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-[0.98]">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            Bayar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Confirmation Modal --}}
    @if ($showConfirmationModal)
    <div wire:ignore.self class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md transform animate-modal-appear">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 text-center">
                    Pembayaran Berhasil
                </h3>

                <p class="text-gray-600 dark:text-gray-300 text-center mb-6">
                    Transaksi telah berhasil diproses
                </p>

                <div class="bg-gray-50 dark:bg-gray-600 rounded-xl p-4 mb-6">
                    <div class="space-y-2 text-sm">
                        @if ($orderToPrint)
                        @php
                        $order = \App\Models\Transaction::find($orderToPrint);
                        @endphp
                        @if ($order)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">No. Transaksi</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $order->transaction_number
                                }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total Bayar</span>
                            <span class="font-medium text-gray-800 dark:text-white">Rp
                                {{ number_format($order->cash_received, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->change > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Kembalian</span>
                            <span class="font-medium text-green-600">Rp
                                {{ number_format($order->change, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @endif
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button wire:click="printBluetooth"
                        class="flex items-center justify-center space-x-2 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                            </path>
                        </svg>
                        <span>Cetak Struk</span>
                    </button>

                    <button wire:click="$set('showConfirmationModal', false)"
                        class="py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-[0.98]">
                        Lewati
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</div>
