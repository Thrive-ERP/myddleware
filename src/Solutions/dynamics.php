<?php
/*********************************************************************************
 * This file is part of Myddleware.
 * @package Myddleware
 * @copyright Copyright (C) 2013 - 2015  Stéphane Faure - CRMconsult EURL
 * @copyright Copyright (C) 2015 - 2017  Stéphane Faure - Myddleware ltd - contact@myddleware.com
 * @link http://www.myddleware.com
 *
 * This file is part of Myddleware.
 *
 * Myddleware is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Myddleware is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Myddleware.  If not, see <http://www.gnu.org/licenses/>.
 *********************************************************************************/

namespace App\Solutions;

use Microsoft\Graph\GraphServiceClient;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Graph\Core\Authentication\GraphPhpLeagueAccessTokenProvider;

class DynamicsCore extends Solution
{
    protected $graphClient;
    public static $tenantId;
    public static $clientId;
    public static $clientSecret;
    public static $tokenContext;
    protected $last_error = '';

    public function getFieldsLogin(): array
    {
        return [
            [
                'name' => 'clientid',
                'type' => TextType::class,
                'label' => 'solution.fields.clientid',
            ],
            [
                'name' => 'clientsecret',
                'type' => PasswordType::class,
                'label' => 'solution.fields.clientsecret',
            ],
            [
                'name' => 'tenantid',
                'type' => TextType::class,
                'label' => 'solution.fields.tenantid',
            ],
        ];
    }

    public function login($paramConnexion)
    {
        $encrypter = new \Illuminate\Encryption\Encrypter(substr($this->parameterBagInterface->get('secret'), -16));
        parent::login($paramConnexion);
        
        self::$clientId = $encrypter->decrypt($paramConnexion['clientid']);
        self::$clientSecret = $encrypter->decrypt($paramConnexion['clientsecret']);
        self::$tenantId = $encrypter->decrypt($paramConnexion['tenantid']);
        // self::$clientId = $paramConnexion['clientid'];
        // self::$clientSecret = $paramConnexion['clientsecret'];
        // self::$tenantId = $paramConnexion['tenantid'];
    
        $scopes = ['https://graph.microsoft.com/.default'];

    
        self::$tokenContext = new ClientCredentialContext(
            self::$tenantId,
            self::$clientId,
            self::$clientSecret
        );
    
        try {
            $this->graphClient = new GraphServiceClient(self::$tokenContext, ['https://graph.microsoft.com/.default']);
            $accessToken = $this->getAppOnlyToken();
            //$this->logger->info('Access token retrieved successfully: ' . $accessToken);
            $this->connexion_valide = true;
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error('Error during login: ' . $error);
            $this->logger->error('Trace: ' . $e->getTraceAsString());
            return ['error' => $error];
        }
    }

    private function getAppOnlyToken() {
        $tokenProvider = new GraphPhpLeagueAccessTokenProvider(self::$tokenContext);
        try {
            return $tokenProvider
                ->getAuthorizationTokenAsync('https://graph.microsoft.com/.default')
                ->wait();
        } catch (\Exception $e) {
            $this->logger->error('Error getting authorization token: ' . $e->getMessage());
            throw $e;
        }
    }


