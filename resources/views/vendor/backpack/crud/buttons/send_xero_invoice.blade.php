@if ($crud->hasAccess('sendXeroInvoice'))
    <a href="{{ url($crud->route.'/'.$entry->getKey().'/send-xero-invoice') }}" class="btn btn-sm btn-link">
        <i class="la la-question"></i> Send Xero Invoice
    </a>
@endif
