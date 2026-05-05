<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Providers\FcmServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAnnouncementNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $announcement;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FcmServiceProvider $fcm)
    {
        $students = $this->announcement->StudentList()->map(function ($student) {
            return $student->device_token;
        })->filter()->unique()->toArray();

        if (count($students) > 0) {
            $fcm->sendMulticast(
                $students,
                "There is an announcement from GPCC",
                $this->announcement->title,
                env('APP_LOGO')
            );
        }
    }
}
