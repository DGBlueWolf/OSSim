<!--Php here for include('session.php')-->
<!DOCTYPE html>
<?php
	session_set_cookie_params(3600,"/");
	session_start();
	ob_start();
?>
<html>
	<head>
	<link rel="stylesheet" type="text/css" href="osstyle.css">
	</head>
<body>
	<?php
		if(isset($_POST["HOME"]))
			header("Location: main.php");
		if(isset($_POST["PROC"]))
			header("Location: process.php");
		if(isset($_POST["MEM"]))
			header("Location: mem.php");
		if(isset($_POST["PAGE"]))
			header("Location: page.php");
	?>
	<div id=header>
		<div id=headercontent>
	 	OS Algorithms Simulator
		</div>
		<div id=date>
		<?php
			$today = getdate();
			echo $today["weekday"] . ' ' . $today["month"] . ' ' . 
				 $today["mday"] . ', ' . $today["year"];
		?>
		</div>
	</div>
	<div id=content>
		<div id=contentBox>
		<a id=head1>Welcome OS Students</a>
		<p id=ptext>This section will discuss the purpose of the site and why 
			learning about OS is a valuable for an undergraduate Computer 
			Science student.<br>
			Choose a topic in the menu to get started.</p>
		</div>
	</div>
	<div id=outmenu>
	<div id=menu>
		<div id=buttons>
		<form method=post>
		<input type=submit id=mbutton value="Home" name="HOME"><br>
		<input type=submit id=mbutton value="Process Scheduler" name="PROC"><br>
		<input type=submit id=mbutton value="Memory Management" name="MEM"><br>
		<input type=submit id=mbutton value="Page Replacement" name="PAGE">
		</form></div>
	</div>
	</div>
</body>
</html>
