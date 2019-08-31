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

	// Get needs data
	$query = "SELECT id, name, lft, rgt FROM u0156984_cement.str_assets ORDER BY id DESC LIMIT 0, 1";
	$assets_object = mysqli_query($mysqli, $query);
	$assets_rows = [];

	while($assets_row = mysqli_fetch_array($assets_object)) {
	    array_push($assets_rows, $assets_row);
	}

	$query = "SELECT id FROM u0156984_cement.str_content WHERE alias='$data[1]'";
	$id_object = mysqli_query($mysqli, $query);
	$id_rows = [];

	while($id_row = mysqli_fetch_array($id_object)) {
	    array_push($id_rows, $id_row);
	}

	$query = "SELECT asset_id FROM u0156984_cement.str_categories WHERE id='$data[2]'";
	$assets_parent_id_object = mysqli_query($mysqli, $query);
	$assets_parent_id_rows = [];

	while($assets_parent_id_row = mysqli_fetch_array($assets_parent_id_object)) {
	    array_push($assets_parent_id_rows, $assets_parent_id_row);
	}


	//Content table
    $table_title = $data[0];
	$table_alias = $data[1];
	$table_category = $data[2];
	$table_images = "{\"sale\":\"0\",\"price\":\"$data[4]\",\"image_intro\":\"$data[3]\",\"image_fulltext\":\"$data[3]\",\"vkcom\":\"0\",\"variants\":\"2\"}";
	$table_attribs = "{\"show_title\":\"\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}";
	$table_asset_id = intval($assets_rows[0][0]) + 1;
	$table_id = intval($id_rows[0][0]);
	$table_metadata = '{"robots":"","author":"","rights":"","xreference":""}';
	$table_introtext = $data[5];
	$table_fulltext = $data[6];


	//Asset table
	$asset_id = $table_asset_id;
	$asset_name = 'com_content.article.' . (intval(substr(strval($assets_rows[0][1]),-3)) + 1);
	$asset_lft = intval($assets_rows[0][2]) + 2;
	$asset_rgt = intval($assets_rows[0][3]) + 2;
	$asset_parent_id = $assets_parent_id_rows[0][0];
	$asset_title = $table_title;
	$asset_rules = '{"core.delete":{"6":1},"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1}}';
	$asset_level = 5;


	// Notification
	echo '<br> +++++++++++++++++++++++++++';
	echo '<br> Asset ID: '. $asset_id;
	echo '<br> Asset Title: '. $asset_title;
	echo '<br> Asset Parent ID: '. $asset_parent_id;
	echo '<br> Asset Name: '. $asset_name;
	echo '<br> Asset Rules: '. $asset_rules;
	echo '<br> Asset LFT: '. $asset_lft;
	echo '<br> Asset RGT: '. $asset_rgt;
	echo '<br> Asset Level: '. $asset_level;
	echo '<br> ----------------------------';
	echo '<br> Item Title: '. $table_title;
	echo '<br> Item Category id: '. $table_category;
	echo '<br> Item Price: '. $data[4];
	echo '<br> Item Images: '. $data[3];
	echo '<br> Item Alias: '. $table_alias;
	echo '<br> Item Introtext: '. $table_introtext;
	echo '<br> Item Fulltext: '. $table_fulltext;
	echo '<br> DB Item ID: '. $table_id;


	// Import SQL
	if ($table_id != '') {
		echo '<br> --------------------';
		echo '<br> Update content table';
		$content_sql = "UPDATE `u0156984_cement`.`str_content` SET title='$table_title', introtext='$table_introtext', catid='$table_category', images='$table_images' WHERE id='$table_id'";	
	}
	else {
		echo '<br> --------------------';
		echo '<br> Insert  content table';
		$content_sql = "INSERT INTO `u0156984_cement`.`str_content` (`asset_id`,`access`, `title`,`alias`, `introtext`, `fulltext`, `state`, `catid`, `created`, `publish_up`, `images`, `attribs`,`created_by`, `urls`, `language` , `metadata`) VALUES ('$table_asset_id', '1', '$table_title', '$table_alias', '$table_introtext', '$table_fulltext', '1', '$table_category', '2019-08-28 23:13:47', '2019-08-29 17:45:52', '$table_images', '$table_attribs','21', '{}', '*', '$table_metadata')";
		
		echo '<br> --------------------';
		echo '<br> Insert asset table';
		$asset_sql = "INSERT INTO `u0156984_cement`.`str_assets` (`id`,`parent_id`, `lft`,`rgt`, `level`, `name`, `title`, `rules`) VALUES ('$asset_id', '$asset_parent_id', '$asset_lft', '$asset_rgt', '$asset_level', '$asset_name', '$asset_title', '$asset_rules')";


		if ($mysqli->query($asset_sql) === TRUE) {
		    echo '<br> New record assets table created successfully';
		} 
		else {
		    echo '<br> Error: ' . $asset_sql . '<br>' . $mysqli->error;
		}
	}

	if ($mysqli->query($content_sql) === TRUE) {
	    echo '<br> New record content table created successfully';
	} else {
	    echo '<br> Error: ' . $content_sql . '<br>' . $mysqli->error;
	}
	echo '<br> +++++++++++++++++++++++++++';
}

$mysqli->close();

?>