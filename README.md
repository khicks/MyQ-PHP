# MyQ-PHP

PHP wrapper for Chamberlain MyQ.

## Installation

```bash
$ sudo apt-get install composer php php-curl
$ composer require khicks/myq-php 
```

## Usage examples

### Basic

```php
<?php

require_once('vendor/autoload.php');

$myq = new MyQ\MyQ($myq_username, $myq_password);
$door = $myq->getGarageDoorDevices()[0];

// Get door state information.
$door->getState()->getDescription();
// "closed"
$door->getState()->getDeltaInt();
// 4924
$door->getState()->getDeltaStr();
// "1 hour, 22 minutes, 4 seconds"

// Open and close door.
$door->open();
sleep(20);
$door->close();

// The security token is like a cookie that you obtain after logging in.
// You should save this value if you want to use it for subsequent runs.
$myq->getSecurityToken()->getValue();
// "5ff81c31-6725-40f5-81a2-dc352ad300dd"
```

### With previous security token

If only given a username and password, MyQ will perform an extra
API call to log in every time the object is created. If you want
to use a previously fetched security token to create a MyQ object,
pass it into the optional third parameter. If the token is invalid,
MyQ-PHP will automatically attempt to log in again and use the new
security token.

```php
<?php

require_once('vendor/autoload.php');

$myq = new MyQ\MyQ($myq_username, $myq_password, $security_token);
$door = $myq->getGarageDoorDevices()[0];
// ...
```
