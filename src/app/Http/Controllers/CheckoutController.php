<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use App\Models\Obat;
use Midtrans\Config;
use App\Models\Pesanan;
use App\Mail\InvoiceMail;
use App\Models\Pengiriman;
use App\Models\PesananItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // Inisialisasi konfigurasi Midtrans
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Proses checkout dan generate Snap token
     */
    public function store(Request $request)
    {
        // Validasi input
        try {
            $data = $request->validate([
                'cart'           => 'required|array|min:1',
                'cart.*.obat_id' => 'required|exists:obats,id',
                'cart.*.qty'     => 'required|integer|min:1',
                'ongkir'         => 'required|integer',
                'alamat'         => 'required|string|max:255',
                'jarak'          => 'required|numeric',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Ambil atau buat profil user
            $user    = Auth::user()->load('profile');
            $profile = $user->profile ?: $user->profile()->create([
                'nama_lengkap'  => $user->name,
                'nomor_telepon' => '-',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => now()->subYears(20),
            ]);

            // Buat header pesanan
            $pesanan = Pesanan::create([
                'profile_id' => $profile->id,
                'tanggal'    => now(),
                'status'     => 'menunggu',
                'total'      => 0,
            ]);
            $pesanan->nomor_pesanan = 'PSN-' . str_pad($pesanan->id, 6, '0', STR_PAD_LEFT);
            $pesanan->save();

            // Persiapkan detail item
            $totalProduk   = 0;
            $midtransItems = [];

            foreach ($data['cart'] as $item) {
                $obat     = Obat::findOrFail($item['obat_id']);
                $subtotal = $obat->harga * $item['qty'];
                $totalProduk += $subtotal;

                PesananItem::create([
                    'pesanan_id' => $pesanan->id,
                    'obat_id'    => $obat->id,
                    'qty'        => $item['qty'],
                    'total'      => $subtotal,
                ]);

                $midtransItems[] = [
                    'id'       => $obat->id,
                    'price'    => $obat->harga,
                    'quantity' => $item['qty'],
                    'name'     => $obat->nama_obat,
                ];
            }

            // Hitung total + ongkir
            $totalAkhir = $totalProduk + $data['ongkir'];
            $pesanan->update(['total' => $totalAkhir]);

            Pengiriman::create([
                'pesanan_id' => $pesanan->id,
                'alamat'     => $data['alamat'],
                'jarak'      => $data['jarak'],
                'total'      => $data['ongkir'],
                'status'     => 'menunggu',
                'pengirim_id'=> null,
            ]);

            // Tambahkan ongkir ke Midtrans item list
            $midtransItems[] = [
                'id'       => 'ONGKIR',
                'price'    => $data['ongkir'],
                'quantity' => 1,
                'name'     => 'Ongkos Kirim',
            ];

            // Buat order_id unik untuk token
            $orderIdFull = $pesanan->nomor_pesanan . '-' . now()->timestamp;

            // Generate Snap token
            try {
                $snapToken = Snap::getSnapToken([
                    'transaction_details' => [
                        'order_id'     => $orderIdFull,
                        'gross_amount' => $totalAkhir,
                    ],
                    'item_details'     => $midtransItems,
                    'customer_details' => [
                        'first_name' => $profile->nama_lengkap,
                        'email'      => $user->email,
                        'phone'      => $profile->nomor_telepon,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Midtrans Snap token error', [
                    'error'    => $e->getMessage(),
                    'order_id' => $orderIdFull,
                ]);
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to generate Snap token',
                    'error'   => $e->getMessage(),
                ], 502);
            }

            DB::commit();

            return response()->json([
                'message'       => 'Pesanan berhasil dibuat.',
                'snap_token'    => $snapToken,
                'order_id'      => $pesanan->nomor_pesanan,
                'order_id_full' => $orderIdFull,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Gagal membuat pesanan',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Snap token untuk pesanan yang sudah pernah dibuat
     */
    public function tokenFromExisting(Pesanan $pesanan)
    {
        // kumpulkan item
        $items = $pesanan->items->map(function($i) {
            return [
                'id'       => $i->obat_id,
                'price'    => $i->total / $i->qty,
                'quantity' => $i->qty,
                'name'     => $i->obat->nama_obat,
            ];
        })->toArray();

        // tambah ongkir sebagai 1 item
        $items[] = [
            'id'       => 'ONGKIR',
            'price'    => $pesanan->pengiriman->total,
            'quantity' => 1,
            'name'     => 'Ongkos Kirim',
        ];

        $orderIdFull = $pesanan->nomor_pesanan . '-' . now()->timestamp;

        // generate snap token
        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id'     => $orderIdFull,
                'gross_amount' => $pesanan->total,
            ],
            'item_details'     => $items,
            'customer_details' => [
                'first_name' => $pesanan->profile->nama_lengkap,
                'email'      => $pesanan->profile->user->email,
                'phone'      => $pesanan->profile->nomor_telepon,
            ],
        ]);

        return response()->json([
            'snap_token'    => $snapToken,
            'order_id_full' => $orderIdFull,
        ]);
    }


    /**
     * Update status menjadi diproses atau gagal
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'order_id_full'      => 'required|string',
            'transaction_status' => 'required|string',
        ]);

        // Parse base order ID (PSN-xxxxxx)
        $parts = explode('-', $validated['order_id_full']);
        $baseOrderId = count($parts) >= 3
            ? $parts[0] . '-' . $parts[1]
            : $validated['order_id_full'];

        $pesanan = Pesanan::where('nomor_pesanan', $baseOrderId)->first();
        if (! $pesanan) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Map status Midtrans ke enum status
        $status = $validated['transaction_status'];
        if (in_array($status, ['settlement','capture'])) {
            $pesanan->status = 'diproses';
        } elseif (in_array($status, ['deny','cancel','expire'])) {
            $pesanan->status = 'dibatalkan';
        }
        // jika pending, biarkan 'menunggu'
        $pesanan->save();

        // Kirim email, tapi bungkus di try-catch agar tidak memblokir response
        try {
            Mail::to($pesanan->profile->user->email)
                ->send(new InvoiceMail($pesanan));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email Invoice', [
                'order' => $pesanan->nomor_pesanan,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'Status updated',
            'status'  => $pesanan->status,
        ], 200);
    }
}
