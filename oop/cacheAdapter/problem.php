<?php
/**
Consider the following Application class which performs some time consuming processing.
And suppose we have the Redis and Memcache classes that come from external libraries, so they cannot be modified.

1. Implement a caching mechanism for the Application using Redis.
2. Design a solution that is flexible enough to allow choosing which cache implementation to use (Redis or Memcache) based on a config parameter.
Consider that the Application class should not suffer changes in the future if we decide to add another cache implementation.
 */

class Application {
    public function doSomething(string $key): string
    {
        // ... other processing

        $value = $this->someTimeConsumingProcessing($key);

        return $value;
    }

    private function someTimeConsumingProcessing(string $key): string
    {
        // ... some processing (like a call to an external service or DB)
        return 'processed value';
    }
}

// External cache library classes
class Redis {
    public function fetch(string $key): string
    {
        return "cached in Redis";
    }
    public function save(string $key, string $value): void {}
}

class Memcache {
    public function fetchMem(string $key): string
    {
        return "cached in Memcache";
    }
    public function saveMem(string $key, string $value): void {}
}

function main()
{
    $app = new Application();
    print($app->doSomething('key'));
}

main();
