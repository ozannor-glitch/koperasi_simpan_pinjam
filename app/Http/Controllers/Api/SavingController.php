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

class SavingController extends Controller
{
    /**
     * Get list of saving types
     * Endpoint: GET /api/saving-types
     */
    public function getSavingTypes()
    {
        try {
            $savingTypes = SavingType::all();

            return response()->json([
                'success' => true,
                'message' => 'Data jenis tabungan berhasil diambil',
                'data' => $savingTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jenis tabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's savings (all types)
     * Endpoint: GET /api/my-savings
     */
    public function getMySavings()
    {
        try {
            $user = Auth::user();

            $memberSavings = MemberSaving::with('savingType')
                ->where('user_id', $user->id)
                ->get();

            // Jika user belum memiliki tabungan, buatkan default untuk semua jenis
            if ($memberSavings->isEmpty()) {
                $savingTypes = SavingType::all();
                foreach ($savingTypes as $type) {
                    MemberSaving::create([
                        'user_id' => $user->id,
                        'saving_type_id' => $type->id,
                        'balance' => 0,
                    ]);
                }
                $memberSavings = MemberSaving::with('savingType')
                    ->where('user_id', $user->id)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'message' => 'Data tabungan berhasil diambil',
                'data' => $memberSavings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's saving transaction history
     * Endpoint: GET /api/saving-history
     */
    /**
     * Get user's saving transaction history
     * Endpoint: GET /api/saving-history
     */
    public function getSavingHistory(Request $request)
    {
        try {
            $user = Auth::user();

            $query = SavingTransaction::with('savingType')
                ->where('user_id', $user->id)
                ->where('status', SavingTransaction::STATUS_SUCCESS);

            // Filter by saving type
            if ($request->has('saving_type_id')) {
                $query->where('saving_type_id', $request->saving_type_id);
            }

            // Filter by transaction type ('setor' atau 'tarik')
            if ($request->has('transaction_type')) {
                $query->where('transaction_type', $request->transaction_type);
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $history = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Riwayat transaksi berhasil diambil',
                'data' => $history
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deposit money (menabung/setor)
     * Endpoint: POST /api/deposit
     */
    public function deposit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'saving_type_id' => 'required|exists:saving_types,id',
                'amount' => 'required|numeric|min:10000',
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

            // Get saving type
            $savingType = SavingType::find($request->saving_type_id);

            // Validasi minimal amount berdasarkan jenis tabungan
            if ($amount < $savingType->minimum_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimal setor untuk {$savingType->name} adalah Rp " . number_format($savingType->minimum_amount, 0, ',', '.'),
                ], 422);
            }

            DB::beginTransaction();

            // Get or create member saving berdasarkan user_id dan saving_type_id
            $memberSaving = MemberSaving::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'saving_type_id' => $request->saving_type_id,
                ],
                [
                    'balance' => 0,
                ]
            );

            // Create transaction record dengan transaction_type = 'setor'
            $transaction = SavingTransaction::create([
                'user_id' => $user->id,
                'saving_type_id' => $request->saving_type_id,
                'transaction_type' => SavingTransaction::TYPE_SETOR, // 'setor'
                'amount' => $amount,
                'status' => SavingTransaction::STATUS_SUCCESS,
            ]);

            // Update balance di member_savings
            $memberSaving->addBalance($amount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil setor {$savingType->name} sebesar Rp " . number_format($amount, 0, ',', '.'),
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $memberSaving->balance,
                    'saving_type' => $savingType->name,
                    'user_id' => $user->id,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw money (tarik tunai)
     * Endpoint: POST /api/withdraw
     */
    public function withdraw(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'saving_type_id' => 'required|exists:saving_types,id',
                'amount' => 'required|numeric|min:10000',
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

            // Get saving type
            $savingType = SavingType::find($request->saving_type_id);

            // Tabungan Deposito tidak bisa ditarik
            if ($savingType->isDeposito()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabungan Deposito tidak dapat ditarik. Hanya bisa diambil saat jatuh tempo.',
                ], 422);
            }

            DB::beginTransaction();

            // Get member saving berdasarkan user_id dan saving_type_id
            $memberSaving = MemberSaving::where('user_id', $user->id)
                ->where('saving_type_id', $request->saving_type_id)
                ->first();

            if (!$memberSaving) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda belum memiliki tabungan {$savingType->name}"
                ], 400);
            }

            // Validasi minimal penarikan untuk tabungan wajib
            if ($savingType->isWajib() && $amount < $savingType->minimum_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimal penarikan untuk tabungan Wajib adalah Rp " . number_format($savingType->minimum_amount, 0, ',', '.'),
                ], 422);
            }

            // Check balance
            if ($memberSaving->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Saldo tabungan {$savingType->name} tidak mencukupi",
                    'current_balance' => $memberSaving->balance,
                ], 400);
            }

            // Untuk tabungan wajib, saldo minimal harus Rp 50.000
            if ($savingType->isWajib()) {
                $minBalance = $savingType->minimum_amount;
                if (($memberSaving->balance - $amount) < $minBalance) {
                    return response()->json([
                        'success' => false,
                        'message' => "Saldo tabungan Wajib minimal harus Rp " . number_format($minBalance, 0, ',', '.'),
                    ], 422);
                }
            }

            // Create transaction record dengan transaction_type = 'tarik'
            $transaction = SavingTransaction::create([
                'user_id' => $user->id,
                'saving_type_id' => $request->saving_type_id,
                'transaction_type' => SavingTransaction::TYPE_TARIK, // 'tarik'
                'amount' => $amount,
                'status' => SavingTransaction::STATUS_SUCCESS,
            ]);

            // Update balance di member_savings (kurangi saldo)
            $memberSaving->subtractBalance($amount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil tarik tunai dari {$savingType->name} sebesar Rp " . number_format($amount, 0, ',', '.'),
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $memberSaving->balance,
                    'saving_type' => $savingType->name,
                    'user_id' => $user->id,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get summary of user's savings
     * Endpoint: GET /api/saving-summary
     */
    /**
     * Get summary of user's savings
     * Endpoint: GET /api/saving-summary
     */
    public function getSavingSummary()
    {
        try {
            $user = Auth::user();

            // Summary per saving type
            $savingsSummary = MemberSaving::with('savingType')
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($saving) {
                    return [
                        'type' => $saving->savingType->name,
                        'balance' => $saving->balance,
                        'minimum_amount' => $saving->savingType->minimum_amount,
                    ];
                });

            $totalBalance = $savingsSummary->sum('balance');

            $totalSetor = SavingTransaction::where('user_id', $user->id)
                ->where('transaction_type', SavingTransaction::TYPE_SETOR)
                ->where('status', SavingTransaction::STATUS_SUCCESS)
                ->sum('amount');

            $totalTarik = SavingTransaction::where('user_id', $user->id)
                ->where('transaction_type', SavingTransaction::TYPE_TARIK)
                ->where('status', SavingTransaction::STATUS_SUCCESS)
                ->sum('amount');

            $lastTransaction = SavingTransaction::with('savingType')
                ->where('user_id', $user->id)
                ->where('status', SavingTransaction::STATUS_SUCCESS)
                ->latest()
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan tabungan berhasil diambil',
                'data' => [
                    'savings_summary' => $savingsSummary,
                    'total_balance' => $totalBalance,
                    'total_setor' => $totalSetor,
                    'total_tarik' => $totalTarik,
                    'last_transaction' => $lastTransaction,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan tabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail of specific saving type
     * Endpoint: GET /api/saving-detail/{id}
     */
    public function getSavingDetail($savingTypeId)
    {
        try {
            $user = Auth::user();

            $memberSaving = MemberSaving::with('savingType')
                ->where('user_id', $user->id)
                ->where('saving_type_id', $savingTypeId)
                ->first();

            if (!$memberSaving) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tabungan tidak ditemukan'
                ], 404);
            }

            $recentTransactions = SavingTransaction::where('user_id', $user->id)
                ->where('saving_type_id', $savingTypeId)
                ->where('status', SavingTransaction::STATUS_SUCCESS)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Detail tabungan berhasil diambil',
                'data' => [
                    'saving' => $memberSaving,
                    'saving_type' => $memberSaving->savingType,
                    'recent_transactions' => $recentTransactions,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail tabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     public function getUserBalance()
    {
        try {
            $user = Auth::user();

            // Ambil semua jenis tabungan
            $savingTypes = SavingType::all();

            $balances = [];
            $totalBalance = 0;

            foreach ($savingTypes as $type) {
                // Cari saldo user untuk setiap jenis tabungan
                $memberSaving = MemberSaving::where('user_id', $user->id)
                    ->where('saving_type_id', $type->id)
                    ->first();

                $balance = $memberSaving ? (float) $memberSaving->balance : 0;
                $totalBalance += $balance;

                // Tentukan bunga (khusus Deposito yang punya bunga)
                $bunga = null;
                if ($type->name === 'Deposito') {
                    $bunga = '2%'; // Bunga deposito per tahun
                }

                $balances[] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'balance' => $balance,
                    'balance_formatted' => 'Rp ' . number_format($balance, 0, ',', '.'),
                    'minimum_amount' => (float) $type->minimum_amount,
                    'minimum_amount_formatted' => 'Rp ' . number_format($type->minimum_amount, 0, ',', '.'),
                    'bunga' => $bunga,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Data saldo berhasil diambil',
                'data' => [
                    'balances' => $balances,
                    'total_balance' => $totalBalance,
                    'total_balance_formatted' => 'Rp ' . number_format($totalBalance, 0, ',', '.'),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data saldo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get balance for specific saving type
     * Endpoint: GET /api/user-balance/{savingTypeId}
     */
    public function getUserBalanceByType($savingTypeId)
    {
        try {
            $user = Auth::user();

            $savingType = SavingType::find($savingTypeId);
            if (!$savingType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis tabungan tidak ditemukan'
                ], 404);
            }

            $memberSaving = MemberSaving::where('user_id', $user->id)
                ->where('saving_type_id', $savingTypeId)
                ->first();

            $balance = $memberSaving ? (float) $memberSaving->balance : 0;

            // Tentukan bunga (khusus Deposito)
            $bunga = null;
            if ($savingType->name === 'Deposito') {
                $bunga = '12%';
            }

            return response()->json([
                'success' => true,
                'message' => 'Data saldo berhasil diambil',
                'data' => [
                    'id' => $savingType->id,
                    'name' => $savingType->name,
                    'balance' => $balance,
                    'balance_formatted' => 'Rp ' . number_format($balance, 0, ',', '.'),
                    'minimum_amount' => (float) $savingType->minimum_amount,
                    'minimum_amount_formatted' => 'Rp ' . number_format($savingType->minimum_amount, 0, ',', '.'),
                    'bunga' => $bunga,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data saldo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all transactions for user (history)
     * Endpoint: GET /api/all-transactions
     */
    public function getAllTransactions(Request $request)
    {
        try {
            $user = Auth::user();

            $query = SavingTransaction::with('savingType')
                ->where('user_id', $user->id)
                ->where('status', SavingTransaction::STATUS_SUCCESS)
                ->orderBy('created_at', 'desc');

            // Filter by transaction type
            if ($request->has('type') && in_array($request->type, ['setor', 'tarik'])) {
                $query->where('transaction_type', $request->type);
            }

            // Filter by saving type
            if ($request->has('saving_type_id')) {
                $query->where('saving_type_id', $request->saving_type_id);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $transactions = $query->paginate($perPage);

            // Format response
            $formattedTransactions = $transactions->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->transaction_type,
                    'type_text' => $item->transaction_type == 'setor' ? 'Setoran' : 'Penarikan',
                    'amount' => (float) $item->amount,
                    'amount_formatted' => 'Rp ' . number_format($item->amount, 0, ',', '.'),
                    'saving_type' => $item->savingType->name,
                    'status' => $item->status,
                    'date' => $item->created_at->format('d F Y H:i'),
                    'date_raw' => $item->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Riwayat transaksi berhasil diambil',
                'data' => [
                    'transactions' => $formattedTransactions,
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total(),
                        'last_page' => $transactions->lastPage(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
