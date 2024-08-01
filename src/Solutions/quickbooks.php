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
            $accessTokenValue = $accessTokenObj->getAccessToken();
            $refreshTokenValue = $accessTokenObj->getRefreshToken();

            $this->paramConnexion['accessToken'] = $accessTokenValue;
            $this->paramConnexion['refreshToken'] = $refreshTokenValue;

            // $qb = $this->entityManager->getRepository(Connector::class)->createQueryBuilder('c');
            // $qb->select('c', 'cp')->leftjoin('c.connectorParams', 'cp');

            // if ($this->getUser()->isAdmin()) {
            //     $qb->where('c.id =:id AND c.deleted = 0')->setParameter('id', $id);
            // } else {
            //     $qb->where('c.id =:id and c.createdBy =:createdBy AND c.deleted = 0')->setParameter(['id' => $id, 'createdBy' => $this->getUser()->getId()]);
            // }
            // // Detecte si la session est le support ---------
            // // Infos du connecteur
            // $connector = $qb->getQuery()->getOneOrNullResult();
    
            // if (!$connector) {
            //     throw $this->createNotFoundException("This connector doesn't exist");
            // }
    
            // if ($this->getUser()->isAdmin()) {
            //     $qb->where('c.id =:id')->setParameter('id', $id);
            // } else {
            //     $qb->where('c.id =:id and c.createdBy =:createdBy')->setParameter(['id' => $id, 'createdBy' => $this->getUser()->getId()]);
            // }
            // // Detecte si la session est le support ---------
            // // Infos du connecteur
            // $connector = $qb->getQuery()->getOneOrNullResult();

            

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

                $query = "SELECT * FROM ".$moduleName." WHERE Metadata.LastUpdatedTime >= '".$date_reference."' ORDER BY Metadata.LastUpdatedTime DESC maxresults ".$param['limit'];
			    $Pay = $this->dataService->Query($query);

                $error = $this->dataService->getLastError();

                if ($error) {
                    $errormessage = 'Return from read the customer with quickbooksapi status code '.$error->getHttpStatusCode().'Helper message is: '.$error->getOAuthHelperError().', Response message is: '.$error->getResponseBody();
                    $this->logger->error($errormessage);
        
                    return ['error' => $errormessage];
                } else {
                    if(isset($Pay) && !empty($Pay) && count($Pay) > 0) {
                        foreach($Pay as $key => $values) {
                            if($values->Balance < $values->TotalAmt) {
                                $values->id = $values->Id;
                                $values->CreateTime = $values->MetaData->CreateTime ? $this->dateTimeToMyddleware($values->MetaData->CreateTime) : NULL;
                                $values->LastUpdatedTime = $values->MetaData->LastUpdatedTime ? $this->dateTimeToMyddleware($values->MetaData->LastUpdatedTime) : NULL;
                                $values->TotalAmt = $values->TotalAmt;
                                $values->Balance = $values->Balance;
                                $values->DueDate = $values->DueDate;
                                $values->CustomerRefName = $values->CustomerRef->name;
                                $values->CustomerRefValue = $values->CustomerRef->value;
                            }
                            $result[] = (array)$values;
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
                        
                        } else {
                            $parameter[$key] = $value;
                        }
                    }

                    unset($parameter['status']);

                    $dataLine = array();
                    if($parameter['Line'] && is_array($parameter['Line'])) {
                        $finalAmount = 0;
                        $discountAmount = 0;
                        foreach($parameter['Line'] as $val) {
                            $ProductName = $val['product_ref'];
                            $qbProduct = $this->dataService->Query("SELECT * FROM Item WHERE Name = '".$ProductName."'");
                            if(isset($qbProduct) && !empty($qbProduct) && count($qbProduct) > 0) {
                                $qbProductId = $qbProduct[0]->Id;
                            } else {
                                throw new \Exception ("Unable to find the product with name ".$ProductName);
                            }

                            if($param['module'] == 'Bill') {
                                $dataLine[] = [
                                    "Amount" => $val['total_ttc'],
                                    "DetailType" => $parameter['DetailType'],
                                    $parameter['DetailType'] => [
                                        "TaxInclusiveAmt" => $val['total_ttc'],
                                        "ItemRef" => [
                                            "value" => $qbProductId
                                        ],
                                        "Qty" => $val['qty'],
                                        // "UnitPrice" => $val['subprice'],
                                    ]
                                ];
                            } else {
                                if($val['remise_percent']) {
                                    $totalamount = $val['subprice']*$val['qty'];
                                    $discountAmount += ($totalamount * $val['remise_percent']) / 100;
                                    $finalAmount += $totalamount - $discountAmount;
                                } else {
                                    $totalamount = $val['total_ttc'];
                                    $finalAmount += 0;
                                    $discountAmount += 0; 
                                }

                                $dataLine[] = [
                                    "Amount" => $totalamount,
                                    "DetailType" => $parameter['DetailType'],
                                    $parameter['DetailType'] => [
                                        "TaxInclusiveAmt" => $totalamount,
                                        "ItemRef" => [
                                            "value" => $qbProductId
                                        ],
                                        "Qty" => $val['qty'],
                                        "UnitPrice" => $val['subprice'],
                                    ]
                                ];
                            }
                        }
                    }

                    if($dataLine) {
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
                        $parameter['Line'] = $dataLine;
                    }

                    if($parameter['DetailType']) {
                        unset($parameter['DetailType']);
                    }
                    
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
                    sleep(2);
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
                    // sleep(2);
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
                        
                        } else {
                            $parameter[$key] = $value;
                        }
                    }

                    $dataLine = array();
                    if($parameter['Line'] && is_array($parameter['Line'])) {
                        $finalAmount = 0;
                        $discountAmount = 0;
                        foreach($parameter['Line'] as $val) {
                            $ProductName = $val['product_ref'];
                            $qbProduct = $this->dataService->Query("SELECT * FROM Item WHERE Name = '".$ProductName."'");
                            if(isset($qbProduct) && !empty($qbProduct) && count($qbProduct) > 0) {
                                $qbProductId = $qbProduct[0]->Id;
                            } else {
                                throw new \Exception ("Unable to find the product with name ".$ProductName);
                            }

                            if($param['module'] == 'Bill') {
                                $dataLine[] = [
                                    "Amount" => $val['total_ttc'],
                                    "DetailType" => $parameter['DetailType'],
                                    $parameter['DetailType'] => [
                                        "TaxInclusiveAmt" => $val['total_ttc'],
                                        "ItemRef" => [
                                            "value" => $qbProductId
                                        ],
                                        "Qty" => $val['qty'],
                                        // "UnitPrice" => $val['subprice'],
                                    ]
                                ];
                            } else {
                                if($val['remise_percent']) {
                                    $totalamount = $val['subprice']*$val['qty'];
                                    $discountAmount += ($totalamount * $val['remise_percent']) / 100;
                                    $finalAmount += $totalamount - $discountAmount;
                                } else {
                                    $totalamount = $val['total_ttc'];
                                    $finalAmount += 0;
                                    $discountAmount += 0; 
                                }
                                
                                $dataLine[] = [
                                    "Amount" => $totalamount,
                                    "DetailType" => $parameter['DetailType'],
                                    $parameter['DetailType'] => [
                                        "TaxInclusiveAmt" => $totalamount,
                                        "ItemRef" => [
                                            "value" => $qbProductId
                                        ],
                                        "Qty" => $val['qty'],
                                        "UnitPrice" => $val['subprice'],
                                    ]
                                ];
                            }
                        }
                    }

                    unset($parameter['status']);

                    if($dataLine) {
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

                        $parameter['Line'] = $dataLine;
                    }

                    if($parameter['DetailType']) {
                        unset($parameter['DetailType']);
                    }
                    
                    // var_dump($parameter);
                    if($parameter) {
                        if($param['module'] == 'Invoice') {
                            $qbInvoice = $this->dataService->Query("SELECT * FROM Invoice WHERE id = '".$target_id."'");

                            $theInvoice = reset($qbInvoice);
    
                            $parameter['sparse'] = 'false';
    
                            $theResourceObj = Invoice::update($theInvoice, $parameter);
                        } elseif($param['module'] == 'Bill') {
                            // Update the Bill
                            // var_dump("SELECT * FROM Bill WHERE id = '".$target_id."'");
					        $qbInvoice = $this->dataService->Query("SELECT * FROM Bill WHERE id = '".$target_id."'");

                            $theInvoice = reset($qbInvoice);
    
                            $parameter['sparse'] = 'false';
    
                            $theResourceObj = Bill::update($theInvoice, $parameter);
                        }

                        $resultingObj = $this->dataService->Update($theResourceObj);

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
