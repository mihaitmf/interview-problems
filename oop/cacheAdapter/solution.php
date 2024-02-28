<?php

class Application {
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function doSomething(string $key): string
    {
        $value = $this->cache->get($key);
        if ($value !== '') {
            return $value;
        }

        // ... some processing
        $value = $this->someTimeConsumingProcessing($key);

        $this->cache->set($key, $value);

        return $value;
    }

    private function someTimeConsumingProcessing(string $key): string
    {
        return 'processed value';
    }
}

interface Cache {
    public function get(string $key): string;
    public function set(string $key, string $value): void;
}

class Redis {
    public function fetch(string $key): string
    {
        return "cached in Redis";
    }
    public function save(string $key, string $value): void {}
}

class RedisAdapter implements Cache {
    public function __construct(
        private readonly Redis $redis,
    ) {}

    public function get(string $key): string
    {
        return $this->redis->fetch($key);
    }

    public function set(string $key, string $value): void
    {
        $this->redis->save($key, $value);
    }
}

class Memcache {
    public function fetchMem(string $key): string
    {
        return "cached in Memcache";
    }
    public function saveMem(string $key, string $value): void {}
}

class MemcacheAdapter implements Cache {
    public function __construct(
        private readonly Memcache $memcache,
    ) {}

    public function get(string $key): string
    {
        return $this->memcache->fetchMem($key);
    }

    public function set(string $key, string $value): void
    {
        $this->memcache->saveMem($key, $value);
    }
}

function main($config = null)
{
    if ($config === 'Memcache') {
        $cache = new MemcacheAdapter(new Memcache());
    } else {
        $cache = new RedisAdapter(new Redis());
    }

    $app = new Application($cache);
    print($app->doSomething('key'));
}

main();
