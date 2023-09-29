# A PSR-18 HTTP Client bridge for React HTTP browser

```bash
composer require veewee/psr18-react-browser
```

```php
<?php

use React\Http\Browser;
use Veewee\Psr18ReactBrowser\Psr18ReactBrowserClient;

$psr18Client = new Psr18ReactBrowserClient(
    new Browser()
);

$psr18Client->sendRequest($request);
```

# Async

Since PHP 8.1 introduced fibers, this client can be used in parallel:

```php
<?php

use function React\Async\async;
use function React\Async\await;
use function React\Async\parallel;

$run = fn($id) => async(fn () => $psr18Client->sendRequest($buildRequestFor($id)));

$responses = await(parallel([
    $run(1),
    $run(2),
    $run(3),
]));
```

