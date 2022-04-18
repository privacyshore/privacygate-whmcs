## PrivacyGate module for WHMCS, version 1.2
**Note: This repository is not actively maintained.**


### About PrivacyGate:
- PrivacyGate is a new service that enables merchants to accept multiple cryptocurrencies directly into a user-controlled wallet.
This module allows you to integrate PrivacyGate easily on your platform.
Additional information can be found at:
https://dash.privacygate.io/

### Requirements:
- Working WHMCS installation (tested up to version 7.4.2).
- PrivacyGate account, you can register for free at https://dash.privacygate.io/signup

### Installation:
- Clone current repository and run `composer install` or download build from [releases page](https://github.com/privacyshore/privacygate-whmcs/releases) and unzip
- Copy modules folder to the root folder of your WHMCS installation.
- Activate the PrivacyGate module in your WHMCS admin panel (Setup -> Payments -> Payment Gateways -> All Payment Gateways).
- Look for "PrivacyGate" button and click on.
- Log into your PrivacyGate Dashboard and go to "Settings" section, copy the Api Key and Webhook Shared Secret from your account and paste them into the corresponding fields at the module's setup page on your WHMCS site.
- Copy the "Webhook subscription url" from your PrivacyGate's module setup and paste it into the "Webhook Url" field at the "Notifications" section of your PrivacyGate dashboard, then save the changes.
- Click on "Save Changes" in your WHMCS site.

### Integrate with other e-commerce platforms
[PrivacyGate Integrations](https://dash.privacygate.io/docs)
