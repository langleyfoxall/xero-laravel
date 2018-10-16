# 💸 Xero Laravel

Xero Laravel allows developers to access the Xero accounting system using 
an Eloquent-like syntax.

## Installation

Xero Laravel can be easily installed using Composer. Just run the following 
command from the root of your project.

```bash
composer require langleyfoxall/xero-laravel
```

If you have never used the Composer dependency manager before, head 
to the [Composer website](https://getcomposer.org/) for more information 
on how to get started.

## Setup

Run the following `artisan` command from the root of your project. This
will publish the package configuration file.

```bash
php artisan vendor:publish --provider="LangleyFoxall\XeroLaravel\Providers\XeroLaravelServiceProvider"
```

TO DO: Populate `xero-laravel-lf.php` config file.

## Usage

TO DO

```
# Supported Syntax:
$xero = (new Xero())->app();

$contacts = $xero->contacts()->get();                               
$contacts = $xero->contacts;
$contacts = $xero->contacts()->where('Name', 'Bank West')->get();
$contact = $xero->contacts()->where('Name', 'Bank West')->first();

```