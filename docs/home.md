# Solution10\ManagedInstance

Managed Instance is a tiny trait that allows you to give classes the ability to
manage instances of themselves. This allows you to create singletons, or use them
as lazy-DI containers by hot-swapping out instances by name.

## Usage

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

Now I can create new instances of Auth as simply as this:

```php
$defaultInstance = Auth::instance();

$anotherInstance = Auth::instance('myOtherInstance');
```

## Constructors

