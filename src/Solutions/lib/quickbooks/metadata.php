<?php
/*********************************************************************************
 * This file is part of Myddleware.

 * @package Myddleware
 * @copyright Copyright (C) 2024 - Accellier Limited - support@accellier.com
 * @link http://www.myddleware.com

 This file is part of Myddleware.

 Myddleware is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Myddleware is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Myddleware.  If not, see <http://www.gnu.org/licenses/>.
*********************************************************************************/

$moduleFields = [
    'Customer' => [
        'Id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DisplayName' => ['label' => 'DisplayName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'CompanyName' => ['label' => 'CompanyName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'CurrencyRef' => ['label' => 'Currency', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'GivenName' => ['label' => 'GivenName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'FullyQualifiedName' => ['label' => 'FullyQualifiedName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'FamilyName' => ['label' => 'FamilyName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],

        'PrimaryPhone' => ['label' => 'PrimaryPhone', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Mobile' => ['label' => 'Mobile', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Fax' => ['label' => 'Fax', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'PrimaryEmailAddr' => ['label' => 'PrimaryEmailAddr', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Active' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            '0' => 'Closed',
            '1' => 'Open'
        ]],
        'Taxable' => ['label' => 'Taxable', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            '0' => 'No',
            '1' => 'Yes'
        ]],
        'Line1' => ['label' => 'Line1', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'City' => ['label' => 'City', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'CountrySubDivisionCode' => ['label' => 'CountrySubDivisionCode', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'PostalCode' => ['label' => 'PostalCode', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Country' => ['label' => 'Country', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'AlternatePhone' => ['label' => 'AlternatePhone', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'customer' => ['label' => 'Customer', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'vendor' => ['label' => 'Vendor', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'Notes' => ['label' => 'Notes', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'Vendor' => [
        'Id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DisplayName' => ['label' => 'DisplayName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'CompanyName' => ['label' => 'CompanyName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'CurrencyRef' => ['label' => 'Currency', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'GivenName' => ['label' => 'GivenName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'FullyQualifiedName' => ['label' => 'FullyQualifiedName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'FamilyName' => ['label' => 'FamilyName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],

        'PrimaryPhone' => ['label' => 'PrimaryPhone', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Mobile' => ['label' => 'Mobile', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Fax' => ['label' => 'Fax', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'PrimaryEmailAddr' => ['label' => 'PrimaryEmailAddr', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Active' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            '0' => 'Closed',
            '1' => 'Open'
        ]],
        'Taxable' => ['label' => 'Taxable', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            '0' => 'No',
            '1' => 'Yes'
        ]],
        'Line1' => ['label' => 'Line1', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'City' => ['label' => 'City', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'CountrySubDivisionCode' => ['label' => 'CountrySubDivisionCode', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'PostalCode' => ['label' => 'PostalCode', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Country' => ['label' => 'Country', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'AlternatePhone' => ['label' => 'AlternatePhone', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'customer' => ['label' => 'Customer', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'vendor' => ['label' => 'Vendor', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'Notes' => ['label' => 'Notes', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        ],
    'Item' => [
        'Id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Name' => ['label' => 'Name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'Active' => ['label' => 'Active', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'FullyQualifiedName' => ['label' => 'FullyQualifiedName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'Taxable' => ['label' => 'Taxable', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'UnitPrice' => ['label' => 'UnitPrice', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'Type' => ['label' => 'Type', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            'NonInventory' => 'Non Inventory',
            'Inventory'    => 'Inventory',
            'Service'      => 'Service'
        ]],
        'IncomeAccountRefname' => ['label' => 'Income Account Ref Name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'IncomeAccountRefvalue' => ['label' => 'Income Account Ref Value', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'ExpenseAccountRefname' => ['label' => 'Expense Account Ref Name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'ExpenseAccountRefvalue' => ['label' => 'Expense Account Ref Value', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'Description' => ['label' => 'Description', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'PurchaseDesc' => ['label' => 'PurchaseDesc', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'service' => ['label' => 'Service', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'forsell' => ['label' => 'For Sell', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'forPurchase' => ['label' => 'For Purchase', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'CostOfPurchase' => ['label' => 'Purchase Cost', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'Invoice' => [
        'Id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Line' => ['label' => 'Line', 'type' => 'text', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'LinesDetailType' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            'SalesItemLineDetail' => 'SalesItemLineDetail'
        ]],
        'LineTaxCodeRef' => ['label' => 'LineTaxCodeRef', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'CurrencyRef' => ['label' => 'CurrencyRef', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'ExchangeRate' => ['label' => 'ExchangeRates', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Multicurrency' => ['label' => 'Multicurrency', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0, 'option' => [
            'true' => 'true',
            'false' => 'false'
        ]],
        'DiscountAccount' => ['label' => 'DiscountAccount', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0, 'option' => [
            'true' => 'true',
            'false' => 'false'
        ]],
        'ApplyTaxAfterDiscount' => ['label' => 'ApplyTaxAfterDiscount', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'CustomerRef' => ['label' => 'CustomerRef', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'BillEmail' => ['label' => 'BillEmail', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'TxnDate' => ['label' => 'TxnDate', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DueDate' => ['label' => 'DueDate', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DocNumber' => ['label' => 'DocNumber', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'status' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'Bill' => [
        'Id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Line' => ['label' => 'Line', 'type' => 'text', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'LinesDetailType' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            'ItemBasedExpenseLineDetail' => 'ItemBasedExpenseLineDetail'
        ]],
        'LineTaxCodeRef' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'VendorRef' => ['label' => 'VendorRef', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'BillEmail' => ['label' => 'BillEmail', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'TxnDate' => ['label' => 'TxnDate', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DueDate' => ['label' => 'DueDate', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DocNumber' => ['label' => 'DocNumber', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'status' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'InvoicePaid' => [
        'Id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'TotalAmt' => ['label' => 'TotalAmt', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Balance' => ['label' => 'Balance', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DueDate' => ['label' => 'DueDate', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'CustomerRefName' => ['label' => 'CustomerRefName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DocNumber' => ['label' => 'DocNumber', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'LastUpdatedTime' => ['label' => 'LastUpdatedTime', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'BillPaid' => [
        'Id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'TotalAmt' => ['label' => 'TotalAmt', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'Balance' => ['label' => 'Balance', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DueDate' => ['label' => 'DueDate', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'CustomerRefName' => ['label' => 'CustomerRefName', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'DocNumber' => ['label' => 'DocNumber', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'LastUpdatedTime' => ['label' => 'LastUpdatedTime', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ]
];

