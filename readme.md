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

To connect to a php 8.0 container correctly configured

- Create a container : `docker run --name paginator-php -v $PWD:/var/www -w /var/www -d php:8.0-fpm`
- Install composer in the container (first time only) :

```
docker exec paginator-php bash -c "curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer"
```

- Update composer dependencies (from the host) :

```
docker run --rm -u $(id -u):$(id -g) -v $PWD:/app composer:2 sh -c "composer config --global platform.php 8.0.30 && composer update"
```

- Connect to container : `docker exec -it paginator-php bash`

Once connected to the container you can :

- Run test : `composer test`
