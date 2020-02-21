# 💸 Xero Laravel

Xero Laravel allows developers to access the Xero accounting system using 
an Eloquent-like syntax.

<p align="center">
    <img src="assets/images/xero-laravel-usage.png" />
</p>

<p align="center">
    <a href="https://github.styleci.io/repos/153256469">
        <img src="https://github.styleci.io/repos/153256469/shield?branch=master" alt="StyleCI">
    </a>
    <a href="https://packagist.org/packages/langleyfoxall/xero-laravel/stats">
        <img src="https://img.shields.io/packagist/dt/langleyfoxall/xero-laravel.svg" />
    </a>
</p>

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

First, run the following `artisan` command from the root of your project. This
will publish the package configuration file.

```bash
php artisan vendor:publish --provider="LangleyFoxall\XeroLaravel\Providers\XeroLaravelServiceProvider"
```

You now need to populate the `config/xero-laravel-lf.php` file with the 
credentials for your Xero app(s). You can create apps and find the
required credentials in the [My Apps](https://developer.xero.com/myapps/) 
section of your Xero account.

If you only intend to use one Xero app, the standard configuration 
file should be sufficient. All you will need to do is add the following 
variables to your `.env` file.

```
XERO_TOKEN=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
XERO_TENANT_ID=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

### OAuth2 authentication flow

If multiple users are going to be using your application, or if you need to dynamically fetch the access tokens you
can use the built in authentication flow helper. This handles URI generation and redirects then allowing you to gain access
to the token(s) without any unwanted mutations.

* A [`Illuminate\Http\RedirectResponse`](https://laravel.com/api/6.x/Illuminate/Http/RedirectResponse.html) is returned from `redirect`.
* A [`League\OAuth2\Client\Token\AccessTokenInterface`](https://github.com/thephpleague/oauth2-client/blob/master/src/Token/AccessTokenInterface.php) is returned from `getToken`.

#### Usage

Instantiate `OAuth2` in a controller and pass in a Request, call `redirect` and then `getToken`.

```php
<?php
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use LangleyFoxall\XeroLaravel\OAuth2;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidConfigException;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidOAuth2StateException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class OAuthController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse|void
     * @throws InvalidConfigException
     * @throws InvalidOAuth2StateException
     * @throws IdentityProviderException
     */
    public function __invoke(Request $request)
    {
        $oauth = new OAuth2($request);

        if (!$response = $oauth->redirect()) {
            $token = $oauth->getToken();

            // Deal with token
            dd($token);
        }

        return $response;
    }
}
```

By default environment variables will be used:
```
XERO_CLIENT_ID=
XERO_CLIENT_SECRET=
XERO_REDIRECT_URI=
```

We pre-define the least amount of scopes for the authentication (`openid email profile`) but you can change these
by adding `XERO_SCOPE` to your `.env` file. You can find a list of available scopes [here](https://developer.xero.com/documentation/oauth2/scopes).

If you need to define the `client_id`, `client_secret`, `redirect_uri` or `scope` on the fly you can do so by
calling the following methods before `redirect`:

```php
<?php
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use LangleyFoxall\XeroLaravel\OAuth2;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidConfigException;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidOAuth2StateException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class OAuthController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse|void
     * @throws InvalidConfigException
     * @throws InvalidOAuth2StateException
     * @throws IdentityProviderException
     */
    public function __invoke(Request $request)
    {
        $oauth = new OAuth2($request);

        $oauth
            ->setClientId('XXXX')
            ->setClientSecret('XXXX')
            ->setRedirectUri('XXXX')
            ->setScope('XXXX');

        if (!$response = $oauth->redirect()) {
            $token = $oauth->getToken();

            // Deal with token
            dd($token);
        }

        return $response;
    }
}
```

## Migration from 1.x/OAuth 1a

There is now only one flow for all applications, which is most similar to the legacy Public application. 
All applications now require the OAuth 2 authorisation flow and specific organisations to be authorised 
at runtime, rather than creating certificates during app creation.

Following [this example](https://github.com/calcinai/xero-php#authorization-code-flow) you can generate the 
required token and tenant id.   

For more information on scopes try the [xero documentation](https://developer.xero.com/documentation/oauth2/scopes).  

## Usage

To use Xero Laravel, you first need to get retrieve an instance of your Xero
app. This can be done as shown below.

```php
$xero = (new Xero())->app();            # To use the 'default' app in the config file
$xero = (new Xero())->app('foobar');    # To use a custom app called 'foobar' in the config file
```

Alternately you can use the Xero facade  
*Note this is only for the default config*
```php
use LangleyFoxall\XeroLaravel\Facades\Xero;

# Retrieve all contacts via facade
$contacts = Xero::contacts()->get();

# Retrieve an individual contact by its GUID
$contact =  Xero::contacts()->find('34xxxx6e-7xx5-2xx4-bxx5-6123xxxxea49');
```

You can then immediately access Xero data using Eloquent-like syntax. The 
following code snippet shows the available syntax. When multiple results 
are returned from the API they will be returned as Laravel Collection.

```php
# Retrieve all contacts
$contacts = $xero->contacts()->get();                               
$contacts = $xero->contacts;

