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

First, run the following `artisan` command from the root of your project. This
will publish the package configuration file.

```bash
php artisan vendor:publish --provider="LangleyFoxall\XeroLaravel\Providers\XeroLaravelServiceProvider"
```

You now need to populate the `config/xero-laravel-lf.php` file with the 
credentials for your Xero app(s). You can create apps and find the
required credentials in the [My Apps](https://developer.xero.com/myapps/) 
section of your Xero account.

If you only intend to use one private Xero app, the standard configuration 
file should be sufficient. All you will need to do is add the following 
variables to your `.env` file and place the `privatekey.pem` file in 
`storage/app/xero-key-pair/`.

```
XERO_CONSUMER_KEY=XXXXXXIXHKXXXXXXST0TU44ZXXXXXX
XERO_CONSUMER_SECRET=XXXXXXBNNUXXXXXXGWAJNFOUXXXXXX
```

*Note: This library currently only supports private Xero apps, designed for 
internal use by your organisation.*

## Usage

To use Xero Laravel, you first need to get retrieve an instance of your Xero
app. This can be done as shown below.

```php
$xero = (new Xero())->app();            # To use the 'default' app in the config file
$xero = (new Xero())->app('foobar');    # To use a custom app called 'foobar' in the config file
```

You can then immediately access Xero data using Eloquent-like syntax. The 
following code snippet shows the available syntax. When multiple results 
are returned from the API they will be returned as Laravel Collection.

```
# Retrieve all contacts
$contacts = $xero->contacts()->get();                               
$contacts = $xero->contacts;

# Retrieve contacts filtered by name
$contacts = $xero->contacts()->where('Name', 'Bank West')->get();

# Retrieve an individual contact fitered by name
$contact = $xero->contacts()->where('Name', 'Bank West')->first();
```

### Available relationship

The list below shows all available relatioships that can be used to access 
data related to your Xero application (e.g. `$xero->relationshipName`). 

*Note: Some of these relationships may not be available if the related 
service(s) are not enabled for your Xero account.*

```php
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

