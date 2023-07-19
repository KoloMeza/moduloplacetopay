# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.10.0] - 2023-06-02

### Removed

- Remove setup for payment methods (set in WC please)

## [1.9.5] - 2023-05-23

### Updated

- Update syntax to support PHP 8.2 (from PHP 7.2+ for now)
    * It requires enable in Magento: composer require alejociro/redirection

## [1.9.4] - 2023-05-08

### Updated

- Notification resolve payments with plugin information

## [1.9.3] - 2023-04-12

### Fixed

- Cron job resolve only payments payed using this plugin

## [1.9.2] - 2023-03-28

### Fixed

- Resolve base price of shipping taxes

## [1.9.1] - 2023-03-17

### Added
- Add UY endopoints
- Add PA country option (CO endpoints)

### Updated
- Update dependency version

## [1.9.0] - 2023-03-07

### Changed
- Downgrade php version to support php 7.2 to 7.4

### Fixed
- Fix resolve refunded payment and return view

### Added
- Honduras and Belize endpoints

## [1.8.11] - 2022-05-09

### Added
- Headers to process pending orders (cronjob)

## [1.8.10] - 2022-04-27

### Added
- Default image for chile
- Translate description and title for payment button

### Updated
- dnetix/redirection package

### Changed
- Chile test endpoint

## [1.8.9] - 2022-03-24

### Changed
- Ecuador test endpoint

## [1.8.8] - 2021-09-17

### Added
- Puerto Rico endpoints
- Base Taxes to transaction request

## [1.8.7] - 2021-09-02

### Fixed
- Remove translate from gateway request

## [1.8.6] - 2021-07-31

### Updated

- Changed chile endpoints
- dnetix/redirection package

### Fixed

- Static url was adding a slash (/) when searching for the image

## [1.8.5] - 2021-07-23

### Fixed

- Override discount to string

## [1.8.4] - 2021-07-22

### Fixed

- Set discount value to string

## [1.8.3] - 2021-07-01

### Fixed

- Remove get order repository from payment method and replace with search criteria

## [1.8.2] - 2021-06-30

### Updated

- Remove paypal dependency on Fieldset

## [1.8.1] - 2021-06-17

### Added

- Chile language pack

## [1.8.0] - 2021-05-05

### Added

- Support to Chile country
- Custom payment url
- Custom image

## [1.7.7] - 2021-02-25

### Added
- Extra payment methods

### Updated
- Redirection package

## [1.7.6] - 2020-12-18

### Added
- Support to multiples stores in the same magento instance

### Fixed
- Get return url from current store for multiples stores
- Show current notification url on admin panel for multiples stores configuration

## [1.7.5] - 2020-12-17

### Added
- Custom placetopay logger

### Fixed
- Error when returning to view from checkout

## [1.7.4] - 2020-11-11

### Changed
- Brand name in views.

## [1.7.3] - 2020-09-14

### Added
- Support to php 7.4
- Costa Rica to Countries list

### Changed
- Payment gateway production endpoint

## [1.7.2] - 2020-08-14

### Added
- Success custom page.

### Fixed
- Return page bug for magento 2.0 / 2.1 / 2.2

## [1.7.1] - 2020-08-12

### Added
- Support magento 2.0 / 2.1 / 2.2
- Optional parameters in redirection request method.

## [1.7.0] - 2020-06-17

### Changed
- Using magento success info block instead of overriding success default page.
- Text translations for failure texts and others.

### Added
- Failure custom page.
- Checkbox for terms and conditions in a checkout.

## [1.6.2] - 2020-05-29

### Changed
- Merged list of taxes by key / value.

## [1.6.1] - 2020-02-26

### Added
- Add email after payment success option.

### Changed
- Apply php-cs-fixer.
- Changed payment method icons list.

## [1.5.3] - 2020-01-21

### Changed
- Set hyperlink to brand image in the frontend.

## [1.5.2] - 2020-01-21

### Changed
- Refactor string in cron log.

### Fixed
- Refactor parse person from address.

## [1.5.1] - 2020-01-20

### Changed
- Add tax parsing for different countries.

## [1.5.0] - 2020-01-20

### Changed
- Add failure, success, pending redirect views to checkout.

### Fixed
- Remove mobile from request.

## [1.2.1] - 2019-12-13

### Changed
- Add minimum stable version to composer

## [1.2.0] - 2019-12-04

### Fixed
- Stable php and magento 2 version in Dockerfile

### Changed
- Add support to php versions >= 7.1

## [1.1.2] - 2019-11-21

### Fixed
- Bug in checkout payment view
- Remove logger in payment checkout method.

### Changed
- Updated dependency redirection from 0.6.1 to 1.0.0
- Update translation changing P2P -> message

## [1.0.0] - 2019-11-20

### Added
- First release

[Unreleased]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.10...HEAD
[1.8.10]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.9...1.8.10
[1.8.9]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.8...1.8.9
[1.8.8]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.7...1.8.8
[1.8.7]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.6...1.8.7
[1.8.6]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.5...1.8.6
[1.8.5]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.4...1.8.5
[1.8.4]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.3...1.8.4
[1.8.3]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.2...1.8.3
[1.8.2]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.1...1.8.2
[1.8.1]: https://github.com/placetopay-org/magento2-placetopay/compare/1.8.0...1.8.1
[1.8.0]: https://github.com/placetopay-org/magento2-placetopay/compare/1.7.7...1.8.0
[1.7.7]: https://github.com/placetopay-org/magento2-placetopay/compare/1.7.6...1.7.7
[1.7.6]: https://github.com/placetopay-org/magento2-placetopay/compare/1.7.5...1.7.6
[1.7.5]: https://github.com/placetopay-org/magento2-placetopay/compare/1.7.4...1.7.5
[1.7.4]: https://github.com/placetopay-org/magento2-placetopay/compare/1.7.3...1.7.4
[1.7.3]: https://github.com/placetopay-org/magento2-placetopay/compare/1.7.2...1.7.3
[1.7.2]: https://github.com/placetopay-org/magento2-placetopay/compare/1.7.1...1.7.2
[1.7.1]: https://github.com/placetopay-org/magento2-placetopay/compare/1.7.0...1.7.1
[1.7.0]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.6.2...1.7.0
[1.6.2]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.6.1...v1.6.2
[1.6.1]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.5.3...v1.6.1
[1.5.3]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.5.2...v1.5.3
[1.5.2]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.5.1...v1.5.2
[1.5.1]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.5.0...v1.5.1
[1.5.0]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.4.0...v1.5.0
[1.4.0]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.2.1...v1.4.0
[1.2.1]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.1.2...v1.2.0
[1.1.2]: https://github.com/placetopay-org/magento2-placetopay/compare/v1.0.0...v1.1.2
[1.0.0]: https://github.com/placetopay-org/magento2-placetopay/releases/tag/v1.0.0
