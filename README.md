# Paginator Library

### Requuirements

- PHP >= 5.6

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
$paginator = new Paginator($adpater);
$paginator->setCurrentPage(1);
$paginator->setItemsPerPage(50);

# Do something with 50 first results...
foreach ($paginator as $element) {
	echo $element;
}
```