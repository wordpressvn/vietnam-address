# Vietnam Address
## Installation
Install `composer`
```properties
composer require wordpressvn/vietnam-address
```
## Usage
```
require 'vendor/autoload.php';

use WPVNTeam\VietnamAddressAPI\Address;

echo "<pre>";

print_r(Address::getProvinces());

Address::setSchema(['name', 'type']);

print_r(Address::getProvinces(['01', 87, 12]));

print_r(Address::getProvince('01'));

print_r(Address::getDistrictsByProvinceId('01'));

print_r(Address::getDistrict('009'));

print_r(Address::getWardsByDistrictId('009'));

print_r(Address::getWard('009', '00346'));

print_r(Address::getDistrictsByProvinceName('Tây Ninh'));

print_r(Address::getDistrictsByProvinceName('tay-ninh', 'slug'));

print_r(Address::getWardsByDistrictName('Tân Biên, Tây Ninh'));

print_r(Address::getWardsByDistrictName('Huyện Tân Biên, Tỉnh Tây Ninh', 'name_with_type'));
```
