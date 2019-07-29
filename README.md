# Paginator Library paginator

### Requuirements

- PHP >= 7.1

### Installation

```
composer require shoppingfeed/paginator
```

### Basic Usage

```php
<?php
namespace ShoppingFeed\Paginator;

# Creates an adapter that directly deal on data source
$adapter = new Adapter\InPlacePaginatorAdapter(range(1, 100));

# Instantiate and setup a paginator
$paginator = new Paginator($adapter);
$paginator->setCurrentPage(1);
$paginator->setItemsPerPage(50);

# Do something with 50 first results...
foreach ($paginator as $element) {
    echo $element;
}
```
