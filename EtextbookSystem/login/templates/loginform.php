<?php
	if(!isset($thisPage)){
		die();
	}
?> 
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="online interactive library of school textbooks">
		<meta name="author" content="Chandan Regmi, email: info@textbookslibrary.com">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<link rel="stylesheet" type="text/css" href="../vendor/semantic.min.css">
		<title><?=Title?></title>
		<style type="text/css">
			body {
			  background-color: #DADADA;
			}
			.myclass-login > .grid {
			  height: 80%;
			}
			.myclass-login .column {
			  max-width: 550px;
			}
			.ui.form .field>label{
				float:left;
			}
		</style>	
	</head>     	  
	<body class="myclass-login">
		<div class="ui inverted blue top attached borderless menu">
			<div class="ui simple dropdown right item">
				<?=Menu?>&nbsp;
				<i class="translate icon"></i>
				<div class="menu">
					<a href="<?=$_SERVER['PHP_SELF'].'?language=en'?>" class="item"><i class="us flag"></i><?=Lang1?></a>
					<a href="<?=$_SERVER['PHP_SELF'].'?language=ne'?>" class="item"><i class="nepal flag"></i><?=Lang2?></a>
				</div>
			</div>
		</div>
		<div class="ui middle aligned center aligned grid">
			<div class="six wide column">
				<img src="../images/logo.png">
				<div class="ui blue header content">
					<?=Message?>
				</div>
				<form class="ui large form" action="index.php?action=login" method="post" enctype="application/x-www-form-urlencoded">
					<div class="ui stacked segment">
						<div class="field">
							<div class="ui left icon input">
							<i class="user icon"></i>
							<input type="text" name="Username" id="name" placeholder="<?=UsernamePlaceholder?>" required autofocus maxlength="20">
						</div>
					</div>
					
					<div class="field">
						<div class="ui left icon input">
							<i class="lock icon"></i>
							<input type="password" name="Password" id="pass" placeholder="<?=PasswordPlaceholder?>" required maxlength="20">
						</div>
					</div>
					
					<input class="ui fluid large blue submit button" type="submit" name="Login" id = "sub" value="<?=Button?>">
					
					<?php if ( isset( $loginError) ) { ?>
					<div class="ui red message">
						<div class="content">
							<i class="attention icon"></i><?=ErrorMessage?>
						</div>
					</div>
					<?php } ?>

				</form>

				<div class="ui header">
					<?=Notice?>&nbsp;<a href="../index.php"><?=ToHome?></a>
				</div>
			</div>
		</div>
	</body>
</html>




