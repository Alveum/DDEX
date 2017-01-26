# DDEX

A library to get DDEX party details.

## Example

```
<?php

require 'vendor/autoload.php';

$d = new \Alveum\DDEX\DDEX('ddex@example.com', 'secret');
var_dump($d->getPartyById('PA-DPIDA-2016121410-U'));
```