@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid" style="margin-left:10px;">
        <h3 class="pt-5 pb-2">Important Stats for {{ date('M Y') }}</h3>
        <p>Important information at a glance about the reseller activity for this month.</p>
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-primary">
                    <div class="card-body pb-0">
                        <div class="btn-group float-right">
                            <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">View New Sales</a><a class="dropdown-item" href="#">View Resellers</a></div>
                        </div>
                        <?php
                            $payments = \App\Models\Payment::whereMonth('created_at', date('m'))->get();
                        ?>
                        <div class="text-value">{{ $payments->count() }}</div>
                        <div>Payments This Month</div>
                    </div>
                    <div class="chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                        <canvas class="chart chartjs-render-monitor" id="card-chart1" height="62" style="display: block; height: 70px; width: 225px;" width="202"></canvas>
                        <div id="card-chart1-tooltip" class="chartjs-tooltip top" style="opacity: 0; left: 134.926px; top: 131.662px;"><div class="tooltip-header"><div class="tooltip-header-item">June</div></div><div class="tooltip-body"><div class="tooltip-body-item"><span class="tooltip-body-item-color" style="background-color: rgb(124, 105, 239);"></span><span class="tooltip-body-item-label">My First dataset</span><span class="tooltip-body-item-value">60</span></div></div></div></div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-danger">
                    <div class="card-body pb-0">
                        <div class="btn-group float-right">
                            <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">View Resellers</a></div>
                        </div>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">View New Sales</a><a class="dropdown-item" href="#">View Resellers</a></div>
                        <?php
                            // all resellers with no payments against them
                            $reseller_qs_q = \App\Models\User::where('role', 'reseller')->get();
                            $reseller_qs_q_with_data = [];
                            foreach ($reseller_qs_q as $reseller_q) {
                                $reseller_qs_q_with_data[] = \App\Models\User::where('id', $reseller_q->id)->with('deals', 'deals.invoices', 'deals.invoices.lineItems', 'deals.invoices.lineItems', 'deals.invoices.payments')->get();
                            }
                            $nonPerformingResellers = [];
                            foreach ($reseller_qs_q_with_data as $reseller_q) {
                                foreach ($reseller_q as $reseller_q_data) {
                                    foreach ($reseller_q_data->deals as $deals) {
                                        foreach ($deals->invoices as $invoices) {
                                            foreach ($invoices->payments as $payment) {

                                                // if the payment date is in the current month
                                                if (date('m', strtotime($payment->date)) == date('m')) {
                                                    $nonPerformingResellers[] = $reseller_q_data;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        ?>
                        <div class="text-value">
                            {{ count($nonPerformingResellers) }}
                        </div>
                        <div>Non-Performing Reseller</div>
                    </div>
                    <div class="chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                        <canvas class="chart chartjs-render-monitor" id="card-chart2" height="62" width="202" style="display: block; height: 70px; width: 225px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-success">
                    <div class="card-body pb-0">
                        <div class="btn-group float-right">
                            <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">View Resellers</a><a class="dropdown-item" href="#">View Sales</a></div>
                        </div>
                        <?php
                            $payments = \App\Models\Payment::whereMonth('created_at', date('m'))->get();
                            $total_amount = 0;
                            foreach ($payments as $payment) {
                                $total_amount += $payment->amount;
                            }
                        ?>
                        <div class="text-value">${{ number_format($total_amount, 2) }}</div>
                        <div>Sold Across All Resellers</div>
                    </div>
                    <div class="chart-wrapper mt-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                        <canvas class="chart chartjs-render-monitor" id="card-chart3" height="62" width="231" style="display: block; height: 70px; width: 257px;"></canvas>
                        <div id="card-chart3-tooltip" class="chartjs-tooltip top" style="opacity: 0; left: 65.6846px; top: 111.4px;"><div class="tooltip-header"><div class="tooltip-header-item">January</div></div><div class="tooltip-body"><div class="tooltip-body-item"><span class="tooltip-body-item-color" style="background-color: rgba(255, 255, 255, 0.2);"></span><span class="tooltip-body-item-label">My First dataset</span><span class="tooltip-body-item-value">78</span></div></div></div></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="" style="margin-left:10px;">
        <h2><span class="text-capitalize">Comissions Overview</span></h2>
        <p>Below you can find all the payments, invoices, and comissions/sales information for all deals. These reports are generated from <u>paid</u> invoices.</p>
        <div class="flex">
            <label>
                Start Date
                <input type="date" placeholder="d/m/t" class="form-control" name="start_date" value="<?php if ($start_date) {echo date('Y-m-d', strtotime($start_date)); } ?>">
            </label>
            <label style="margin-left:5px;">
                End Date
                <input type="date" placeholder="d/m/t" class="form-control" name="end_date" value="<?php if ($end_date) {echo date('Y-m-d', strtotime($end_date)); } ?>">
            </label>
            <div class="btn btn-primary" data-style="zoom-in" id="filter_results_button" style="height: 37px;border:none;margin-top:-2px;margin-left:5px;cursor:pointer;"><span class="ladda-label">Filter</span></div>
            <div class="btn btn-primary" data-style="zoom-in" id="clear_filter_button" style="height: 37px;border:none;margin-top:-2px;margin-left:5px;cursor:pointer;"><span class="ladda-label">Clear</span></div>
        </div>
    </div>

    <style>
        table td, table td * {
            vertical-align: top;
        }
    </style>
    <div class="">
        <div style="width: 49%;float: left;vertical-align: top;margin-left:10px;">
            <h4 style="margin-top:20px;margin-left:0;">
                <span class="text-capitalize">Top Performing Resellers</span>
            </h4>
            <div class="mx-auto rounded bg-white px-4 py-2 mt-3">
                <table cellspacing="0" cellpadding="0" width="100%" style="margin-bottom:15px;">
                    <tr style="border-bottom: 1px dashed #ccc;">
                        <td width="40%">
                            <p class="p-0 m-0 py-2" style="font-weight: bold">Name / Company</p>
                        </td>
                        <td width="20%">
                            <p class="p-0 m-0 py-2" style="font-weight: bold;text-align: right;">Total Sales</p>
                        </td>
                        <td width="20%">
                            <p class="p-0 m-0 py-2" style="font-weight: bold;text-align: right;">Total Comission</p>
                        </td>
                        <td width="20%">

                        </td>
                    </tr>
                    @php
                        $total = 0;
                    @endphp
                    @forelse($resellers_with_data as $key => $reseller)
                        <tr style="border-bottom: 1px dashed #ccc;">
                            <td width="30%">
                                <p class="p-0 m-0 py-2" style="margin-top:4px;">{{ Str::limit($reseller->first_name . ' ' . $reseller->last_name, 35) }}</p>
                            </td>
                            <td width="20%">
                                <p class="p-0 m-0 py-2" style="text-align: right;margin-top:4px;">${{ number_format($reseller->total_payments, 2) ?? '' }}</p>
                            </td>
                            <td width="20%">
                                <p class="p-0 m-0 py-2" style="text-align: right;margin-top:4px;">${{ number_format($reseller->total_comission, 2) ?? '' }}</p>
                            </td>
                            <td width="30%">
                                <div style="display: flex;margin-left:10px;">
                                    <div class="btn btn-primary" data-style="zoom-in" style="border:none;margin-left:5px;cursor:pointer;float:right;margin-top:2px;margin-right:2px;" onclick="document.getElementById('send_single_reseller_invoice_{{ $reseller->id }}').submit();"><span class="ladda-label">Send</span></div>
                                    <div class="btn btn-primary" data-style="zoom-in" style="border:none;margin-left:2px;cursor:pointer;float:right;margin-top:2px;margin-right:1px;" onclick="document.getElementById('download_single_reseller_report_{{ $reseller->id }}').submit();"><span class="ladda-label">Report</span></div>
                                </div>
                                <form action="{{ backpack_url('comission/report-single') }}" method="post" id="download_single_reseller_report_{{ $reseller->id }}">
                                    @csrf
                                    <input type="hidden" name="reseller_id" value="{{ $reseller->id }}">
                                    <input type="hidden" name="start_date" value="{{ $start_date ?? '' }}" class="start_date">
                                    <input type="hidden" name="end_date" value="{{ $end_date ?? '' }}" class="end_date">
                                </form>
                                <form action="{{ backpack_url('comission/send-single') }}" method="post" id="send_single_reseller_invoice_{{ $reseller->id }}">
                                    @csrf
                                    <input type="hidden" name="reseller_id" value="{{ $reseller->id }}">
                                    <input type="hidden" name="start_date" value="{{ $start_date ?? '' }}" class="start_date">
                                    <input type="hidden" name="end_date" value="{{ $end_date ?? '' }}" class="end_date">
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"><p style="text-align: center;" class="mt-2">Sorry - something went wrong...No resellers found.</p></td>
                        </tr>
                    @endforelse
                </table>
            </div>
            <div class="mt-3 flex justify-content-between" >
                <div class="btn btn-primary" data-style="zoom-in" onclick="document.getElementById('report_all_form').submit();" style="cursor:pointer;"><span class="ladda-label">Monthly Report</span></div>
                <form action="{{ backpack_url('comission/report-all') }}" method="post" id="report_all_form">
                    @csrf
                </form>
            </div>
        </div>
        <div style="width: 49%;float: right;vertical-align: top;">
            <h4 style="margin-top:20px;margin-left:0;">
                <span class="text-capitalize">Top Performing Sales Managers</span>
            </h4>
            <div class="mx-auto rounded bg-white px-3 py-5 mt-3 text-center">
                Coming Soon
            </div>
            <div class="mt-3 flex justify-content-between" >
                <a href="#" class="btn btn-primary" data-style="zoom-in"><span class="ladda-label">Monthly Report</span></a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#filter_results_button').click(function() {
                var start_date = $('input[name="start_date"]').val();
                var end_date = $('input[name="end_date"]').val();
                var start_date = start_date.split('-');
                var end_date = end_date.split('-');
                start_date = start_date[2] + '-' + start_date[1] + '-' + start_date[0];
                end_date = end_date[2] + '-' + end_date[1] + '-' + end_date[0];
                window.location.href = '/admin/comission/' + start_date + '/' + end_date;
            });

            // clear the filters
            $('#clear_filter_button').click(function() {
                window.location.href = '/admin/comission';
            });

            // when the start_date or end_date is changed, update the form action
            $('input[name="start_date"]').change(function() {
                let start_date = $('input[name="start_date"]').val();
                let new_start_date = start_date.split('-');
                start_date = new_start_date[2] + '-' + new_start_date[1] + '-' + new_start_date[0];
                $('.start_date').val(start_date);
            });

            // do this for the end date
            $('input[name="end_date"]').change(function() {
                let end_date = $('input[name="end_date"]').val();
                let new_end_date = end_date.split('-');
                end_date = new_end_date[2] + '-' + new_end_date[1] + '-' + new_end_date[0];
                $('.end_date').val(end_date);
            });
        });
    </script>
@endsection
