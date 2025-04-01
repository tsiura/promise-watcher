<?php

declare(strict_types=1);

namespace Zeran\PromiseWatcher;

use React\Promise\Deferred;
use Zeran\PromiseWatcher\Exception\PromiseWatcherException;
use Zeran\PromiseWatcher\Exception\WatchingTimeoutException;

class ObjectWatcher
{
    public const int DEFAULT_TIMEOUT = 5;

    /**
     * @var WatcherTask[]
     */
    private array $tasks = [];

    public function createWatching(EvaluatedObjectInterface $object, float $timeout = self::DEFAULT_TIMEOUT): Watching
    {
        $id = spl_object_hash($object);
        if (isset($this->tasks[$id])) {
            return $this->tasks[$id]->watching;
        }

        $deferred = new Deferred();
        $watching = new Watching(
            fn () => $deferred->promise(),
            fn () => $this->cancelWatching($id),
        );
        $timeout = $timeout > 0 ? (microtime(true) + $timeout) : null;
        $this->tasks[$id] = new WatcherTask($id, $object, $deferred, $watching, $timeout);

        return $watching;
    }

    /**
     * @param mixed $object
     * @return int Count evaluated tasks
     */
    public function evaluate(mixed $object): int
    {
        $result = 0;
        foreach ($this->tasks as $task) {
            if ($this->validateTask($task) && $task->expression->evaluate($object)) {
                $this->removeTask($task);
                $task->deferred->resolve($object);
                $result++;
            }
        }

        return $result;
    }

    /**
     * @return int Count of invalidated tasks
     */
    public function validateTasks(): int
    {
        $result = 0;
        foreach ($this->tasks as $task) {
            if (!$this->validateTask($task)) {
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

    private function cancelWatching(string $id): void
    {
        $task = $this->getTask($id);
        if (null === $task) {
            return;
        }
        $this->removeTask($task);
        $task->deferred->reject(new PromiseWatcherException('Task was cancelled'));
    }

    private function validateTask(WatcherTask $task): bool
    {
        if (null !== $task->timeout && $task->timeout <= microtime(true)) {
            $this->removeTask($task);
            $task->deferred->reject(new WatchingTimeoutException($task));
            return false;
        }

        return true;
    }

    private function removeTask(WatcherTask $task): void
    {
        if (isset($this->tasks[$task->id])) {
            unset($this->tasks[$task->id]);
        }
    }

    private function getTask(string $id): ?WatcherTask
    {
        return $this->tasks[$id] ?? null;
    }
}
