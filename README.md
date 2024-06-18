# promise-watcher
A PHP library for asynchronous, promise-based object watching.

### basic usage example
```
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

$w1 = $watcher->createWatching(new EvalObjNum(10), 1);
$w1->start()
    ->then(function ($value) {
        echo sprintf('Evaluated successfully with value ' . $value) . PHP_EOL;
    }, function (\Throwable $e) { echo $e->getMessage() . PHP_EOL; });

$watcher->evaluate(11);
```
#### this example with print `Timed out watching 0:(10)`
#### in case we evaluate with number 10 output will be `Evaluated successfully with value 10`

### For evaluating more complex object may be used `webmozart/expression`