# NESTOR

*Smart groceries shopping list manager*

---

#### Requirements

- **[PHP](https://www.php.net/)** `7.2.5 or greater`
- **[MySQL](https://www.mysql.com/)** `8.0.19 or greater`
- **[Composer](https://getcomposer.org/)** `1.10.6 or greater`
- **[Yarn](https://yarnpkg.com)** `1.22.4 or greater`
- **[Symfony client](https://symfony.com/download)** `4.15.0 or greater`

---

#### Installation

- create `.env.local` based on `.env` and set values for your local environment.
- `composer install` to get the framework dependencies.
- `yarn install` to get the assets dependencies.
- `php bin/console doctrine:database:create` to create database.
- `php bin/console doctrine:migrations:migrate` to setup database structure.
- `php bin/console doctrines:fixtures:load` to load data.

---

#### Local environment

- `yarn encore dev --watch` to launch webpack.
- `symfony server:start` to launch local server.

---
