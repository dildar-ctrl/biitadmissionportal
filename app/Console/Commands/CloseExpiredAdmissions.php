<?php

namespace App\Console\Commands;

use App\Models\AdmissionSchedule;
use App\Models\Program;
use Illuminate\Console\Command;

class CloseExpiredAdmissions extends Command
{
    protected $signature = 'admissions:close-expired';
    protected $description = 'Close programs for sessions whose admission window has passed';

    public function handle()
    {
        $expiredSchedules = AdmissionSchedule::whereDate('admissionclosedate', '<', now())->get();

        foreach ($expiredSchedules as $schedule) {
            $closed = Program::where('session_id', $schedule->sessionid)
                ->where('status', 'open')
                ->update(['status' => 'closed']);

            if ($closed > 0) {
                $this->info("Closed {$closed} program(s) for session_id {$schedule->sessionid}");
            }
        }
    }
}