# Express PHP Validator
Express PHP validator, is an Express PHP framework module. It is used to validate a given string against a given criteria.

## How to
To use the Express Validator module, you just have to inject it into Express PHP Object.
Open your `index.php` file in Express PHP root directory.
```php
// Import ExpressValidator module...
$app->import('ExpressValidator');
```
Done! you have successfully, injected the module.

### Using the Express Validator
To use the Express Validator module, you access it through, the Express PHP Object.
For example:
```php
$app->_ExpressValidator;
```

#### validate()
`$app->_ExpressValidator->validate([type], [string])`
**Validation Types:**
- email
- url
- integer
- ip

To validate a given string against a criteria, use the validate method!
```php
// Test if the given string is in the email format
var_dump($app->_ExpressValidator->validate('email', 'victor.olorunbunmi@gmail.com'));

// Test if the given string is in the URl format
var_dump($app->_ExpressValidator->validate('email', 'fb.com'));
```
#### test()
Allows you to test a string against a regex, return boolean:
`$app->_ExpressValidator->test([regex], [string])`
**Example:** Test if the provide string matches a URL.
```php
var_dump($app->_ExpressValidator->test("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", "http://wwww.github.com"));
```

### sanitize()
The sanitize() method allows you to purify a given string, against certain criteria.
`$app->_ExpressValidator->sanitize([type], [string])`
**Sanitation Types:**
- email
- string
- url
- special char
- integer
- float
**Example:** Remove HTML tags from string
```php
echo $app->_ExpressValidator->validate('string', '<h1>Hello Express Validator</h1>');
```

# License
[The MIT License](LICENSE.md)