# Retrieve contacts filtered by name
$contacts = $xero->contacts()->where('Name', 'Bank West')->get();

# Retrieve an individual contact filtered by name
$contact = $xero->contacts()->where('Name', 'Bank West')->first();

# Retrieve an individual contact by its GUID
$contact = $xero->contacts()->find('34xxxx6e-7xx5-2xx4-bxx5-6123xxxxea49');

# Retrieve multiple contact by their GUIDS
$contacts = $xero->contacts()->find([
    '34xxxx6e-7xx5-2xx4-bxx5-6123xxxxea49',
    '364xxxx7f-2xx3-7xx3-gxx7-6726xxxxhe76',
]);
```

### Available relationships

The list below shows all available relationships that can be used to access 
data related to your Xero application (e.g. `$xero->relationshipName`). 

*Note: Some of these relationships may not be available if the related 
service(s) are not enabled for your Xero account.*

```
accounts
addresses
assetsAssetTypeBookDepreciationSettings
assetsAssetTypes
assetsOverviews
assetsSettings
attachments
bankTransactionBankAccounts
bankTransactionLineItems
bankTransactions
bankTransferFromBankAccounts
bankTransferToBankAccounts
bankTransfers
brandingThemes
contactContactPeople
contactGroups
contacts
creditNoteAllocations
creditNotes
currencies
employees
expenseClaimExpenseClaims
expenseClaims
externalLinks
filesAssociations
filesFiles
filesFolders
filesObjects
invoiceLineItems
invoiceReminders
invoices
itemPurchases
itemSales
items
journalJournalLines
journals
linkedTransactions
manualJournalJournalLines
manualJournals
organisationBills
organisationExternalLinks
organisationPaymentTerms
organisationSales
organisations
overpaymentAllocations
overpaymentLineItems
overpayments
payments
payrollAUEmployeeBankAccounts
payrollAUEmployeeHomeAddresses
payrollAUEmployeeLeaveBalances
payrollAUEmployeeOpeningBalances
payrollAUEmployeePayTemplateDeductionLines
payrollAUEmployeePayTemplateEarningsLines
payrollAUEmployeePayTemplateLeaveLines
payrollAUEmployeePayTemplateReimbursementLines
payrollAUEmployeePayTemplateSuperLines
payrollAUEmployeePayTemplates
payrollAUEmployeeSuperMemberships
payrollAUEmployeeTaxDeclarations
payrollAUEmployees
payrollAULeaveApplicationLeavePeriods
payrollAULeaveApplications
payrollAUPayItemDeductionTypes
payrollAUPayItemEarningsRates
payrollAUPayItemLeaveTypes
payrollAUPayItemReimbursementTypes
payrollAUPayItems
payrollAUPayRuns
payrollAUPayrollCalendars
payrollAUPayslipDeductionLines
payrollAUPayslipEarningsLines
payrollAUPayslipLeaveAccrualLines
payrollAUPayslipLeaveEarningsLines
payrollAUPayslipReimbursementLines
payrollAUPayslipSuperannuationLines
payrollAUPayslipTaxLines
payrollAUPayslipTimesheetEarningsLines
payrollAUPayslips
payrollAUSettingAccounts
payrollAUSettingTrackingCategories
payrollAUSettings
payrollAUSuperFundProducts
payrollAUSuperFundSuperFunds
payrollAUSuperFunds
payrollAUTimesheetTimesheetLines
payrollAUTimesheets
payrollUSEmployeeBankAccounts
payrollUSEmployeeHomeAddresses
payrollUSEmployeeMailingAddresses
payrollUSEmployeeOpeningBalances
payrollUSEmployeePayTemplates
payrollUSEmployeePaymentMethods
payrollUSEmployeeSalaryAndWages
payrollUSEmployeeTimeOffBalances
payrollUSEmployeeWorkLocations
payrollUSEmployees
payrollUSPayItemBenefitTypes
payrollUSPayItemDeductionTypes
payrollUSPayItemEarningsTypes
payrollUSPayItemReimbursementTypes
payrollUSPayItemTimeOffTypes
payrollUSPayItems
payrollUSPayRuns
payrollUSPaySchedules
payrollUSPaystubBenefitLines
payrollUSPaystubDeductionLines
payrollUSPaystubEarningsLines
payrollUSPaystubLeaveEarningsLines
payrollUSPaystubReimbursementLines
payrollUSPaystubTimeOffLines
payrollUSPaystubTimesheetEarningsLines
payrollUSPaystubs
payrollUSSalaryandWages
payrollUSSettingAccounts
payrollUSSettingTrackingCategories
payrollUSSettings
payrollUSTimesheetTimesheetLines
payrollUSTimesheets
payrollUSWorkLocations
phones
prepaymentAllocations
prepaymentLineItems
prepayments
purchaseOrderLineItems
purchaseOrders
receiptLineItems
receipts
repeatingInvoiceLineItems
repeatingInvoiceSchedules
repeatingInvoices
reportBalanceSheets
reportBankStatements
reportBudgetSummaries
reportProfitLosses
reportReports
reportTaxTypes
salesTaxBases
salesTaxPeriods
taxRateTaxComponents
taxRates
taxTypes
trackingCategories
trackingCategoryTrackingOptions
userRoles
users
```

