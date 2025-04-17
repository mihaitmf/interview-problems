<?php

class Application {
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function doSomething(string $key): string
    {
        // ... other processing

        $value = $this->cache->get($key);
        if ($value !== '') {
            return $value;
        }

        $value = $this->someTimeConsumingProcessing($key);

        $this->cache->set($key, $value);

        return $value;
    }

    private function someTimeConsumingProcessing(string $key): string
    {
        // ... some processing (like a call to an external service or DB)
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

class CacheFactory {
    public static function getCache(string $type): Cache
    {
        switch (strtolower($type)) {
            case 'memcache':
                return new MemcacheAdapter(new Memcache());
            case 'redis':
                return new RedisAdapter(new Redis());
            default:
                throw new InvalidArgumentException("Unknown cache type: $type");
        }
    }
}


function main(string $config = 'redis')
{
    $cache = CacheFactory::getCache($config);
    $app = new Application($cache);
    print($app->doSomething('key'));
}

main();
