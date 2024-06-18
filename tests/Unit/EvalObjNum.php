<?php

namespace Tests\Unit;

use Tsiura\PromiseWatcher\EvaluatedObjectInterface;

class EvalObjNum implements EvaluatedObjectInterface
{
    public function __construct(
        private readonly int $value,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s', $this->value);
    }

    public function evaluate(mixed $object): bool
    {
        return (is_numeric($object) && $object == $this->value);
    }
}
