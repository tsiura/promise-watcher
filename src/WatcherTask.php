<?php

declare(strict_types=1);

namespace Zeran\PromiseWatcher;

use React\Promise\Deferred;

readonly class WatcherTask
{
    public function __construct(
        public string $id,
        public EvaluatedObjectInterface $expression,
        public Deferred $deferred,
        public Watching $watching,
        public ?float $timeout = null,
    ) {
    }
}
