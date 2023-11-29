<?php

namespace Backpack\CRUD\app\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    protected $data = []; // the information we send to the view

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        // get all users who have the role reseller
        $resellers = User::where('role', 'reseller')->get();
        $resellers_with_data = [];
        foreach ($resellers as $reseller) {
            $resellers_with_data[] = User::where('id', $reseller->id)->with('deals', 'deals.invoices', 'deals.invoices.lineItems', 'deals.invoices.lineItems', 'deals.invoices.payments')->get();
        }

        // if a start and an end date exist, filter the data by removing it from the array
        if ($start_date && $end_date) {
            foreach ($resellers_with_data as $key => $reseller) {
                foreach ($reseller as $key2 => $reseller_data) {
                    foreach ($reseller_data->deals as $key3 => $deal) {
                        foreach ($deal->invoices as $key4 => $invoice) {
                            if ($invoice->created_at < $start_date || $invoice->created_at > $end_date) {
                                unset($resellers_with_data[$key][$key2]->deals[$key3]->invoices[$key4]);
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
                foreach ($reseller_data->deals as $deal) {
                    foreach ($deal->invoices as $invoice) {
                        foreach ($invoice->payments as $payment) {
                            $total_payments += $payment->amount;
                        }
                    }
                }
                $reseller_data->total_payments = $total_payments;
                $reseller_data->total_comission = $total_payments / $reseller_data->discount_comission;
            }
        }

        // order the resellers by the total payments
        usort($resellers_with_data, function ($a, $b) {
            return $a->total_payments < $b->total_payments;
        });

        $sales = [];

        return view(backpack_view('dashboard'), $this->data);
    }

    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {

        return redirect(backpack_url('dashboard'));
    }
}
