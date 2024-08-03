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

class dolibarr extends solution
{
    protected $dolibarrClient;
    protected $suffixurl;
    public $token;


	protected int $limitCall = 100;

    public $dolibarrurl = '/api/index.php';

    protected array $required_fields = ['default' => ['id', 'date_creation', 'date_modification']];

    public function getFieldsLogin(): array
    {
        return [
            [
                'name' => 'url',
                'type' => TextType::class,
                'label' => 'solution.fields.url',
            ],
            [
                'name' => 'user',
                'type' => TextType::class,
                'label' => 'solution.fields.user',
            ],
            [
                'name' => 'password',
                'type' => PasswordType::class,
                'label' => 'solution.fields.password',
            ],
        ];
    }

    public function login($paramConnexion)
    {
        parent::login($paramConnexion);

        try {
            $this->dolibarrClient = new curl();
            $url = '/api/index.php/login';
            
            $params = array(
                'login'    => $this->paramConnexion['user'],
                'password' => $this->paramConnexion['password'],
            );

            $serverurl = $this->paramConnexion['url'].$url;
            
            $response = $this->dolibarrClient->get($serverurl, $params);

            if($response) {
                if($this->dolibarrClient->info['http_code'] == 200) {
                    $responseobj = json_decode($response);
                    if($responseobj->success->code == 200) {
                        $this->token = $responseobj->success->token;
                        $this->connexion_valide = true;
                    } else {
                        throw new \Exception('Wrong dolibarr user or Password');
                    }
                } else {
                    throw new \Exception('Something Went wrong');
                }
            } else {
                throw new \Exception('Something Went wrong');
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            return array('error' => $error);
        }
    }

    public function get_modules($type = 'source'): array
    {
        try {
            // if ('source' == $type) {

                if($this->token) {
                    $this->dolibarrClient = new curl();
                    $this->dolibarrClient->header = array(
                        'DOLAPIKEY: '.$this->token,
                        'Content-Type: application/json',
                    );
                    $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/setup/modules';
                    $response = $this->dolibarrClient->get($serverurl);
        
                    if($response) {
                        if($this->dolibarrClient->info['http_code'] == 200) {
                            $responseobj = json_decode($response);
                            $moduleArray = array();
                            foreach($responseobj as $value) {
                                $notViewale = array('export', 'fckeditor', 'api', 'modulebuilder');
                                if(in_array($value, $notViewale)) { continue; } // Don't need to shwo the api module
                                $viewName = ucfirst(str_replace('_', '', $value));
                                if($value == 'societe') {
                                    $viewName = 'Third Party';
                                } elseif($value == 'societevendor') {
                                    $viewName = 'Vendor';
                                }
                                $moduleArray[$value] = $viewName;
                            }


                            $moduleArray['societevendor'] = 'Vendor';
                            $moduleArray['invoices'] = 'Invoices';
                            $moduleArray['supplierinvoices'] = 'Supplier Invoices';
                            $moduleArray['supllierinvoicesettopaid'] = 'Supplier Invoices Payment';
                            $moduleArray['invoicesettopaid'] = 'Invoices Setto Paid';
                            $moduleArray['invoicepayment'] = 'Invoice Payment';

                            return $moduleArray;
                        } else {
                            return [];
                        }
                    } else {
                        return [];
                    }
                }
            // }
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
            // Use Dolibarr metadata
            require 'lib/dolibarr/metadata.php';

            if($moduleFields['supllierinvoicesettopaid'] || $moduleFields['invoicepayment']) {
                $this->dolibarrClient = new curl();
                $this->dolibarrClient->header = array(
                    'DOLAPIKEY: '.$this->token,
                    'Content-Type: application/json',
                );
                $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/bankaccounts';
    
                $data = array(
                    'sortfield' => 't.rowid',
                    'sortorder' => 'ASC',
                    'limit'     => 100,
                );
    
                $response = $this->dolibarrClient->get($serverurl, $data);
    
                if($response) {
                    if($this->dolibarrClient->info['http_code'] == 200) {
                        $responseobj = json_decode($response);
                        $bankaccounts = array();
                        foreach($responseobj as $value) {
                            $bankaccounts[$value->id] = $value->ref;
                        }
                    }
                }

                // Payments Type
                $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/setup/dictionary/payment_types';
    
                $data = array(
                    'sortfield' => 'code',
                    'sortorder' => 'ASC',
                    'limit'     => 100,
                    'active'    => 1,
                );
    
                $response1 = $this->dolibarrClient->get($serverurl, $data);
                if($response1) {
                    if($this->dolibarrClient->info['http_code'] == 200) {
                        $responseobj1 = json_decode($response1);
                        $paymenttypes = [];
                        foreach($responseobj1 as $keys => $valuew) {
                            $paymenttypes[$valuew->id] = $valuew->label;
                        }
                    }
                }
    

                $moduleFields['supllierinvoicesettopaid']['accountid']['option'] = $bankaccounts;
                $moduleFields['supllierinvoicesettopaid']['payment_mode_id']['option'] = $paymenttypes;

                $moduleFields['invoicepayment']['accountid']['option'] = $bankaccounts;
                $moduleFields['invoicepayment']['paymentid']['option'] = $paymenttypes;
            }

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
        try {
            $result = [];

            if (empty($param['limit'])) {
                $param['limit'] = $this->limitCall;
            }

            // $dateRefWooFormat = $this->dateTimeFromMyddleware($param['date_ref']);
            // var_dump($param);exit;
            if($param['module'] == 'societe' || $param['module'] == 'societevendor') {
                $this->dolibarrClient = new curl();
                $this->dolibarrClient->header = array(
                    'DOLAPIKEY: '.$this->token,
                    'Content-Type: application/json',
                );
                $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/thirdparties';

                $date_reference = $this->dolibarrApiDateFormate($param['date_ref']);
                
                if($param['module'] == 'societevendor') {
                    $data = array(
                        'sortfield' => 't.rowid',
                        'sortorder' => 'DESC',
                        'limit'     => $param['limit'],
                        'sqlfilters' => "(t.tms:>=:'".$date_reference."') AND (t.fournisseur:=:1)",
                    );    
                } else {
                    $data = array(
                        'sortfield' => 't.rowid',
                        'sortorder' => 'DESC',
                        'limit'     => $param['limit'],
                        'sqlfilters' => "(t.tms:>=:'".$date_reference."')",
                    );    
                }

                $response = $this->dolibarrClient->get($serverurl, $data);

                if($response) {
                    if($this->dolibarrClient->info['http_code'] == 200) {
                        $responseobj = json_decode($response);
                        $moduleArray = array();
                        foreach($responseobj as $value) {
                            if($value->country_code) {
                                $countryresponse = $this->dolibarrClient->get($this->paramConnexion['url'].$this->dolibarrurl.'/setup/dictionary/countries/byCode/'.$value->country_code);
                                $countryname = json_decode($countryresponse);
                                $value->country = $countryname->label;
                            }

                            if($value->state_id) {
                                $stateresponse = $this->dolibarrClient->get($this->paramConnexion['url'].$this->dolibarrurl.'/setup/dictionary/states/'.$value->state_id);
                                $statename = json_decode($stateresponse);
                                $value->state = $statename->name;
                            }

                            $value->date_modification = date('Y-m-d H:i:s', $value->date_modification);
                            $result[] = (array)$value;
                        }
                    }
                }
    

            } elseif($param['module'] == 'product') {
                $this->dolibarrClient = new curl();
                $this->dolibarrClient->header = array(
                    'DOLAPIKEY: '.$this->token,
                    'Content-Type: application/json',
                );
                $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/products';

                $date_reference = $this->dolibarrApiDateFormate($param['date_ref']);
                
                $data = array(
                    'sortfield' => 't.rowid',
                    'sortorder' => 'DESC',
                    'limit'     => $param['limit'],
                    'sqlfilters' => "(t.tms:>=:'".$date_reference."')",
                );

                $response = $this->dolibarrClient->get($serverurl, $data);
                // var_dump($serverurl);
                if($response) {
                    if($this->dolibarrClient->info['http_code'] == 200) {
                        $responseobj = json_decode($response);
                        $moduleArray = array();
                        foreach($responseobj as $value) {
                            $value->date_modification = $value->date_modification;
                            $result[] = (array)$value;
                        }
                    }
                }
            } elseif($param['module'] == 'invoices' || $param['module'] == 'supplierinvoices') {
                $this->dolibarrClient = new curl();
                $this->dolibarrClient->header = array(
                    'DOLAPIKEY: '.$this->token,
                    'Content-Type: application/json',
                );
                $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/'.$param['module'];

                $date_reference = $this->dolibarrApiDateFormate($param['date_ref']);
                
                $data = array(
                    'sortfield' => 't.rowid',
                    'sortorder' => 'DESC',
                    'limit'     => $param['limit'],
                    'sqlfilters' => "(t.tms:>=:'".$date_reference."')",
                );

                $response = $this->dolibarrClient->get($serverurl, $data);

                if($response) {
                    if($this->dolibarrClient->info['http_code'] == 200) {
                        $responseobj = json_decode($response);
                        $moduleArray = array();
                        foreach($responseobj as $value) {
                            if($value->socid) {
                                // Thirdparty records
                                $thirdpartyurl = $this->paramConnexion['url'].$this->dolibarrurl.'/thirdparties/'.$value->socid;
                                $socresponse = $this->dolibarrClient->get($thirdpartyurl);
                                if($socresponse) {
                                    $socresponseobj = json_decode($socresponse);
                                    $value->soc_name = $socresponseobj->name;
                                    $value->soc_name_alias = $socresponseobj->name_alias;
                                    $value->soc_code_client = $socresponseobj->code_client;
                                    $value->soc_code_fournisseur = $socresponseobj->code_fournisseur;
                                    $value->soc_email = $socresponseobj->email;
                                }
                
                            }
                            if($param['module'] == 'supplierinvoices') {
                                $value->date_creation = date('Y-m-d H:i:s', $value->datec);
                                $value->date_modification = date('Y-m-d H:i:s', $value->tms);
                            } else {
                                $value->date_creation = date('Y-m-d H:i:s', $value->date_creation);
                                $value->date_modification = date('Y-m-d H:i:s', $value->date_modification);
                            }
                            $result[] = (array)$value;
                        }
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
        // var_dump($param);
		if(!(isset($param['data']))) {
			throw new \Exception ('Data missing for create');
		}

        $this->dolibarrClient = new curl();
        $this->dolibarrClient->header = array(
            'DOLAPIKEY: '.$this->token,
            'Content-Type: application/json',
        );

        if($param['module'] == 'invoicesettopaid' || $param['module'] == 'invoicepayment' || $param['module'] == 'supllierinvoicesettopaid') {

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
                    $parameter[$key] = $value;
                }

                if($parameter['balance'] == 0 || $parameter['balance'] == '0') {
                    if($parameter['id']) {
                        $id = $parameter['id'];
                    } elseif($parameter['ref']) { // if id is not pass then find out the id via ref number
                        if($param['module'] == 'supllierinvoicesettopaid') {
                            $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/supplierinvoices';
                        } else {
                            $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/invoices';
                        }
        
                        $data = array(
                            'sortfield' => 't.rowid',
                            'sortorder' => 'ASC',
                            'limit'     => 1,
                            'sqlfilters' => "(t.ref:like:'".$parameter['ref']."')",
                        );
        
                        $response = $this->dolibarrClient->get($serverurl, $data);
                        // var_dump($response);
                        if($response) {
                            if($this->dolibarrClient->info['http_code'] == 200) {
                                $responseobj = json_decode($response);
                                $moduleArray = array();
                                foreach($responseobj as $value) {
                                    $id = $value->id;
                                }
                            }
                        }
                    }
                    

                    // settopaid
                    // if(!$id) {
                    //     $result[$idDoc] = [
                    //         'id' => '-1',
                    //         'error' => 'Id is missing or ref number is missing',
                    //     ];
                    // }

                    if($id){
                        if($param['module'] == 'invoicesettopaid') {
                            $data = array('id' => $id);

                            $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/invoices/'.$id.'/settopaid';
                        } elseif($param['module'] == 'invoicepayment') {
                            $data = array(
                                        'id' => $id,
                                        'datepaye' => strtotime($parameter['datepaye']),
                                        'paymentid' => $parameter['paymentid'],
                                        'closepaidinvoices' => $parameter['closepaidinvoices'],
                                        'accountid' => $parameter['accountid']
                                    );
                            
                            $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/invoices/'.$id.'/payments';
                        } elseif($param['module'] == 'supllierinvoicesettopaid') {
                            $data = array(
                                        'id' => $id,
                                        'datepaye' => strtotime($parameter['datepaye']),
                                        'payment_mode_id' => $parameter['payment_mode_id'],
                                        'closepaidinvoices' => $parameter['closepaidinvoices'],
                                        'accountid' => $parameter['accountid']
                                    );
                            
                            $serverurl = $this->paramConnexion['url'].$this->dolibarrurl.'/supplierinvoices/'.$id.'/payments';
                        }

                        $response = $this->dolibarrClient->post($serverurl, json_encode($data));
                        // var_dump($response);exit;
                        if($response) {
                            if($this->dolibarrClient->info['http_code'] == 200) {
                                if($param['module'] == 'supllierinvoicesettopaid') {
                                    $result[$idDoc] = [
                                        'id' => $response.'_'.$id,
                                        'error' => false,
                                    ];
                                } elseif($param['module'] == 'invoicepayment') {
                                    $result[$idDoc] = [
                                        'id' => $response.'_'.$id,
                                        'error' => false,
                                    ];
                                } elseif($param['module'] == 'invoicesettopaid') {
                                    $result[$idDoc] = [
                                        'id' => $id,
                                        'error' => false,
                                    ];
                                }
                            } else {
                                $result[$idDoc] = [
                                    'id' => '-1',
                                    'error' => 'Something Went wrong',
                                ];
                            }
                        } else {
                            $result[$idDoc] = [
                                'id' => '-1',
                                'error' => $response,
                            ];
                        }
                    } else {
                        $result[$idDoc] = [
                            'id' => '-1',
                            'error' => 'Id is missing or ref number is missing',
                        ];
                    }
                } else {
                    $result[$idDoc] = [
                        'id' => '-1',
                        'error' => 'Balance is not Zero',
                    ];
                }
            }
        }

        $this->updateDocumentStatus($idDoc, $result[$idDoc], $param);

        return $result;

    }



    /**
    * @throws \Exception
    */
    protected function dateTimeFromMyddleware($dateTime)
    {
        $date = new \DateTime($dateTime);

        return $date->format('U');
    }

    protected function dolibarrApiDateFormate($dateTime) {

        $date = new \DateTime($dateTime);

        return $date->format('YmdHis');
    }

    public function getRefFieldName($param): string
    {
        return 'date_modification';
    }
    

}