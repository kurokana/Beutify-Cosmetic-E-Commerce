<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Voucher;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CartService  $cartService,
    ) {}

    /**
     * Display the checkout page.
     * Requirements: 4.1, 4.2
     *
     * GET /checkout
     */
    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Load cart items with product and variant
        $cartItems = CartItem::where('user_id', $user->id)
            ->with(['product.images', 'variant'])
            ->get()
            ->map(function (CartItem $item) {
                $item->unit_price = $this->cartService->getUnitPrice($item);
                $item->subtotal   = $item->unit_price * $item->quantity;
                return $item;
            });

        // Redirect back to cart if empty
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong. Tambahkan produk terlebih dahulu.');
        }

        $subtotal = $cartItems->sum('subtotal');

        // Load user's saved addresses
        $addresses = Address::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'addresses'));
    }

    /**
     * Process the checkout and create the order.
     * Requirements: 4.7, 4.8
     *
     * POST /checkout
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'address_id'      => ['required', 'integer', 'exists:addresses,id'],
            'courier_name'    => ['required', 'string', 'max:100'],
            'courier_service' => ['required', 'string', 'max:100'],
            'shipping_cost'   => ['required', 'numeric', 'min:0'],
            'voucher_code'    => ['nullable', 'string', 'max:50'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        // Ensure the address belongs to the authenticated user
        $address = Address::where('id', $validated['address_id'])
            ->where('user_id', $user->id)
            ->first();

        if (! $address) {
            return back()
                ->withErrors(['address_id' => 'Alamat pengiriman tidak valid.'])
                ->withInput();
        }

        try {
            $order = $this->orderService->createOrder($user, $validated);

            // Redirect to payment page so the customer can complete payment (Requirement 5.2)
            return redirect()->route('payment.show', $order->id)
                ->with('success', "Pesanan #{$order->order_number} berhasil dibuat. Silakan selesaikan pembayaran.");
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.')
                ->withInput();
        }
    }
}
