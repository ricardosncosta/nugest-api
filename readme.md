# Nugest
A basic web menu suggester written in PHP and Laravel 5.1. It uses machine learning to suggest dishes based on past experiences.

### Notes
#### app/Http/Controllers/[Controller](https://github.com/ricardosncosta/nugest/blob/master/app/Http/Controllers/Controller.php)
A setFlashMessage($type, $message) function was added to make it easier to set a message along with twitter's bootstrap .class-type, e.g.: `$this->setFlashMessage('danger', 'Oops! Something went wrong...');`

#### app/Http/Controllers/Auth/[Auth controller](https://github.com/ricardosncosta/nugest/blob/master/app/Http/Controllers/Auth/AuthController.php)
Only Signin and Signout built-in traits are being used since I needed to costumize user registration action.


#### app/Http/Controllers/User/[User controller](https://github.com/ricardosncosta/nugest/blob/master/app/Http/Controllers/User/UserController.php)
Email confirmation expires after 7 days as a security measure. Only valid confirmations will affect the user and be persisted to database.

#### Custom validators
- [checkauth](https://github.com/ricardosncosta/nugest/blob/master/app/Providers/PasswordValidationServiceProvider.php): Checks if given field value matches encrypted user password. Mainly used when updating important information such as password or email address.

#### Tests
Unit/functional tests run using SQLite's memory database to speed the process.
