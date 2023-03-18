<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="bs/img/icons/icon-48x48.png" />

   

	{{-- <link rel="canonical" href="https://demo-basic.adminkit.io/pages-blank.html" /> --}}

	<!--Data Tables Responsive-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">

	<title>{{ $content }} | Silat</title>

	<link href="{{ url('bs/css/app.css') }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<div class="wrapper">
		
		@include('template.sidebar')
		<div class="main">
			
			@include('template.navbar')
			<main class="content">
				<div class="container-fluid p-0">
					
					@yield('content')
				</div>
			</main>

			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
								<a class="text-muted" href="#" target="_blank"><strong>SILAT</strong></a> - <a class="text-muted" href="/" target="_blank"><strong>Sistem Infromasi Fasilitas dan Targeting</strong></a>								&copy;
							</p>
						</div>
						<div class="col-6 text-end">
							<ul class="list-inline">
								<li class="list-inline-item">
									<a class="text-muted" href="#" target="_blank">Support</a>
								</li>
								<li class="list-inline-item">
									<a class="text-muted" href="#" target="_blank">Help Center</a>
								</li>
								<li class="list-inline-item">
									<a class="text-muted" href="#" target="_blank">Privacy</a>
								</li>
								<li class="list-inline-item">
									<a class="text-muted" href="#" target="_blank">Terms</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>

	
	<script src="{{ url('bs/js/app.js') }}"></script>
	<script src="{{ url('js/main.js') }}"></script>
	{{-- Data Tables JS --}}
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
	
	<script>
		// Data Tables Responsive name id  JS
		$(document).ready(function () {
			$('#users_table').DataTable();
			$('#scoring_table').DataTable();
		});
		
	</script>

</body>

</html>