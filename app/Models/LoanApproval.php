<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    
    class LoanApproval extends Model
    {
        protected $fillable = [
            'loan_id',
            'level',
            'approved_by',
            'status',
            'note',
            'approved_at',
        ];

        protected $casts = [
            'approved_at' => 'datetime',
        ];

        // 🔗 relasi ke loan
        public function loan()
        {
            return $this->belongsTo(Loan::class);
        }

        // 🔗 relasi ke user approver
        public function approver()
        {
            return $this->belongsTo(User::class, 'approved_by');
        }
    }
