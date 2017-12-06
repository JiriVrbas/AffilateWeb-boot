<?php
include_once 'classes/affilatePartner.php';
include_once 'includes/dbh.inc.php';
$rows    = 3;
$columns = 3;

$uid = null;

if ( isset( $_SESSION['come_link'] ) ) {
	$uid = getUserIdByLink( $_SESSION['come_link'] );
} elseif ( isset( $_SESSION['u_id'] ) ) {
	$uid = mysqli_real_escape_string( $conn, $_SESSION['u_id'] );
}

if ( $uid == null ) {
	$uid = 1;//Id hlavního uživatele
}

$partners    = array();
$partnersIds = array();

$sql  = "SELECT ap.name, ap.image_link, up.link_to_affilate, ap.partner_id FROM user_partners up 
          JOIN affilate_partners ap ON ap.partner_id = up.partner_id 
          AND up.user_id = ? 
          ORDER BY up.active ASC LIMIT ?;";
$stmt = mysqli_stmt_init( $conn );
if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
	header( "Location: index.php?partners=stmperror");
	exit();
} else {
	$count = $rows * $columns;
	mysqli_stmt_bind_param( $stmt, "si", $uid, $count );
	mysqli_stmt_execute( $stmt );
	$result = mysqli_stmt_get_result( $stmt );

	while ( $row = mysqli_fetch_assoc( $result ) ) {
		$partners[]    = array(
			"ap_name"             => $row['name'],
			"ap_image_link"       => $row['image_link'],
			"up_link_to_affilate" => $row['link_to_affilate']
		);
		$partnersIds[] = $row['partner_id'];
	}
	$rows_count = count( $partners );
	if ( $rows_count < $count ) {
		if ( count( $partnersIds ) > 0 ) {
			$where_statement = ' WHERE up.user_id = 1 AND ap.partner_id NOT IN (' . implode( ",", $partnersIds ) . ')';
			$sql             = 'SELECT ap.name, ap.image_link, up.link_to_affilate FROM affilate_partners ap 
                LEFT JOIN user_partners up ON ap.partner_id=up.partner_id' . $where_statement . 'LIMIT ?;';
		} else {
			$sql = "SELECT ap.name, ap.image_link, up.link_to_affilate FROM affilate_partners ap 
                LEFT JOIN user_partners up ON ap.partner_id=up.partner_id 
                WHERE up.user_id = 1 
                LIMIT ?;";
		}

		$stmt = mysqli_stmt_init( $conn );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			header( "Location: index.php?partners=stmperror" );
			exit();
		} else {
			$rows_to_fill = $count - $rows_count;
			mysqli_stmt_bind_param( $stmt, "i", $rows_to_fill );
			mysqli_stmt_execute( $stmt );
			$result = mysqli_stmt_get_result( $stmt );

			while ( $row = mysqli_fetch_assoc( $result ) ) {
				$partners[] = array( "ap_name"             => $row['name'],
				                     "ap_image_link"       => $row['image_link'],
				                     "up_link_to_affilate" => $row['link_to_affilate']
				);
			}
		}
	}
}
function getUserIdByLink($link){
	//global $conn
	$conn = $GLOBALS['conn'];
	$p_link = mysqli_real_escape_string($conn, $link);

	if (!empty($p_link)) {
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
?>

<div class="table-responsive">
    <table>
		<?php
		$actualIndex = 0;
		for ( $x = 0; $x < $rows; $x ++ ) {
			echo "<tr class='d-lg-table-row'>";
			for ( $y = 0; $y < $columns; $y ++ ) {
				if ( $actualIndex >= count( $partners ) ) {
					break;
				}
				$p_name             = $partners[ $actualIndex ]['ap_name'];
				$p_image            = $partners[ $actualIndex ]['ap_image_link'];
				$p_link_to_affilate = $partners[ $actualIndex ]['up_link_to_affilate'];

				echo "<td class='d-lg-table-cell'><a href='$p_link_to_affilate' target='_blank'>
                    <img src='$p_image' alt='$p_name' width='400px' height='400px'>
                  </a></td>";

				$actualIndex ++;
			}
			echo "</tr>";
		} ?>
    </table>
</div>