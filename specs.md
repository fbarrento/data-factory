# What we want to build

- Build a Factory class based on the Laravel Eloquent Model Factories concept.
- Instead of being tied to Eloquent models, it should be generic and work with any PHP class "Data Objects"
- The Data Objects they can implement two methods toArray() and toJson()
- The Factory will make a new instance of the Data Object class, and fill it with fake data using FakerPHP or some other data.
- The Factory should allow defining default states, and also allow overriding those states when creating instances.
- The Factory should support creating single instances and collections of instances.
