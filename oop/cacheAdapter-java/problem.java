/**
 Consider the following Application class which performs some time consuming processing.
 And suppose we have the Redis and Memcache classes that come from external libraries, so they cannot be modified.

 1. Implement a caching mechanism for the Application using Redis.
 2. Design a solution that is flexible enough to allow choosing which cache implementation to use (Redis or Memcache) based on a config parameter.
 Consider that the Application class should not suffer changes in the future if we decide to add another cache implementation.
 */

class Application {
    public String doSomething(String key) {
        // ... other processing

        String value = someTimeConsumingProcessing(key);

        return value;
    }

    private String someTimeConsumingProcessing(String key) {
        // ... some processing (like a call to an external service or DB)
        return "processed value";
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
