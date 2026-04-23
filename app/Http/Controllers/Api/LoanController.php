<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\LoanInstallment;
use App\Models\LoanInstallmentPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    /**
     * Get all loan types
     * Endpoint: GET /api/loan-types
     */
    public function getLoanTypes()
    {
        try {
            $loanTypes = LoanType::all();

            $formattedTypes = $loanTypes->map(function($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'max_plafon' => $type->max_plafon,
                    'max_plafon_formatted' => 'Rp ' . number_format($type->max_plafon, 0, ',', '.'),
                    'interest_rate_percent' => $type->interest_rate_percent,
                    'max_tenor_months' => $type->max_tenor_months,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data jenis pinjaman berhasil diambil',
                'data' => $formattedTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jenis pinjaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit loan application
     * Endpoint: POST /api/loans/submit
     *
     * Request body (multipart/form-data):
     * - loan_type_id (required)
     * - amount (required)
     * - tenor (required)
     * - akad (required) - PDF file
     * - collateral_name (required) - nama jaminan
     * - collateral_value (required) - nilai jaminan
     * - collateral_photo (required) - foto jaminan
     */
    public function submitLoan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'loan_type_id' => 'required|exists:loan_types,id',
                'amount' => 'required|numeric|min:100000',
                'tenor' => 'required|integer|min:1|max:24',
                'akad' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'collateral_name' => 'required|string|max:255',
                'collateral_value' => 'required|numeric|min:0',
                'collateral_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Foto max 2MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $loanType = LoanType::find($request->loan_type_id);

            // Validasi maksimal plafon
            if ($request->amount > $loanType->max_plafon) {
                return response()->json([
                    'success' => false,
                    'message' => "Jumlah pinjaman melebihi maksimal plafon untuk {$loanType->name} (Rp " . number_format($loanType->max_plafon, 0, ',', '.') . ")",
                ], 422);
            }

            // Validasi tenor maksimal
            if ($request->tenor > $loanType->max_tenor_months) {
                return response()->json([
                    'success' => false,
                    'message' => "Tenor melebihi maksimal untuk {$loanType->name} ({$loanType->max_tenor_months} bulan)",
                ], 422);
            }

            // Validasi data bank dari user
            if (!$user->nama_bank || !$user->atas_nama || !$user->no_rek) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lengkapi data bank terlebih dahulu di profile Anda',
                ], 422);
            }

            DB::beginTransaction();

            // Upload Akad PDF
            $akadPath = null;
            if ($request->hasFile('akad')) {
                $akadFile = $request->file('akad');
                $akadPath = $akadFile->store('loans/akad', 'public');
            }

            // Upload Collateral Photo
            $collateralPhotoPath = null;
            if ($request->hasFile('collateral_photo')) {
                $photoFile = $request->file('collateral_photo');
                $collateralPhotoPath = $photoFile->store('loans/collateral', 'public');
            }

            // Buat pengajuan pinjaman dengan data lengkap
            $loan = Loan::create([
                'user_id' => $user->id,
                'loan_type_id' => $request->loan_type_id,
                'total_amount' => $request->amount,
                'tenor' => $request->tenor,
                'status' => 'pending',
                'submitted_at' => now(),
                'akad' => $akadPath,
                'recipient_name' => $user->atas_nama,
                'bank_name' => $user->nama_bank,
                'account_number' => $user->no_rek,
                'collateral_name' => $request->collateral_name,
                'collateral_value' => $request->collateral_value,
                'collateral_photo' => $collateralPhotoPath,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pinjaman berhasil dikirim, menunggu persetujuan admin',
                'data' => [
                    'loan_id' => $loan->id,
                    'status' => $loan->status,
                    'amount' => $request->amount,
                    'tenor' => $request->tenor,
                    'loan_type' => $loanType->name,
                    'recipient_name' => $user->atas_nama,
                    'bank_name' => $user->nama_bank,
                    'account_number' => $user->no_rek,
                    'collateral_name' => $request->collateral_name,
                    'collateral_value' => $request->collateral_value,
                    'akad_url' => $akadPath ? asset('storage/' . $akadPath) : null,
                    'collateral_photo_url' => $collateralPhotoPath ? asset('storage/' . $collateralPhotoPath) : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan pinjaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's loan history
     * Endpoint: GET /api/my-loans
     */
    public function getMyLoans(Request $request)
    {
        try {
            $user = Auth::user();

            $query = Loan::with(['loanType', 'installments'])
                ->where('user_id', $user->id);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $loans = $query->orderBy('created_at', 'desc')->get();

            $formattedLoans = $loans->map(function($loan) {
                return [
                    'id' => $loan->id,
                    'loan_type' => $loan->loanType->name,
                    'total_amount' => $loan->total_amount,
                    'total_amount_formatted' => 'Rp ' . number_format($loan->total_amount, 0, ',', '.'),
                    'tenor' => $loan->tenor . ' bulan',
                    'status' => $loan->status,
                    'status_label' => $this->getStatusLabel($loan->status),
                    'submitted_at' => $loan->submitted_at,
                    'submitted_at_formatted' => $loan->submitted_at ? $loan->submitted_at->format('d M Y') : null,
                    'approved_at' => $loan->approved_at,
                    'approved_at_formatted' => $loan->approved_at ? $loan->approved_at->format('d M Y') : null,
                    'disbursed_at' => $loan->disbursed_at,
                    'disbursed_at_formatted' => $loan->disbursed_at ? $loan->disbursed_at->format('d M Y') : null,
                    'installments_count' => $loan->installments->count(),
                    'installments_paid' => $loan->installments->where('status', 'paid')->count(),
                    'next_installment' => $this->getNextInstallment($loan),
                    'collateral_name' => $loan->collateral_name,
                    'collateral_value' => $loan->collateral_value,
                    'collateral_value_formatted' => 'Rp ' . number_format($loan->collateral_value, 0, ',', '.'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Riwayat pinjaman berhasil diambil',
                'data' => $formattedLoans
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat pinjaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get loan detail by ID
     * Endpoint: GET /api/loans/{id}
     */
    public function getLoanDetail($id)
    {
        try {
            $user = Auth::user();

            $loan = Loan::with(['loanType', 'installments.payments'])
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$loan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pinjaman tidak ditemukan'
                ], 404);
            }

            $totalPaid = 0;
            foreach ($loan->installments as $installment) {
                foreach ($installment->payments as $payment) {
                    $totalPaid += $payment->amount_paid;
                }
            }
            $remainingAmount = $loan->total_amount - $totalPaid;

            $installments = $loan->installments->map(function($installment) {
                $isOverdue = $installment->due_date < now() && $installment->status == 'unpaid';

                return [
                    'id' => $installment->id,
                    'installment_number' => $installment->installment_number,
                    'due_date' => $installment->due_date,
                    'due_date_formatted' => $installment->due_date ? $installment->due_date->format('d M Y') : null,
                    'amount_due' => $installment->amount_due,
                    'amount_due_formatted' => 'Rp ' . number_format($installment->amount_due, 0, ',', '.'),
                    'status' => $installment->status,
                    'status_label' => $installment->status == 'paid' ? 'Lunas' : ($isOverdue ? 'Tunggakan' : 'Belum Dibayar'),
                    'is_overdue' => $isOverdue,
                    'payments' => $installment->payments->map(function($payment) {
                        return [
                            'amount_paid' => $payment->amount_paid,
                            'amount_paid_formatted' => 'Rp ' . number_format($payment->amount_paid, 0, ',', '.'),
                            'penalty' => $payment->penalty,
                            'paid_at' => $payment->paid_at,
                            'paid_at_formatted' => $payment->paid_at ? $payment->paid_at->format('d M Y') : null,
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail pinjaman berhasil diambil',
                'data' => [
                    'id' => $loan->id,
                    'loan_type' => $loan->loanType->name,
                    'total_amount' => $loan->total_amount,
                    'total_amount_formatted' => 'Rp ' . number_format($loan->total_amount, 0, ',', '.'),
                    'remaining_amount' => $remainingAmount,
                    'remaining_amount_formatted' => 'Rp ' . number_format($remainingAmount, 0, ',', '.'),
                    'tenor' => $loan->tenor,
                    'status' => $loan->status,
                    'status_label' => $this->getStatusLabel($loan->status),
                    'submitted_at' => $loan->submitted_at,
                    'submitted_at_formatted' => $loan->submitted_at ? $loan->submitted_at->format('d M Y') : null,
                    'approved_at' => $loan->approved_at,
                    'approved_at_formatted' => $loan->approved_at ? $loan->approved_at->format('d M Y') : null,
                    'disbursed_at' => $loan->disbursed_at,
                    'disbursed_at_formatted' => $loan->disbursed_at ? $loan->disbursed_at->format('d M Y') : null,
                    'recipient_name' => $loan->recipient_name,
                    'bank_name' => $loan->bank_name,
                    'account_number' => $loan->account_number,
                    'collateral_name' => $loan->collateral_name,
                    'collateral_value' => $loan->collateral_value,
                    'collateral_value_formatted' => 'Rp ' . number_format($loan->collateral_value, 0, ',', '.'),
                    'akad_url' => $loan->akad ? asset('storage/' . $loan->akad) : null,
                    'collateral_photo_url' => $loan->collateral_photo ? asset('storage/' . $loan->collateral_photo) : null,
                    'installments' => $installments,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pinjaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pay loan installment
     * Endpoint: POST /api/loans/pay-installment
     */
    public function payInstallment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'installment_id' => 'required|exists:loan_installments,id',
                'amount' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $installment = LoanInstallment::with('loan')->find($request->installment_id);

            if ($installment->loan->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke cicilan ini'
                ], 403);
            }

            if ($installment->status == 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cicilan ini sudah lunas'
                ], 422);
            }

            $amount = $request->amount;
            $amountDue = $installment->amount_due;

            $penalty = 0;
            if ($installment->due_date < now()) {
                $daysLate = now()->diffInDays($installment->due_date);
                $penalty = $amountDue * 0.001 * $daysLate;
                $penalty = min($penalty, $amountDue * 0.1);
            }

            $totalDue = $amountDue + $penalty;

            if ($amount < $totalDue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran kurang. Total yang harus dibayar: Rp ' . number_format($totalDue, 0, ',', '.'),
                ], 422);
            }

            DB::beginTransaction();

            $payment = LoanInstallmentPayment::create([
                'loan_installment_id' => $installment->id,
                'amount_paid' => $amount,
                'penalty' => $penalty,
                'paid_at' => now(),
            ]);

            $installment->status = 'paid';
            $installment->save();

            $unpaidInstallments = LoanInstallment::where('loan_id', $installment->loan_id)
                ->where('status', 'unpaid')
                ->count();

            if ($unpaidInstallments == 0) {
                $installment->loan->status = 'completed';
                $installment->loan->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran cicilan berhasil',
                'data' => [
                    'installment_id' => $installment->id,
                    'installment_number' => $installment->installment_number,
                    'amount_paid' => $amount,
                    'amount_paid_formatted' => 'Rp ' . number_format($amount, 0, ',', '.'),
                    'penalty' => $penalty,
                    'penalty_formatted' => 'Rp ' . number_format($penalty, 0, ',', '.'),
                    'paid_at' => $payment->paid_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get loan summary
     * Endpoint: GET /api/loan-summary
     */
    public function getLoanSummary()
    {
        try {
            $user = Auth::user();

            $activeLoans = Loan::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'disbursed'])
                ->get();

            $totalActiveLoan = $activeLoans->sum('total_amount');

            $unpaidInstallments = LoanInstallment::whereHas('loan', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'unpaid')->get();

            $totalUnpaid = $unpaidInstallments->sum('amount_due');

            $overdueInstallments = $unpaidInstallments->filter(function($installment) {
                return $installment->due_date < now();
            });

            $totalOverdue = $overdueInstallments->sum('amount_due');

            $totalSubmitted = Loan::where('user_id', $user->id)->sum('total_amount');

            $totalPaid = LoanInstallmentPayment::whereHas('installment.loan', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->sum('amount_paid');

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan pinjaman berhasil diambil',
                'data' => [
                    'total_active_loan' => $totalActiveLoan,
                    'total_active_loan_formatted' => 'Rp ' . number_format($totalActiveLoan, 0, ',', '.'),
                    'total_unpaid' => $totalUnpaid,
                    'total_unpaid_formatted' => 'Rp ' . number_format($totalUnpaid, 0, ',', '.'),
                    'total_overdue' => $totalOverdue,
                    'total_overdue_formatted' => 'Rp ' . number_format($totalOverdue, 0, ',', '.'),
                    'total_submitted' => $totalSubmitted,
                    'total_submitted_formatted' => 'Rp ' . number_format($totalSubmitted, 0, ',', '.'),
                    'total_paid' => $totalPaid,
                    'total_paid_formatted' => 'Rp ' . number_format($totalPaid, 0, ',', '.'),
                    'active_loans_count' => $activeLoans->count(),
                    'overdue_count' => $overdueInstallments->count(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan pinjaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next installment for a loan
     */
    private function getNextInstallment($loan)
    {
        $nextInstallment = $loan->installments
            ->where('status', 'unpaid')
            ->sortBy('installment_number')
            ->first();

        if ($nextInstallment) {
            return [
                'installment_number' => $nextInstallment->installment_number,
                'amount_due' => $nextInstallment->amount_due,
                'amount_due_formatted' => 'Rp ' . number_format($nextInstallment->amount_due, 0, ',', '.'),
                'due_date' => $nextInstallment->due_date,
                'due_date_formatted' => $nextInstallment->due_date ? $nextInstallment->due_date->format('d M Y') : null,
            ];
        }

        return null;
    }

    /**
     * Get status label
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'disbursed' => 'Dicairkan',
            'completed' => 'Lunas',
        ];

        return $labels[$status] ?? $status;
    }
}
