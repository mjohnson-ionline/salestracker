@if(backpack_auth()->user()->role == 'admin')
    @includeWhen(class_exists(\Backpack\DevTools\DevToolsServiceProvider::class), 'backpack.devtools::buttons.sidebar_item')
@endif

@if(backpack_auth()->user()->role == 'admin')
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user" style="color:white;"></i> Users</a></li>
@endif
@if(backpack_auth()->user()->role != 'reseller')
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('comission') }}"><i class="nav-icon la la-dollar" style="color:white;"></i> Comissions</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('deal') }}"><i class="nav-icon la la-book" style="color:white;"></i> Deals</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('invoice') }}"><i class="nav-icon la la-book-reader" style="color:white;"></i> Invoices</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('line-item') }}"><i class="nav-icon lar la-hand-point-right" style="color:white;"></i> Line Items</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('payment') }}"><i class="nav-icon lar la-hand-point-up" style="color:white;"></i> Payments</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('products') }}"><i class="nav-icon la la-product-hunt" style="color:white;"></i> Products</a></li>
@else
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('comission') }}"><i class="nav-icon la la-dollar" style="color:white;"></i> Comissions</a></li>
@endif
