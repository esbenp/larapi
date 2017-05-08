# Larapi

Larapi is an API-friendly fork of Laravel, batteries included. If you want to read a more in-depth description 
of the offerings and how to do modern API development in Laravel I have written a series of blogposts on the topic.

[A Modern REST API in Laravel 5](http://esbenp.github.io/2016/04/11/modern-rest-api-laravel-part-0/)

Larapi comes included with...
* Laravel 5.4
* Laravel Passport for OAuth Authentication, including a proxy for password and refresh-token grants
* A new directory structure optimized for separating infrastructure and domain code. Groups your controllers, models, etc. by resource-type. 
[Read more and view the source code here](https://github.com/esbenp/distributed-laravel)
* [Optimus\Heimdal](https://github.com/esbenp/heimdal): A modern exception handler for APIs with Sentry and Bugsnag integration out-of-the-box
* [Optimus\Bruno](https://github.com/esbenp/bruno): A base controller class that gives sorting, filtering, eager loading and pagination for your endpoints
* [Optimus\Genie](https://github.com/esbenp/genie): A base repository class for requesting entities from your database. Includes integration with Bruno.
* [Optimus\Architect](https://github.com/esbenp/architect): A library for creating advanced structures of related entities
* [Optimus\ApiConsumer](https://github.com/esbenp/laravel-api-consumer): A small class for making internal API requests 

## Motivation

We maintain a fairly large Laravel API at [Traede](https://traede.com). Our philosophy is to utilize the framework as much as possible. 
However, we also strongly feel that the amount of people that use Laravel for API development is not as large as it could be. 
We *feel* that Laravel as a framework tries to focus more on traditional web development (i.e. rendering HTML). We try to modify the framework 
just a little bit so it is more suited for API development. Larapi is the result of the changes we have made at Traede in order 
to make Laravel more enjoyable as an API framework.

## Installation 

First clone the repository

```bash
git clone https://github.com/esbenp/larapi my-api
```

Install dependencies

```bash
composer install
```

Copy the `.env` file an create an application key

```
cp .env.example .env && php artisan key:generate
```
Larapi comes with Passport, included as the default authenticatin method. The Passport service provider registers its own database migration directory with the framework, so firstly you should migrate your database by running `migrate` command before running `passport:install` command. The Passport migrations will create the tables your application needs to store clients and access tokens. ( [more details on passport installation](https://laravel.com/docs/5.4/passport#installation) )

```
php artisan migrate
``` 

After running the migration, you should now install it using this command.

```
php artisan passport:install
```

Copy-paste the generated secrets and IDs into your `.env` file like so.

```
PERSONAL_CLIENT_ID=1
PERSONAL_CLIENT_SECRET=mR7k7ITv4f7DJqkwtfEOythkUAsy4GJ622hPkxe6
PASSWORD_CLIENT_ID=2
PASSWORD_CLIENT_SECRET=FJWQRS3PQj6atM6fz5f6AtDboo59toGplcuUYrKL
```

If you want to save it elsewhere or change the naming be sure to modify the LoginProxy in `infrastructure/Auth/LoginProxy.php`

Finally,the installation has completed, ready for testing.

## Test installation

You can quickly test if the authentication works by creating an user using the include command.

```bash
php artisan users:add Esben esben@esben.dk 1234
```

Now serve your application and try to request a token using cURL

```bash
php artisan serve
curl -X POST http://localhost:8000/login -H 'Content-Type:application/json' -d '
{
    "email":"esben@esben.dk",
    "password":"1234"
}'
```

This should return a token.

```json
{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImM0MWZiOWFjZjkyZmRiY2RhYjE0ZmEwYTFlMzMwYjBjYTEwMmRiMTA1ZGI4MmZjYzllZGUwMjRiNzI2MjA2YjRhZDU4MGZhMjUxODU2Y2RkIn0.eyJhdWQiOiIyIiwianRpIjoiYzQxZmI5YWNmOTJmZGJjZGFiMTRmYTBhMWUzMzBiMGNhMTAyZGIxMDVkYjgyZmNjOWVkZTAyNGI3MjYyMDZiNGFkNTgwZmEyNTE4NTZjZGQiLCJpYXQiOjE0ODk5NTM3MDYsIm5iZiI6MTQ4OTk1MzcwNiwiZXhwIjoxNDg5OTU0MzA2LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.SmsEyCEXBiwSgl0yMcjvCxoZ2a_7D6GDJTxTs_J-6yzUeJkOofrSV7RRafO3VvUckrNqy5sGgglrwGH_HN7_lNPU6XcgaaNzbzf-g7vCSzCicJiYZVzxqJpZVwqQ4WIQrc0lYdk7suZ7hwQulOD_Z79JhBNh1KSAyo3ABWHiRjh9NR_-iAjvlCohh7nAETDeVqoMrR99m3fwQYOjdtvRBHJ8Ei-Kx3Gn1DyOXyh8eGa5-yDtj-ZVI9x66YMXlm8wk4IMA_Oh7KJISfdpoQs4fPyrGsFAxQMFp02qEW2fzKl2eesZeiIAyDGWE4StHsuY3E4jZL0P-pjv08j5W4CBP0P64gkNw_GdbxlPPA-qZUzJlc3EtjrzZ9WZq3JAKKCGy5I1jHECDOqaQ1z7axm6rmxRWmXmRGwwkne8QxfPlXsN0sm5q98mJckeqCLUuir1VPyFn5Z-B7D80-sc7Zm-7zi-awJtZUGMcHSo_yNHXjVGcbJwFk04xoIL2QzMXpOVPLaUdlBp_obCJhdzT5Bx0o5SDdK2LwgEwbMkksqmrTJ7ypoezsc3ihVQIrMelK2lNfkH_cDcVdD3ub8oFTthbA62U6atXaIADcsgTCgOtgQ2uXTIko_btjECgL35LZDd8UxiyQT3w-pDrELGDPx17DQCsIZDJ8mC1s6E0d7EPsA","expires_in":600}
```

Now try to request all users `GET /users` using the newly issued token.

```bash
curl http://localhost:8000/users -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImM0MWZiOWFjZjkyZmRiY2RhYjE0ZmEwYTFlMzMwYjBjYTEwMmRiMTA1ZGI4MmZjYzllZGUwMjRiNzI2MjA2YjRhZDU4MGZhMjUxODU2Y2RkIn0.eyJhdWQiOiIyIiwianRpIjoiYzQxZmI5YWNmOTJmZGJjZGFiMTRmYTBhMWUzMzBiMGNhMTAyZGIxMDVkYjgyZmNjOWVkZTAyNGI3MjYyMDZiNGFkNTgwZmEyNTE4NTZjZGQiLCJpYXQiOjE0ODk5NTM3MDYsIm5iZiI6MTQ4OTk1MzcwNiwiZXhwIjoxNDg5OTU0MzA2LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.SmsEyCEXBiwSgl0yMcjvCxoZ2a_7D6GDJTxTs_J-6yzUeJkOofrSV7RRafO3VvUckrNqy5sGgglrwGH_HN7_lNPU6XcgaaNzbzf-g7vCSzCicJiYZVzxqJpZVwqQ4WIQrc0lYdk7suZ7hwQulOD_Z79JhBNh1KSAyo3ABWHiRjh9NR_-iAjvlCohh7nAETDeVqoMrR99m3fwQYOjdtvRBHJ8Ei-Kx3Gn1DyOXyh8eGa5-yDtj-ZVI9x66YMXlm8wk4IMA_Oh7KJISfdpoQs4fPyrGsFAxQMFp02qEW2fzKl2eesZeiIAyDGWE4StHsuY3E4jZL0P-pjv08j5W4CBP0P64gkNw_GdbxlPPA-qZUzJlc3EtjrzZ9WZq3JAKKCGy5I1jHECDOqaQ1z7axm6rmxRWmXmRGwwkne8QxfPlXsN0sm5q98mJckeqCLUuir1VPyFn5Z-B7D80-sc7Zm-7zi-awJtZUGMcHSo_yNHXjVGcbJwFk04xoIL2QzMXpOVPLaUdlBp_obCJhdzT5Bx0o5SDdK2LwgEwbMkksqmrTJ7ypoezsc3ihVQIrMelK2lNfkH_cDcVdD3ub8oFTthbA62U6atXaIADcsgTCgOtgQ2uXTIko_btjECgL35LZDd8UxiyQT3w-pDrELGDPx17DQCsIZDJ8mC1s6E0d7EPsA'
```

This should return a response like so

```json
{"users":[{"id":1,"name":"Esben","email":"esben@esben.dk","created_at":"2017-03-19 19:59:15","updated_at":"2017-03-19 19:59:15"}]}
```

You can refresh a new token by requesting `POST /login/refresh` and logout using `POST /logout`

## Contributing

Please see [CONTRIBUTING](https://github.com/esbenp/architect/blob/master/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](https://github.com/esbenp/architect/blob/master/LICENSE) for more information.
