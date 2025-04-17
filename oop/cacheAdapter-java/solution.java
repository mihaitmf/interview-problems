// Example usage, run with: java oop/cacheAdapter-java/solution.java
public class Main {
    public static void main(String[] args) {
        Cache cache = CacheFactory.getCache("redis"); // Change to "memoryCache" for MemoryCache
        Application app = new Application(cache);
        System.out.println(app.doSomething("testKey"));
    }
}

// External cache library classes
class Redis {
    public String fetch(String key) {
        return "cached in Redis";
    }

    public void save(String key, String value) {}
}

class MemoryCache {
    public String fetchMem(String key) {
        return "cached in MemoryCache";
    }

    public void saveMem(String key, String value) {}
}

// Define a common interface for caching mechanisms
interface Cache {
    String fetch(String key);
    void save(String key, String value);
}

// Adapter for Redis
class RedisAdapter implements Cache {
    private Redis redisCache = new Redis();

    @Override
    public String fetch(String key) {
        return redisCache.fetch(key);
    }

    @Override
    public void save(String key, String value) {
        redisCache.save(key, value);
    }
}

// Adapter for MemoryCache
class MemoryCacheAdapter implements Cache {
    private MemoryCache memoryCache = new MemoryCache();

    @Override
    public String fetch(String key) {
        return memoryCache.fetchMem(key);
    }

    @Override
    public void save(String key, String value) {
        memoryCache.saveMem(key, value);
    }
}

// Application class using caching mechanism
class Application {
    private Cache cache;

    public Application(Cache cache) {
        this.cache = cache;
    }

    public String doSomething(String key) {
        // ... other processing

        String cachedValue = cache.fetch(key);
        if (cachedValue != null) {
            return cachedValue;
        }

        String value = someTimeConsumingProcessing(key);

        cache.save(key, value);

        return value;
    }

    private String someTimeConsumingProcessing(String key) {
        // ... some processing (like a call to an external service or DB)
        return "processed value";
    }
}

// Factory to get cache based on configuration
class CacheFactory {
    public static Cache getCache(String type) {
        if ("redis".equalsIgnoreCase(type)) {
            return new RedisAdapter();
        } else if ("memoryCache".equalsIgnoreCase(type)) {
            return new MemoryCacheAdapter();
        }
        throw new IllegalArgumentException("Unknown cache type");
    }
}
