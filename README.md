# Password-Middleware

Whenever you are handling passwords you should as soon as possible convert the
plaintext that is sent over-the-wire into something that can not leak the
cleartext-password.

For that purpose I built a [Password-ValueObject](https://github.com/heiglandreas/password)
that can replace the password and allows you to safely handle it fore whatever
need you have.

What is missing in the ValueObject though is the possibility to actually get the
password from the request and convert it directly. This is what this middleware
does. It intercepts the request, converts every parameter that is configured
into a password-ValueObject and replaces the plaintext password in the request.

This will only work for form-parameters that were sent via POST request. You
should *never ever* send passwords or other sensitive information via GET
parameters as they will be recorded in the servers access logs!!!

## Installation

Do I really need to describe this?

```bash
$ composer install org_heigl/password-middleware
```

## Usage

```php
use Org_Heigl\PasswordMiddleware\PasswordMiddleware;
use Slim\App;

$app = new App();
$app->add(new PasswordMiddleware('password', 'password-verification'));
```

Now you can use this in your controller:

```php
class Controller
{
    public function handle($request, $response): ServerResponse
    {
        /** @var \Org_Heigl\Password\Password $password */
        $password = $request->getParsedBody()['password'];
        $passwordVerification = $request->getParsedBody()['password-verification'];
        if ($password == $passwordVerification) {
            throw new RuntimeException('Passwords do not match');
        }
    }
}
```

**Caveat:** Currently only fields in the first level of the parsed body are
available! So if you nest parameters this will currently not work! This is
one of the next features that will be implemented!

**Caveat:** Currently the raw body will not be modified! So the clear text
password will always be in the raw request stream! This is also one of the next
things on the list!


