# Paginator Library paginator

## Requirements

- PHP >= 7.4

## Installation

```
composer require shoppingfeed/paginator
```

## Paginator

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

## PaginatedIterator

The `ShoppingFeed\Paginator\PaginatedIterator` class allow to iterates over all elements of the inner collection,
iterating page per page until the end of the collection is reached.

```php
<?php
namespace ShoppingFeed\Paginator;

# Creates an adapter that directly deal on data source
$adapter  = new Adapter\InPlacePaginatorAdapter(range(1, 100));
$iterator = PaginatedIterator::withAdapter($adapter);
$iterator->setCurrentPage(2);
$iterator->setItemsPerPage(50);

# Iterates over all elements, 50 by 50, starting from page 2
foreach ($iterator as $element) {
    echo $element;
}
```
