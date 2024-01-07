<?php

namespace App\Event;

final class JobEvents
{
    public const BOOT_EVENT = 'app.job.boot.event';
    public const EXECUTE_EVENT = 'app.job.execute.event';
    public const SHUTDOWN_EVENT = 'app.job.shutdown.event';
}
