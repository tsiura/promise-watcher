<?php

declare(strict_types=1);

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';


use React\EventLoop\Loop;
use Tsiura\PromiseWatcher\EvaluatedObjectInterface;
use Tsiura\PromiseWatcher\ObjectWatcher;

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

$watcher = new ObjectWatcher(Loop::get());

$w1 = $watcher->createWatching(new EvalObjNum(11), 1);
$w1->start()
    ->then(function ($value) {
        echo sprintf('Evaluated successfully with value ' . $value) . PHP_EOL;
    }, function (\Throwable $e) { echo $e->getMessage() . PHP_EOL; });

$watcher->evaluate(11);

