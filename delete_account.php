<?php
if(empty($_POST["user_token"]))
{
	http_response_code(400);
	exit;
}

require "src/include.php";

$db->query("DELETE FROM `users` WHERE `netdex_id`=?", "s", verifyData("netdex_id", $_POST["user_token"]));
?>
<script src="common.js"></script>
<script>
	removeUserItems();
	location.href="/";
</script>
