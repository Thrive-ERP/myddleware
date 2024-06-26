<?php

require_once realpath(__DIR__ . '/vendor/autoload.php');
require_once 'GraphHelper.php';

print('PHP Graph Tutorial'.PHP_EOL.PHP_EOL);

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env.local');
$dotenv->load();
$dotenv->required(['CLIENT_ID', 'CLIENT_SECRET', 'TENANT_ID']);

initializeGraph();

$choice = -1;

while ($choice != 0) {
    echo('Please choose one of the following options:'.PHP_EOL);
    echo('0. Exit'.PHP_EOL);
    echo('1. Display access token'.PHP_EOL);
    echo('2. List users'.PHP_EOL);
    echo('3. List contacts'.PHP_EOL);
    echo('4. Make a Graph call'.PHP_EOL);

    $choice = (int)readline('');

    switch ($choice) {
        case 1:
            displayAccessToken();
            break;
        case 2:
            listUsers();
            break;
        case 3:
            listContacts();
            break;
        case 4:
            makeGraphCall();
            break;
        case 0:
        default:
            print('Goodbye...'.PHP_EOL);
    }
}

function initializeGraph(): void {
    GraphHelper::initializeGraphForAppOnlyAuth();
}

function displayAccessToken(): void {
    try {
        $token = GraphHelper::getAppOnlyToken();
        print('App-only token: '.$token.PHP_EOL.PHP_EOL);
    } catch (Exception $e) {
        print('Error getting access token: '.$e->getMessage().PHP_EOL.PHP_EOL);
    }
}

function listUsers(): void {
    try {
        $users = GraphHelper::getUsers();

        // Output each user's details
        foreach ($users->getValue() as $user) {
            print('User: '.$user->getDisplayName().PHP_EOL);
            print('  ID: '.$user->getId().PHP_EOL);
            $email = $user->getMail();
            $email = isset($email) ? $email : 'NO EMAIL';
            print('  Email: '.$email.PHP_EOL);
        }

        $nextLink = $users->getOdataNextLink();
        $moreAvailable = isset($nextLink) && $nextLink != '' ? 'True' : 'False';
        print(PHP_EOL.'More users available? '.$moreAvailable.PHP_EOL.PHP_EOL);
    } catch (Exception $e) {
        print(PHP_EOL.'Error getting users: '.$e->getMessage().PHP_EOL.PHP_EOL);
    }
}

function listContacts(): void {
    try {
        $contacts = GraphHelper::getContacts();

        foreach ($contacts->getValue() as $contact) {
            print('Contact: '.$contact->getDisplayName().PHP_EOL);
            print('  ID: '.$contact->getId().PHP_EOL);
            $emails = $contact->getEmailAddresses();
            $email = isset($emails) ? implode(', ', array_map(fn($e) => $e->getAddress(), $emails)) : 'NO EMAIL';
            print('  Email: '.$email.PHP_EOL);
        }

        $nextLink = $contacts->getOdataNextLink();
        $moreAvailable = isset($nextLink) && $nextLink != '' ? 'True' : 'False';
        print(PHP_EOL.'More contacts available? '.$moreAvailable.PHP_EOL.PHP_EOL);
    } catch (Exception $e) {
        print(PHP_EOL.'Error getting contacts: '.$e->getMessage().PHP_EOL.PHP_EOL);
        print('Stack Trace: '.$e->getTraceAsString().PHP_EOL.PHP_EOL);
    }
}


function makeGraphCall(): void {
    try {
        GraphHelper::makeGraphCall();
    } catch (Exception $e) {
        print(PHP_EOL.'Error making Graph call'.PHP_EOL.PHP_EOL);
    }
}

?>
