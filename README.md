# Symfony4 multiple projets

This project aims to create multiple projects with symfony using a single Kenerl and Console. In this demo there is an api and a frontal but you could add all you want.

  - PHP 7
  - Symfony 4
  - Symfony Cache Pools
  - Guzzle
  - PHPSTAN
  - PHP CS FIXER

# Features!

  - _Create a virtual Symfony Kernel_
  - _Single Console instance for all projects_
  - _Multiple configurations_
  - _Use this project for any purpose_
 
### Todo
  - _Tests_

### Installation

This project require [PHP](#) 7 to run.

Install the dependencies and devDependencies and start the server.

```sh
$ git clone https://github.com/patrice-blp/symfony4-multiple-projets.git
$ cd symfony4-multiple-projets
$ composer install
```
Run with this command
By default ./bin/console will run **_frontal_** APP

```sh
$ APP_NAME=frontal ./bin/console s:r or ./bin/console s:r
$ APP_NAME=api ./bin/console s:r 
```

And for another projects

```sh
$ APP_NAME=projectName ./bin/console s:r 
```

You can use Env variable **APP_NAME** to avoid writing **this** on the console

For production environments you can follow instructions here
[Symfony deploy](https://symfony.com/doc/current/deployment.html)

### _Development_

Want to contribute? Great!

Please read this **[Contributing](https://github.com/MarcDiethelm/contributing/blob/master/README.md)**

Open your favorite IDE and start coding.

License
----

MIT


**Free Software, Hell Yeah!**

### **Enjoy**
