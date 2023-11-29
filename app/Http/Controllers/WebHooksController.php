<?php

namespace App\Http\Controllers;

use App\Mail\SendInvoiceGeneratedNoticeMail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webfox\Xero\OauthCredentialManager;
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;
use XeroAPI\XeroPHP\FinanceObjectSerializer;
use Devio\Pipedrive\Pipedrive;
use App\Models\Log;
use App\Models\User;
use DB;
use App\Models\XeroSync;
use App\Models\Products;

/*
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE `deals`;
TRUNCATE `invoices`;
TRUNCATE `line_items`;
TRUNCATE `logs`;
TRUNCATE `payments`;
TRUNCATE `transactions`;
TRUNCATE `xero_syncs`;
SET FOREIGN_KEY_CHECKS=1;
 */
class WebHooksController extends Controller
{
    // https://developer.xero.com/app/manage/app/ace2680c-fad1-4c26-97c1-627b1ae8f25c/webhooks
    public function xero(Request $request)
    {

        // if the signatures don't match, return a 401
        $xero_signature = $request->header('x-xero-signature');
        $signature = base64_encode(hash_hmac('sha256', $request->getContent(), env('XERO_WEBHOOK_KEY'), true));
        if (!hash_equals($signature, $xero_signature)) {
            return response(
                'null',
                401
            );
        }

        // log the webhook for processing
        $log = Log::create([
            'type' => 'payment.received',
            'platform' => 'xero',
            'payload' => $request->getContent(),
        ]);

        // continue with the rest
        return response(
            'null',
            200
        );
    }

    public function pipedrive(Request $request, OauthCredentialManager $xeroCredentials)
    {

        // make sure the same webhook doesnt fire multiple times for the same deal - there is also a command that runs every minute to clean up duplicates - remove:duplicated-pipedrive-syncs that runs every minute as a scheduled task
        // this is useless as the webhook_id is different every time
        /*$webhook_id = Log::where('webhook_id', $request->meta['webhook_id'])->get();
        if (!is_null($webhook_id)) {
            if( count($webhook_id) >= 1 ) {
                exit();
            }
        }*/

        $log = Log::create([
            'type' => 'deal.updated',
            'payload' => $request->all(),
            'webhook_id' => $request->meta['webhook_id'],
            'platform' => 'pipedrive',
        ]);
        $this->write_local_log_file(serialize($log));

        // return a 200 response
        return response()->json([
            'success' => true,
        ], 200);

    }




























































    //
    public function xero_old(Request $request)
    {
        // capture all xero payments entered
        // check all deals in pipedrive for the invoice number
        // update the activity in pipedrive with the payment details
        // send an email to service@ with the payment details

        // https://developer.xero.com/app/manage/app/ace2680c-fad1-4c26-97c1-627b1ae8f25c/webhooks

        // return the intent to receive the payment
        $log = Log::create([
            'type' => 'xero_x-xero-signature',
            'platform' => 'xero',
            'payload' => $request->header('x-xero-signature'),
        ]);
        $xero_signature = $request->header('x-xero-signature');
        \Log::info("xero_sig: " .$xero_signature);

        $signature = base64_encode(hash_hmac('sha256', $request->getContent(), env('XERO_WEBHOOK_KEY'), true));
        \Log::info("hashed_sig: " .$signature);
        $log = Log::create([
            'type' => 'xero_computed',
            'platform' => 'xero',
            'payload' => $signature,
        ]);

        ////// DISABLE THIS BLOCK TO GET THE INTENTS SETUP //////
        $log = Log::create([
            'type' => 'xero_content',
            'platform' => 'xero',
            'payload' => $request->getContent(),
        ]);
        ////// DISABLE THIS BLOCK TO GET THE INTENTS SETUP //////

        // if the signatures don't match, return a 401
        if (!hash_equals($signature, $xero_signature)) {
            return response(
                'null',
                401
            );
        }

        // continue with the rest
        return response(
            'null',
            200
        );
    }

