<?php

//include_once '../classes/user.php';

function saveInformationAboutUser( $uid, $jmeno, $prijmeni ) {
	$conn    = $GLOBALS['conn'];
	$u_first = mysqli_real_escape_string( $conn, $jmeno );
	$u_last  = mysqli_real_escape_string( $conn, $prijmeni );
	$u_id    = mysqli_real_escape_string( $conn, $uid );

	if ( ! empty( $u_id ) ) {
		$sql  = "UPDATE users SET first_name=?, last_name=? WHERE user_id=?;";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?saveInformationAboutUser=error" );
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "sss", $u_first, $u_last, $u_id );

			mysqli_stmt_execute( $stmt );
			$result       = mysqli_stmt_get_result( $stmt );
			$affectedRows = mysqli_num_rows( $result );
			if ( $affectedRows > 0 ) {
				$_SESSION['u_first'] = $u_first;
				$_SESSION['u_last'] = $u_last;
			}
		}
	}
}
function saveCashOut($uid,$cislouctu,$kodbanky,$castka){
	$conn    = $GLOBALS['conn'];
	$a_cislo_uctu = mysqli_real_escape_string( $conn, $cislouctu );
	$a_kod_banky  = mysqli_real_escape_string( $conn, $kodbanky );
	$a_castka    = mysqli_real_escape_string( $conn, $castka );
	$u_id    = mysqli_real_escape_string( $conn, $uid );

	if ( !empty( $u_id ) && !empty( $a_cislo_uctu ) && !empty( $a_kod_banky )&& !empty( $a_castka )) {
		mysqli_begin_transaction($conn);
		$sql  = "INSERT INTO applications_of_cashout(user_id, account_number, bank_code, amount) VALUES (?,?,?,?);";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?saveCashOut=error" );
			mysqli_rollback($conn);
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "ssss", $u_id, $a_cislo_uctu, $a_kod_banky, $a_castka);

			mysqli_stmt_execute( $stmt );
			$affectedRows = mysqli_stmt_affected_rows( $stmt );

			if($affectedRows == 0){
				mysqli_rollback($conn);
				return false;
			}
			//return $affectedRows > 0;
		}

		$sql  = "SELECT balance FROM users WHERE user_id = ?;";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?saveCashOut=error" );
			mysqli_rollback($conn);
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "s", $uid);

			mysqli_stmt_execute( $stmt );
			$result = mysqli_stmt_get_result( $stmt );
			if ( $row = mysqli_fetch_assoc( $result ) ) {
				 $newBalance = $row['balance'] - $a_castka;
			}

			if(!isset($newBalance)){
				mysqli_rollback($conn);
				return false;
			}
			//return $affectedRows > 0;
		}

		$sql  = "UPDATE users SET balance = ? WHERE user_id = ?;";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?saveCashOut=error" );
			mysqli_rollback($conn);
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "ss", $a_castka, $uid);

			mysqli_stmt_execute( $stmt );
			$affectedRows = mysqli_stmt_affected_rows( $stmt );

			if($affectedRows == 0){
				mysqli_rollback($conn);
				return false;
			}
			$_SESSION['u_balance'] = $newBalance;
			//return $affectedRows > 0;
		}
		mysqli_commit($conn);
		return true;
	}
	return false;
}

function saveClick( $partnerid, $uid ) {
	//global $conn;
	$conn = $GLOBALS['conn'];
	$p_id = mysqli_real_escape_string( $conn, $partnerid );
	$u_id = mysqli_real_escape_string( $conn, $uid );

	if ( ! empty( $p_id ) && ! empty( $u_id ) ) {
		$sql  = "INSERT INTO clicks_history(time_of_click,user_id,affilate_partner_id) VALUES (NOW(),?,?);";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?saveClick=error" );
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "ss", $u_id, $p_id );

			mysqli_stmt_execute( $stmt );
			$affectedRows = mysqli_stmt_affected_rows( $stmt );
		}
	}
}

