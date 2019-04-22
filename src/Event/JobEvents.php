<?php

namespace App\Event;

final class JobEvents
{
    const BOOT_EVENT = 'app.job.boot.event';
    const EXECUTE_EVENT = 'app.job.execute.event';
    const SHUTDOWN_EVENT = 'app.job.shutdown.event';
}
