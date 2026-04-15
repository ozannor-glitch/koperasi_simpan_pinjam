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

            // Jika user belum memiliki tabungan, buatkan default
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

            // Filter by transaction type
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
     * Deposit money (menabung)
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

            DB::beginTransaction();

            // Get or create member saving
            $memberSaving = MemberSaving::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'saving_type_id' => $request->saving_type_id,
                ],
                [
                    'balance' => 0,
                ]
            );

            // Create transaction record
            $transaction = SavingTransaction::create([
                'user_id' => $user->id,
                'saving_type_id' => $request->saving_type_id,
                'transaction_type' => SavingTransaction::TYPE_DEPOSIT,
                'amount' => $amount,
                'status' => SavingTransaction::STATUS_SUCCESS,
            ]);

            // Update balance
            $memberSaving->addBalance($amount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menabung',
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $memberSaving->balance,
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

            DB::beginTransaction();

            // Get member saving
            $memberSaving = MemberSaving::where('user_id', $user->id)
                ->where('saving_type_id', $request->saving_type_id)
                ->first();

            if (!$memberSaving) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum memiliki tabungan jenis ini'
                ], 400);
            }

            // Check balance
            if ($memberSaving->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak mencukupi'
                ], 400);
            }

            // Create transaction record
            $transaction = SavingTransaction::create([
                'user_id' => $user->id,
                'saving_type_id' => $request->saving_type_id,
                'transaction_type' => SavingTransaction::TYPE_WITHDRAWAL,
                'amount' => $amount,
                'status' => SavingTransaction::STATUS_SUCCESS,
            ]);

            // Update balance
            $memberSaving->subtractBalance($amount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menarik tunai',
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $memberSaving->balance,
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
    public function getSavingSummary()
    {
        try {
            $user = Auth::user();

            $totalBalance = MemberSaving::where('user_id', $user->id)->sum('balance');

            $totalDeposit = SavingTransaction::where('user_id', $user->id)
                ->where('transaction_type', SavingTransaction::TYPE_DEPOSIT)
                ->where('status', SavingTransaction::STATUS_SUCCESS)
                ->sum('amount');

            $totalWithdrawal = SavingTransaction::where('user_id', $user->id)
                ->where('transaction_type', SavingTransaction::TYPE_WITHDRAWAL)
                ->where('status', SavingTransaction::STATUS_SUCCESS)
                ->sum('amount');

            $lastTransaction = SavingTransaction::where('user_id', $user->id)
                ->where('status', SavingTransaction::STATUS_SUCCESS)
                ->latest()
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan tabungan berhasil diambil',
                'data' => [
                    'total_balance' => $totalBalance,
                    'total_deposit' => $totalDeposit,
                    'total_withdrawal' => $totalWithdrawal,
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
}
