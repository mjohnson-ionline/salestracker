@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid" style="margin-top:40px;margin-left:10px;">
        <h2>
            <span class="text-capitalize">Send Xero Invoice</span>
        </h2>
    </div>
@endsection

@section('content')
    <style>
        table td, table td * {
            vertical-align: top;
        }
    </style>
    <div class="mx-auto rounded bg-white px-4 py-2 mt-3" style="width: 100%;">
        <table cellspacing="0" cellpadding="0" width="100%" style="margin-bottom:15px;width:100%;" >
            <tr>
                <td colspan="3">
                    <hr>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <strong>Client Details</strong><br>
                    {{ $invoice->user->organisation_name ?? '' }}<br>
                    {{ $invoice->user->first_name ?? '' }} {{ $invoice->user->last_name ?? '' }}<br>
                    @if($invoice->user->accounts_email)
                        {{ $invoice->user->accounts_email ?? '' }}<br>
                    @else
                        {{ $invoice->user->email ?? '' }}<br>
                    @endif
                </td>
                <td width="25%">
                    <strong>Deal Name</strong><br>
                    {{ $invoice->deal->name ?? '' }}<br><br>
                    <strong>Reseller Details</strong><br>
                    {{ $invoice->deal->reseller->first_name ?? '---' }} {{ $invoice->deal->reseller->last_name ?? '---' }}<br>
                    {{ $invoice->deal->reseller->organisation_name ?? '---' }}
                </td>
                <td width="25%" style="text-align: right;">
                    <strong>Date Generated</strong><br>
                    @php
                        echo date('d-m-Y', strtotime('now'));
                    @endphp<br><br>
                    <strong>Due Date</strong><br>
                    @php
                        echo date('d-m-Y', strtotime('+7 days'));
                    @endphp
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <hr>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <p><strong>Name (Xero Code)</strong></p>
                </td>
                <td width="25@%">
                    <p><strong>Quantity</strong></p>
                </td>
                <td width="25@%" style="text-align: right;">
                    <p><strong>Amount</strong></p>
                </td>
            </tr>
            <?php $total = 0; ?>
            @forelse($invoice->lineItems as $key => $lineItem)
                <tr width="50%" style="border-bottom: 1px dashed #ccc;">
                    <td >
                        <p class="p-0 m-0 py-2">{{ $lineItem->name ?? '' }} / ({{ $lineItem->code ?? '' }})</p>
                    </td>
                    <td width="25%">
                        <p class="p-0 m-0 py-2">{{ $lineItem->quantity ?? '' }}</p>
                    </td>
                    <td width="25%" style="text-align: right;">
                        <p class="p-0 m-0 py-2">${{ number_format($lineItem->amount, 2) ?? '' }}</p>
                    </td>
                </tr>
                    <?php $total += $lineItem->amount; ?>
            @empty
                <tr>
                    <td colspan="3"><p style="text-align: center;">Sorry - something went wrong...No line items found on this invoice.</p></td>
                </tr>
            @endforelse
            <tr>
                <td width="50%"></td>
                <td width="25%" style="border-bottom: 1px dashed #ccc;">
                    <p class="p-0 m-0 py-2" style="font-size:20px;"><strong>TOTAL (ex gst)</strong></p>
                </td>
                <td width="25%" style="border-bottom: 1px dashed #ccc;" style="text-align: right;">
                    <p class="p-0 m-0 py-2 text-right" style="font-size:20px;">${{ number_format($total, 2) ?? '' }}</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="mt-3 flex justify-content-between" style="margin-left:10px;">
        <a href="<?php echo backpack_url('invoice');?>" class="btn btn-primary" data-style="zoom-in"><span class="ladda-label"><i class="las la-arrow-left"></i> Back To Invoices</span></a>
        <div class="btn btn-primary" data-style="zoom-in" style="margin-left:5px;cursor:pointer;" onclick="document.getElementById('send_xero_form').submit();"><span class="ladda-label"><i class="las la-thumbs-up"></i> Send To Xero</span></div>
    </div>
    <form method="post" action="<?php echo backpack_url('invoice/' . $invoice->id . '/send-xero-invoice'); ?>" id="send_xero_form">
        @csrf
    </form>
@endsection
