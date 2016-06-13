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
	function get_refs($frm,$len)
	{
		$a = array();
		for( $i = 0 ; $i < $frm ; $i++ )
		{
			$r = rand(ceil($len/$frm),$len);
			$a = array_merge($a,array_fill(0,$r,$i));
		}
		$c = array();
		for( $i = 0 ; $i < $len ; $i++ )
		{
			$b = rand(0,count($a));
			array_push($c,$a[$b]);
		}
		return $c;
	}
	function fifo($prefs,$nframe)
	{
		$queue = Null;
		$pagefaults = 0;
		foreach( $prefs as $ref )
		{
			if(!isset($queue[$ref]))
			{
				$pagefaults++;
				if(count($queue)>=$nframe)
					unset($queue[$key($queue)]);
				$queue[$ref] = $pagefaults;
			}
		}
		return $pagefaults;
	}
		
?>
<html>
	<head>
	<link rel="stylesheet" type="text/css" href="osstyle.css">
	</head>
<body>
	<?php
		store_post("ALG","");
		store_post("PGNFRAME",10);
		store_post("LENREF",20);
		store_post("UPINT",5);

		$alg = sget("ALG");
		$pgnframe = sget("PGNFRAME");
		$lenref = sget("LENREF");
		$upint = sget("UPINT");
		$refs = sget("REFS");
		
		$pgsim = ispset("PGSIM");
		if($pgsim)
		{
			$refs = get_refs($pgnframe,$lenref);
			sset("REFS");
		}
	
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
			<a id=head1>Page Replacement</a><br>
			<p id=ptext>This section will discuss the problem that page
				replacement algorithms are trying to solve, and how these
				algorithms impact system performance.</p>
		</div>
		<form method="POST" id=algSelect>
			Pick An Algorithm:
			<input type=submit id=algButton name="ALG" value="FIFO"></input>
			<input type=submit id=algButton name="ALG" value="OPT"></input>
			<input type=submit id=algButton name="ALG" value="LRU"></input>
			<input type=submit id=algButton name="ALG" value="LFU"></input>
			<input type=submit id=algButton name="ALG" value="NRU"></input>
		</form>
		<?php
			if($alg != ""){
			echo '<div id=contentBox> ';
			echo '<a id=head2>';
			if($alg == "FIFO")
			{
				echo 'First-In First-Out';
				echo '</a><br><a id=ptext> ';
				echo 'Wasting time doing something complex isnt worth it. Just'.
				' loop through the table replacing the page in the next'.
				' position';
			}
			else if($alg == "OPT")
			{
				echo 'Optimal';
				echo '</a><br><a id=ptext> ';
				echo 'The page which will be accessed farthest in the future'.
				' should be replaced.';
			}
			else if($alg == "LRU")
			{
				echo "Least Recently Used";
				echo '</a><br><a id=ptext> ';
				echo 'The oldest page that we have accessed should be replaced.';
			}
			else if($alg == "LFU")
			{
				echo "Least Frequently Used";
				echo '</a><br><a id=ptext> ';
				echo 'Infrequently used pages should be replaced';
			}
			else if($alg == "NRU")
			{
				echo "Not Recently Used";
				echo '</a><br><a id=ptext> ';
				echo 'According to the text this is the Enhanced Second-Chance'.
				' Algorithm. The computer stores a reference bit and a modified'.
				' bit for each page in memory. These bits are updated/cleared'.
				' at regular intervals. When a page-fault occurs, the OS'.
				' selects page closest to not-referenced and not-modified as a'.
				' victim.';
					
			}
			echo '</a></div>';}
		?>
		<form method="POST" id=algSelect>
			<?php
				echo 'Number of Frames: ';
				echo '<input type=text id=intbox name="PGNFRAME" value='.
					$pgnframe.'></input> ';
				echo 'Reference String Len: ';
				echo '<input type=text id=intbox name="LENREF" value='.
					$lenref.'></input> ';
				if( $alg == "NRU" )
				{
					echo 'Update Interval: ';
					echo '<input type=text id=intbox name="UPINT" value='.
						$upint.'></input> ';
				}
			?>
			<br><a id=ptext>Page Faults: <?php echo fifo($refs,$pgnframe); ?></a>
			<br><input type=submit id=simButton name="PGSIM" value="GO"></input>
		</form>
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
