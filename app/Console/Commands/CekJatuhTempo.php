<?php

namespace App\Console\Commands;

use App\Mail\JatuhTempoMail;
use App\Models\LoanInstallment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CekJatuhTempo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cek-jatuh-tempo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    $today = now()->format('Y-m-d');

    $installments = LoanInstallment::whereDate('due_date', $today)->get();

    foreach ($installments as $i) {
        Mail::to($i->user->email)->send(new JatuhTempoMail($i));
    }
}
}
