<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zeran\PromiseWatcher\ObjectWatcher;

class BasicUsageTest extends TestCase
{
    public function testResolveSuccess()
    {
        $watcher = new ObjectWatcher();

        $result = 0;

        $w1 = $watcher->createWatching(new EvalObjNum(11), 1);
        $w1->start()
            ->then(function ($value) use (&$result) {
                $result = $value;
            }, function (\Throwable $e) {
            });

        $watcher->evaluate(11);

        self::assertEquals(11, $result);
    }
}