    public function get_modules($type = 'source'): array
    {
        try {
            if ('source' == $type) {
                return [
                    'users' => 'Users',
                ];
            }

            return [
                'users' => 'Users',
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
            $moduleFields = [];
            if ($module === 'users') {
                $moduleFields = [
                    'id' => [
                        'label' => 'ID',
                        'type' => 'string',
                        'length' => 36, 
                        'required' => true
                    ],
                    'displayName' => [
                        'label' => 'User',
                        'type' => 'string',
                        'length' => 255, 
                        'required' => true
                    ],
                    'mail' => [
                        'label' => 'Email',
                        'type' => 'string',
                        'length' => 255,
                        'required' => false
                    ]
                ];
            }
    
            return $moduleFields;
        } catch (\Exception $e) {
            $error = $e->getMessage() . ' ' . $e->getFile() . ' Line : ( ' . $e->getLine() . ' )';
            $this->logger->error($error);
    
            return ['error' => $error];
        }
    }

    public function read($param)
    {
        $module = $param['module'];
        $result = [];
        $dateRef = $this->dateTimeFromMyddleware($param['date_ref']);
        $limit = $param['limit'] ?? 10;
    
        try {
            $queryParams = [
                '$select' => implode(',', array_keys($param['fields'])),
                '$orderby' => 'createdDateTime desc',
                '$filter' => "createdDateTime ge {$dateRef}",
                '$top' => $limit
            ];
    
            $this->logger->info('Requesting data with query parameters: ' . json_encode($queryParams));
    
            $usersRequestBuilder = $this->graphClient->users();
            $usersResponse = $usersRequestBuilder->get()->wait(); // Wait for the promise to resolve
    
            $this->logger->info('User fields: ' . json_encode($usersResponse->getValue()[0]));
    
            // Iterate over the response
            foreach ($usersResponse->getValue() as $user) {
                $row = [];
                foreach ($param['fields'] as $field) {
                    $row[$field] = $user->$field ?? null;
                }
                $row['id'] = $user->getId();
                
                // if (isset($user->createdDateTime)) {
                //     $row['date_created'] = $this->dateTimeToMyddleware($user->createdDateTime);
                // } else {
                //     $row['date_created'] = null; 
                // }
                if (isset($user->lastModifiedDateTime)) {
                    $row['date_modified'] = $this->dateTimeToMyddleware($user->lastModifiedDateTime);
                } elseif (isset($user->createdDateTime)) {
                    $row['date_modified'] = $this->dateTimeToMyddleware($user->createdDateTime);
                } else {
                    $row['date_modified'] = null; // or some default value
                }
                $result[] = $row;
            }
        } catch (\Exception $e) {
            $result['error'] = 'Error : '.$e->getMessage().' '.$e->getFile().' Line : ('.$e->getLine().')';
            $this->logger->error('Error reading data: ' . $e->getMessage());
        }
    
        return $result;
    }
    

    public function createData($param): array
    {
        return $this->upsert('create', $param);
    }

    public function updateData($param): array
    {
        return $this->upsert('update', $param);
    }

    public function upsert($method, $param): array
    {
        $result = [];
        foreach ($param['data'] as $idDoc => $data) {
            try {
                $param['method'] = $method;
                $module = $param['module'];
                $data = $this->checkDataBeforeCreate($param, $data, $idDoc);

                if ($method === 'create') {
                    unset($data['target_id']);
                    $recordResult = $this->graphClient->users()->create($data)->wait();
                } else {
                    $targetId = $data['target_id'];
                    unset($data['target_id']);
                    $recordResult = $this->graphClient->users()->update($targetId, $data)->wait();
                }

                $response = $recordResult;
                if ($response) {
                    $record = $response;
                    if (!empty($record->id)) {
                        $result[$idDoc] = [
                            'id' => $record->id,
                            'error' => false,
                        ];
                    } else {
                        throw new \Exception('Error during ' . print_r($response, true));
                    }
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $result[$idDoc] = [
                    'id' => '-1',
                    'error' => $error,
                ];
            }
            $this->updateDocumentStatus($idDoc, $result[$idDoc], $param);
        }

        return $result;
    }

    protected function dateTimeToMyddleware($dateTime)
    {
        $dto = new \DateTime($dateTime);
        return $dto->format('Y-m-d H:i:s');
    }

    protected function dateTimeFromMyddleware($dateTime)
    {
        $dto = new \DateTime($dateTime);
        return $dto->format('Y-m-d\TH:i:s') . 'Z';
    }
}

class dynamics extends DynamicsCore
{
}
