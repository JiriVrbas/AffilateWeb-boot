<?php
function generateRandomEmailValidationString() {
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < 15; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}

function sendConfirmationMail( $receiver, $uid ) {
	$confirmationLink = "http://" . $_SERVER['HTTP_HOST'] . "/AffilateWeb-BootStrap/confirmation.php?link=" . generateRandomEmailValidationString();
	saveConfirmationMail( $uid, $confirmationLink );

	$to      = $receiver;
	$subject = "Aktivační email";

	$message = "
<html>
<head>
<title>Aktivační email</title>
</head>
<body>
<p>Děkujeme za registraci kliknutím na níže uvedený odkaz aktivujete svůj účet.</p>
<a href='$confirmationLink'>Aktivovat nyní!</a>
</body>
</html>
";

// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
	$headers .= 'From: <vrbasji@gmail.com>' . "\r\n";

	mail( $to, $subject, $message, $headers );
}

if ( isset( $_SESSION['u_id'] ) ){
    $user_full_link = "http://" . $_SERVER['HTTP_HOST'] . "/AffilateWeb-BootStrap/index.php?link=".$_SESSION['u_link'];
if ( isset( $_GET['sendEmail'] ) && isset( $_SESSION['u_email'] ) ) {
	sendConfirmationMail( $_SESSION['u_email'], $_SESSION['u_id'] );
}
if ( isset( $_POST['cashout'] ) ) {
	if ( isset( $_POST['castka'] ) ) {
		if ( $_SESSION['u_balance'] >= $_POST['castka'] ) {
			saveCashOut( $_SESSION['u_id'], $_POST['cislo_uctu'], $_POST['kod_banky'], $_POST['castka'] );
		}
	}
}
if ( isset( $_GET['saveInformation'] ) ) {
	saveInformationAboutUser( $_SESSION['u_id'], $_GET['jmeno'], $_GET['prijmeni'] );

	//createAccount($_SESSION['u_id'],$_GET['cislo_uctu'],$_GET['kod_banky'],$_GET['jmeno'],$_GET['prijmeni']);
}
if ( isUserEmailConfirmed( $_SESSION['u_id'] ) ){
?>
<div class="container text-center">
    <h2 class="mb-4">Přihlášen jako uživatel <?php echo( $_SESSION['u_login'] ) ?></h2>
    <p class="mb-4"><b>Odkaz pro vaše příspěvky:</b> <?php echo( $user_full_link ) ?></p>
    <!-- TODO: link musí být celý link na stránky který si uživatel může zkopírovat pod fotky -->
    <div class="mb-4">
        <p class="mb-4"><b>Provize:</b> <?php echo( $_SESSION['u_balance'] ) ?> kč</p>
        <!-- Rozbalovací menu pro správu finančních prostředků -->
        <!-- Vyskakovací okno jako tady (http://www.truebounce.com/cust_fab.htm) kde bude platba -->
        <div class="mb-4">
            <div class="span4 collapse-group">
                <a class="btn" data-toggle="collapse" data-target="#viewaccounts">Vybrat provizi</a>
                <div class="collapse" id="viewaccounts">
                    <form action="index.php" method="post">
                        Číslo účtu: <input class="form-control" type="text" name="cislo_uctu"><br>
                        Kód banky: <input class="form-control" type="text" name="kod_banky"><br>
                        Částka: <input class="form-control" type="text" name="castka"><br>
                        <input class="btn btn-default" type="submit" value="Odeslat žádost o výběr" name="cashout"/>
                    </form>
                    <!--<table class="table-responsive">
                            <tr>
                                <th>Číslo účtu</th>
                                <th>Kód banky</th>
                                <th>Jméno</th>
                                <th>Příjmení</th>
                            </tr>
							<?php /*
							$accounts = getAccounts( $_SESSION['u_id'] );
							foreach ( $accounts as $account ) {
								echo "<tr>";
								echo "<td>$account->account_number</td>";
								echo "<td>$account->bank_code</td>";
								echo "<td>$account->first_name</td>";
								echo "<td>$account->last_name</td>";
								echo "<td><a href='#pop-up'>Vybrat</a></td>";
								echo "</tr>";
							}
							*/
					?>
                        </table> -->
                </div>
            </div>
        </div>
    </div>


    <!-- Rozbalovací menu pro zobrazení uživatelů registrovaných přes nás -->
    <div class="mb-4">
        <div class="span4 collapse-group">
            <h4><a class="btn" data-toggle="collapse" data-target="#viewdetails">Uživatelé registrovaní přes váš
                    účet</a></h4>
            <div class="collapse" id="viewdetails">
                <table class="table">
                    <tr>
                        <th>Login</th>
                        <th>Email</th>
                        <th>Link</th>
                        <th>First name</th>
                        <th>Last name</th>
                    </tr>
					<?php
					$users = getRegisteredUsersForUser( $_SESSION['u_id'] );
					foreach ( $users as $user ) {
						echo "<tr>";
						echo "<td>$user->login</td>";
						echo "<td>$user->email</td>";
						echo "<td>$user->link</td>";
						echo "<td>$user->first_name</td>";
						echo "<td>$user->last_name</td>";
						echo "</tr>";
					}
					?>
                </table>
            </div>
        </div>
    </div>
    <!-- Rozbalovací menu pro vyplnění informací o uživatli -->
    <div class="mb-4">
        <div class="span4 collapse-group">
            <h4><a class="btn" data-toggle="collapse" data-target="#viewinformation">Informace o vás</a></h4>
            <div class="collapse" id="viewinformation">
                <form action="index.php" method="get">
                    Jméno: <input class="form-control" type="text" name="jmeno" value="<?php echo( $_SESSION['u_first'] ) ?>"><br>
                    Příjmení: <input class="form-control" type="text" name="prijmeni" value="<?php echo( $_SESSION['u_last'] ) ?>"><br>
                    <input class="btn btn-default" type="submit" value="Uložit" name="saveInformation"/>
                </form>
            </div>
        </div>
    </div>

	<?php
	} else {
		?>
        <div class="container text-center">
            <h2 class="mb-4">Nejdříve potvrdtě emailovou adresu.</h2>
            <form action="index.php" method="get">
                <p>Odeslat email znovu?</p>
                <input type="hidden" name="sendEmail" value="true"/>
                <input type="submit" value="Odeslat"/>
            </form>
        </div>

		<?php
	}
	} else {
		//Nepřihlášený uživatel
		?>
        <script>
            function ValidatePassword() {
                var pass1 = document.getElementById("pwd").value;
                var pass2 = document.getElementById("repeatPwd").value;
                if (pass1 != pass2) {
                    document.getElementById("signUp").isDisabled = true;
                } else {
                    document.getElementById("signUp").isDisabled = false;
                }
            }
        </script>
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <form action="includes/login.inc.php" method="post" class="form-control">
                        <h2 class="section-heading">Přihlášení uživatele</h2>
                        <hr class="my-4">
                        <input class="form-control" type="text" name="uid" placeholder="Uživatelské jméno/Email">
                        <input class="form-control" type="password" name="pwd" placeholder="Heslo">
                        <button class="btn btn-primary btn-xl js-scroll-trigger" type="submit" name="submit">Přihlásit
                        </button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <form action="includes/signup.inc.php" method="post" class="form-control">
                        <h2 class="section-heading">Registrace uživatele</h2>
                        <hr class="my-4">
                        <input type="text" class="form-control" name="login" placeholder="Uživatelské jméno">
                        <input type="text" class="form-control" name="email" placeholder="E-mail">
                        <input type="password" name="pwd" class="form-control" placeholder="Heslo" id="pwd"
                               onchange="ValidatePassword()">
                        <input type="password" class="form-control" placeholder="Heslo znovu" id="repeatPwd"
                               onchange="ValidatePassword()">
                        <button class="btn btn-primary btn-xl js-scroll-trigger" type="submit" name="submit"
                                id="signUp">Registrovat
                        </button>
                    </form>
                </div>
            </div>
        </div>
		<?php
	}
	?>
