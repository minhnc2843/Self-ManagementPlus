<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use App\Notifications\LoanDueNotification;
use Carbon\Carbon;

class CheckLoanDue extends Command
{
    protected $signature = 'loans:check-due';
    protected $description = 'Kiểm tra và gửi thông báo cho các khoản vay sắp đến hạn';

    public function handle()
    {
        $upcomingLoans = Loan::where('status', 'pending')
            ->whereDate('due_date', '=', Carbon::tomorrow())
            ->get();

        foreach ($upcomingLoans as $loan) {
            $loan->user->notify(new LoanDueNotification($loan));
        }

        $this->info('Đã kiểm tra và gửi thông báo.');
    }
}