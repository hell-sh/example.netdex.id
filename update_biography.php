<?php
if(empty($_POST["user_token"])||!isset($_POST["biography"]))
{
	http_response_code(400);
	exit;
}

require "src/include.php";

$biography = substr($_POST["biography"], 0, 255);

$db->query("UPDATE `users` SET `biography`=? WHERE `netdex_id`=?", "ss", $biography, verifyData("netdex_id", $_POST["user_token"]));
?>
<script>
	localStorage.setItem("user_biography", JSON.parse('<?=json_encode($biography, JSON_HEX_QUOT); ?>'));
	location.href="/";
</script>
