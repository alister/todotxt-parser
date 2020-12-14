# alister/todotxt-parser


<!--  [![Source Code][badge-source]][source]-->
[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Build Status][badge-build]][build]
<!--  [![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]-->


[badge-source]: http://img.shields.io/badge/source-alister/todotxt-parser-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/packagist/v/alister/todotxt-parser.svg?style=flat-square&label=release
[badge-license]: https://img.shields.io/packagist/l/alister/todotxt-parser.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/alister/todotxt-parser.svg?style=flat-square
[badge-build]: https://github.com/alister/todotxt-parser/workflows/Unit%20Tests/badge.svg
[badge-coverage]: https://img.shields.io/coveralls/github/alister/todotxt-parser/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/alister/todotxt-parser.svg?style=flat-square&colorB=mediumvioletred

[source]: https://github.com/alister/todotxt-parser
[packagist]: https://packagist.org/packages/alister/todotxt-parser
[license]: https://github.com/alister/todotxt-parser/blob/master/LICENSE
[php]: https://php.net
[build]: https://travis-ci.org/alister/todotxt-parser
[coverage]: https://coveralls.io/r/alister/todotxt-parser?branch=master
[downloads]: https://packagist.org/packages/alister/todotxt-parser


parser for the TodoTxt format



## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require alister/todotxt-parser
```

<!--
## Usage

Provide a brief description or short example of how to use this library.
If you need to provide more detailed examples, use the `docs/` directory
and provide a link here to the documentation.

``` php
use Alister\Todotxt\Parser\Example;

$example = new Example();
echo $example->greet('fellow human');
```
-->


## Contributing

Contributions are welcome! Before contributing to this project, familiarize
yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

To develop this project, you will need [PHP](https://www.php.net) 7.4 or greater,
[Composer](https://getcomposer.org), [Node.js](https://nodejs.org/), and
[Yarn](https://yarnpkg.com).

After cloning this repository locally, execute the following commands:

``` bash
cd /path/to/repository
composer install
yarn install
```

Now, you are ready to develop!

### Tooling

This project uses [Husky](https://github.com/typicode/husky) and
[lint-staged](https://github.com/okonet/lint-staged) to validate all staged
changes prior to commit.

#### Composer Commands

To see all the commands available in the project `td` namespace for
Composer, type:

``` bash
composer list td
```

##### Composer Command Autocompletion

If you'd like to have Composer command auto-completion, you may use
[bamarni/symfony-console-autocomplete](https://github.com/bamarni/symfony-console-autocomplete).
Install it globally with Composer:

``` bash
composer global require bamarni/symfony-console-autocomplete
```

Then, in your shell configuration file — usually `~/.bash_profile` or `~/.zshrc`,
but it could be different depending on your settings — ensure that your global
Composer `bin` directory is in your `PATH`, and evaluate the
`symfony-autocomplete` command. This will look like this:

``` bash
export PATH="$(composer config home)/vendor/bin:$PATH"
eval "$(symfony-autocomplete)"
```

Now, you can use the `tab` key to auto-complete Composer commands:

``` bash
composer td:[TAB][TAB]
```

#### Coding Standards

This project follows a superset of [PSR-12](https://www.php-fig.org/psr/psr-12/)
coding standards, enforced by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).
The project PHP_CodeSniffer configuration may be found in `phpcs.xml.dist`.

lint-staged will run PHP_CodeSniffer before committing. It will attempt to fix
any errors it can, and it will reject the commit if there are any un-fixable
issues. Many issues can be fixed automatically and will be done so pre-commit.

You may lint the entire codebase using PHP_CodeSniffer with the following
commands:

``` bash
# Lint
composer td:lint

# Lint and autofix
composer td:lint:fix
```

#### Static Analysis

This project uses a combination of [PHPStan](https://github.com/phpstan/phpstan)
and [Psalm](https://github.com/vimeo/psalm) to provide static analysis of PHP
code. Configurations for these are in `phpstan.neon.dist` and `psalm.xml`,
respectively.

lint-staged will run PHPStan and Psalm before committing. The pre-commit hook
does not attempt to fix any static analysis errors. Instead, the commit will
fail, and you must fix the errors manually.

You may run static analysis manually across the whole codebase with the
following command:

``` bash
# Static analysis
composer td:analyze
```

### Project Structure

This project uses [pds/skeleton](https://github.com/php-pds/skeleton) as its
base folder structure and layout.

| Name              | Description                                    |
| ------------------| ---------------------------------------------- |
| **bin/**          | Commands and scripts for this project          |
| **build/**        | Cache, logs, reports, etc. for project builds  |
| **docs/**         | Project-specific documentation                 |
| **resources/**    | Additional resources for this project          |
| **src/**          | Project library and application source code    |
| **tests/**        | Tests for this project                         |





## Copyright and License

The alister/todotxt-parser library is copyright © [Alister Bulman](https://abulman.co.uk/)
and licensed for use under the terms of the
MIT License (MIT). Please see [LICENSE](LICENSE) for more information.


