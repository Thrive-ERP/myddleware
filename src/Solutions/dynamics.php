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
use Microsoft\Kiota\Abstractions\Store\BackedModel;
use Microsoft\Kiota\Abstractions\RequestInformation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Graph\Core\Authentication\GraphPhpLeagueAccessTokenProvider;
use Microsoft\Kiota\Abstractions\Authentication\BaseBearerTokenAuthenticationProvider;
use YourNamespace\YourAccessTokenProvider;

class DynamicsCore extends Solution
{
    protected $graphClient;
    public static $tenantId;
    public static $clientId;
    public static $clientSecret;

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

        $tokenContext = new ClientCredentialContext(
            self::$tenantId,
            self::$clientId,
            self::$clientSecret
        );

        try {
            $this->graphClient = new GraphServiceClient($tokenContext, $scopes);
            $accessToken = $this->getAppOnlyToken();
            dump("Connecté avec succès. Token: " . $accessToken);
            die();
            // $authenticationProvider = new ClientCredentialProvider($tokenContext);
            // $this->graphClient = GraphServiceClient::createWithAuthenticationProvider($authenticationProvider);
            // $accessToken = $this->getAppOnlyToken();
            // echo "Connecté avec succès. Token: " . $accessToken . "\n";
    
            return true;
        } catch (Exception $e) {
            $this->last_error = "Erreur lors de la connexion : " . $e->getMessage();
            return false;
        }
    }

    private function getAppOnlyToken()
{
    //$tokenProvider = new GraphPhpLeagueAccessTokenProvider($this->graphClient->createWithAuthenticationProvider());
    //return $this->graphClient->getAuthenticationProvider()->getAuthorizationTokenAsync('https://graph.microsoft.com/.default').wait();
    //return $tokenProvider->getAuthorizationTokenAsync('https://graph.microsoft.com/.default')->wait();
    return $this->graphClient->getAuthenticationProvider()->getAccessToken();
}
}

class dynamics extends dynamicscore
{
}
