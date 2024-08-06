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
            '0' => 'No'
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
        'client' => ['label' => 'Client/Customer', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'multicurrency_code' => ['label' => 'Multicurrency Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'forme_juridique' => ['label' => 'forme_juridique', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'forme_juridique_code' => ['label' => 'forme_juridique_code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'absolute_discount' => ['label' => 'absolute_discount', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'absolute_creditnote' => ['label' => 'absolute_creditnote', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],    
    'societevendor' => [
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
            '0' => 'No'
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
        'client' => ['label' => 'Client/Customer', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'multicurrency_code' => ['label' => 'Multicurrency Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'forme_juridique' => ['label' => 'forme_juridique', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'forme_juridique_code' => ['label' => 'forme_juridique_code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'absolute_discount' => ['label' => 'absolute_discount', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'absolute_creditnote' => ['label' => 'absolute_creditnote', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
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
        'price_ttc' => ['label' => 'Price Inc Tax', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'status' => ['label' => 'Status', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0, 'option' => [
            '1' => 'For Sale',
            '0' => 'Not For Sale'
        ]],
        'status_buy' => ['label' => 'Status Buy', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0, 'option' => [
            '0' => 'Not for Purchase',
            '1' => 'For Purchase0'
        ]],

        'tva_tx' => ['label' => 'tva_tx', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'remise_percent' => ['label' => 'remise_percent', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'localtax1_tx' => ['label' => 'localtax1_tx', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'localtax2_tx' => ['label' => 'localtax2_tx', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'localtax1_type' => ['label' => 'localtax1_type', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'localtax2_type' => ['label' => 'localtax2_type', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'desc_supplier' => ['label' => 'desc_supplier', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'vatrate_supplier' => ['label' => 'vatrate_supplier', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'default_vat_code_supplier' => ['label' => 'default_vat_code_supplier', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fourn_multicurrency_price' => ['label' => 'fourn_multicurrency_price', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fourn_multicurrency_unitprice' => ['label' => 'fourn_multicurrency_unitprice', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fourn_multicurrency_tx' => ['label' => 'fourn_multicurrency_tx', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fourn_multicurrency_id' => ['label' => 'fourn_multicurrency_id', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fourn_multicurrency_code' => ['label' => 'fourn_multicurrency_code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'packaging' => ['label' => 'packaging', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'lifetime' => ['label' => 'lifetime', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'qc_frequency' => ['label' => 'qc_frequency', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'stock_reel' => ['label' => 'stock_reel', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'stock_theorique' => ['label' => 'stock_theorique', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'cost_price' => ['label' => 'cost_price', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'pmp' => ['label' => 'pmp', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'seuil_stock_alerte' => ['label' => 'seuil_stock_alerte', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'desiredstock' => ['label' => 'desiredstock', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'duration_value' => ['label' => 'duration_value', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'duration_unit' => ['label' => 'duration_unit', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'duration' => ['label' => 'duration', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fk_default_workstation' => ['label' => 'fk_default_workstation', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'product_fourn_price_id' => ['label' => 'product_fourn_price_id', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'status_batch' => ['label' => 'status_batch', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'batch_mask' => ['label' => 'batch_mask', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'customcode' => ['label' => 'customcode', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'weight' => ['label' => 'weight', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'weight_units' => ['label' => 'weight_units', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'length' => ['label' => 'length', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'length_units' => ['label' => 'length_units', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'width' => ['label' => 'width', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'width_units' => ['label' => 'width_units', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'height' => ['label' => 'height', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'height_units' => ['label' => 'height_units', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'surface' => ['label' => 'surface', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'surface_units' => ['label' => 'surface_units', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'volume' => ['label' => 'volume', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'volume_units' => ['label' => 'volume_units', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'net_measure' => ['label' => 'net_measure', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'net_measure_units' => ['label' => 'net_measure_units', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'accountancy_code_sell_intra' => ['label' => 'accountancy_code_sell_intra', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],


        'accountancy_code_sell_export' => ['label' => 'accountancy_code_sell_export', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'accountancy_code_buy_intra' => ['label' => 'accountancy_code_buy_intra', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'accountancy_code_buy_export' => ['label' => 'accountancy_code_buy_export', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'barcode' => ['label' => 'barcode', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'stats_proposal_supplier' => ['label' => 'stats_proposal_supplier', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fk_price_expression' => ['label' => 'fk_price_expression', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fourn_qty' => ['label' => 'fourn_qty', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'fk_unit' => ['label' => 'fk_unit', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'price_autogen' => ['label' => 'price_autogen', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'sousprods' => ['label' => 'sousprods', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'res' => ['label' => 'res', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'is_object_used' => ['label' => 'is_object_used', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'is_sousproduit_qty' => ['label' => 'is_sousproduit_qty', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'is_sousproduit_incdec' => ['label' => 'is_sousproduit_incdec', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'mandatory_period' => ['label' => 'mandatory_period', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],

        'date_creation' => ['label' => 'Date Creation', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'date_modification' => ['label' => 'Date Modification', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'accountancy_code_sell' => ['label' => 'Account sell Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'accountancy_code_buy' => ['label' => 'Account purchase Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
    ],
    'invoices' => [
        'id' => ['label' => 'ID', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'ref' => ['label' => 'Reference', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'lines' => ['label' => 'Lines', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'soc_name' => ['label' => 'Third Party Name', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'multicurrency_code' => ['label' => 'Multicurrency Code', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'multicurrency_tx' => ['label' => 'Currency Exchange Rate', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'multicurrency_total_ht' => ['label' => 'Multicurrency Price(Without Tax)', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'multicurrency_total_tva' => ['label' => 'Multicurrency Tax Price', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'multicurrency_total_ttc' => ['label' => 'Multicurrency Price (With Tax)', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'total_ht' => ['label' => 'Total Price(Without Tax)', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'total_tva' => ['label' => 'Tax Price', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
        'total_ttc' => ['label' => 'Total Price(With Tax)', 'type' => 'varchar(255)', 'type_bdd' => 'varchar(255)', 'required' => 0],
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

