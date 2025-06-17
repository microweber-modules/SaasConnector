# SaaS Connector Module

This module provides SaaS connectivity features for Microweber CMS.

## Installation

1. Install via composer:
```bash
composer require microweber-modules/saas-connector
```

2. Configure the module by setting up your environment variables:
```env
SAAS_MANAGER_URL=your-saas-manager-url
```

## Features

- Template setup wizard
- SaaS authentication integration
- Website activation management
- Ads bar for inactive websites

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Modules\SaasConnector\Providers\SaasConnectorServiceProvider" --tag="config"
```

## Usage

The module provides several features:

1. Template Setup Wizard:
   - Access via admin panel
   - Choose and install templates

2. Website Activation:
   - Shows activation bar for inactive websites
   - Provides login with token functionality

3. Integration Functions:
   - `getSaasWebsiteInfoFromServer()`
   - `getWebsiteManagerUrl()`
   - `validateLoginWithToken($token)`
