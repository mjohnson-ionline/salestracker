<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SendComissionToResellerMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ComissionController extends Controller
{

    public function reportSingle(Request $request)
    {

        // get the reseller with all the details
        $reseller = User::where('id', $request->reseller_id)->with('deals', 'deals.invoices', 'deals.invoices.lineItems', 'deals.invoices.lineItems', 'deals.invoices.payments')->get();
        if (!$reseller) {
            return redirect()->back()->with('error', 'Reseller not found');
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        // if a start and an end date exist, filter the data by removing it from the array
        if ($request->start_date && $request->end_date) {
            foreach ($reseller as $key => $reseller_data) {
                if (!is_null($reseller_data->deals)) {
                    foreach ($reseller_data->deals as $key2 => $deal) {
                        if (!is_null($deal->invoices)) {
                            foreach ($deal->invoices as $key3 => $invoice) {
                                if (!is_null($invoice->payments)) {
                                    foreach ($invoice->payments as $key5 => $payment) {

                                        $unix_start_date = strtotime($start_date);
                                        $unix_end_date = strtotime($end_date);
                                        $unix_created_at = strtotime($payment->created_at);

                                        if ($unix_created_at < $unix_start_date || $unix_created_at > $unix_end_date) {
                                            unset($reseller[$key]->deals[$key2]->invoices[$key3]->payments[$key5]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // loop through each of the payments and calculate the total and download it as a csv
        $total_payments = 0;
        $csv_data = [];
        foreach ($reseller as $reseller_data) {
            foreach ($reseller_data->deals as $key => $deal) {
                $csv_data[] = ['Deal #' . $key + 1 , $deal->name];

                foreach ($deal->invoices as $invoice) {
                    foreach ($invoice->payments as $payment) {
                        $csv_data[] = ['Amount / Date', '$' . number_format($payment->amount, 2) . ' / ' . date('d-m-Y', strtotime($payment->created_at))];
                        $total_payments += $payment->amount;
                    }
                }
            }
            $reseller_data->total_payments = $total_payments;
            $reseller_data->total_comission = $total_payments / $reseller_data->discount_comission;

            $csv_data[] = ['Total Payments', '$' . number_format($total_payments, 2)];
            $csv_data[] = ['Total Comission ('.$reseller_data->discount_comission. ' %)', '$' . number_format($total_payments / $reseller_data->discount_comission, 2)];

        }

        // download it as a csv
        $headers = array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=report.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        );
        $callback = function() use ($csv_data)
        {
            $FH = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function sendSingle(Request $request)
    {

        // get the reseller with all the details
        $reseller = User::where('id', $request->reseller_id)->with('deals', 'deals.invoices', 'deals.invoices.lineItems', 'deals.invoices.lineItems', 'deals.invoices.payments')->get();
        if (!$reseller) {
            return redirect()->back()->with('error', 'Reseller not found');
        }

        // if a start and an end date exist, filter the data by removing it from the array
        if ($request->start_date && $request->end_date) {
            foreach ($reseller as $key => $reseller_data) {
                if (!is_null($reseller_data->deals)) {
                    foreach ($reseller_data->deals as $key2 => $deal) {
                        if (!is_null($deal->invoices)) {
                            foreach ($deal->invoices as $key3 => $invoice) {
                                if ($invoice->created_at < $request->start_date || $invoice->created_at > $request->end_date) {
                                    unset($reseller[$key]->deals[$key2]->invoices[$key3]);
                                }
                            }
                        }
                    }
                }

            }
        }

        // loop through each of the payments and calculate the total and download it as a csv
        $total_payments = 0;
        foreach ($reseller as $reseller_data) {
            if (!is_null($reseller_data->deals)) {
                foreach ($reseller_data->deals as $key => $deal) {
                    if (!is_null($deal->invoices)) {
                        foreach ($deal->invoices as $invoice) {
                            if (!is_null($invoice->payments)) {
                                foreach ($invoice->payments as $payment) {
                                    $total_payments += $payment->amount;
                                }
                            }

                        }
                    }

                }
            }

            if ($total_payments != 0) {
                $reseller_data->total_payments = $total_payments;
                $reseller_data->total_comission = $total_payments / $reseller_data->discount_comission;
            }

        }

        try {
            Mail::to('matthew@ionline.com.au')->send(new SendComissionToResellerMail($reseller_data));
        } catch (\Throwable $th) {
            \Alert::add('error', 'Unable To Send Reseller Comission Report Email')->flash();
        }

        \Alert::add('success', 'Send Reseller Comission Report Email')->flash();
        return redirect()->back();

    }

    public function reportAll(Request $request)
    {

        // set the start and end date to be the first and last day of the month
        $start_date = date('01-m-Y');
        $end_date = date('t-m-Y');

        // get all the resellers
        $resellers_with_data = User::where('role', 'reseller')->with('deals', 'deals.invoices', 'deals.invoices.lineItems', 'deals.invoices.lineItems', 'deals.invoices.payments')->get();

        // if a start and an end date exist, remove all payments from the array
        if ($start_date && $end_date) {
            foreach ($resellers_with_data as $key => $reseller_data) {
                if (!is_null($reseller_data->deals)) {
                    foreach ($reseller_data->deals as $key2 => $deal) {
                        if (!is_null($deal->invoices)) {
                            foreach ($deal->invoices as $key3 => $invoice) {
                                if (!is_null($invoice->payments)) {
                                    foreach ($invoice->payments as $key4 => $payment) {

                                        $unix_start_date = strtotime($start_date);
                                        $unix_end_date = strtotime($end_date);
                                        $unix_created_at = strtotime($payment->created_at);

                                        if ($unix_created_at < $unix_start_date || $unix_created_at > $unix_end_date) {
                                            unset($resellers_with_data[$key]->deals[$key2]->invoices[$key3]->payments[$key4]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // calculate the total number of payments for each reseller
        $total_payments = 0;
        $csv_data = [];
        foreach ($resellers_with_data as $reseller) {
            $csv_data[] = ['Reseller' , $reseller->first_name . ' ' . $reseller->last_name . ' (' . $reseller->organisation_name . ')'];
            if (!is_null($reseller->deals)) {
                foreach ($reseller->deals as $deal) {
                    if (!is_null($deal->invoices)) {
                        foreach ($deal->invoices as $invoice) {
                            if (!is_null($invoice->payments)) {
                                foreach ($invoice->payments as $payment) {
                                    $csv_data[] = ['Amount / Date', '$' . number_format($payment->amount, 2) . ' / ' . date('d-m-Y', strtotime($payment->created_at))];
                                    $total_payments += $payment->amount;
                                }
                            }
                        }
                    }
                }
            }

            if ($total_payments != 0) {
                $reseller_data->total_payments = $total_payments;
                $reseller_data->total_comission = $total_payments / $reseller_data->discount_comission;
                $csv_data[] = ['Total Payments', '$' . number_format($total_payments, 2)];
                $csv_data[] = ['Total Comission ('.$reseller_data->discount_comission. ' %)', '$' . number_format($total_payments / $reseller_data->discount_comission, 2)];
            }
        }

        // download it as a csv
        $headers = array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=report.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        );
        $callback = function() use ($csv_data)
        {
            $FH = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function index($start_date = null, $end_date = null)
    {

        // get all users who have the role reseller
        $resellers = User::where('role', 'reseller')->get();
        $resellers_with_data = [];
        foreach ($resellers as $reseller) {
            $resellers_with_data[] = User::where('id', $reseller->id)->with('deals', 'deals.invoices', 'deals.invoices.lineItems', 'deals.invoices.lineItems', 'deals.invoices.payments')->get();
        }

        // if a start and an end date exist, remove all payments from the array
        if ($start_date && $end_date) {
            foreach ($resellers_with_data as $key => $reseller) {
                foreach ($reseller as $key2 => $reseller_data) {
                    if (!is_null($reseller_data->deals)) {
                        foreach ($reseller_data->deals as $key3 => $deal) {
                            if (!is_null($deal->invoices)) {
                                foreach ($deal->invoices as $key4 => $invoice) {
                                    if (!is_null($invoice->payments)) {
                                        foreach ($invoice->payments as $key5 => $payment) {

                                            $unix_start_date = strtotime($start_date);
                                            $unix_end_date = strtotime($end_date);
                                            $unix_created_at = strtotime($payment->created_at);

                                            if ($unix_created_at < $unix_start_date || $unix_created_at > $unix_end_date) {
                                                unset($resellers_with_data[$key][$key2]->deals[$key3]->invoices[$key4]->payments[$key5]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // calculat the total number of payments for each reseller
        $total_payments = 0;
        foreach ($resellers_with_data as $reseller) {
            foreach ($reseller as $reseller_data) {
                if (!is_null($reseller_data->deals)) {
                    foreach ($reseller_data->deals as $deal) {
                        if (!is_null($deal->invoices)) {
                            foreach ($deal->invoices as $invoice) {
                                if (!is_null($invoice->payments)) {
                                    foreach ($invoice->payments as $payment) {
                                        $total_payments += $payment->amount;
                                    }
                                }
                            }
                        }
                    }

                    if ($total_payments != 0) {
                        $reseller_data->total_payments = $total_payments;
                        $reseller_data->total_comission = $total_payments / $reseller_data->discount_comission;
                    }
                }
            }
        }

        // order the resellers by the total payments
        if ($total_payments != 0) {
            usort($resellers_with_data, function ($a, $b) {
                return $a->total_payments < $b->total_payments;
            });
        }

        $sales = [];
        return view('crud::pages.comission', compact('resellers_with_data', 'sales', 'start_date', 'end_date'));
    }
}
