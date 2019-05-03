# module-save-cart
Magento 2 Save Cart extension

Installation
============

With [Composer](http://getcomposer.org/):

1. Add the repository to composer.json file

```
"repositories": [
    {
        "url": "https://github.com/olof/module-save-cart.git",
        "type": "git"
    }
]
```

2. Extension install

```
$ composer require lof/module-save-cart
$ php bin/magento setup:upgrade
$ php bin/magento cache:clean
```
