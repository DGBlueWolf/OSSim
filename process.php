<!--Php here for include('session.php')-->
<!DOCTYPE html>
<?php
	ob_start( );
	function sset($name,$value)
	{
		$_SESSION[$name]=$value;
	}
	function sget($name)
	{
		if(issset($name))
			return $_SESSION[$name];
		else
			return NULL;
	}
	function pget($name)
	{
		if(ispset($name))
			return $_POST[$name];
		else
			return NULL;
	}
	function ispset($name)
	{
		return isset($_POST[$name]);
	}
	function issset($name)
	{
		return isset($_SESSION[$name]);
	}
	function store_post($name,$def=NULL)
	{
		if(!issset($name))
			sset($name,$def);
		else if(ispset($name))
			sset($name,pget($name));
	} 
?>
<html>
	<head>
	<link rel="stylesheet" type="text/css" href="osstyle.css">
	</head>
<body>
	<?php
		store_post("NUMPROC",10);
		store_post("ALG","");
		store_post("SIMPROC","Simulate");
		store_post("QUANTA",5);
		$alg = sget("ALG");
		$procs = sget("NUMPROC");
		$sim = sget("SIMPROC");
		$quanta = sget("QUANTA");
		
		if(isset($_POST["HOME"]))
			header("Location: main.php");
		if(isset($_POST["PROC"]))
			header("Location: process.php");
		if(isset($_POST["MEM"]))
			header("Location: mem.php");
		if(isset($_POST["PAGE"]))
			header("Location: page.php");
	?>
	<div id=content>
		<div id=contentBox>
			<a id=head1>Process Scheduler</a><br>
			<p id=ptext>Discuss things about the CPU scheduler.</p>
		</div>
		<form method="POST" id=algSelect>
			Pick An Algorithm:
			<input type=submit id=algButton name="ALG" value="FCFS"></input>
			<input type=submit id=algButton name="ALG" value="RoundRobin"></input>
			<input type=submit id=algButton name="ALG" value="Priority"></input>
			<input type=submit id=algButton name="ALG" value="SJF"></input>
		</form>
		<?php
			if($alg != ""){
			echo '<div id=contentBox> ';
			echo '<a id=head2>';
			if($alg == "FCFS")
			{
				echo 'First-Come First-Serve';
				echo '</a><br><a id=ptext> ';
				echo 'Some words about the FCFS Algorithm.';
			}
			else if($alg == "RoundRobin")
			{
				echo 'Round Robin';
				echo '</a><br><a id=ptext> ';
				echo 'Some words about the FCFS Algorithm.';
			}
			else if($alg == "Priority")
			{
				echo "Priority Queue";
				echo '</a><br><a id=ptext> ';
				echo 'Some words about the FCFS Algorithm.';
			}
			else if($alg == "SJF")
			{
				echo "Shortest Job First";
				echo '</a><br><a id=ptext> ';
				echo 'Some words about the FCFS Algorithm.';
			}
			echo '</a></div>';}
		?>
		<form method="POST" id=algSelect>
			Number of Process:  
			<?php
				echo '<input type=text id=intbox name="NUMPROC" value='.$procs.'></input>';
				if($alg == "RoundRobin")
				{
					echo ' Time Quanta (integer): <input type=text id=intbox name="QUANTA"'.
						' value='.$quanta.'></input>';
				}
			?>
			<br><input type=submit id=simButton name="SIMPROC" value="Simulate"></input><br>
		</form>
		<?php
			if( pget("SIMPROC")=="Simulate" )
				exec('python gantt.py ' . $procs . ' ' . $quanta);
			if($sim == "Simulate"){
			echo '<table id=procTable>';
			echo '<tr>';
			echo '<th id=procHead>Process ID</th>';
			echo '<th id=procHead>Arrival Time</th>';
			echo '<th id=procHead>Burst Time</th>';
			echo '<th id=procHead>Priority</th>';
			echo '</tr>';
			echo shell_exec("cat procs.txt");
			echo '</table>';
			if($alg == "FCFS")
				echo shell_exec("cat prcfcfs.txt");
			else if($alg == "RoundRobin")
				echo shell_exec("cat prcrr.txt");
			else if($alg == "Priority")
				echo shell_exec("cat prcpri.txt");
			else if($alg == "SJF")
				echo shell_exec("cat prcsjf.txt");
			}
		?>
		
		<form method=POST id=algSelect>
			<input type=submit id=simButton name="SIMPROC" value="Reset"></input>
		</form>
	</div>
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
