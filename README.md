## Quarkino Test Project 

This project implement with DDD.

To start project please follow these steps:

- run `php artisan migrate`
- run `php artisan db:seed`

## Authorization

This project is used basic auth for authorization users.

Please set `Authorization` header like this:

```
{
    //... other headers
    "Authorization" : "Basic youremail@test.com"   
    //... other headers
}
```

*Please use this email for authorization:* `active.user@yasnateam.com`

## Api documentation

In root of project you can find `*.http` files. These files made by `Phpstorm` and you can run these files inside IDE.

You can find `Postmand Collection` inside folder called `PostmanCollection`.

## Testing

To run tests, please use laravel run test command:

`php artisan test`
