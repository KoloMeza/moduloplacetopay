# Magento 2 Gateway to PlacetoPay

[PlacetoPay][link-placetopay] Plugin Payment for [Magento 2][link-magento] Stable

## Prerequisites
- `php` ^7.1
- `ext-bcmath`
- `ext-ctype`
- `ext-curl`
- `ext-gd`
- `ext-hash`
- `ext-iconv`
- `ext-intl`
- `ext-mbstring`
- `ext-openssl`
- `ext-pdo_mysql`
- `ext-xml`
- `ext-soap`
- `ext-spl`
- `ext-xsl`
- `ext-zip`
- `lib-libxml`
- `composer` @latest
- `database`
    - `MySQL` 5.7
    - `MariaDB` 10.*
- `web server`
    - `apache` 2.2 or 2.4 with mod_rewrite and mod_versions
    - `nginx` 1.*
    
## Compatibility Version

| Magento | Plugin   | Comments       |
|------------|----------|----------------|
| 2.x.x      | ~1.8.0   | From 2.0.x  |

View releases [here][link-releases]

## Manual Installation

Create `PlacetoPay\Payments` folder (this is required, with this name)

```bash
mkdir /var/www/html/app/code/PlacetoPay/Payments
```

Clone Project in modules
 
```bash
git clone https://github.com/placetopay-org/magento2-placetopay.git /var/www/html/app/code/PlacetoPay/Payments
```

Set permissions and install dependencies with composer

```bash
cd /var/www/html \
    && composer require dnetix/redirection \
    && cd app/code/PlacetoPay/Payments \
    && sudo setfacl -dR -m u:www-data:rwX -m u:`whoami`:rwX `pwd` \
    && sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX `pwd`
```

Set up the module by running the following commands:

```bash
cd /var/www/html \
    && bin/magento setup:upgrade \
    && bin/magento cache:flush \
    && bin/magento cache:clean
```

If you run Magento in production mode, you also must compile and deploy the module’s static files:

```bash
cd /var/www/html \
    && bin/magento setup:upgrade \
    && bin/magento setup:di:compile \
    && bin/magento setup:static-content:deploy \
    && bin/magento cache:flush \
    && bin/magento cache:clean
```

If for some reasons, the language not show in spanish, run these commands:

```bash
cd /var/www/html \
    && bin/magento setup:static-content:deploy es_ES \
    && bin/magento setup:static-content:deploy es_CO \
    && bin/magento setup:static-content:deploy -f \
    && bin/magento cache:flush \
    && bin/magento cache:clean
```

## Composer Installation

Set up the module by running the following commands:

```bash
cd /var/www/html \
    && composer require placetopay/magento2-module-payments \
    && bin/magento module:enable PlacetoPay_Payments --clear-static-content \
    && bin/magento setup:upgrade \
    && bin/magento cache:flush \
    && bin/magento cache:clean
```

If you run Magento in production mode, you also must compile and deploy the module’s static files:

```bash
cd /var/www/html \
    && bin/magento setup:di:compile \
    && bin/magento setup:static-content:deploy \
    && bin/magento cache:flush \
    && bin/magento cache:clean
```

If for some reasons, the language not show in spanish, run these commands:

```bash
cd /var/www/html \
    && bin/magento setup:static-content:deploy es_ES \
    && bin/magento setup:static-content:deploy es_CO \
    && bin/magento setup:static-content:deploy -f \
    && bin/magento cache:flush \
    && bin/magento cache:clean
```

## Docker Installation
First, you must edit the auth.json, follow the next instructions:

```bash
cd /path/to/magento/.docker/magento-{version}
cp auth.json.example auth.json
```

For install magento 2, just exec this command in terminal, make sure you can execute make commands 
 
```bash
cd /var/www/html/
make build-magento-{version}
```

Then... (Please wait few minutes, while install ALL and load Apache :D to continue), you can go to
 
- [store](http://localhost)
- [admin](http://localhost/admin)

If you want, go to /etc/hosts file and add the next line:

```bash
sudo vim /etc/hosts
add -> 127.0.0.1 magento.test
```

__Magento 2 Admin Access__
 
- user: admin@admin.com
- password: Admin12*

__MySQL Access__

- user: root
- password: root
- database: magento

See details in `docker-compose.yml` file or run `make config` command

if you wat to change the users and passwords, edit the env file. 

### Customize docker installation

Default versions

- Magento: 2.3.3
- PHP: 7.2
- MySQL: 5.6.23

You can change versions in `.docker/Dockerfile`

```bash
MAGENTO_VERSION
```

If you find an error in the docker installation process, you must verify and update the github-oauth in auth.json, to do so, you must log in your github personal account and go to the settings, then click on the developer settings option and generate a personal access token. 

## Quality

During package development I try as best as possible to embrace good design and development practices, to help ensure that this package is as good as it can
be. My checklist for package development includes:

- Be fully [PSR1][link-psr-1], [PSR2][link-psr-2], and [PSR4][link-psr-1] compliant.
- Include comprehensive documentation in README.md.
- Provide an up-to-date CHANGELOG.md which adheres to the format outlined
    at [keepachangelog][link-keepachangelog].
- Have no [phpcs][link-phpcs] warnings throughout all code, use `composer test` command.

[link-placetopay]: https://www.placetopay.com
[link-magento]: https://magento.com
[link-releases]: https://github.com/placetopay-org/magento2-placetopay/releases
[link-psr-1]: https://www.php-fig.org/psr/psr-1/
[link-psr-2]: https://www.php-fig.org/psr/psr-2/
[link-psr-4]: https://www.php-fig.org/psr/psr-4/
[link-keepachangelog]: https://keepachangelog.com
[link-phpcs]: http://pear.php.net/package/PHP_CodeSniffer
