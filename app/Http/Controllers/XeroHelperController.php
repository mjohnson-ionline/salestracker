<?php

use App\Http\Controllers\Controller;
use Webfox\Xero\OauthCredentialManager;
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;
use XeroAPI\XeroPHP\FinanceObjectSerializer;

class XeroHelperController extends Controller
{
    public static function getXeroContacts($xeroTenantId, $apiInstance, $returnObj = false)
    {
        echo 123;
        exit();
        $str = '';

        //[Contacts:Read]
        // read all contacts
        $result = $apiInstance->getContacts($xeroTenantId);

        // filter by contacts by status
        $where = 'ContactStatus=="ACTIVE"';
        $result2 = $apiInstance->getContacts($xeroTenantId, null, $where);
        //[/Contacts:Read]

        $str = $str . "Get Contacts Total: " . count($result->getContacts()) . "<br>";
        $str = $str . "Get ACTIVE Contacts Total: " . count($result2->getContacts()) . "<br>";

        if ($returnObj) {
            return $result2;
        } else {
            return $str;
        }

    }
}
