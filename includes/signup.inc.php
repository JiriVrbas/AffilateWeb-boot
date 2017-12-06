<?php
if ( isset( $_POST['submit'] ) ) {
	include_once '../includes/dbh.inc.php';
	if ( session_status() == PHP_SESSION_NONE ) {
		session_start();
	}

	$email = mysqli_real_escape_string( $conn, $_POST['email'] );
	$pwd   = mysqli_real_escape_string( $conn, $_POST['pwd'] );
	$login = mysqli_real_escape_string( $conn, $_POST['login'] );

	if ( isset( $_SESSION['come_link'] ) ) {
		$link = mysqli_real_escape_string( $conn, $_SESSION['come_link'] );

		$sql  = "SELECT user_id FROM users WHERE link=?;";
		$stmt = mysqli_stmt_init( $conn );

		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			$superiorId = 1;
		} else {
			mysqli_stmt_bind_param( $stmt, "s", $link );
			mysqli_stmt_execute( $stmt );
			$result = mysqli_stmt_get_result( $stmt );

			if ( $row = mysqli_fetch_assoc( $result ) ) {
				$superiorId = $row['user_id'];
			}
		}
	} else {
		$superiorId = 1;
	}

	//Error handlers
	//Check for empty fields
	if ( empty( $email ) || empty( $pwd ) || empty( $login ) ) {
		header( "Location: ../signup.php?signup=empty" );
		exit();
	} else {
		//Check if email is valid
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			header( "Location: ../signup.php?signup=email" );
			exit();
		} else {
			$sql  = "SELECT * FROM users WHERE login=? or email=?;";
			$stmt = mysqli_stmt_init( $conn );
			if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
				header( "Location: ../signup.php?signup=error" );
				exit();
			} else {
				mysqli_stmt_bind_param( $stmt, "ss", $login, $email );
				mysqli_stmt_execute( $stmt );
				$result      = mysqli_stmt_get_result( $stmt );
				$resultCheck = mysqli_num_rows( $result );

				if ( $resultCheck > 0 ) {
					header( "Location: ../signup.php?signup=usertaken" );
					exit();
				} else {
					//Hashing the password
					$hashedPwd = password_hash( $pwd, PASSWORD_DEFAULT );
					//Insert the user into database
					$sql = "INSERT INTO users(login,email,link,password,superior_id) 
                            VALUES (?,?,?,?,?);";
					if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
						header( "Location: ../signup.php?signup=error" );
						exit();
					} else {
						$link = getRandomUniqueString();
						mysqli_stmt_bind_param( $stmt, "sssss", $login, $email, $link, $hashedPwd, $superiorId );
						mysqli_stmt_execute( $stmt );

						sendConfirmationMail($email,$stmt->insert_id);

						header( "Location: ../index.php?success" );
						exit();
					}
				}
			}
		}
	}
} else {
	header( "Location: ../signup.php" );
	exit();
}
function sendConfirmationMail( $receiver, $uid ) {
	$confirmationLink = "http://".$_SERVER['HTTP_HOST']."/AffilateWeb-BootStrap/confirmation.php?link=" . generateRandomEmailValidationString();
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

function generateRandomEmailValidationString() {
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < 15; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}

function generateRandomString() {
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < 5; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}

function getRandomUniqueString() {
	$ulink = generateRandomString();
	while ( ! isUniqueLink( $ulink ) ) {
		$ulink = generateRandomString();
	}

	return $ulink;
}

function isUniqueLink( $link ) {
	$sql = "SELECT * FROM users WHERE link='$link';";
	global $conn;
	$result = $conn->query( $sql );

	$resultCheck = mysqli_num_rows( $result );

	return $resultCheck == 0;
}
