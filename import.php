<?php
include_once './pass.php';

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}
// Set charset
$mysqli->set_charset("utf8");



$handle = fopen('./base.csv', 'r');
while (($data = fgetcsv($handle, ';')) !== FALSE) {

	$query = "SELECT MAX(asset_id) FROM u0156984_cement.str_content";
	$assets_id_object = mysqli_query($mysqli, $query);
	$assets_id_rows = [];

	while($assets_id_row = mysqli_fetch_array($assets_id_object)) {
	    array_push($assets_id_rows, $assets_id_row);
	}

	$query = "SELECT id FROM u0156984_cement.str_content WHERE alias='$data[1]'";
	$id_object = mysqli_query($mysqli, $query);
	$id_rows = [];

	while($id_row = mysqli_fetch_array($id_object)) {
	    array_push($id_rows, $id_row);
	}

    $table_title = $data[0];
	$table_alias = $data[1];
	$table_category = $data[2];
	$table_images = "{\"sale\":\"0\",\"price\":\"$data[4]\",\"image_intro\":\"$data[3]\",\"image_fulltext\":\"$data[3]\",\"vkcom\":\"0\",\"variants\":\"2\"}";
	$table_attribs = "{\"show_title\":\"\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}";
	$table_asset_id = intval($assets_id_rows[0][0]) + 1;
	$table_id = intval($id_rows[0][0]);


	// Notification
	echo '<br> +++++++++++++++++++++++++++';
	echo '<br> +++++++++++++++++++++++++++';
	echo '<br> Item Title: '. $table_title;
	echo '<br> Item Category id: '. $table_category;
	echo '<br> Item Price: '. $data[4];
	echo '<br> Item Images: '. $data[3];
	echo '<br> Item Alias: '. $table_alias;
	echo '<br> DB Item ID: '. $id_rows[0][0];

	if ($table_id != '') {
		echo '<br> --------------------';
		echo '<br> Update table';
		$sql = "UPDATE `u0156984_cement`.`str_content` SET title='$table_title', catid='$table_category', images='$table_images' WHERE id='$table_id'";	
	}
	else {
		echo '<br> --------------------';
		echo '<br> Insert table';
		$sql = "INSERT INTO `u0156984_cement`.`str_content` (`asset_id`,`access`, `title`,`alias`, `introtext`, `fulltext`, `state`, `catid`, `created`, `publish_up`, `images`, `attribs`,`created_by`, `urls`, `language`) VALUES ('$table_asset_id', '1', '$table_title', '$table_alias', '$table_title', '$table_title', '1', '$table_category', '2019-08-28 23:13:47', '2019-08-29 17:45:52', '$table_images', '$table_attribs','21', '{}', '*')";
	}

	if ($mysqli->query($sql) === TRUE) {
	    echo '<br> New record created successfully';
	} else {
	    echo '<br> Error: ' . $sql . '<br>' . $mysqli->error;
	}
}


$mysqli->close();
?>