function saveConfirmationMail( $uid, $link ) {
	//global $conn;
	$conn   = $GLOBALS['conn'];
	$r_link = mysqli_real_escape_string( $conn, $link );
	$u_id   = mysqli_real_escape_string( $conn, $uid );

	if ( ! empty( $r_link ) && ! empty( $u_id ) ) {
		$sql  = "INSERT INTO mail_confirmation(user_id, confirmation_link, confirmed) VALUES (?,?,?);";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?saveClick=error" );
			exit();
		} else {
			$firstSend = intval( false );
			mysqli_stmt_bind_param( $stmt, "ssi", $u_id, $r_link, $firstSend );

			mysqli_stmt_execute( $stmt );

		}
	}
}
function createAccount($uid, $account_number,$bank_code, $first_name, $last_name){
	//global $conn;
	$conn   = $GLOBALS['conn'];
	$a_acc_number = mysqli_real_escape_string( $conn, $account_number );
	$a_acc_bank_code = mysqli_real_escape_string( $conn, $bank_code );
	$a_acc_first = mysqli_real_escape_string( $conn, $first_name );
	$a_acc_last = mysqli_real_escape_string( $conn, $last_name );
	$u_id   = mysqli_real_escape_string( $conn, $uid );

	if ( ! empty( $a_acc_number ) && ! empty( $u_id ) && ! empty( $a_acc_bank_code )
	     && ! empty( $a_acc_first )&& ! empty( $a_acc_last )) {
		$sql  = "INSERT INTO accounts(account_number, bank_code, first_name,second_name,user_id) VALUES (?,?,?,?,?);";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?createAccount=error" );
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "sssss", $a_acc_number, $a_acc_bank_code, $a_acc_first, $a_acc_last, $u_id);

			mysqli_stmt_execute( $stmt );
			$result       = mysqli_stmt_get_result( $stmt );
			$affectedRows = mysqli_num_rows( $result );
			return $affectedRows >0;
		}
	}
	return false;
}

function isUserEmailConfirmed( $uid ) {
	$conn = $GLOBALS['conn'];
	$u_id = mysqli_real_escape_string( $conn, $uid );

	if ( ! empty( $u_id ) ) {
		$sql  = "SELECT * FROM mail_confirmation WHERE user_id=? AND confirmed = TRUE;";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?isUserConfirmed=error" );
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "s", $u_id );
			mysqli_stmt_execute( $stmt );

			$result       = mysqli_stmt_get_result( $stmt );
			$affectedRows = mysqli_num_rows( $result );

			return $affectedRows > 0;
		}
	}

	return false;
}

function confirmMail( $link ) {
	//global $conn;
	$conn   = $GLOBALS['conn'];
	$r_link = mysqli_real_escape_string( $conn, $link );

	if ( ! empty( $r_link ) ) {
		$sql  = "UPDATE mail_confirmation SET confirmed=TRUE WHERE confirmation_link LIKE ?;";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?confirmMail=error" );
			exit();
		} else {
			$r_link = "%" . $r_link;
			mysqli_stmt_bind_param( $stmt, "s", $r_link );

			mysqli_stmt_execute( $stmt );
			$result       = mysqli_stmt_get_result( $stmt );
			$affectedRows = mysqli_num_rows( $result );

			return $affectedRows > 0;
		}
	}

	return false;
}
function getAccounts( $uid ){
	$conn  = $GLOBALS['conn'];
	$u_id  = mysqli_real_escape_string( $conn, $uid );
	$accounts = array();
	if ( ! empty( $u_id ) ) {
		$sql  = "SELECT * FROM accounts  WHERE user_id = ?;";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?getAccounts=error" );
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "s", $u_id );

			mysqli_stmt_execute( $stmt );
			$result = mysqli_stmt_get_result( $stmt );
			while ( $row = mysqli_fetch_assoc( $result ) ) {
				$accounts[] = new account( $row['account_id'], $row['account_number'], $row['bank_code'], $row['first_name'], $row['second_name'], $row['user_id'] );
			}
		}
	}

	return $accounts;
}

function getRegisteredUsersForUser( $uid ) {
	$conn  = $GLOBALS['conn'];
	$u_id  = mysqli_real_escape_string( $conn, $uid );
	$users = array();
	if ( ! empty( $u_id ) ) {
		$sql  = "SELECT u2.user_id, u2.login, u2.email, u2.link, u2.first_name, u2.last_name, u2.superior_id, u2.account_id,u2.balance FROM users u1 JOIN users u2 ON u1.user_id = u2.superior_id WHERE u1.user_id = ?;";
		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: ../index.php?getRegisteredUsersForUser=error" );
			exit();
		} else {
			mysqli_stmt_bind_param( $stmt, "s", $u_id );

			mysqli_stmt_execute( $stmt );
			$result = mysqli_stmt_get_result( $stmt );
			while ( $row = mysqli_fetch_assoc( $result ) ) {
				$users[] = new user( $row['user_id'], $row['login'], $row['email'], $row['link'], $row['first_name'], $row['last_name'], $row['superior_id'], $row['account_id'], $row['balance'] );
			}
		}
	}

	return $users;
}

 //PHP My admin
$dbServername = "localhost";
$dbUserName   = "root";
$dbPassword   = "";
$dbName       = "affilatesystem";

$conn = mysqli_connect( $dbServername, $dbUserName, $dbPassword, $dbName );


/* //Azure db
$dbServername = "affilate.database.windows.net";
$dbUserName   = "AffilAdmin@affilate";
$dbPassword   = "Ahoj123456";
$dbName       = "affilateDB";

$conn = mysqli_connect( $dbServername, $dbUserName, $dbPassword, $dbName );

*/


