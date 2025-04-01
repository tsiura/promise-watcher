<?php

declare(strict_types=1);

namespace Zeran\PromiseWatcher;

use Stringable;

interface EvaluatedObjectInterface extends Stringable
{
    public function evaluate(mixed $object): bool;
}
