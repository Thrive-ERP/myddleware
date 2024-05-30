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
            $this->connexion_valide = true;
            $this->tryGettingData();
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            return ['error' => $error];
        }
    }

    // Permet de récupérer tous les modules accessibles à l'utilisateur
    public function get_modules($type = 'source')
    {
        try {
            if ('source' == $type) {
                return [
                    'users' => 'Users',
                ];
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function tryGettingData()
    {
        
        try {
            $userPackage = $this->graphClient->users()->get()->wait();

            // The user's name is in the 'displayName' field
            $userList = $userPackage->getValue();
            $userName = $userList[0]->getDisplayName();
            $cashmir = 'toto';

            echo "User's display name: $userName\n";
        } catch (\Exception $e) {
            $this->logger->error('Error when trying to get data: ' . $e->getMessage());
        }
    }

    private function getAppOnlyToken() {
        $tokenProvider = new GraphPhpLeagueAccessTokenProvider(self::$tokenContext);
        return $tokenProvider
            ->getAuthorizationTokenAsync('https://graph.microsoft.com/.default')
            ->wait();
    }
}

class dynamics extends dynamicscore
{
}
