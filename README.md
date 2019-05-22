[![Build Status](https://travis-ci.org/EmicoEcommerce/Magento2Tweakwise.svg?branch=master)](https://travis-ci.org/EmicoEcommerce/Magento2Tweakwise)
[![Code Climate](https://codeclimate.com/github/EmicoEcommerce/Magento2Tweakwise.png)](https://codeclimate.com/github/EmicoEcommerce/Magento2Tweakwise)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/a273bc8c5317438c9d18c6f2c2c67c3f)](https://www.codacy.com/app/Fgruntjes/Magento2Tweakwise?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=EmicoEcommerce/Magento2Tweakwise&amp;utm_campaign=Badge_Grade)

## Installation
Install package using composer
```sh
composer require emico/tweakwise
```

Install package using zip file
```sh
Extract tweakwise-export.zip src folder to app/code/Emico/TweakwiseExport/
```

Enable module(s) and run installers
```sh
php bin/magento module:enable Emico_TweakwiseExport Emico_Tweakwise
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## Configuration
When the extension is installed it is disabled by default. There are three different parts which can be enabled separately. Configuration can be found at Stores -> Configuration -> Catalog -> Tweakwise.

## Events
Currently there are no events documented, this will be done in the coming version(s).
