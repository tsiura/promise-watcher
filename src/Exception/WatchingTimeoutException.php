<?php

declare(strict_types=1);

namespace Tsiura\PromiseWatcher\Exception;

use Tsiura\PromiseWatcher\WatcherTask;

class WatchingTimeoutException extends PromiseWatcherException
{
    public function __construct(WatcherTask $task)
    {
        parent::__construct(sprintf('Timed out watching %d:(%s)', $task->id, $task->expression));
    }
}
