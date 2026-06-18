<?php

namespace App\Console\Schedule;

use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule): void {
    $schedule->command('messages:finalize')
        ->everyMinute()
        ->withoutOverlapping(60)
        ->name('Finalize Mass Messages');

};
