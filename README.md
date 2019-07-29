# Paginator Library paginator [![buddy pipeline](https://app.buddy.works/shopping-feed/paginator/pipelines/pipeline/201690/badge.svg?token=e2c6cf2c773f19aab3115f910e2dfd29c2a5692a16c629ae50084075289162dc "buddy pipeline")](https://app.buddy.works/shopping-feed/paginator/pipelines/pipeline/201690)

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