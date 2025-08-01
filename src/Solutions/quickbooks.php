<?php
/*********************************************************************************
 * This file is part of Myddleware.

 * @package Myddleware
 * @copyright Copyright (C) 2024  Accellier Limited - support@accellier.com
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

namespace App\Solutions;

use App\Solutions\lib\curl;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
//use Psr\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Connector;

use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Vendor;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Bill;
use QuickBooksOnline\API\Facades\Payment;
use QuickBooksOnline\API\Facades\BillPayment;


class quickbooks extends solution
{
    protected $dolibarrClient;
    protected $suffixurl;
    public $token;

    protected $dataService;
	protected int $limitCall = 100;

    public $dolibarrurl = '/api/index.php';

    protected array $required_fields = ['default' => ['id', 'Id', 'LastUpdatedTime', 'CreateTime']];

    public function getFieldsLogin(): array
    {
        return [
            [
                'name' => 'clientId',
                'type' => TextType::class,
                'label' => 'solution.fields.clientId',
            ],
            [
                'name' => 'clientSecret',
                'type' => PasswordType::class,
                'label' => 'solution.fields.clientSecret',
            ],
            [
                'name' => 'accessToken',
                'type' => TextType::class,
                'label' => 'solution.fields.accessToken',
            ],
            [
                'name' => 'refreshToken',
                'type' => PasswordType::class,
                'label' => 'solution.fields.refreshToken',
            ],
            [
                'name' => 'realmId',
                'type' => TextType::class,
                'label' => 'solution.fields.realmId',
            ],
            [
                'name' => 'baseurl',
                'type' => TextType::class,
                'label' => 'solution.fields.baseurl',
            ],
            [
                'name' => 'connectorId',
                'type' => TextType::class,
                'label' => 'solution.fields.connectorId',
            ],
        ];
    }

    public function login($paramConnexion)
    {
        parent::login($paramConnexion);
        
        try {

            $this->dataService = DataService::Configure(array(
                'auth_mode'       => 'oauth2',
                'ClientID'        => $this->paramConnexion['clientId'],
                'ClientSecret'    => $this->paramConnexion['clientSecret'],
                'accessTokenKey'  => $this->paramConnexion['accessToken'],
                'refreshTokenKey' => $this->paramConnexion['refreshToken'],
                'QBORealmID'      => $this->paramConnexion['realmId'],
                'baseUrl'         => $this->paramConnexion['baseurl']
            ));

            $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
            $accessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($this->paramConnexion['refreshToken']);
            // $accessTokenValue = $accessTokenObj->getAccessToken();
            // $refreshTokenValue = $accessTokenObj->getRefreshToken();


            $connectorid = $this->paramConnexion['connectorId'];

            if($connectorid && $connectorid > 0) {
                /*$currentDateTime = new \DateTime($accessTokenObj->getAccessTokenExpiresAt());

                // Get the current date and time
                $now = new \DateTime();
                
                // Calculate the difference
                $interval = $now->diff($currentDateTime);
                
                // Convert the difference to total minutes
                $totalMinutes = ($interval->days * 24 * 60) +
                                ($interval->h * 60) +
                                $interval->i;*/

                // if($totalMinutes < 59) {
                    $accessTokenValue = $accessTokenObj->getAccessToken();
                    $refreshTokenValue = $accessTokenObj->getRefreshToken();

                    $encrypter = new \Illuminate\Encryption\Encrypter(substr($this->parameterBagInterface->get('secret'), -16));
                    $newrefreshtoken = $encrypter->encrypt($refreshTokenValue);
                    $newaccesstoken = $encrypter->encrypt($accessTokenValue);


                    $this->paramConnexion['accessToken'] = $accessTokenValue;
                    $this->paramConnexion['refreshToken'] = $refreshTokenValue;

                    $sql = "UPDATE connectorparam SET value = '".$newaccesstoken."' WHERE conn_id = $connectorid AND name = 'accessToken'";
                    $stmt = $this->connection->prepare($sql);
                    $stmt->execute();
                    
                    $sqlrefresh = "UPDATE connectorparam SET value = '".$newrefreshtoken."' WHERE conn_id = $connectorid AND name = 'refreshToken'";
                    $stmtTemp = $this->connection->prepare($sqlrefresh);
                    $stmtTemp->execute();
                // }
                
            }

            $this->connexion_valide = true;

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            return array('error' => $error);
        }
    }

    public function get_modules($type = 'source'): array
    {
        try {
            if ('source' == $type) {
                return [
                    'Customer' => 'Customer',
                    'Vendor' => 'Vendor',
                    'Item' => 'Item',
                    'Invoice' => 'Invoice',
                    'Bill' => 'Bill',
                    'InvoicePaid' => 'Paid Invoice',
                    'BillPaid' => 'Paid Bill',
                    'Payment' => 'Payment',
                ];
            }

            return [
                'Customer' => 'Customer',
                'Vendor' => 'Vendor',
                'Item' => 'Item',
                'Invoice' => 'Invoice',
                'Bill' => 'Bill',
                'InvoicePaid' => 'Paid Invoice',
                'BillPaid' => 'Paid Bill',
                'Payment' => 'Payment',
            ];
        } catch (\Exception $e) {
            $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
            $this->logger->error($error);

            return ['error' => $error];
        }

    }

    public function get_module_fields($module, $type = 'source', $param = null): array
    {
		parent::get_module_fields($module, $type);

        try {
            // Use quickbooks metadata
            require 'lib/quickbooks/metadata.php';
            if (!empty($moduleFields[$module])) {
                $this->moduleFields = array_merge($this->moduleFields, $moduleFields[$module]);
            }
            
            return $this->moduleFields;
        } catch (\Exception $e) {
            $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
            $this->logger->error($error);

            return ['error' => $error];
        }
    }

    public function read($param): array
    {

        if (empty($param['limit'])) {
            $param['limit'] = $this->limitCall;
        }

        try {
            $result = [];

            if($param['module'] == 'Customer' || $param['module'] == 'Vendor') {
                if($param['module'] == 'Customer') {
                    $moduleName = 'Customer';
                } elseif($param['module'] == 'Vendor') {
                    $moduleName = 'Vendor';
                }
                $query = "SELECT * FROM ".$moduleName." ORDER BY Metadata.LastUpdatedTime, Metadata.CreateTime DESC maxresults ".$param['limit'];
			    $qbcustomer = $this->dataService->Query($query);

                $error = $this->dataService->getLastError();

                if ($error) {
                    $errormessage = 'Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody();
                    $this->logger->error($errormessage);
        
                    return ['error' => $errormessage];
                } else {
                    if(isset($qbcustomer) && !empty($qbcustomer) && count($qbcustomer) > 0) {
                        foreach($qbcustomer as $key => $values) {
                            $values->id = $values->Id;
                            $values->AlternatePhone = $values->AlternatePhone ? $values->AlternatePhone->FreeFormNumber : NULL;
                            $values->PrimaryPhone = $values->PrimaryPhone ? $values->PrimaryPhone->FreeFormNumber : NULL;
                            $values->Mobile = $values->Mobile ? $values->Mobile->FreeFormNumber : NULL;
                            $values->Fax = $values->Fax ? $values->Fax->FreeFormNumber : NULL;
                            $values->PrimaryEmailAddr = $values->PrimaryEmailAddr ? $values->PrimaryEmailAddr->Address : NULL;
                            $values->Line1 = $values->BillAddr ? $values->BillAddr->Line1 : NULL;
                            $values->City = $values->BillAddr ? $values->BillAddr->City : NULL;
                            $values->CountrySubDivisionCode = $values->BillAddr ? $values->BillAddr->CountrySubDivisionCode : NULL;
                            $values->PostalCode = $values->BillAddr ? $values->BillAddr->PostalCode : NULL;
                            $values->Country = $values->BillAddr ? $values->BillAddr->Country : NULL;
                            $values->CreateTime = $values->MetaData->CreateTime ? $values->MetaData->CreateTime : NULL;
                            $values->LastUpdatedTime = $values->MetaData->LastUpdatedTime ? $values->MetaData->LastUpdatedTime : NULL;
                            $result[] = (array)$values;
                        }
                    } else {
                        return [];
                    }
                }
            } elseif($param['module'] == 'InvoicePaid' || $param['module'] == 'BillPaid') {
                if($param['module'] == 'InvoicePaid') {
                    $moduleName = 'Invoice';
                } elseif($param['module'] == 'BillPaid') {
                    $moduleName = 'Bill';
                }

                $date_reference = $this->dateTimeFromMyddlewareToQuickbooks($param['date_ref']);

                $query = "SELECT * FROM ".$moduleName." WHERE Metadata.LastUpdatedTime > '".$date_reference."' ORDER BY Metadata.LastUpdatedTime DESC maxresults ".$param['limit'];
			    $Pay = $this->dataService->Query($query);

                $error = $this->dataService->getLastError();
                
                if ($error) {
                    $errormessage = 'Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody();
                    $this->logger->error($errormessage);
        
                    return ['error' => $errormessage];
                } else {
                    if(isset($Pay) && !empty($Pay) && count($Pay) > 0) {
                        // var_dump($Pay);exit;
                        $valuesA = array();
                        foreach($Pay as $key => $values) {
                            if($values->Balance < $values->TotalAmt) {
                                $valuesA['Id'] = $values->Id;
                                $valuesA['DocNumber'] = $values->DocNumber;
                                $valuesA['id'] = $values->Id;
                                $valuesA['CreateTime'] = $values->MetaData->CreateTime ? $this->dateTimeToMyddleware($values->MetaData->CreateTime) : NULL;
                                $valuesA['LastUpdatedTime'] = $values->MetaData->LastUpdatedTime ? $this->dateTimeToMyddleware($values->MetaData->LastUpdatedTime) : NULL;
                                $valuesA['TotalAmt'] = $values->TotalAmt;
                                $valuesA['Balance'] = $values->Balance;
                                $valuesA['DueDate'] = $values->DueDate;
                                $valuesA['CustomerRefName'] = $values->CustomerRef->name;
                                $valuesA['CustomerRefValue'] = $values->CustomerRef->value;

                                $result[] = $valuesA;
                            }
                        }
                    } else {
                        return [];
                    }
                }
            }
        } catch (\Exception $e) {
            $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
            $this->logger->error($error);

            return ['error' => $error];
        }

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function createData($param): array
    {
		if(!(isset($param['data']))) {
			throw new \Exception ('Data missing for create');
		}
        // var_dump($param);exit;
        if($param['module'] == 'Customer' || $param['module'] == 'Vendor') {
            try {
                $parameters = array();
                $i=0;
                $nb_record = count($param['data']);
                foreach($param['data'] as $idDoc => $data) {

                    // Check control before create
                    $data = $this->checkDataBeforeCreate($param, $data, $idDoc);

                    // Generate a reference and store it in an array
                    $i++;	
                    $idDocReference['Ref'.$i] = $idDoc;
                    $parameter = array();
                    // $parameter['attributes'] = array('type' => $param['module'], 'referenceId' => 'Ref'.$i);
                    $adressadded = array();
                    foreach ($data as $key => $value) {
                        if($key == 'PrimaryPhone' || $key == 'Mobile' || $key == 'Fax' || $key == 'AlternatePhone') {
                            $parameter[$key] = array("FreeFormNumber" => $value);
                        } elseif($key == 'PrimaryEmailAddr') {
                            $parameter[$key] = array("Address" => $value);
                        } elseif($key == 'Active') {
                            if($value == 0) {
                                $parameter[$key] = false;
                            } else {
                                $parameter[$key] = true;
                            }
                        } elseif($key == 'Line1' || $key == 'City' || $key == 'CountrySubDivisionCode' || $key == 'PostalCode' || $key == 'Country') {
                            $adressadded[$key] = $value;
                        } elseif($key == 'CurrencyRef') {
                            if($value) {
                                $parameter[$key] = ['value' => $value];
                            }
                        } else {
                            $parameter[$key] = $value;
                        }
                    }

                    if($adressadded) {
                        $parameter['BillAddr'] = $adressadded;
                    }
                    if($parameter) {
                        $vendorcustomer = false;
                        if($param['module'] == 'Vendor') {
                            if(($parameter['customer'] != 1 || $parameter['customer'] != 3) && $parameter['vendor'] == 1) { // if the thirdparty is not customer but is vendor
                                $vendorcustomer = true;
                            } else {
                                $vendorcustomer = false;
                            }
                        } elseif($param['module'] == 'Customer') {
                            if(($parameter['customer'] == 1 || $parameter['customer'] == 3) && $parameter['vendor'] == 0) { // id the thirdparty is customer but not a vendor
                                $vendorcustomer = true;
                            } else {
                                $vendorcustomer = false;
                            }
                        } 

                        if($vendorcustomer == true) {
                            if($param['module'] == 'Vendor') {
                                $query = "SELECT * FROM Vendor WHERE DisplayName = '".$parameter['DisplayName']."'";
                            } elseif($param['module'] == 'Customer') {
                                $query = "SELECT * FROM Customer WHERE DisplayName = '".$parameter['DisplayName']."'";
                            }

                            unset($parameter['customer']);
                            unset($parameter['vendor']);

                            $qbcustomer = $this->dataService->Query($query);

                            $error = $this->dataService->getLastError();
                            
                            if ($error) {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Return from create the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                ];
                                // throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            }

                            if(isset($qbcustomer) && !empty($qbcustomer) && count($qbcustomer) > 0) {
                                $qbCustomerId = $qbcustomer[0]->Id;
                                $result[$idDoc] = [
                                    'id' => $qbCustomerId,
                                    'error' => false,
                                ];
                            } else {
                                $qbCustomerId = '';
                            }

                            if(!$qbCustomerId) {
                                if($param['module'] == 'Vendor') {
                                    $customerObj = Vendor::create($parameter);
                                } elseif($param['module'] == 'Customer') {
                                    $customerObj = Customer::create($parameter);
                                }
                                $resultingCustomerObj = $this->dataService->Add($customerObj);
                                $error = $this->dataService->getLastError();
                                // var_dump($resultingCustomerObj);continue;
                                if ($error) {
                                    $result[$idDoc] = [
                                        'id' => '-1',
                                        'error' => 'Return from create the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                    ];
                                    // throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                                } else {
                                    $qbCustomerId = $resultingCustomerObj->Id;
                                    $result[$idDoc] = [
                                        'id' => $qbCustomerId,
                                        'error' => false,
                                    ];
                                }
                            } elseif($qbcustomer) {
                                $theCustomer = reset($qbcustomer);
                                $parameter['sparse'] = 'false';
                                if($param['module'] == 'Vendor') {
                                    $updateCustomer = Vendor::update($theCustomer, $parameter);
                                } elseif($param['module'] == 'Customer') {
                                    $updateCustomer = Customer::update($theCustomer, $parameter);
                                }
                                $resultingCustomerUpdatedObj = $this->dataService->Update($updateCustomer);
                                $error = $this->dataService->getLastError();
                                // var_dump($resultingCustomerUpdatedObj);continue;
                                if ($error) {
                                    $result[$idDoc] = [
                                        'id' => '-1',
                                        'error' => 'Return from create the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                    ];
                                    // throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                                }
                                $qbCustomerId = $resultingCustomerUpdatedObj->Id;
                                $result[$idDoc] = [
                                    'id' => $qbCustomerId,
                                    'error' => false,
                                ];
                            } else {
                                $qbCustomerId = '';
                            }
                        } else {
                            $result[$idDoc] = [
                                'id' => '-1',
                                'error' => false,
                            ];
                        }
                    }

                    // Modification du statut du flux
                    $this->updateDocumentStatus($idDoc, $result[$idDoc], $param);
                    // sleep(2);
                }

            }  catch (\Exception $e) {
                $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
                $this->logger->error($error);

                return ['error' => $error];
            }

            return $result;
        } elseif($param['module'] == 'Item') { // Product and service creation
            try {
                $parameters = array();
                $i=0;
                $nb_record = count($param['data']);
                foreach($param['data'] as $idDoc => $data) {

                    // Check control before create
                    $data = $this->checkDataBeforeCreate($param, $data, $idDoc);

                    // Generate a reference and store it in an array
                    $i++;	
                    $idDocReference['Ref'.$i] = $idDoc;
                    $parameter = array();
                    // $parameter['attributes'] = array('type' => $param['module'], 'referenceId' => 'Ref'.$i);
                    $adressadded = array();
                    $IncomeAccountRef = array();
                    $ExpenseAccountRef = array();
                    foreach ($data as $key => $value) {
                        if($key == 'IncomeAccountRefname' || $key == 'IncomeAccountRefvalue') {
                            if($key == 'IncomeAccountRefvalue') {
                                if($value) {
                                    $IncomeAccountRef['value'] = $value;
                                } else {
                                    $IncomeAccountRef['value'] = '79';
                                }
                            }
                    } elseif($key == 'ExpenseAccountRefname' || $key == 'ExpenseAccountRefvalue') {
                            if($key == 'ExpenseAccountRefvalue') {
                                if($value) {
                                    $ExpenseAccountRef['value'] = $value;
                                } else {
                                    $ExpenseAccountRef['value'] = '80';
                                }
                            }
                        } else {
                            $parameter[$key] = $value;
                        }
                    }

                    if($parameter['forPurchase'] == 1) {
                        $parameter['PurchaseCost'] = $parameter['CostOfPurchase'];
                        if($ExpenseAccountRef) {
                            $parameter['ExpenseAccountRef'] = $ExpenseAccountRef;
                        }
                    }

                    if($parameter['forsell'] == 1) {
                        if($IncomeAccountRef) {
                            $parameter['IncomeAccountRef'] = $IncomeAccountRef;
                        }
                    }

                    if($parameter['service'] == 1 || $parameter['service'] == '1') {
                        $parameter['Type'] = 'Service';
                    }

                    unset($parameter['service']);
                    unset($parameter['forsell']);
                    unset($parameter['forPurchase']);
                    unset($parameter['ExpenseAccountRefname']);
                    unset($parameter['ExpenseAccountRefvalue']);
                    unset($parameter['IncomeAccountRefname']);
                    unset($parameter['IncomeAccountRefvalue']);
                    unset($parameter['CostOfPurchase']);

                    if($parameter) {

			            $qbProduct = $this->dataService->Query("SELECT * FROM Item WHERE Name = '".$parameter['Name']."'");

                        $error = $this->dataService->getLastError();
                        
                        if ($error) {
                            $result[$idDoc] = [
                                'id' => '-1',
                                'error' => 'Return from create the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                            ];
                            // throw new \Exception ('Return from read the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                        }

                        if(isset($qbProduct) && !empty($qbProduct) && count($qbProduct) > 0) {
                            $qbProductId = $qbProduct[0]->Id;
                            $result[$idDoc] = [
                                'id' => $qbProductId,
                                'error' => false,
                            ];
                        } else {
                            $qbProductId = '';
                        }

                        if(!$qbProductId) {

                            $Item = Item::create($parameter);

                            $resultingObj = $this->dataService->Add($Item);
                            $error = $this->dataService->getLastError();

                            if ($error) {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Return from create the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                ];
			                    // throw new \Exception ('Return from read the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            } else {
                                $qbProductId = $resultingObj->Id;
                                $result[$idDoc] = [
                                    'id' => $qbProductId,
                                    'error' => false,
                                ];
                            }
                        } elseif($qbProduct) {
                            $theItem = reset($qbProduct);
                            $parameter['sparse'] = 'false';

			                $Item = Item::update($theItem, $parameter);
                            $resultingObj = $this->dataService->Update($Item);

                            $error = $this->dataService->getLastError();
                            if ($error) {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Return from create the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                ];
			                    // throw new \Exception ('Return from read the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            }
				            $qbProductId = $resultingObj->Id;
                            $result[$idDoc] = [
                                'id' => $qbProductId,
                                'error' => false,
                            ];
                        } else {
                            $qbProductId = '';
                        }
                    }

                    // Modification du statut du flux
                    $this->updateDocumentStatus($idDoc, $result[$idDoc], $param);
                    sleep(2);
                }

            }  catch (\Exception $e) {
                $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
                $this->logger->error($error);

                return ['error' => $error];
            }

            return $result;
        } elseif($param['module'] == 'Invoice' || $param['module'] == 'Bill') { // Product and service creation
            try {
                $parameters = array();
                $i=0;
                $nb_record = count($param['data']);
                foreach($param['data'] as $idDoc => $data) {

                    // Check control before create
                    $data = $this->checkDataBeforeCreate($param, $data, $idDoc);

                    // Generate a reference and store it in an array
                    $i++;	
                    $idDocReference['Ref'.$i] = $idDoc;
                    $parameter = array();
                    // $parameter['attributes'] = array('type' => $param['module'], 'referenceId' => 'Ref'.$i);
                    $adressadded = array();
                    $IncomeAccountRef = array();
                    $ExpenseAccountRef = array();
                    foreach ($data as $key => $value) {
                        if($key == 'LinesDetailType') {
                            $parameter['DetailType'] = $value;
                        } elseif($key == 'BillEmail') {
                            $parameter['BillEmail'] = array("Address" => $value);
                        } elseif($key == 'TxnDate') {
                            $parameter[$key] = date('Y-m-d', $value);
                        } elseif($key == 'DueDate') {
                            $parameter[$key] = date('Y-m-d', $value);
                        } elseif($key == 'CustomerRef' || $key == 'VendorRef') {
                            if($param['module'] == 'Invoice') {
                                $query = "SELECT * FROM Customer WHERE DisplayName = '".$value."'";
                            } elseif($param['module'] == 'Bill') {
                                $query = "SELECT * FROM Vendor WHERE DisplayName = '".$value."'";
                            }
                            // var_dump($query);
                            $qbcustomer = $this->dataService->Query($query);

                            $error = $this->dataService->getLastError();
                            if ($error) {
                                throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            }
                            
                            if(isset($qbcustomer) && !empty($qbcustomer) && count($qbcustomer) > 0) {
                                $qbCustomerId = $qbcustomer[0]->Id;
                                $result[$idDoc] = [
                                    'id' => $qbCustomerId,
                                    'error' => false,
                                ];
                            } else {
                                $qbCustomerId = '';
                            }
                            
                            $custmorPara['DisplayName'] = $value;

                            if(!$qbCustomerId) {
                                if($param['module'] == 'Bill') {
                                    $customerObj = Vendor::create($custmorPara);
                                } elseif($param['module'] == 'Invoice') {
                                    $customerObj = Customer::create($custmorPara);
                                }
                                
                                $resultingCustomerObj = $this->dataService->Add($customerObj);
                                $error = $this->dataService->getLastError();
                                if ($error) {
                                    throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                                } else {
                                    $qbCustomerId = $resultingCustomerObj->Id;
                                    $result[$idDoc] = [
                                        'id' => $qbCustomerId,
                                        'error' => false,
                                    ];
                                }
                            }

                            if($param['module'] == 'Invoice') {
                                $parameter['CustomerRef'] = array("value" => $qbCustomerId);
                            } elseif($param['module'] == 'Bill') {
                                $parameter['VendorRef'] = array("value" => $qbCustomerId);
                            }
                        
                        } elseif($key == 'CurrencyRef') {    
                            if($value) {
                                $parameter[$key] = ["value" => $value];
                            }
                        } elseif($key == 'ExchangeRate') {    
                            if($value) { 
                                $parameter[$key] = $value;
                            }
                        } else {
                            $parameter[$key] = $value;
                        }
                    }

                    if($param['module'] == 'Bill')  {
                        if (!isset($parameter['DetailType']) || empty($parameter['DetailType'])) {
                            $parameter['DetailType'] = 'ItemBasedExpenseLineDetail'; // Default value
                        }
                    }

                    if($param['module'] == 'Invoice') {
                        if (!isset($parameter['DetailType']) || empty($parameter['DetailType'])) {
                            $parameter['DetailType'] = 'SalesItemLineDetail'; // Default value
                        }
                    }
                    

                    $dataLine = array();

                    if ($parameter['Line'] && is_array($parameter['Line'])) {
                        $isMulticurrency = $parameter['Multicurrency'] ?? false;
                        $hasDiscountAccount = $parameter['DiscountAccount'] ?? false;
                        $finalAmount = 0;
                        $discountAmount = 0;

                        foreach ($parameter['Line'] as $val) {
                            $ProductName = $val['product_ref'] ?? '';
                            $qbProductId = '';
                            
                            if (!empty($ProductName)) {
                                try {
                                    $qbProduct = $this->dataService->Query("SELECT * FROM Item WHERE Name = '".$ProductName."'");
                                    if (!empty($qbProduct)) {
                                        $qbProductId = $qbProduct[0]->Id;
                                    }
                                } catch (Exception $e) {
                                    // Log error but continue
                                    error_log("QuickBooks product query failed: " . $e->getMessage());
                                }
                            }

                            // 2. Handle tax code
                            $taxvalue = "NON"; // Default
                            if (!empty($val['tva_tx']) && floatval($val['tva_tx']) > 0) {
                                $taxvalue = $parameter['LineTaxCodeRef'] ?? 'TAX';
                            } elseif (!empty($parameter['LineTaxCodeExemptRef'])) {
                                $taxvalue = $parameter['LineTaxCodeExemptRef'];
                            }

                            // 3. Handle description
                            $descVal = strip_tags($val['desc'] ?? $val['description'] ?? '');

                            // 4. Handle ItemRef
                            $itemrefArray = [
                                'name' => $ProductName ?: 'Services',
                                'value' => $qbProductId ?: '1'
                            ];

                            // 5. Calculate amounts
                            $subprice = $isMulticurrency ? ($val['multicurrency_subprice'] ?? 0) : ($val['subprice'] ?? 0);
                            $qty = $val['qty'] ?? 1;
                            
                            if (!empty($val['remise_percent'])) {
                                $discountPercent = floatval($val['remise_percent']);
                                $linediscountAmount = $subprice * (1 - ($discountPercent / 100));
                                $LinediscountTotalAmount = $linediscountAmount * $qty;
                                $discountAmount += ($subprice * $qty) * ($discountPercent / 100);
                            } else {
                                $linediscountAmount = $subprice;
                                $LinediscountTotalAmount = $subprice * $qty;
                            }
                            
                            $finalAmount += $LinediscountTotalAmount;

                            // 6. Build line item
                            $dataLineItem = [
                                "TaxInclusiveAmt" => $isMulticurrency ? ($val['multicurrency_total_ttc'] ?? 0) : ($val['total_ttc'] ?? 0),
                                "ItemRef" => $itemrefArray,
                                "TaxCodeRef" => ["value" => $taxvalue],
                                "Qty" => $qty,
                                "UnitPrice" => $hasDiscountAccount ? $linediscountAmount : $subprice,
                            ];

                            // Add ItemAccountRef if product not found
                            if (empty($qbProductId)) {
                                $dataLineItem["ItemAccountRef"] = ["value" => "1"];
                            }

                            if($param['module'] == 'Bill')  {
                                $dataLine[] = [
                                    "Amount" => $LinediscountTotalAmount,
                                    "DetailType" => $parameter['DetailType'] ?? 'ItemBasedExpenseLineDetail',
                                    "Description" => $descVal,
                                    ($parameter['DetailType'] ?? 'ItemBasedExpenseLineDetail') => $dataLineItem
                                ];
                            }

                            if($param['module'] == 'Invoice') {
                                $dataLine[] = [
                                    "Amount" => $LinediscountTotalAmount,
                                    "DetailType" => $parameter['DetailType'] ?? 'SalesItemLineDetail',
                                    "Description" => $descVal,
                                    ($parameter['DetailType'] ?? 'SalesItemLineDetail') => $dataLineItem
                                ];
                            }
                            
                        }
                    }

                    if($dataLine) {
                        if($param['module'] == 'Invoice') {
                            if($parameter['DiscountAccount'] != true) {
                                if($discountAmount) {
                                    $dataLine[] = [
                                        'Amount' => $discountAmount,
                                        'DetailType' => 'DiscountLineDetail',
                                        'DiscountLineDetail' => [
                                            'PercentBased' => false,
                                            'DiscountAccountRef' => [
                                                'value' => '86'
                                            ]
                                        ]
                                    ];
                                }
                            }
                        }
                        $parameter['Line'] = $dataLine;
                    }

                    if($parameter['Multicurrency'] != true || $parameter['Multicurrency'] != true) {
                        unset($parameter['ExchangeRate']);
                        unset($parameter['CurrencyRef']);
                    }

                    if($parameter['DetailType']) {
                        unset($parameter['DetailType']);
                    }

                    if($parameter['status']) {
                        unset($parameter['status']);
                    }

                    if($parameter['Multicurrency']) {
                        unset($parameter['Multicurrency']);
                    }

                    unset($parameter['DiscountAccount']);
                    unset($parameter['LineTaxCodeRef']);
		            unset($parameter['LineTaxCodeExemptRef']);

                    // var_dump($parameter);
                    if($parameter) {

                        if($param['module'] == 'Invoice') {
                            //Add a new Invoice
                            $theResourceObj = Invoice::create(
                                $parameter
                            );
                        } elseif($param['module'] == 'Bill') {
                            //Add a new Invoice
                            $theResourceObj = Bill::create(
                                $parameter
                            );
                        }

                        $resultingObj = $this->dataService->Add($theResourceObj);

                        $error = $this->dataService->getLastError();

                        if ($error) {
                            $result[$idDoc] = [
                                'id' => '-1',
                                'error' => 'Return from create the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                            ];
                            // throw new \Exception ('Return from read the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                        } else {
                            $result[$idDoc] = [
                                'id' => $resultingObj->Id,
                                'error' => false,
                            ];
                        }
                    }

                    // Modification du statut du flux
                    $this->updateDocumentStatus($idDoc, $result[$idDoc], $param);
                    // sleep(2);
                }

            }  catch (\Exception $e) {
                $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
                $this->logger->error($error);

                return ['error' => $error];
            }

            return $result;
        }

    }

    /**
    * @throws \Exception
    */
    public function updateData($param): array
    {
		if(!(isset($param['data']))) {
			throw new \Exception ('Data missing for create');
		}
        // var_dump($param);exit;
        if($param['module'] == 'Customer' || $param['module'] == 'Vendor') {
            try {
                $parameters = array();
                $i=0;
                $nb_record = count($param['data']);
                foreach($param['data'] as $idDoc => $data) {
                    
                    // Check control before create
                    $data = $this->checkDataBeforeUpdate($param, $data, $idDoc);

                    // Generate a reference and store it in an array
                    $i++;	
                    $idDocReference['Ref'.$i] = $idDoc;
                    $parameter = array();
                    // $parameter['attributes'] = array('type' => $param['module'], 'referenceId' => 'Ref'.$i);
                    $adressadded = array();
                    foreach ($data as $key => $value) {
                        if($key == "target_id") {
			        	    $target_id = $value;
                            continue;
                        } elseif($key == 'PrimaryPhone' || $key == 'Mobile' || $key == 'Fax' || $key == 'AlternatePhone') {
                            $parameter[$key] = array("FreeFormNumber" => $value);
                        } elseif($key == 'PrimaryEmailAddr') {
                            $parameter[$key] = array("Address" => $value);
                        } elseif($key == 'Active') {
                            if($value == 0) {
                                $parameter[$key] = false;
                            } else {
                                $parameter[$key] = true;
                            }
                        } elseif($key == 'Line1' || $key == 'City' || $key == 'CountrySubDivisionCode' || $key == 'PostalCode' || $key == 'Country') {
                            $adressadded[$key] = $value;
                        } elseif($key == 'CurrencyRef') {
                            if($value) {
                                $parameter[$key] = ['value' => $value];
                            }
                        } else {
                            $parameter[$key] = $value;
                        }
                    }

                    if($adressadded) {
                        $parameter['BillAddr'] = $adressadded;
                    }
                    if($parameter) {

                        unset($parameter['customer']);
                        unset($parameter['vendor']);
                        if($target_id) {
                            if($param['module'] == 'Vendor') {
			                    $qbcustomer = $this->dataService->Query("SELECT * FROM Vendor WHERE Id = '".$target_id."'");
                            } elseif($param['module'] == 'Customer') {
			                    $qbcustomer = $this->dataService->Query("SELECT * FROM Customer WHERE Id = '".$target_id."'");
                            }
                        }

                        if(isset($qbcustomer) && !empty($qbcustomer) && count($qbcustomer) > 0) {
                            $qbCustomerId = $qbcustomer[0]->Id;
                        } else {
                            if($param['module'] == 'Vendor') {
                                $query = "SELECT * FROM Vendor WHERE DisplayName = '".$parameter['DisplayName']."'";
                            } elseif($param['module'] == 'Customer') {
                                $query = "SELECT * FROM Customer WHERE DisplayName = '".$parameter['DisplayName']."'";
                            }

                            $qbcustomer = $this->dataService->Query($query);

                            $error = $this->dataService->getLastError();
                            
                            if ($error) {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Return from create the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                ];
                                // throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            }

                            if(isset($qbcustomer) && !empty($qbcustomer) && count($qbcustomer) > 0) {
                                $qbCustomerId = $qbcustomer[0]->Id;
                                $result[$idDoc] = [
                                    'id' => $qbCustomerId,
                                    'error' => false,
                                ];
                            } else {
                                $qbCustomerId = '';
                            }
                        }

                        if(!$qbCustomerId) {
                            if($param['module'] == 'Vendor') {
                                $customerObj = Vendor::create($parameter);
                            } elseif($param['module'] == 'Customer') {
                                $customerObj = Customer::create($parameter);
                            }
                            $resultingCustomerObj = $this->dataService->Add($customerObj);
                            $error = $this->dataService->getLastError();
                            // var_dump($resultingCustomerObj);continue;
                            if ($error) {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Return from create the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                ];
			                    // throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            } else {
                                $qbCustomerId = $resultingCustomerObj->Id;
                                $result[$idDoc] = [
                                    'id' => $qbCustomerId,
                                    'error' => false,
                                ];
                            }
                        } elseif($qbcustomer) {
                            $theCustomer = reset($qbcustomer);
                            $parameter['sparse'] = 'false';
                            if($param['module'] == 'Vendor') {
                                $updateCustomer = Vendor::update($theCustomer, $parameter);
                            } elseif($param['module'] == 'Customer') {
                                $updateCustomer = Customer::update($theCustomer, $parameter);
                            }
                            $resultingCustomerUpdatedObj = $this->dataService->Update($updateCustomer);
                            $error = $this->dataService->getLastError();
                            // var_dump($resultingCustomerUpdatedObj);continue;
                            if ($error) {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Return from create the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                ];
			                    // throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            }
				            $qbCustomerId = $resultingCustomerUpdatedObj->Id;
                            $result[$idDoc] = [
                                'id' => $qbCustomerId,
                                'error' => false,
                            ];
                        } else {
                            $qbCustomerId = '';
                        }
                    }

                    // Modification du statut du flux
                    $this->updateDocumentStatus($idDoc, $result[$idDoc], $param);
                    // sleep(3);
                }

            }  catch (\Exception $e) {
                $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
                $this->logger->error($error);

                return ['error' => $error];
            }

            return $result;
        }  elseif($param['module'] == 'Item') { // Product and service creation
            try {
                $parameters = array();
                $i=0;
                $nb_record = count($param['data']);
                foreach($param['data'] as $idDoc => $data) {

                    // Check control before create
                    $data = $this->checkDataBeforeUpdate($param, $data, $idDoc);

                    // Generate a reference and store it in an array
                    $i++;	
                    $idDocReference['Ref'.$i] = $idDoc;
                    $parameter = array();
                    // $parameter['attributes'] = array('type' => $param['module'], 'referenceId' => 'Ref'.$i);
                    $adressadded = array();
                    $IncomeAccountRef = array();
                    $ExpenseAccountRef = array();
                    foreach ($data as $key => $value) {
                        if($key == "target_id") {
			                $target_id = $value;
                            continue;
                        } elseif($key == 'IncomeAccountRefname' || $key == 'IncomeAccountRefvalue') {
                            if($key == 'IncomeAccountRefvalue') {
                                if($value) {
                                    $IncomeAccountRef['value'] = $value;
                                } else {
                                    $IncomeAccountRef['value'] = '79';
                                }
                            }
                        } elseif($key == 'ExpenseAccountRefname' || $key == 'ExpenseAccountRefvalue') {
                            if($key == 'ExpenseAccountRefvalue') {
                                if($value) {
                                    $ExpenseAccountRef['value'] = $value;
                                } else {
                                    $ExpenseAccountRef['value'] = '80';
                                }
                            }
                        } else {
                            $parameter[$key] = $value;
                        }
                    }

                    if($parameter['forPurchase'] == 1) {
                        $parameter['PurchaseCost'] = $parameter['CostOfPurchase'];
                        if($ExpenseAccountRef) {
                            $parameter['ExpenseAccountRef'] = $ExpenseAccountRef;
                        }
                    } else {
                        $parameter['PurchaseCost'] = '';
                        $parameter['PurchaseDesc'] = '';
                        $parameter['ExpenseAccountRef'] = [];
                    }

                    if($parameter['forsell'] == 1) {
                        if($IncomeAccountRef) {
                            $parameter['IncomeAccountRef'] = $IncomeAccountRef;
                        }
                    } else {
                        
                        $parameter['IncomeAccountRef'] = [];
                    }

                    if($parameter['service'] == 1 || $parameter['service'] == '1') {
                        $parameter['Type'] = 'Service';
                    }

                    unset($parameter['service']);
                    unset($parameter['forsell']);
                    unset($parameter['forPurchase']);
                    unset($parameter['ExpenseAccountRefname']);
                    unset($parameter['ExpenseAccountRefvalue']);
                    unset($parameter['IncomeAccountRefname']);
                    unset($parameter['IncomeAccountRefvalue']);
                    unset($parameter['CostOfPurchase']);

                    // var_dump($parameter);exit;

                    if($parameter) {

			            $qbProduct = $this->dataService->Query("SELECT * FROM Item WHERE Id = '".$target_id."'");

                        $error = $this->dataService->getLastError();
                        
                        if ($error) {
                            $result[$idDoc] = [
                                'id' => '-1',
                                'error' => 'Return from create the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                            ];
                            // throw new \Exception ('Return from read the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                        }

                        if(isset($qbProduct) && !empty($qbProduct) && count($qbProduct) > 0) {
                            $qbProductId = $qbProduct[0]->Id;
                            $result[$idDoc] = [
                                'id' => $qbProductId,
                                'error' => false,
                            ];
                        } else {
                            $qbProductId = '';
                        }

                        if(!$qbProductId) {
                            $theItem = reset($qbProduct);

                            $parameter['sparse'] = 'false';

                            $Item = Item::update($theItem, $parameter);

                            $resultingObj = $this->dataService->Update($Item);
                            $error = $this->dataService->getLastError();
                            // var_dump($error);exit;
                            if ($error) {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Return from create the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                ];
			                    // throw new \Exception ('Return from read the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            } else {
                                $qbProductId = $resultingObj->Id;
                                $result[$idDoc] = [
                                    'id' => $qbProductId,
                                    'error' => false,
                                ];
                            }
                        } elseif($qbProduct) {
                            $theItem = reset($qbProduct);
                            $parameter['sparse'] = 'false';

			                $Item = Item::update($theItem, $parameter);
                            $resultingObj = $this->dataService->Update($Item);

                            $error = $this->dataService->getLastError();
                            // var_dump($error);exit;
                            if ($error) {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Return from create the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                                ];
			                    // throw new \Exception ('Return from read the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            }
				            $qbProductId = $resultingObj->Id;
                            $result[$idDoc] = [
                                'id' => $qbProductId,
                                'error' => false,
                            ];
                        } else {
                            $qbProductId = '';
                        }
                    }

                    // Modification du statut du flux
                    $this->updateDocumentStatus($idDoc, $result[$idDoc], $param);
                    // sleep(2);
                }

            }  catch (\Exception $e) {
                $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
                $this->logger->error($error);
                // var_dump($error);exit;
                return ['error' => $error];
            }

            return $result;
        } elseif($param['module'] == 'Invoice' || $param['module'] == 'Bill') { // Product and service creation
            try {
                $parameters = array();
                $i=0;
                $nb_record = count($param['data']);
                foreach($param['data'] as $idDoc => $data) {

                    // Check control before create
                    $data = $this->checkDataBeforeUpdate($param, $data, $idDoc);

                    // Generate a reference and store it in an array
                    $i++;	
                    $idDocReference['Ref'.$i] = $idDoc;
                    $parameter = array();
                    // $parameter['attributes'] = array('type' => $param['module'], 'referenceId' => 'Ref'.$i);
                    $adressadded = array();
                    $IncomeAccountRef = array();
                    $ExpenseAccountRef = array();
                    foreach ($data as $key => $value) {
                        if($key == "target_id") {
			        	    $target_id = $value;
                            continue;
                        } elseif($key == 'LinesDetailType') {
                            $parameter['DetailType'] = $value;
                        } elseif($key == 'BillEmail') {
                            $parameter['BillEmail'] = array("Address" => $value);
                        } elseif($key == 'TxnDate') {
                            $parameter[$key] = date('Y-m-d', $value);
                        } elseif($key == 'DueDate') {
                            $parameter[$key] = date('Y-m-d', $value);
                        } elseif($key == 'CustomerRef' || $key == 'VendorRef') {
                            if($param['module'] == 'Invoice') {
                                $query = "SELECT * FROM Customer WHERE DisplayName = '".$value."'";
                            } elseif($param['module'] == 'Bill') {
                                $query = "SELECT * FROM Vendor WHERE DisplayName = '".$value."'";
                            }
                            // var_dump($query);
                            $qbcustomer = $this->dataService->Query($query);

                            $error = $this->dataService->getLastError();
                            if ($error) {
                                throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                            }
                            
                            if(isset($qbcustomer) && !empty($qbcustomer) && count($qbcustomer) > 0) {
                                $qbCustomerId = $qbcustomer[0]->Id;
                                $result[$idDoc] = [
                                    'id' => $qbCustomerId,
                                    'error' => false,
                                ];
                            } else {
                                $qbCustomerId = '';
                            }
                            
                            $custmorPara['DisplayName'] = $value;

                            if(!$qbCustomerId) {
                                if($param['module'] == 'Bill') {
                                    $customerObj = Vendor::create($custmorPara);
                                } elseif($param['module'] == 'Invoice') {
                                    $customerObj = Customer::create($custmorPara);
                                }
                                
                                $resultingCustomerObj = $this->dataService->Add($customerObj);
                                $error = $this->dataService->getLastError();
                                if ($error) {
                                    throw new \Exception ('Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                                } else {
                                    $qbCustomerId = $resultingCustomerObj->Id;
                                    $result[$idDoc] = [
                                        'id' => $qbCustomerId,
                                        'error' => false,
                                    ];
                                }
                            }

                            if($param['module'] == 'Invoice') {
                                $parameter['CustomerRef'] = array("value" => $qbCustomerId);
                            } elseif($param['module'] == 'Bill') {
                                $parameter['VendorRef'] = array("value" => $qbCustomerId);
                            }
                        
                        } elseif($key == 'CurrencyRef') {      
                            if($value) {        
                                $parameter[$key] = ["value" => $value];
                            }
                        } elseif($key == 'ExchangeRate') {    
                            if($value) {
                                $parameter[$key] = $value;
                            }
                        } else {
                            $parameter[$key] = $value;
                        }
                    }

                    if($param['module'] == 'Bill')  {
                        if (!isset($parameter['DetailType']) || empty($parameter['DetailType'])) {
                            $parameter['DetailType'] = 'ItemBasedExpenseLineDetail'; // Default value
                        }
                    }

                    if($param['module'] == 'Invoice') {
                        if (!isset($parameter['DetailType']) || empty($parameter['DetailType'])) {
                            $parameter['DetailType'] = 'SalesItemLineDetail'; // Default value
                        }
                    }

                    $dataLine = array();

                    if ($parameter['Line'] && is_array($parameter['Line'])) {
                        // Set default values for optional parameters
                        $isMulticurrency = $parameter['Multicurrency'] ?? false;
                        $hasDiscountAccount = $parameter['DiscountAccount'] ?? false;
                        $finalAmount = 0;
                        $discountAmount = 0;

                        foreach ($parameter['Line'] as $val) {
                            // 1. Handle product reference and query
                            $ProductName = $val['product_ref'] ?? '';
                            $qbProductId = '';
                            
                            if (!empty($ProductName)) {
                                try {
                                    $qbProduct = $this->dataService->Query("SELECT * FROM Item WHERE Name = '".$ProductName."'");
                                    if (!empty($qbProduct)) {
                                        $qbProductId = $qbProduct[0]->Id;
                                    }
                                } catch (Exception $e) {
                                    // Log error but continue
                                    // error_log("QuickBooks product query failed: " . $e->getMessage());
                                }
                            }

                            // 2. Handle tax code
                            $taxvalue = "NON"; // Default
                            if (!empty($val['tva_tx']) && floatval($val['tva_tx']) > 0) {
                                $taxvalue = $parameter['LineTaxCodeRef'] ?? 'TAX';
                            } elseif (!empty($parameter['LineTaxCodeExemptRef'])) {
                                $taxvalue = $parameter['LineTaxCodeExemptRef'];
                            }

                            // 3. Handle description
                            $descVal = strip_tags($val['desc'] ?? $val['description'] ?? '');

                            // 4. Handle ItemRef
                            $itemrefArray = [
                                'name' => $ProductName ?: 'Services',
                                'value' => $qbProductId ?: '1'
                            ];

                            // 5. Calculate amounts
                            $subprice = $isMulticurrency ? ($val['multicurrency_subprice'] ?? 0) : ($val['subprice'] ?? 0);
                            $qty = $val['qty'] ?? 1;
                            
                            if (!empty($val['remise_percent'])) {
                                $discountPercent = floatval($val['remise_percent']);
                                $linediscountAmount = $subprice * (1 - ($discountPercent / 100));
                                $LinediscountTotalAmount = $linediscountAmount * $qty;
                                $discountAmount += ($subprice * $qty) * ($discountPercent / 100);
                            } else {
                                $linediscountAmount = $subprice;
                                $LinediscountTotalAmount = $subprice * $qty;
                            }
                            
                            $finalAmount += $LinediscountTotalAmount;

                            // 6. Build line item
                            $dataLineItem = [
                                "TaxInclusiveAmt" => $isMulticurrency ? ($val['multicurrency_total_ttc'] ?? 0) : ($val['total_ttc'] ?? 0),
                                "ItemRef" => $itemrefArray,
                                "TaxCodeRef" => ["value" => $taxvalue],
                                "Qty" => $qty,
                                "UnitPrice" => $hasDiscountAccount ? $linediscountAmount : $subprice,
                            ];

                            // Add ItemAccountRef if product not found
                            if (empty($qbProductId)) {
                                $dataLineItem["ItemAccountRef"] = ["value" => "1"];
                            }
                            // ItemBasedExpenseLineDetail

                            if($param['module'] == 'Bill')  {
                                $dataLine[] = [
                                    "Amount" => $LinediscountTotalAmount,
                                    "DetailType" => $parameter['DetailType'] ?? 'ItemBasedExpenseLineDetail',
                                    "Description" => $descVal,
                                    ($parameter['DetailType'] ?? 'ItemBasedExpenseLineDetail') => $dataLineItem
                                ];
                            }

                            if($param['module'] == 'Invoice') {
                                $dataLine[] = [
                                    "Amount" => $LinediscountTotalAmount,
                                    "DetailType" => $parameter['DetailType'] ?? 'SalesItemLineDetail',
                                    "Description" => $descVal,
                                    ($parameter['DetailType'] ?? 'SalesItemLineDetail') => $dataLineItem
                                ];
                            }

                        }
                    }
                    
                    if($dataLine) {
                        if($param['module'] == 'Invoice') {
                            if($parameter['DiscountAccount'] != true) {
                                if($discountAmount) {
                                    $dataLine[] = [
                                        'Amount' => $discountAmount,
                                        'DetailType' => 'DiscountLineDetail',
                                        'DiscountLineDetail' => [
                                            'PercentBased' => false,
                                            'DiscountAccountRef' => [
                                                'value' => '86'
                                            ]
                                        ]
                                    ];
                                }
                            }
                        }
                        $parameter['Line'] = $dataLine;
                    }
                    
                    if($parameter['Multicurrency'] != true || $parameter['Multicurrency'] != true) {
                        unset($parameter['ExchangeRate']);
                        unset($parameter['CurrencyRef']);
                    }

                    if($parameter['DetailType']) {
                        unset($parameter['DetailType']);
                    }

                    if($parameter['status']) {
                        unset($parameter['status']);
                    }

                    if($parameter['Multicurrency']) {
                        unset($parameter['Multicurrency']);
                    }

                    unset($parameter['DiscountAccount']);
                    unset($parameter['LineTaxCodeRef']);
		            unset($parameter['LineTaxCodeExemptRef']);
                    // var_dump($parameter);
                    if($parameter) {
                        if($param['module'] == 'Invoice') {
                            $qbInvoice = $this->dataService->Query("SELECT * FROM Invoice WHERE id = '".$target_id."'");
                            if($qbInvoice[0]->Id) {
                                $theInvoice = reset($qbInvoice);
                                $parameter['sparse'] = 'false';
                                $theResourceObj = Invoice::update($theInvoice, $parameter);
                                $resultingObj = $this->dataService->Update($theResourceObj);
                            } else {
                                //Add a new Invoice
                                $theResourceObj = Invoice::create(
                                    $parameter
                                );
                                $resultingObj = $this->dataService->Add($theResourceObj);
                            }
                        } elseif($param['module'] == 'Bill') {
                            $qbInvoice = $this->dataService->Query("SELECT * FROM Bill WHERE id = '".$target_id."'");
                            if($qbInvoice[0]->Id) {
                                // Update the Bill
                                $theInvoice = reset($qbInvoice);
                                $parameter['sparse'] = 'false';
                                $theResourceObj = Bill::update($theInvoice, $parameter);
                                $resultingObj = $this->dataService->Update($theResourceObj);
                            } else {
                                //Add a new Bill
                                $theResourceObj = Bill::create(
                                    $parameter
                                );
                                $resultingObj = $this->dataService->Add($theResourceObj);
                            }
                        }

                        $error = $this->dataService->getLastError();
                        // var_dump($error);
                        if ($error) {
                            $result[$idDoc] = [
                                'id' => '-1',
                                'error' => 'Return from create the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody(),
                            ];
                            // throw new \Exception ('Return from read the Item with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody());
                        } else {
                            $result[$idDoc] = [
                                'id' => $resultingObj->Id,
                                'error' => false,
                            ];
                        }
                    }

                    // Modification du statut du flux
                    $this->updateDocumentStatus($idDoc, $result[$idDoc], $param);
                    // sleep(2);
                }

            }  catch (\Exception $e) {
                $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
                $this->logger->error($error);

                return ['error' => $error];
            }

            return $result;
        }

    }


    /**
    * @throws \Exception
    */
    protected function dateTimeFromMyddleware($dateTime)
    {
        $date = new \DateTime($dateTime);

        return $date->format('U');
    }

    public function getRefFieldName($param): string
    {
        return 'LastUpdatedTime';
    }

    protected function dateTimeFromMyddlewareToQuickbooks($dateTime)
    {
        // Step 1: Create a DateTime object with the UTC datetime
        $utcDateTime = new \DateTime($dateTime, new \DateTimeZone('UTC'));

        // Step 2: Set the timezone to -07:00
        $targetTimeZone = new \DateTimeZone('-07:00');
        $utcDateTime->setTimezone($targetTimeZone);

        // Step 3: Format the datetime in ISO 8601 format
        $formattedDateTime = $utcDateTime->format('Y-m-d\TH:i:sP');

        return $formattedDateTime;
    }

    // Function de conversion de datetime format solution à un datetime format Myddleware
    protected function dateTimeToMyddleware($dateTime)
    {
        return date('Y-m-d H:i:s', strtotime($dateTime));
    }


}
