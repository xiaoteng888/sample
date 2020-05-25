<!DOCTYPE html>
<html>
<head>
	<title>@yield('title','Sample')</title>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
</head>
<body>
       @include('layouts._header')
	<div class="container">
		 @yield('content')
		 @include('layouts._footer')
	</div>   
</body>
</html>