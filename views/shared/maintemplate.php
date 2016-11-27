<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="../../favicon.ico">
		<link href="<?php URLHelper::renderUrl('/assets/css/base.css')?>" rel="stylesheet">
		<title>MyTrainer</title>
	</head>
	<body>    
		<nav class="mt-topbar">
			<div class="container-fluid">
				<div class="mt-topbar__logo">
					<a class="" href="#"><img src="mt/assets/images/mtlogo_b_inv.png" alt="My-trainer"></a>
				</div>
				<div class="mt-topnav">
					<ul class="nav">
						<li class="active" ><a class="mt-topnav__link" href="/">Workout<span class="hidden-xs"> Generator</span> <span class="sr-only">(current)</span></a></li>
						<li><a class="mt-topnav__link" href="/blog">Blog <span class="sr-only">(current)</span></a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="main-content container-fluid">
			<?php require($viewFile) ?>
		</div>
		<script src="<?php URLHelper::renderURL('assets/scripts/bundle.js'); ?>"></script>
	</body>
</html> 
 