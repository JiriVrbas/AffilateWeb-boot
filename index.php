<?php
include_once 'classes/user.php';
include_once 'classes/account.php';

if ( session_status() == PHP_SESSION_NONE ) {
	session_start();
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 180)) {
	// last request was more than 30 minutes ago
	session_unset();     // unset $_SESSION variable for the run-time
	session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if ( isset( $_GET['link'] ) ) {
	$_SESSION['come_link'] = $_GET['link'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Affilate web</title>
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'
          rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic'
          rel='stylesheet' type='text/css'>
    <!-- Plugin CSS -->
    <link href="vendor/magnific-popup/magnific-popup.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/creative.min.css" rel="stylesheet">
</head>
<body id="page-top">
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="#page-top">Affilate web</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#partners">Partneři</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#howitworks">Jak to funguje</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#contact">Kontakt</a>
                </li>
				<?php
				if ( isset( $_SESSION['u_id'] ) ) {
					echo '<li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#usermanagement">Správa uživatele</a>
                          </li>';
					echo '<li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="includes/logout.inc.php">Odhlášení</a>
                          </li>';
				} else {
					echo '<li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#usermanagement">Přihlášení/Registrace</a>
                          </li>';
				}
				?>
            </ul>
        </div>
    </div>
</nav>

<header class="masthead text-center text-white d-flex">
    <div class="container my-auto">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="text-uppercase">
                    <strong>Hlavní popis stránek</strong>
                </h1>
                <hr>
            </div>
            <div class="col-lg-8 mx-auto">
                <p class="text-faded mb-5">Nějaký úvodní kecy atd..</p>
                <a class="btn btn-primary btn-xl js-scroll-trigger" href="#partners">Affilate partneři</a>
            </div>
        </div>
    </div>
</header>

<section class="bg-primary" id="partners">
	<?php include_once 'web/partners_test.php' ?>
</section>

<section id="howitworks">
	<?php include_once 'web/howitworks.php' ?>
</section>

<section class="bg-primary" id="usermanagement">
	<?php include_once 'web/usermanagement.php' ?>
</section>

<section class="bg-dark text-white">
    <div class="container text-center">
        <h2 class="mb-4">Nějaká patička??</h2>
        <a class="btn btn-light btn-xl sr-button" href="http://startbootstrap.com/template-overviews/creative/">Download
            Now!</a>
    </div>
</section>

<section id="contact">
	<?php include_once 'web/contact.php' ?>
</section>

<!-- Bootstrap core JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Plugin JavaScript -->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="vendor/scrollreveal/scrollreveal.min.js"></script>
<script src="vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
<!-- Custom scripts for this template -->
<script src="js/creative.min.js"></script>
</body>
</html>
