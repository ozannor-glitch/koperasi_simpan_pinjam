<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberSaving;
use App\Models\SavingTransaction;
use App\Models\SavingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function requestPayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'saving_type_id' => 'required|exists:saving_types,id',
                'amount' => 'required|numeric|min:10000',
                'payment_method' => 'required|string|in:bank_transfer,qris,gopay',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $amount = $request->amount;
            $savingType = SavingType::find($request->saving_type_id);

            if ($amount < $savingType->minimum_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimal setor untuk {$savingType->name} adalah Rp " . number_format($savingType->minimum_amount, 0, ',', '.'),
                ], 422);
            }

            // Generate order ID unik
            $orderId = 'Saving-' . time() . '-' . Str::random(6) . '-' . $user->id;

            // Simpan transaksi pending
            $transaction = SavingTransaction::create([
                'user_id' => $user->id,
                'saving_type_id' => $request->saving_type_id,
                'transaction_type' => 'setor',
                'amount' => $amount,
                'status' => 'pending',
                'order_id' => $orderId,
                'payment_method' => $request->payment_method,
            ]);

            // Gunakan APP_URL dari .env (URL ngrok)
            $appUrl = env('APP_URL', 'https://grower-immersion-diploma.ngrok-free.dev');

            // Buat URL redirect ke WebView Midtrans
            $redirectUrl = $appUrl . '/midtrans_payment.php?' . http_build_query([
                'order_id' => $orderId,
                'amount' => $amount,
                'name' => $user->name,
                'email' => $user->email,
                'saving_type_id' => $request->saving_type_id,
                'transaction_id' => $transaction->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Silakan lanjutkan pembayaran',
                'data' => [
                    'redirect_url' => $redirectUrl,
                    'order_id' => $orderId,
                    'amount' => $amount,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook untuk menerima notifikasi dari Midtrans
     * Endpoint: POST /api/payment/webhook
     */
    public function webhook(Request $request)
    {
        try {
            // Log semua request webhook untuk debugging
            Log::info('Midtrans Webhook Received:', $request->all());

            $payload = $request->all();

            // Ambil data dari payload
            $orderId = $payload['order_id'] ?? null;
            $transactionStatus = $payload['transaction_status'] ?? null;
            $fraudStatus = $payload['fraud_status'] ?? null;
            $paymentType = $payload['payment_type'] ?? null;
            $statusCode = $payload['status_code'] ?? null;

            if (!$orderId) {
                Log::warning('Webhook: Order ID not found');
                return response()->json(['message' => 'Order ID not found'], 400);
            }

            // Cari transaksi berdasarkan order_id
            $transaction = SavingTransaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::warning('Webhook: Transaction not found for order_id: ' . $orderId);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            Log::info('Webhook: Processing transaction', [
                'order_id' => $orderId,
                'current_status' => $transaction->status,
                'new_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            // Proses berdasarkan status dari Midtrans
            if ($transactionStatus == 'capture') {
                // Untuk credit card, cek fraud status
                if ($fraudStatus == 'accept') {
                    $this->completePayment($transaction);
                }
            }
            elseif ($transactionStatus == 'settlement') {
                // Pembayaran sukses
                $this->completePayment($transaction);
            }
            elseif ($transactionStatus == 'pending') {
                // Pembayaran pending
                $transaction->status = 'pending';
                $transaction->save();
                Log::info('Webhook: Payment pending for order ' . $orderId);
            }
            elseif ($transactionStatus == 'deny') {
                // Pembayaran ditolak
                $transaction->status = 'failed';
                $transaction->save();
                Log::info('Webhook: Payment denied for order ' . $orderId);
            }
            elseif ($transactionStatus == 'cancel') {
                // Pembayaran dibatalkan
                $transaction->status = 'failed';
                $transaction->save();
                Log::info('Webhook: Payment cancelled for order ' . $orderId);
            }
            elseif ($transactionStatus == 'expire') {
                // Pembayaran expired
                $transaction->status = 'failed';
                $transaction->save();
                Log::info('Webhook: Payment expired for order ' . $orderId);
            }

            // Selalu return 200 ke Midtrans
            return response()->json(['message' => 'OK'], 200);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Complete payment and update balance
     */
    private function completePayment($transaction)
    {
        DB::beginTransaction();

        try {
            // Cek apakah sudah success sebelumnya (idempotent)
            if ($transaction->status == 'success') {
                Log::info('Payment already completed for order: ' . $transaction->order_id);
                DB::commit();
                return;
            }

            // Update status transaksi
            $transaction->status = 'success';
            $transaction->save();

            // Update saldo member saving
            $memberSaving = MemberSaving::firstOrCreate(
                [
                    'user_id' => $transaction->user_id,
                    'saving_type_id' => $transaction->saving_type_id,
                ],
                [
                    'balance' => 0,
                ]
            );

            $memberSaving->addBalance($transaction->amount);

            Log::info('Payment completed successfully', [
                'order_id' => $transaction->order_id,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'new_balance' => $memberSaving->balance
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Complete payment error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order_id');

        // Cek status transaksi dari database
        $transaction = SavingTransaction::where('order_id', $orderId)->first();

        $appUrl = env('APP_URL', 'https://grower-immersion-diploma.ngrok-free.dev');

        if ($transaction && $transaction->status == 'success') {
            return redirect($appUrl . '/payment_success.html');
        } elseif ($transaction && $transaction->status == 'pending') {
            return redirect($appUrl . '/payment_pending.html');
        } else {
            return redirect($appUrl . '/payment_failed.html');
        }
    }

    public function paymentFailed(Request $request)
    {
        $orderId = $request->query('order_id');
        $transaction = SavingTransaction::where('order_id', $orderId)->first();

        if ($transaction && $transaction->status != 'success') {
            $transaction->status = 'failed';
            $transaction->save();
        }

        $appUrl = env('APP_URL', 'https://grower-immersion-diploma.ngrok-free.dev');
        return redirect($appUrl . '/payment_failed.html');
    }
}
