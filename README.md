Тестовое задание
========================

[![Travis](https://api.travis-ci.org/ggcstop/test_schedule.svg)]()
[![Scrutinizer](https://img.shields.io/scrutinizer/g/ggcstop/test_schedule.svg)]()

Реализовано на фреймворке Symfony 3

Зависимости:
* php (писал на системе с 7.0.9, но должно работать с версиями 5.5.9+)
* mysql
* composer
* npm
* bower
* gulp (стабильная 3.9.1 версия, у 4 другой синтаксис)

Для развертывания последовательно выполнить:

* git clone https://github.com/ggcstop/test_schedule.git
* cd test_schedule
* composer install
* npm install
* bower install
* gulp build
* php bin/console doctrine:database:create
* php bin/console doctrine:schema:create
* импортировать БД или сгенерировать тетовые данные командой php bin/console doctrine:fixtures:load


