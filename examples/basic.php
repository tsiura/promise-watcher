<?php

declare(strict_types=1);

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Zeran\PromiseWatcher\EvaluatedObjectInterface;
use Zeran\PromiseWatcher\ObjectWatcher;

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

$value = mt_rand(1, 10);
echo sprintf('Create watching for %d', $value) . PHP_EOL;

$watcher = new ObjectWatcher();
$w1 = $watcher->createWatching(new EvalObjNum($value), 1.0);
$w1->start()
    ->then(function ($value) {
        echo sprintf('Evaluated successfully with value ' . $value) . PHP_EOL;
    }, function (\Throwable $e) {
        echo $e->getMessage() . PHP_EOL;
    });

while (true) {
    $value = mt_rand(1, 10);
    echo sprintf('[%s] Evaluating with %d', time(), $value) . PHP_EOL;
    if ($watcher->evaluate($value) > 0 || $watcher->count() === 0) {
        break;
    }
    usleep(100000);
}

