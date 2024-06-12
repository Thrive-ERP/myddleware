<?php

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
        self::$clientId = $paramConnexion['clientid'];
        self::$clientSecret = $paramConnexion['clientsecret'];
        self::$tenantId = $paramConnexion['tenantid'];
    
        $scopes = ['https://graph.microsoft.com/.default'];
    
        self::$tokenContext = new ClientCredentialContext(
            self::$tenantId,
            self::$clientId,
            self::$clientSecret
        );
    
        try {
            $this->graphClient = new GraphServiceClient(self::$tokenContext, ['https://graph.microsoft.com/.default']);
            $accessToken = $this->getAppOnlyToken();
            print_r($accessToken);
            $this->logger->info('Access token retrieved successfully: ' . $accessToken);
            $this->connexion_valide = true;
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            return ['error' => $error];
        }
    }

    private function getAppOnlyToken() {
        $tokenProvider = new GraphPhpLeagueAccessTokenProvider(self::$tokenContext);
        return $tokenProvider
            ->getAuthorizationTokenAsync('https://graph.microsoft.com/.default')
            ->wait();
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
    
            $userResponse = $this->graphClient->users()
                ->request($queryParams)
                ->getAsync()
                ->wait();
    
            foreach ($userResponse->getValue() as $user) {
                $row = [];
                foreach ($param['fields'] as $field) {
                    $row[$field] = $user->$field ?? null;
                }
                $row['id'] = $user->getId();
                $row['date_modified'] = $this->dateTimeToMyddleware($user->getLastModifiedDateTime());
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
