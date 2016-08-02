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
		<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php URLHelper::renderUrl('/'); ?>"><img src="mt/assets/images/mtlogo_b_inv.png" alt="My-trainer" /></a>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#">Workout Generator <span class="sr-only">(current)</span></a></li>
						<li><a href="/blog">Blog</a></li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div>
		</nav>
		<div class="main-content container-fluid">
			<div class="row">
				<div class="col-sm-12">
					<?php require($viewFile) ?>
				</div>
			</div>
		</div>
		<script src="<?php URLHelper::renderURL('assets/scripts/bundle.js'); ?>"></script>
	</body>
</html> 
 