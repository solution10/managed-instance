# Solution10\ManagedInstance

Managed Instance is a tiny trait that allows you to give classes the ability to
manage instances of themselves. This allows you to create singletons, or use them
as lazy-DI containers by hot-swapping out instances by name.

## Integration

Let's say I have a class called "Auth" that I want to have multiple instances of itself,
all of which should be managed through the class.

```php
class Auth
{
    public function login()
    {
        $_SESSION['logged_in'] = true;    
    }
    
    public function logout()
    {
        $_SESSION['logged_in'] = false;
    }
}
```

**NOTE**: This is the worst Auth class in the world. Never use it.

To add the ability to manage instances, all I need do is use the managed instance trait:

```php
use Solution10\ManagedInstance\ManagedInstance;

class Auth
{
    use ManagedInstance;

    public function login()
    {
        $_SESSION['logged_in'] = true;    
    }
    
    public function logout()
    {
        $_SESSION['logged_in'] = false;
    }
}
```

Now I can register instances of my Auth class with the manager.

## Registering Instances

Before I can recall an instance, I need to first register it:

```php
$defaultAuth = new Auth();
$defaultAuth->registerInstance(); // Registers this as the 'default' instance

$defaultInstance = Auth::instance(); // Fetches the 'default' instance.

// $defaultAuth === $defaultInstance

$anotherAuth = new Auth();
$anotherAuth->registerInstance('myOtherInstance'); // Registers 'myOtherInstance'

$anotherInstance = Auth::instance('myOtherInstance'); // Returns 'myOtherInstance' instance.

// $anotherAuth === $anotherInstance
```

As you can see, ManagedInstance does not provide a factory for you. It's up to you to construct and set
up the instance before registering it with `registerInstance`.

You can then get your instances back out with the static `instance()` method.

## Unregistering Instances

I might also want to un-register an instance that has previously been registered. That's not a problem:

```php
$instanceToDelete->unregisterInstance();
```

## Fetching All Instances

You might want to fetch every instance that the class knows about. Not a problem.

```php
$instances = Auth::instances();
```

This will return a map of each instance with the key as it's name.

## Getting/Setting Instance Names

You can also read the name of an instance, or even rename it entirely:

```php
$instance = Auth::instance('myOtherInstance');
echo $instance->instanceName();

// now change it:
$instance->instanceName('mySuperInstance');

// now to read it, you need to do:
$instance2 = Auth::instance('mySuperInstance');
```
