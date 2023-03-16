# shoppingfeed/paginator

### Installation

```
composer require shoppingfeed/paginator
```

### Documentation

Documentation is driven by [mkdocs](https://www.mkdocs.org/) and uses [material theme](https://squidfunk.github.io/mkdocs-material/)

- Find it at [docs/index.md](docs/index.md)
- Or Run the doc server locally :

```
docker run --rm -it -p 8000:8000 -v ${PWD}:/docs squidfunk/mkdocs-material
```

### Contributing

To connect to a php 7.4 container correctly configured

- Create a container : `docker run --name paginator-php -v $PWD:/var/www -d ghcr.io/shoppingflux/php:7.4.16-fpm`
- Connect to container : `docker exec -it paginator-php bash`

Once connected to the container you can :

- Update composer dependencies : `composer update`
- Run test : `composer test`
