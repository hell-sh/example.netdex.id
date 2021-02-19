<?php
require "src/include.php";
?>
<html>
<head>
	<title>example.netdex.id</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdn.hell.sh/uikit/3.6.13/uikit.css" integrity="sha384-LDLXMmtP14v8QWSTzirex+/kwPzOR5n0US3+VD7FuQOp5pyFGi2TaqaRw/wMKfK5" crossorigin="anonymous">
</head>
<body>
	<div class="uk-margin-top uk-container">
		<div id="logged-out">
			<h1>example.netdex.id</h1>
			<p><a class="uk-button uk-button-primary" href="https://netdex.id/app_auth/<?=$netdex_app_id;?>">Login</a></p>
		</div>
		<div id="logged-in" class="uk-hidden">
			<h1>Hi there<span id="display_name"></span>!</h1>
			<img id="user-pic" style="width:100px;height:100px" />
			<p id="bio" class="uk-hidden"></p>
			<p>
				<a id="add-display-name" class="uk-button uk-button-primary" href="https://netdex.id/app_auth/<?=$netdex_app_id;?>?scopes=display_name">Add your display name</a>
				<a id="edit-display-name" class="uk-hidden uk-button uk-button-default" onclick="netdex.updateDisplayName();">Edit your display name</a>
				<a id="add-bio" class="uk-button uk-button-primary" href="https://netdex.id/app_auth/<?=$netdex_app_id;?>?scopes=bio">Add your bio</a>
				<a id="edit-bio" class="uk-hidden uk-button uk-button-default" onclick="netdex.updateBio();">Edit your bio</a>
				<a class="uk-button uk-button-secondary" onclick="logout();">Log out</a>
			</p>
			<h2>Your Personal Text</h2>
			<form method="POST" action="update_text.php">
				<input type="hidden" name="user_token" />
				<input type="text" name="text" class="uk-input" />
				<input type="submit" class="uk-button uk-button-primary" value="Save" />
			</form>
			<h2>I'm over it</h2>
			<form method="POST" action="delete_account.php">
				<input type="hidden" name="user_token" />
				<input type="submit" class="uk-button uk-button-danger" value="Delete account" />
			</form>
		</div>
		<hr>
		<p>You can get <a href="https://github.com/hell-sh/example.netdex.id" target="_blank" rel="noreferer">the source code of this website</a> and use it as a template for your own website using login with Netdex!</p>
	</div>
	<script src="common.js"></script>
	<script src="https://integrate.netdex.id/v1.js"></script>
	<script>
		netdex.app_id="<?=$netdex_app_id;?>";

		if(localStorage.getItem("user_token"))
		{
			let user_id=atob(localStorage.getItem("user_token")).split(":")[1];
			document.getElementById("user-pic").src="https://api.netdex.id/v1/user_pic/"+user_id;

			document.getElementById("logged-out").classList.add("uk-hidden");
			document.getElementById("logged-in").classList.remove("uk-hidden");
			if(localStorage.getItem("user_display_name"))
			{
				document.getElementById("add-display-name").classList.add("uk-hidden");
				document.getElementById("edit-display-name").classList.remove("uk-hidden");
				document.getElementById("display_name").textContent = ", " + localStorage.getItem("user_display_name");
			}
			if(localStorage.getItem("user_bio"))
			{
				document.getElementById("add-bio").classList.add("uk-hidden");
				document.getElementById("edit-bio").classList.remove("uk-hidden");
				document.getElementById("bio").textContent = "Your bio: " + localStorage.getItem("user_bio");
				document.getElementById("bio").classList.remove("uk-hidden");
			}
			document.querySelector("[name='text']").value=localStorage.getItem("user_text");
			document.querySelectorAll("[name='user_token']").forEach(elm=>{
				elm.value = localStorage.getItem("user_token");
			});
		}

		function logout()
		{
			removeUserItems();
			location.reload();
		}
	</script>
</body>
</html>
