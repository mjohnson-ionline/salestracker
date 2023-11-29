<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Webfox\Xero\OauthCredentialManager;
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;
use XeroAPI\XeroPHP\FinanceObjectSerializer;
use App\Http\Controllers\XeroHelperController;


class XeroController extends Controller
{

    public function index(Request $request, OauthCredentialManager $xeroCredentials)
    {
        try {
            // Check if we've got any stored credentials
            if ($xeroCredentials->exists()) {
                /*
                 * We have stored credentials so we can resolve the AccountingApi,
                 * If we were sure we already had some stored credentials then we could just resolve this through the controller
                 * But since we use this route for the initial authentication we cannot be sure!
                 */
                $xero             = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);
                $organisationName = $xero->getOrganisations($xeroCredentials->getTenantId())->getOrganisations()[0]->getName();
                $user             = $xeroCredentials->getUser();
                $username         = "{$user['given_name']} {$user['family_name']} ({$user['username']})";
            }
        } catch (\throwable $e) {
            // This can happen if the credentials have been revoked or there is an error with the organisation (e.g. it's expired)
            $error = $e->getMessage();

        }

        // dd($this->getXeroContacts($xeroCredentials->getTenantId(), $xero));

        return view('xero', [
            'connected'        => $xeroCredentials->exists(),
            'error'            => $error ?? null,
            'organisationName' => $organisationName ?? null,
            'username'         => $username ?? null,
            'xeroCredentials'  => $xeroCredentials,
        ]);
    }

    // generate a strong 64 character long random string for the PKCE challenge
    public function generatePKCECodeVerifier()
    {
        $randomBytes = random_bytes(64);
        return rtrim(strtr(base64_encode($randomBytes), '+/', '-_'), '=');
    }

    public function getXeroContacts($xeroTenantId, $apiInstance, $returnObj = false)
    {

        $str = '';

        //[Contacts:Read]
        // read all contacts
        $result = $apiInstance->getContacts($xeroTenantId);

        // filter by contacts by status
        $where = 'ContactStatus=="ACTIVE"';
        $result2 = $apiInstance->getContacts($xeroTenantId, null, $where);
        return $result2;
        //[/Contacts:Read]

        // import all contacts and create them as users
        /*foreach ($result->getContacts() as $contact) {
            // check to make sure this contact doesnt already exist
            $client = Client::where('email', $contact['email_address'])->first();
            if ($client) {
                continue;
            }
            $new_client = new Client();
            $new_client->first_name = $contact['first_name'] ?? '';
            $new_client->last_name = $contact['last_name'] ?? '';
            $new_client->company_name = $contact['name'] ?? '';
            $new_client->email = $contact['email_address'] ?? '';
            $new_client->phone = $contact['contact_number'] ?? '';
            $new_client->status = 1;
            $new_client->created_by = 1;
            $new_client->save();
        }

        $str = $str . "Get Contacts Total: " . count($result->getContacts()) . "<br>";
        $str = $str . "Get ACTIVE Contacts Total: " . count($result2->getContacts()) . "<br>";

        if ($returnObj) {
            return $result2;
        } else {
            return $str;
        }*/
    }



}
