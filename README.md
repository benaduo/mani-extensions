# mani-php-extensions

Contains custom php helper methods to be used in projects
## How to Use
1. Include the file mani-extensions.php in your project
```
include_once 'mani-extensions/mani-extensions.php;
```
2. To call an SQL helper method
```
use ManiExtensions\MySql;
use ManiExtensions\EmailClient;
use ManiExtensions\Validator;
use ManiExtensions\Actions;

MySql::createResourceConnection();
EmailClient::sendMail();
Validator::validateUserInput();
Actions::showAlert();
```
