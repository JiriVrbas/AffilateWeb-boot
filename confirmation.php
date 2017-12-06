<?php
include_once 'includes/dbh.inc.php';
if ( isset( $_GET['link'] ) ){
	$link = $_GET['link'];

	$confirmed = confirmMail($link);

	if($confirmed){
		header( "Location: index.php?emailconfirmed=true" );
	}else{
		header( "Location: index.php?emailconfirmed=false" );
	}

	exit();
}