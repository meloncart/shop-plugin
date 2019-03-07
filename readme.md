# MelonCart for October CMS

MelonCart is a comprehensive and flexible shopping cart plugin for October CMS.

## Requirements

* [RainLab.User](https://octobercms.com/plugin/rainlab-user), [RainLab.Location](https://octobercms.com/plugin/rainlab-location), [RainLab.UserPlus](https://octobercms.com/plugin/rainlab-userplus) plugins
* At least one RainLab.User user in the database

## Installation

* `git clone` to */plugins/meloncart/shop* directory
* `php artisan plugin:refresh MelonCart.Shop`

The database will automatically be seeded with some test data and use the first RainLab.User user found as a customer. 