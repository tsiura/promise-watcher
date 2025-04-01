<?php

declare(strict_types=1);

namespace Zeran\PromiseWatcher\Exception;

use Zeran\PromiseWatcher\WatcherTask;

class WatchingTimeoutException extends PromiseWatcherException
{
    public function __construct(WatcherTask $task)
    {
        parent::__construct(sprintf('Timed out watching %d:(%s)', $task->id, $task->expression));
    }
}
