<?php

use Microsoft\Graph\Core\Authentication\GraphPhpLeagueAccessTokenProvider;
use Microsoft\Graph\Generated\Models;
use Microsoft\Graph\Generated\Users\UsersRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\UsersRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Graph\Generated\Users\Item\Contacts\ContactsRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\Contacts\ContactsRequestBuilderGetRequestConfiguration;

class GraphHelper {
    
    private static string $clientId = '';
    private static string $clientSecret = '';
    private static string $tenantId = '';
    private static ClientCredentialContext $tokenContext;
    private static GraphServiceClient $appClient;

    public static function initializeGraphForAppOnlyAuth(): void {
        GraphHelper::$clientId = $_ENV['CLIENT_ID'];
        GraphHelper::$clientSecret = $_ENV['CLIENT_SECRET'];
        GraphHelper::$tenantId = $_ENV['TENANT_ID'];

        GraphHelper::$tokenContext = new ClientCredentialContext(
            GraphHelper::$tenantId,
            GraphHelper::$clientId,
            GraphHelper::$clientSecret);

        GraphHelper::$appClient = new GraphServiceClient(
            GraphHelper::$tokenContext, ['https://graph.microsoft.com/.default']);
    }
    
    public static function getAppOnlyToken(): string {
        $tokenProvider = new GraphPhpLeagueAccessTokenProvider(GraphHelper::$tokenContext);
        return $tokenProvider
            ->getAuthorizationTokenAsync('https://graph.microsoft.com/.default')
            ->wait();
    }
    
    public static function getUsers(): Models\UserCollectionResponse {
        $configuration = new UsersRequestBuilderGetRequestConfiguration();
        $configuration->queryParameters = new UsersRequestBuilderGetQueryParameters();
        $configuration->queryParameters->select = ['displayName','id','mail'];
        $configuration->queryParameters->orderby = ['displayName'];
        $configuration->queryParameters->top = 25;

        return GraphHelper::$appClient->users()->get($configuration)->wait();
    }
    
    public static function getContacts(): Models\ContactCollectionResponse {
        try {
            $configuration = new ContactsRequestBuilderGetRequestConfiguration();
            $configuration->queryParameters = new ContactsRequestBuilderGetQueryParameters();
            $configuration->queryParameters->select = ['displayName','id','emailAddresses'];
            $configuration->queryParameters->orderby = ['displayName'];
            $configuration->queryParameters->top = 25;

            $response = GraphHelper::$appClient->me()->contacts()->get($configuration)->wait();
            print_r(GraphHelper::$appClient->me());
            print_r($response);

            return $response;
        } catch (\Exception $e) {
            echo 'Erreur lors de la récupération des contacts: '.$e->getMessage().PHP_EOL;
            echo 'Stack Trace: '.$e->getTraceAsString().PHP_EOL;
            throw $e;
        }
    }

    public static function makeGraphCall(): void {
        $configuration = new ContactsRequestBuilderGetRequestConfiguration();
        $configuration->queryParameters = new ContactsRequestBuilderGetQueryParameters();
        $configuration->queryParameters->select = ['displayName','id','mail'];
        $configuration->queryParameters->orderby = ['displayName'];
        $configuration->queryParameters->top = 25;

        $result =  GraphHelper::$appClient->me()->contacts()->get($configuration)->wait();
        
        echo 'toto'.chr(10);
        print_r($result);
    }
}
?>