    public function generateRandomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass); //turn the array into a string
    }

    public function sync_xero(Request $request, OauthCredentialManager $xeroCredentials)
    {
        exit();
        // mock data
        $file = fopen('sync_xero.txt', 'r');
        $data = fread($file, filesize('/home/ionline_admin/webapps/proposal-app/public/logs.txt'));
        $user_data = unserialize($data);
        fclose($file);

        // always make a connection to xero
        $xero = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);

        // check to see if there is a contact with the same email address locally and if it has a xero id
        $local_user = User::where('email', '=', uniqid() . $user_data['xero_contact_email'])->first();
        if (is_null($local_user)) {
            $local_user = User::create([
                'organisation_name' => $user_data['xero_organisation_name'],
                'first_name' => $user_data['xero_contact_first_name'],
                'last_name' => $user_data['xero_contact_last_name'],
                'email' => uniqid() . $user_data['xero_contact_email'],
                'phone' => $user_data['xero_contact_phone'],
                'address_line_1' => $user_data['xero_address_line_1'],
                'address_line_2' => $user_data['xero_address_line_2'],
                'address_suburb' => $user_data['xero_address_suburb'],
                'address_state' => $user_data['xero_address_state'],
                'address_postcode' => $user_data['xero_address_postcode'],
                'address_country' => $user_data['xero_address_country'],
                'password' => bcrypt($this->generateRandomPassword()),
                'type' => 2,
            ]);

            // save the xero_contact id locally
            $xero_org_object = $xero->createContacts($xeroCredentials->getTenantId(), [
                'Contacts' => [
                    [
                        'Name' => $local_user->organisation_name . ' - ' . uniqid(),
                        'FirstName' => $local_user->first_name,
                        'LastName' => $local_user->last_name,
                        'EmailAddress' => uniqid() . $local_user->email,
                        'Phones' => [
                            [
                                'PhoneType' => 'DEFAULT',
                                'PhoneNumber' => $local_user->phone
                            ]
                        ],
                        'Addresses' => [
                            [
                                'AddressType' => 'STREET',
                                'AddressLine1' => $local_user->address_line_1,
                                'City' => $local_user->address_suburb,
                                'PostalCode' => $local_user->address_postcode,
                                'Country' => $local_user->address_country
                            ]
                        ],
                    ]
                ]
            ]);
            $local_user->xero_id = $xero_org_object->getContacts()[0]->getContactID();
            $local_user->save();

        } else {
            // get the xero org object by contact id
            $xero_org_object = $xero->getContact($xeroCredentials->getTenantId(), $local_user->xero_id);
        }

        // if we got the xero org object
        if ($xero_org_object) {

            // create a draft invoice for each of the items in the payment schedule
            $payment_data = $user_data['payments_data'];
            if (!is_null($payment_data)) {
                if (is_array($payment_data)) {
                    if (count($payment_data) > 0) {
                        foreach ($payment_data as $schedule) {
                            $xero_invoice_object = $xero->createInvoices($xeroCredentials->getTenantId(), [
                                'Invoices' => [
                                    [
                                        'Type' => 'ACCREC',
                                        'Contact' => [
                                            'ContactID' => $local_user->xero_id
                                        ],
                                        'DueDate' => date('Y-m-d', strtotime($schedule->due_at)),
                                        'LineItems' => [
                                            [
                                                'Description' => $schedule->description,
                                                'Quantity' => 1,
                                                'UnitAmount' => $schedule->amount,
                                                'AccountCode' => '210',
                                            ]
                                        ]
                                    ]
                                ]
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function sync_x2p(OauthCredentialManager $xeroCredentials)
    {

        exit();

        // always make a connection to xero
        $xero = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);

        /// always make a connection to pipedrive
        $client = new \GuzzleHttp\Client();

        // get all payments
        /*
        $xero_payments = $xero->getPayments($xeroCredentials->getTenantId(), null, null, null, null, null, null, null, null, null, null, null, null, null);
        $all_payments = [];
        foreach ($xero_payments as $k => $payment) {
            if ($payment['status'] == 'AUTHORISED') {
                if ($payment['payment_type'] == 'ACCRECPAYMENT') {
                    $all_payments[$k]['contact_id'] = $payment['invoice']['contact']['contact_id'];
                    $all_payments[$k]['date'] = $this->convertToMySQLDateTime($payment['date']);
                }
            }
        }

        // for each payment, if the date is older than 2 years, remove it
        foreach ($all_payments as $k => $payment) {
            if (strtotime($payment['date']) < strtotime('-2 years')) {
                unset($all_payments[$k]);
            }
        }

        // foreach payment, remove any duplicates so we only have a unique list of customers
        $deduplicated_array = [];
        foreach ($all_payments as $k => $payment) {
            if (!in_array($payment['contact_id'], $deduplicated_array)) {
                $deduplicated_array[] = $payment['contact_id'];
            }
        }

        // foreach of the $deduplicated_array, create a new entry in the XeroSyncs table
        foreach ($deduplicated_array as $k => $contact_id) {
            $xero_sync = XeroSync::create([
                'contact_id' => $contact_id,
            ]);
        }
        */

        // get all the syncs from the XeroSyncs table that have a status of null, when they are inserted into the db, mark them as 'inserted'
        /*$xero_syncs = XeroSync::where('status', '=', null)->get();
        foreach ($xero_syncs as $key => $sync) {
            $xero_contact = $xero->getContact($xeroCredentials->getTenantId(), $sync->contact_id);
            $sync->company_name = $xero_contact[0]['name'];
            $sync->company_number = $xero_contact[0]['company_number'];
            $sync->first_name = $xero_contact[0]['first_name'];
            $sync->last_name = $xero_contact[0]['last_name'];
            $sync->email_address = $xero_contact[0]['email_address'];
            foreach ($xero_contact[0]['phones'] as $phone) {
                if ($phone['phone_type'] == 'DEFAULT') {
                    $sync->primary_phone = $phone['phone_number'];
                }
            }
            $sync->address_line_1 = $xero_contact[0]['addresses'][0]['address_line1'];
            $sync->address_line_2 = $xero_contact[0]['addresses'][0]['address_line2'];
            $sync->address_suburb = $xero_contact[0]['addresses'][0]['city'];
            $sync->address_state = $xero_contact[0]['addresses'][0]['region'];
            $sync->address_postcode = $xero_contact[0]['addresses'][0]['postal_code'];
            $sync->address_country = $xero_contact[0]['addresses'][0]['country'];
            $sync->status = 'imported';
            $sync->save();
            sleep(1);
        }*/

        // get all the syncs from the XeroSyncs table, and if they have no first_name or last_name update it as "No First Name Provided" and "No Last Name Provided"
        /*$xero_syncs = XeroSync::all();
        foreach ($xero_syncs as $key => $sync) {
            if (is_null($sync->first_name) || empty($sync->first_name)) {
                $sync->first_name = 'No First Name Provided';
            }
            if (is_null($sync->last_name) || empty($sync->last_name)) {
                $sync->last_name = 'No Last Name Provided';
            }
            $sync->save();
        }
        exit();*/

        // add the organisation and person to pipedrive, give them a status of uploaded.
        /*$pipedrive_syncs = XeroSync::where('status', '=', 'imported')->get();
        foreach ($pipedrive_syncs as $key => $c_array) {

            // check to see if there are any persons with this email address
            $res = $client->request('GET', 'https://api.pipedrive.com/v1/persons/search?term=' . $c_array['email_address'] . '&api_token='.config('app.pipedrive_api_token'));
            $results = json_decode($res->getBody()->getContents());

            // if there are no results
            if (is_array($results->data->items) && count($results->data->items) > 0) {
                $c_array->status = 'uploaded';
                $c_array->save();
                continue;

            }

            // add the company
            $res = $client->request('POST', 'https://api.pipedrive.com/v1/organizations?api_token='.config('app.pipedrive_api_token'), [
                'form_params' => [
                    'name' => $c_array['company_name'] ?? 'No Company Name Found',
                    'owner_id' => '14161235', // team_ionline
                    'visible_to' => '3',
                    'address' => $c_array['address_line_1'] . ' ' . $c_array['address_line_2'] . ' ' . $c_array['address_suburb'] . ' ' . $c_array['address_state'] . ' ' . $c_array['address_postcode'] . ' ' . $c_array['address_country'],
                ]
            ]);
            $results = json_decode($res->getBody()->getContents());
            $company_id = $results->data->id;

            // now add the person
            $res = $client->request('POST', 'https://api.pipedrive.com/v1/persons?api_token='.config('app.pipedrive_api_token'), [
                'form_params' => [
                    'name' => $c_array['first_name'] . ' ' . $c_array['last_name'],
                    'owner_id' => '14161235', // team_ionline
                    'visible_to' => '3',
                    'org_id' => $company_id,
                    'email' => $c_array['email_address'],
                    'phone' => $c_array['primary_phone'],
                ]
            ]);
            $results = json_decode($res->getBody()->getContents());

            $c_array->status = 'uploaded';
            $c_array->save();

        }*/

        // forcefully update all the lastnames for the persons
        $pipedrive_syncs = XeroSync::where('status', '=', 'uploaded')->get();
        foreach ($pipedrive_syncs as $key => $c_array) {

            // check to see if there are any persons with this email address
            $res = $client->request('GET', 'https://api.pipedrive.com/v1/persons/search?term=' . $c_array['email_address'] . '&api_token=' . config('app.pipedrive_api_token'));
            $results = json_decode($res->getBody()->getContents());

            // update the last name for the person
            $res = $client->request('PUT', 'https://api.pipedrive.com/v1/persons/' . $results->data->items[0]->item->id . '?api_token=' . config('app.pipedrive_api_token'), [
                'form_params' => [
                    'last_name' => $c_array['last_name'],
                ]
            ]);

        }

    }

    public function convertToMySQLDateTime($xeroDate)
    {
        // Extract the timestamp using regex
        if (preg_match('/\/Date\((\d+)([+-]\d+)?\)\//', $xeroDate, $matches)) {
            $timestamp = $matches[1] / 1000; // Convert from milliseconds to seconds

            // Convert the timestamp to MySQL datetime format
            $date = new \DateTime("@$timestamp"); // '@' specifies that the constructor should expect a Unix timestamp
            return $date->format('Y-m-d H:i:s');
        }
        return false; // Return false if the format doesn't match
    }

    public function view($id = null)
    {
        // get the newest log
        $log = Log::all()->last();
        dd($log->payload['meta'], $log->payload['current']);
    }

    public function view_transactions($id = null)
    {
        // get the log and all associated transactions
        // get the last log
        $log = Log::all()->last();
        // display the transactions in a neat table
        foreach ($log->transactions as $transaction) {
            echo $transaction->details . '<br>';
        }
    }

    public function write_local_log_file($data)
    {
        $file = fopen('/home/ionline_admin/webapps/proposal-app/public/logs.txt', 'w');
        fwrite($file, $data);
        fclose($file);
    }

    public function unserial()
    {
        $file = fopen('/home/ionline_admin/webapps/proposal-app/public/logs.txt', 'r');
        $data = fread($file, filesize('logs.txt'));
        $unserial = unserialize($data);
        fclose($file);
        // dd(unserialize($data));
        dd($unserial['payload']);
    }

    public function sync_pipedrive_products()
    {

        $pipedrive = new Pipedrive(config('app.pipedrive_api_token'));

        // get all products from pipedrive of page 1
        $products = $pipedrive->products->all(['start' => 0, 'limit' => 500]);

        // update or create them
        if (!is_null($products)) {
            if (is_array($products->getContent()->data) && count($products->getContent()->data) > 0) {
                foreach ($products->getContent()->data as $key => $data) {
                    $product = Products::updateOrCreate(['pipedrive_id' => $data->id], ['pipedrive_id' => $data->id, 'name' => $data->name, 'price' => $data->prices[0]->price, 'xero_code' => $data->code,]);
                }
            }
        }

    }


}
