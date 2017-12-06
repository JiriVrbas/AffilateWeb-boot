<?php
include_once '../classes/userPartner.php';

session_start();
if (!empty($_POST['affilate'])) {
	include_once 'dbh.inc.php';

	$partnerId = key($_POST['affilate']);

	if(isset($_SESSION['come_link'])){
		$uid = getUserIdByLink($_SESSION['come_link']);
	}elseif(isset($_SESSION['u_id'])){
		$uid = mysqli_real_escape_string($conn, $_SESSION['u_id']);
	}

	if($uid == null){
		$uid = 1;//Id hlavního uživatele
	}

	$partner = getAffilatePartnerById($partnerId, $uid);

	if (is_null($partner)) {
		header("Location: ../web/affilatePartners.php?error=parnterisnull");
		exit();
	} else {
		saveClick($partner->partner_id, $uid);
		header("Location: $partner->link_to_affilate");
		exit();
	}
}
function getUserIdByLink($link){
	//global $conn
	$conn = $GLOBALS['conn'];
	$p_link = mysqli_real_escape_string($conn, $link);

	if (!empty($p_id)&&!empty($u_id)) {
		$sql = "SELECT user_id FROM users WHERE link = ?;";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			header("Location: ../index.php?getUserByLink=error");
			exit();
		} else {
			mysqli_stmt_bind_param($stmt, "s",$p_link);

			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$row = mysqli_fetch_assoc($result);
			if($row != null){
				return $row['user_id'];
			}
		}
	}
	return null;
}

function getAffilatePartnerById($partnerid,$uid){
	//global $conn
	$conn = $GLOBALS['conn'];
	$p_id = mysqli_real_escape_string($conn, $partnerid);
	$u_id = mysqli_real_escape_string($conn, $uid);
	if (!empty($p_id)&&!empty($u_id)) {
		$sql = "SELECT partner_id, user_id, link_to_affilate, active FROM user_partners WHERE partner_id=? AND user_id = ?;";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			header("Location: ../index.php?getAffilateParntnerById=error");
			exit();
		} else {
			mysqli_stmt_bind_param($stmt, "ss", $p_id,$u_id);

			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$row = mysqli_fetch_assoc($result);
			if($row != null){
				return new userPartner($row['partner_id'],$row['user_id'],$row['link_to_affilate'],$row['active']);
			}
		}
	}
	return null;
}