<?php

declare(strict_types=1);

namespace Tsiura\PromiseWatcher;

use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\Promise;
use Tsiura\PromiseWatcher\Exception\PromiseWatcherException;
use Tsiura\PromiseWatcher\Exception\WatchingTimeoutException;

class ObjectWatcher
{
    public const int DEFAULT_TIMEOUT = 5;

    /**
     * @var WatcherTask[]
     */
    private array $tasks = [];

    public function __construct(
        private readonly LoopInterface $loop,
    ) {
    }

    public function createWatching(EvaluatedObjectInterface $object, float $timeout = self::DEFAULT_TIMEOUT): Watching
    {
        $id = spl_object_hash($object);
        if (isset($this->tasks[$id])) {
            return $this->tasks[$id]->watching;
        }

        $deferred = new Deferred();
        $watching = new Watching(
            fn () => $this->startWatching($deferred, $id, $timeout),
            fn () => $this->cancelWatching($id),
        );
        $this->tasks[$id] = new WatcherTask($id, $object, $deferred, $watching);

        return $watching;
    }

    /**
     * @param mixed $object
     * @return int Count evaluated objects
     */
    public function evaluate(mixed $object): int
    {
        $result = 0;
        foreach ($this->tasks as $task) {
            if ($task->expression->evaluate($object)) {
                $this->removeTask($task);
                $task->deferred->resolve($object);
                $result++;
            }
        }

        return $result;
    }

    public function count(): int
    {
        return count($this->tasks);
    }

    public function clear(): self
    {
        foreach ($this->tasks as $task) {
            $this->removeTask($task);
        }

        return $this;
    }

    private function startWatching(Deferred $deferred, string $id, ?float $timeout = 0): Promise
    {
        $task = $this->getTask($id);
        if (null !== $task && null !== $timeout && $timeout > 0) {
            // Case when task not resolved yet, should add overdue timer
            $task->timer = $this->loop->addTimer($timeout, fn () => $this->taskTimeout($id));
        }
        // Even if task already resolved and removed from task list we should return result in same promise
        return $deferred->promise();
    }

    private function cancelWatching(string $id): void
    {
        $task = $this->getTask($id);
        if (null === $task) {
            return;
        }
        $this->removeTask($task);
        $task->deferred->reject(new PromiseWatcherException('Task was cancelled'));
    }

    private function taskTimeout(string $id): void
    {
        $task = $this->getTask($id);
        if (null === $task) {
            return;
        }
        $this->removeTask($task);
        $task->deferred->reject(new WatchingTimeoutException($task));
    }

    private function removeTask(WatcherTask $task): void
    {
        if (null !== $task->timer) {
            $this->loop->cancelTimer($task->timer);
        }
        if (isset($this->tasks[$task->id])) {
            unset($this->tasks[$task->id]);
        }
    }

    private function getTask(string $id): ?WatcherTask
    {
        return $this->tasks[$id] ?? null;
    }
}
