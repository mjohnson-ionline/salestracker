<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <title>iOnline Proposal Systems</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="">
    <meta property="og:type" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="icon.png">
    <link rel="manifest" href="site.webmanifest">
    <meta name="theme-color" content="#fafafa">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/jSignature.min.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body class="">
<div class="h-12" style="background-color: orange;">
    <div class="container mx-auto">
        <div class="flex">
            <div class="w-full md:w-1/2">
                <p class="text-sm text-left pt-3 text-white font-semibold">{{ $client_proposal->title }} for {{ $client->first_name . ' ' . $client->last_name }}</p>
            </div>
            <div class="w-full md:w-1/2">
                <p class="text-sm text-right pt-3 text-white font-semibold">{{ date('l jS \of F Y h:i:s A') }}</p>
            </div>
        </div>
    </div>
</div>

<p style="text-align: center"><img src="{{ asset('img/ionline_logo.png') }}" style="width: 350px;margin:15px auto;"></p>

<div style="width:650px;" class="mx-auto">
    <h2 class="text-center text-base font-semibold text-white py-2"  style="background-color: orange;">Swipe your mouse / finger in the box below to sign</h2>
    <div id="signature" class="shadow" style="width:650px;margin:0 auto;"></div>
    <div class="flex justify-between">
        {{--<a href="/sign/{{ $client_proposal->id }}/{{ $token }}" class="text-center text-base font-semibold text-white py-2 px-4 mt-4 rounded" style="background-color: orange;">CLEAR SIGNATURE</a>--}}
        <span class="text-center text-base font-semibold text-white py-2 px-4 mt-4 rounded cursor-pointer" style="background-color: orange;" id="clearForm">CLEAR SIGNATURE</span>
        <span class="text-center text-base font-semibold text-white py-2 px-4 mt-4 rounded cursor-pointer" style="background-color: orange;" id="submitForm">ACCEPT PROPOSAL</span>
    </div>
    <p class="bg-red-900 text-white p-2 mb-4 rounded text-center text-sm mt-4" id="error_msg" style="display: none;">An e-Signature is required for before accepting the proposal.</p>
    @if(!is_null($client_proposal->tcuploads))
        <p class="text-center text-sm mt-4">If you do not wish to provide an e-Signature, you have the option of downloading and returning the Terms and Conditions to us directly by <a href="{{ config('app.url') }}/storage/{{ $client_proposal->tcuploads }}" class="underline" target="_blank">clicking this link</a>.</p>
    @endif
</div>

<div class="h-12"></div>

<div class="container mx-auto bg-gray-100 p-12 pt-6 pb-6 rounded-2xl">
    <h2 class="text-left text-base font-semibold underline">By Providing Your e-Signature:-</h2>
    <ul style="margin-left:35px;">
        <li class="text-sm list-disc text-left">You are indicating your intention of proceeding with the digital proposal / presentation located <a href="{{ $client_proposal->canva_link }}" class="underline" target="_blank">here</a>. </li>
        <li class="text-sm list-disc text-left">You are agreeing to the terms and conditions located <a href="{{ config('app.url') }}/storage/{{ $client_proposal->tcuploads }}" class="underline" target="_blank">here</a>. </li>
        <li class="text-sm list-disc text-left">You are contenting that you are the intended recipient of the email communications of the digital proposal / presentation, ie you have engaged with iOnline Pty Ltd for Web / Digital Marketing Services. </li>
        <li class="text-sm list-disc text-left">You are consenting that your name is {{ $client->first_name . ' ' . $client->last_name }} and that your company name is {{ $client->company_name }}. </li>
    </ul>
</div>
<script>
    $(document).ready(function() {

        // render jSignature
        $("#signature").jSignature();

        // reset the pad
        $("#clearForm").click(function() {
            $("#signature").jSignature("reset")
        });

        // when submitForm is clicked
        $("#submitForm").click(function() {

            // get the signature
            var signature = $("#signature").jSignature("getData", "svgbase64");

            // test against an empty signature
            if (signature[1] == 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj48c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iMCIgaGVpZ2h0PSIwIj48L3N2Zz4=') {
                $("#error_msg").fadeIn();
                return;
            }

            // post the signature to the server
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post("/store/{{ $client_proposal->id }}", {signature: signature}, function (data) {
                if (data.success == true) {
                    console.log(data);
                    window.location.href = "/thankyou/{{ $client_proposal->id }}/{{ $token }}";
                } else {
                    window.location.href = "/error/{{ $client_proposal->id }}/{{ $token }}";
                }
            });

        });

    })
</script>
</body>
</html>
