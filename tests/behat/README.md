
## **Tests de Behat para qlowcode**

Sobre la instalacion de behat: https://git.isyc.com/unimoodle/hybridteaching/-/blob/develop/tests/behat/README.md?ref_type=heads

Para intentar crear alguna pregunta qlow usa el siguiente comando:

`vendor/bin/behat --config /yourmoodledata_behat/behatrun/behat/behat.yml --name="Set external services, settings and create question for qlowcourse" --profile=geckodriver --format=pretty`

Importante
- Cambiar los  Description, URL, API URL y API TOKEN
- Cambiar el email de 'teacher' para asociarlo al usuario que ha creado el workspace deseado.