<?php
if(empty($_POST["user_token"])||!isset($_POST["text"]))
{
	http_response_code(400);
	exit;
}

require "src/include.php";

$text = substr($_POST["text"], 0, 255);

$db->query("UPDATE `users` SET `text`=? WHERE `netdex_id`=?", "ss", $text, verifyData("netdex_id", $_POST["user_token"]));
?>
<script>
	localStorage.setItem("user_text", JSON.parse('<?=json_encode($text, JSON_HEX_QUOT); ?>'));
	location.href="/";
</script>
