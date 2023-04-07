<?php

declare(strict_types=1);

namespace Tsiura\PromiseWatcher;

use Closure;
use Exception;
use React\Promise\PromiseInterface;
use function React\Promise\reject;

class Watching
{
    private bool $cancelled = false;

    public function __construct(
        private readonly Closure $cbStart,
        private readonly Closure $cbCancel,
    ) {
    }

    public function start(): PromiseInterface
    {
        if ($this->cancelled) {
            return reject(new Exception('task cancelled'));
        }

        return call_user_func($this->cbStart);
    }

    public function cancel(): void
    {
        $this->cancelled = true;
        call_user_func($this->cbCancel);
    }
}
