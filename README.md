# Laravel Token

A tool for easy public token implementation in some common tasks: password reset, account activation, etc. In general, when need to send a token through mail (for example). We are developed with Laravel 5.x in mind.

## Installation

Add in your composer.json:

    {
        "require": {
            "malahierba-lab/token": "~1.0"
        }
    }

Then you need run the `composer update` command.

## How Work

The tokens are save using Laravel Cache Facade, so you don't need a table in your database (unless you are using the database cache as driver), so... you don't need alter your current database, and tokens expire automatically without headaches :)

## Use

***Important**: For documentation purposes, in the examples below, always we assume than you import the library into your namespace using `use Malahierba\Token\Token;`*

You can generate a token for any model (Eloquent/Model) in your project. In this examples below we are using the User Model, but you can use the Model than you need.

**1.- Make a Token Instance**

    //Get a Model Instance... in this case, a User instance.
    $user = User::find(1);
    
    // Create a password reset token instance
    // 'password reset' is just a example... you can use the string than you want :)
    // so.. if you want a token named 'my token type' just use it as second parameter. No predefined types.
    $token = new Token($user, 'password reset');
    
    //You can define the duration, in minutes, until the token expire. Default is 60.
    //This example set 15 minutes to expire the token
    $token = new Token($user, 'password reset', 15);
    
    //Also you can define the number of chars for token. Default is 48
    //This example set token length in 32 chars (and use the default minutes to expire)
    $token = new Token($user, 'password reset', null, 32);
    
    //This example set 15 minutes to expire the token and token length in 32 chars
    $token = new Token($user, 'password reset', 15, 32);
    
**2.- Get the Token string**

    $token_str = $token->get();
    
If no previous token exists (or previous token is expired), then you will get a new randomly generated string. If previous token exists (and is not expired yet) then you get that token.

**3.- Check the Token**

You can validate a token entered by an user or when come from url (for example if you mail a link for password reset with the token as parameter):

    if ($token->check($string))
        // code for token validated
        
**4.- Delete the Token**

When you don't need the token anymore (for example, your user already setup a new password in password reset lifecycle) you can delete it:

    $token->delete();
    
*The delete method no destroy the token instance, just delete the old token string. If you run $token->get() you will receive a new token for same user (and same type) because the instance is the same.*

## Licence

This project has MIT licence. For more information please read LICENCE file.