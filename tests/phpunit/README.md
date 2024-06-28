## **Phpunit para qlowcode**

Sobre la instalacion de phpunit: https://moodledev.io/general/development/tools/phpunit

Para sacar el coverage utilizar el siguiente comando:

`vendor/bin/phpunit --testsuite qtype_qlowcode_testsuite --testdox --coverage-html reports/$(date +'%Y%m%d')`