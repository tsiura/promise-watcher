<?php

declare(strict_types=1);

namespace Zeran\PromiseWatcher;

use React\Promise\Deferred;

class WatcherTask
{
    public function __construct(
        public readonly string $id,
        public readonly EvaluatedObjectInterface $expression,
        public readonly Deferred $deferred,
        public readonly Watching $watching,
        public readonly ?float $timeout = null,
    ) {
    }
}
