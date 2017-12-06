<?php
include_once 'classes/affilatePartner.php';
include_once 'includes/dbh.inc.php';
$rows = 3;
$columns = 3;

$partners = array();
if(isset($_SESSION['u_id'])){
	$uid = mysqli_real_escape_string($conn, $_SESSION['u_id']);
}else{
	$uid = 1;//Id hlavního uživatele
}
$sql = "SELECT ap.partner_id, ap.name, ap.image_link, ap.link, up.link_to_affilate, up.active, up.partner_id AS up_partner_id FROM affilate_partners ap 
            LEFT JOIN user_partners up on ap.partner_id=up.partner_id AND up.user_id = ? ORDER BY up.active ASC LIMIT ?;";
$stmt = mysqli_stmt_init($conn);
if(!mysqli_stmt_prepare($stmt,$sql)) {
	//header("Location: ../web/login.php?mySettings=notLogedIn");
	exit();
}else{
	$count = $rows*$columns;
	mysqli_stmt_bind_param($stmt, "si", $uid,$count);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);

	while($row = mysqli_fetch_assoc($result)) {
		$partners[] = new affilatepartner($row['partner_id'],$row['link'],$row['name'],$row['image_link']);
	}
}
?>

<form action="includes/affilatePartners.inc.php" method="post">
    <table>
		<?php
		$actualIndex = 0;
		for ($x = 0; $x < $rows; $x++) {
			echo "<tr>";
			for ($y = 0; $y < $columns; $y++){
				if($actualIndex >= count($partners))break;
				$p_id = $partners[$actualIndex]->partner_id;
				$p_name = $partners[$actualIndex]->name;
				$p_image = $partners[$actualIndex]->image_link;

				echo "<td>
                        <input class='img-fluid' type='image' src='$p_image' name='affilate[$p_id]'/>
                      </td>";
				$actualIndex++;
			}
			echo "</tr>";
		}?>
    </table>
</form>
<!--
<div class="container-fluid p-0">
    <div class="row no-gutters popup-gallery">
        <div class="col-lg-4 col-sm-6">
            <a class="portfolio-box" href="img/portfolio/fullsize/1.jpg">
                <img class="img-fluid" src="img/portfolio/thumbnails/1.jpg" alt="">
                <div class="portfolio-box-caption">
                    <div class="portfolio-box-caption-content">
                        <div class="project-category text-faded">
                            Category
                        </div>
                        <div class="project-name">
                            Project Name
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-sm-6">
            <a class="portfolio-box" href="img/portfolio/fullsize/2.jpg">
                <img class="img-fluid" src="img/portfolio/thumbnails/2.jpg" alt="">
                <div class="portfolio-box-caption">
                    <div class="portfolio-box-caption-content">
                        <div class="project-category text-faded">
                            Category
                        </div>
                        <div class="project-name">
                            Project Name
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-sm-6">
            <a class="portfolio-box" href="img/portfolio/fullsize/3.jpg">
                <img class="img-fluid" src="img/portfolio/thumbnails/3.jpg" alt="">
                <div class="portfolio-box-caption">
                    <div class="portfolio-box-caption-content">
                        <div class="project-category text-faded">
                            Category
                        </div>
                        <div class="project-name">
                            Project Name
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-sm-6">
            <a class="portfolio-box" href="img/portfolio/fullsize/4.jpg">
                <img class="img-fluid" src="img/portfolio/thumbnails/4.jpg" alt="">
                <div class="portfolio-box-caption">
                    <div class="portfolio-box-caption-content">
                        <div class="project-category text-faded">
                            Category
                        </div>
                        <div class="project-name">
                            Project Name
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-sm-6">
            <a class="portfolio-box" href="img/portfolio/fullsize/5.jpg">
                <img class="img-fluid" src="img/portfolio/thumbnails/5.jpg" alt="">
                <div class="portfolio-box-caption">
                    <div class="portfolio-box-caption-content">
                        <div class="project-category text-faded">
                            Category
                        </div>
                        <div class="project-name">
                            Project Name
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-sm-6">
            <a class="portfolio-box" href="img/portfolio/fullsize/6.jpg">
                <img class="img-fluid" src="img/portfolio/thumbnails/6.jpg" alt="">
                <div class="portfolio-box-caption">
                    <div class="portfolio-box-caption-content">
                        <div class="project-category text-faded">
                            Category
                        </div>
                        <div class="project-name">
                            Project Name
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
//-->