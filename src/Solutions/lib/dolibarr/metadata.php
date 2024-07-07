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
    'societe' => [
        'id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'name' => ['label' => 'Third-party name	', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'name_alias' => ['label' => 'Alias name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'client' => ['label' => 'Prospect / Customer', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            '2' => 'Prospect',
            '3' => 'Prospect / Customer',
            '4' => 'Customer',
            '0' => 'Not prospect, nor customer'
        ]],
        'fournisseur' => ['label' => 'Vendor', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            '1' => 'Yes',
            '2' => 'No'
        ]],
        'code_client' => ['label' => 'Customer Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'code_fournisseur' => ['label' => 'Vendor Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'status' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0, 'option' => [
            '0' => 'Closed',
            '1' => 'Open'
        ]],
        'address' => ['label' => 'Address', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'zip' => ['label' => 'Zip', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'town' => ['label' => 'Town', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'country' => ['label' => 'Country', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'state' => ['label' => 'State', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'currency' => ['label' => 'Currency', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'email' => ['label' => 'Email', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'phone' => ['label' => 'Phone', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fax' => ['label' => 'Fax', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'skype' => ['label' => 'Skype', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'twitter' => ['label' => 'Twitter', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'facebook' => ['label' => 'Facebook', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'linkedin' => ['label' => 'Linkedin', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'url' => ['label' => 'Url', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_creation' => ['label' => 'Date Creation', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_modification' => ['label' => 'Date Modification', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'country_code' => ['label' => 'Country Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        
    ],
    'product' => [
        'id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'ref' => ['label' => 'Ref', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'description' => ['label' => 'Description', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'label' => ['label' => 'Label', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'type' => ['label' => 'Type', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0, 'option' => [
            '0' => 'Product',
            '1' => 'Service'
        ]],
        'price_base_type' => ['label' => 'Price Type', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0,  'option' => [
            'TTC' => 'Without Tax',
            'HT' => 'With Tax'
        ]],
        'price' => ['label' => 'Price', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'status' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0, 'option' => [
            '1' => 'For Sale',
            '0' => 'Not For Sale'
        ]],
        'status_buy' => ['label' => 'Status Buy', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0, 'option' => [
            '0' => 'Not for Purchase',
            '1' => 'For Purchase0'
        ]],
        'date_creation' => ['label' => 'Date Creation', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_modification' => ['label' => 'Date Modification', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'invoices' => [
        'id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'ref' => ['label' => 'Reference', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'lines' => ['label' => 'Lines', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_name' => ['label' => 'Third Party Name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_name_alias' => ['label' => 'Third Party Alias Name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_code_client' => ['label' => 'Third Party Client Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_code_fournisseur' => ['label' => 'Third Party Vendor code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_email' => ['label' => 'Third Party Email', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_timestamp' => ['label' => 'Invoice Date in timestamp format', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_lim_reglement_timestamp' => ['label' => 'Invoice due date in timestamp format', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date' => ['label' => 'Invoice date', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_lim_reglement' => ['label' => 'Invoice Due Date', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'status' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_creation' => ['label' => 'Date Creation', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_modification' => ['label' => 'Date Modification', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'supplierinvoices' => [
        'id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'ref' => ['label' => 'Reference', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'lines' => ['label' => 'Lines', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_name' => ['label' => 'Third Party Name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_name_alias' => ['label' => 'Third Party Alias Name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_code_client' => ['label' => 'Third Party Client Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_code_fournisseur' => ['label' => 'Third Party Vendor code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_email' => ['label' => 'Third Party Email', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_timestamp' => ['label' => 'Invoice Date in timestamp format', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_echeance_timestamp' => ['label' => 'Invoice due date in timestamp format', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date' => ['label' => 'Invoice date', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_echeance' => ['label' => 'Invoice Due Date', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'status' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_creation' => ['label' => 'Date Creation', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_modification' => ['label' => 'Date Modification', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'invoicesettopaid' => [
        'id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'ref' => ['label' => 'Ref', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'close_code' => ['label' => 'close_code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'close_note' => ['label' => 'close_note', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'balance'    => ['label' => 'Remaining Balance', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
    ],
    'supllierinvoicesettopaid' => [
        'id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'ref' => ['label' => 'Ref', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'datepaye' => ['label' => 'close_code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'payment_mode_id' => ['label' => 'close_note', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'closepaidinvoices' => ['label' => 'close_note', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            'yes' => 'yes',
            'no'  => 'no'
        ]],
        'accountid' => ['label' => 'close_note', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'balance'    => ['label' => 'Remaining Balance', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
    ],
    'invoicepayment' => [
        'id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'ref' => ['label' => 'Ref', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'datepaye' => ['label' => 'Payment Date', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'paymentid' => ['label' => 'Payment Id', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'closepaidinvoices' => ['label' => 'close_note', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1, 'option' => [
            'yes' => 'yes',
            'no'  => 'no'
        ]],
        'accountid' => ['label' => 'Account Id', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
        'balance'    => ['label' => 'Remaining Balance', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 1],
    ]
];

