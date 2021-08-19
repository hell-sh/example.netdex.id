<?php
if(empty($_POST["user_data"])||empty($_POST["user_data_sig"]))
{
	http_response_code(400);
	exit;
}

$user_data=json_decode($_POST["user_data"],true);
if(time() - $user_data["time"] > 6)
{
	die("Netdex has signed your data too long ago. <a href='.'>Back to homepage.</a>");
}

// netdex.id public key, as obtained from https://api.netdex.id/v1/public_key
$key = openssl_pkey_get_public("-----BEGIN PUBLIC KEY-----\nMIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAtp/qRPCCv/p7aFDJgTsQLML1elZhleEP65qaOJemSjCNY1RgA2woXcr1HpBcmayMKsrq3w/sHfu455C3+Rz2oVoD4FHu8kt5DHzTWTA7iQsdQTFKo4yFgQedrtNS4heKLwoEkFUCp37lA95p6Hgf/TixWoxIiFoqXwZucCbqBJgQx/+EU1qHClC6byd19olflx5PC9Mcq8jqm/O5++bawTjZ8jLzz8NC0eCGSYjBVe12jVnKQNKAA5qBazHjgivqDCdfCqLgyFxz1FKxkXdp54ioP2IPyIlLSD9pYRnC2tmYOFFE4l6nFOuK7tvO42oa25K+nL3E89bsMp0Fcvk8rJBzgEUnCrgNoqVj4Bggx0aPSatjhd4KsCYa4hG+bpefFoC8FIkL+cDlOGDbTNcmTaN31EUzvb9o1rYp1D6XtZEEv1dmXyeJENVDu/mPx8NABfmXhSMSkWdDIBF9cUscwYwVXMIMzfv4wLWLiVpsse3+mzDIVrG83gMkmLCqRU/BrTT+HOXiQ+hXxiuyz1nWlCfjSsH/s+QGiNRQtnpD6wNFVJHtvmpqZoUumdXtdd6r5wT24q0Mrst8PI7DMXfNBgpA/omCQ80mD/g6fyaEzmFDi9b6/JaAp9fe51DJZJGZD+veGDntzIqbn5qxh9MR1L0hDDrqpgJGe0rXZ3qYyGECAwEAAQ==\n-----END PUBLIC KEY-----");

if(openssl_verify($_POST["user_data"], base64_decode($_POST["user_data_sig"]), $key) != 1)
{
	die("Invalid signature. <a href='/'>Back to homepage.</a>");
}

if(PHP_MAJOR_VERSION < 8)
{
	openssl_free_key($key);
}

require "src/include.php";

$display_name = $user_data["display_name"] ?? "";
$bio = $user_data["bio"] ?? "";

$res = $db->query("SELECT `display_name`,`bio`,`text` FROM `users` WHERE `netdex_id`=?", "s", $user_data["id"]);
if($res)
{
	if($res[0]["display_name"] != $display_name)
	{
		$db->query("UPDATE `users` SET `display_name`=? WHERE `netdex_id`=?", "ss", $display_name, $user_data["id"]);
	}
	if($res[0]["bio"] != $bio)
	{
		$db->query("UPDATE `users` SET `bio`=? WHERE `netdex_id`=?", "ss", $bio, $user_data["id"]);
	}
	$text = $res[0]["text"];
}
else
{
	$db->query("INSERT INTO `users` (`netdex_id`, `display_name`, `bio`) VALUES (?, ?, ?)", "sss", $user_data["id"], $display_name, $bio);
	$text = "";
}

$token = signData("netdex_id", $user_data["id"]);
?>
<script>
	localStorage.setItem("user_token", JSON.parse('<?=json_encode($token, JSON_HEX_APOS); ?>'));
	localStorage.setItem("user_display_name", JSON.parse('<?=json_encode($display_name, JSON_HEX_APOS); ?>'));
	localStorage.setItem("user_bio", JSON.parse('<?=json_encode($bio, JSON_HEX_APOS); ?>'));
	localStorage.setItem("user_text", JSON.parse('<?=json_encode($text, JSON_HEX_APOS); ?>'));
	location.href=".";
</script>
