<?php

declare(strict_types=1);

namespace Tsiura\PromiseWatcher;

use React\EventLoop\TimerInterface;
use React\Promise\Deferred;

class WatcherTask
{
    public ?TimerInterface $timer = null;

    public function __construct(
        public readonly string $id,
        public readonly EvaluatedObjectInterface $expression,
        public readonly Deferred $deferred,
        public readonly Watching $watching,
    ) {
    }
}
