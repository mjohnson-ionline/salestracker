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
                <p class="text-sm text-left pt-3 text-white font-semibold">iOnline Pty Ltd</p>
            </div>
            <div class="w-full md:w-1/2">
                <p class="text-sm text-right pt-3 text-white font-semibold">{{ date('l jS \of F Y h:i:s A') }}</p>
            </div>
        </div>
    </div>
</div>

<p style="text-align: center"><img src="{{ asset('img/ionline_logo.png') }}" style="width: 350px;margin:15px auto;"></p>

<div style="width:850px;" class="mx-auto bg-gray-100 rounded">
    <h2 class="text-center text-2xl font-semibold text-black py-2 pt-4">Whoops, something went wrong.</h2>
    <p class="text-center text-base text-blank py-2 pb-4">Please give us a call on 1800 466 546 or email <a href="mailto:service@ionline.com.au" class="underline">service@ionline.com.au</a></p>
</div>

</body>
</html>