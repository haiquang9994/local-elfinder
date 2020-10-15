### Install
```
composer require lpks/local-elfinder
```


### Config
```
require_once './vendor/autoload.php';

use Lpks\LocalElfinder\Connector;

$elfinder = new Connector(__DIR__ . '/uploads', 'assets/elfinder');

$elfinder->addOption('courses', [
    'uploadAllow' => ['image'],
    'uploadDeny' => [],
    'uploadOrder' => ['allow', 'deny'],
    'disabled' => ['mkfile', 'archive', 'extract'],
]);

$elfinder->addOption('customers', [
    'uploadAllow' => ['image'],
    'uploadDeny' => [],
    'uploadOrder' => ['allow', 'deny'],
    'disabled' => ['mkfile', 'rm', 'archive', 'extract'],
]);
```