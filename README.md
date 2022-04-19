# Trellis Mass Reset Customer Password

Adds a new mass action when viewing the customers grid in admin that allows an admin to mass-reset the password of any 
selected customers.

## Installation Instructions
Follow the instructions to install this extension using Composer.

```
TODO: update composer config command: composer config ...
composer require trellis/module-mass-reset-customer-password
bin/magento module:enable --clear-static-content Trellis_MassResetPassword
bin/magento setup:upgrade
bin/magento cache:flush
```

## Configuration
No configuration necessary.

## Usage Instructions

1. Login to Magento Admin.
2. Navigate to Customers > All Customers
3. Select any customers that need a password reset.
4. Select "Reset password" from the Actions dropdown.
5. Click "OK" in the confirmation prompt.

**Expected Result**

The standard Magento password reset email will be sent to any customers that where selected. The standard Magento 
email template contains a link the customer can click to set a new password for their account.