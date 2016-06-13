<!--Php here for include('session.php')-->
<!DOCTYPE html>
<?php
	ob_start( );
	function sset($name,$value=Null)
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
		$sim["hits"] = 0;
		$sim["hitrow"] = 0;
		$sim["refs"] = 0;
		$sim["perc"] = 0;
		if( !issset("IND"))
			sset("IND",0);
		if( !issset("TLB"))
			sset("TLB");
		if( !issset("HIT"))
			sset("HIT",True);
		if( !issset("SIM"))
			sset("SIM",$sim);
		
		$sim = sget("SIM");	
		$hit = sget("HIT");
		$ind = sget("IND");
		$ref = sget("REF");
		$tlb = sget("TLB");
		
		store_post("MEMSIM","");
		store_post("NUMFRAME",40);
		store_post("NUMPAGE",40);
		store_post("TLBSIZE",10);
		store_post("TSHOW","Show TLB");
		store_post("PSHOW","Show Page Table");
		
		$tshow = (sget("TSHOW") == "Show TLB" );
		$pshow = (sget("PSHOW") == "Show Page Table");
		
		$go = ispset("MEMSIM");
		$reset = ispset("MRESET");
		$next = ispset("NEXT");
		$numframe = intval(sget("NUMFRAME"));
		$numpage = intval(sget("NUMPAGE"));
		$tlbsize = intval(sget("TLBSIZE"));
		
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
			<a id=head1>Memory Management With the TLB</a><br>
			<p id=ptext>This section will discuss the memory access problem and
				how it is solved with the translation look-aside buffer and a 
				layered page table.</p>
		</div>
		
		<form method="POST" id=algSelect>
			<?php
				echo 'Number of Frames: ';
				echo '<input type=text id=intbox name="NUMFRAME" value='.
					$numframe.'></input> ';
				echo 'Number of Pages: ';
				echo '<input type=text id=intbox name="NUMPAGE" value='.
					$numpage.'></input> ';
				echo 'TLB Size: ';
				echo '<input type=text id=intbox name="TLBSIZE" value='.
					$tlbsize.'></input> ';
			?>
			<br><input type=submit id=simButton name="MEMSIM" value="GO"></input>
		</form>
		<div id=algSelect >TLB Hits/References: <a style="font-family: monospace;font-size:16px;">
		<?php
			$sim["perc"] = 100*$sim["hits"]/$sim["refs"];
			echo $sim["hits"] . '/' . $sim["refs"] . ' ' . number_format($sim["perc"],2) . '%';
		?></a>
		</div>
		<div id=algSelect>Effective Access Time: <a style="font-family: monospace;font-size:16px;">
		<?php
			echo number_format((50*(2-($sim["perc"])/100)),2) . 'ms';
		?></a>
		</div>
		<form method="POST" id=algSelect>Generated Address:
			<?php 
				if($reset)
				{
					$tlb = Null;
					$sim = Null;
					$ind = 0;
					$ref = Null;
					sset("REF",$ref);
					sset("IND",$ind);
					sset("SIM",$sim);
					sset("TLB",$tlb);
				}
				if($go)
				{
					$tlb = Null;
					$sim = Null;
					$ind = 0;
					$ref = Null;
					exec('python randref.py '.$numpage.' '.$numframe,$ref);
					sset("REF",$ref);
					sset("IND",$ind);
					sset("SIM",$sim);
					sset("TLB",$tlb);
				}
				if($next)
				{
					$sim["refs"]++;
					$ind = rand(0,count($ref)/3-1);
					$page = 3*$ind;
					$frame = $page + 1;
					$page = $ref[$page];
					$frame = $ref[$frame];
					if( isset($tlb[$page]) )
					{
						$hit = True; 
						$sim["hits"]++;
						$sim["hitrow"] = array_search($page,array_keys($tlb));
					}
					else if( count($tlb) >= $tlbsize )
					{
						$hit = False;
						unset($tlb[key($tlb)]);
						$tlb[$page] = $frame;
					}
					else
						$tlb[$page] = $frame;
					
					sset("SIM",$sim);
					sset("HIT",$hit);
					sset("TLB",$tlb);
					sset("IND",$ind);
				}
				$page = 3*$ind;
				$frame = $page + 1;
				$offset = $frame + 1;
				echo '<a style="font-family:monospace;font-size:16px;">'.$ref[$page].':'.$ref[$offset].'</a>';
				$rampos = ceil(($ref[$frame]*99*5)/$numframe)/5;
				$pagpos = ceil(($ref[$page]*99*5)/$numpage)/5;
				$tlbpos = ceil(($sim["hitrow"]*95*5)/$tlbsize)/5;
			?>
			<br><input type=submit id=algButton name="NEXT" style="border-radius:3px;" value="New Address"></input>
		</form>
		<div id=algSelect>
		<!--TLB-->
		<div style="width:100px;">TLB: &nbsp&nbsp<b style="<?php if( $hit ) {echo 'color:green';} else {echo 'color:red';}?>;">
			<?php if( $hit ){echo 'Hit';} else {echo 'Miss';}?></b> </div> 
			<div id=memBlock style="width:20%;<?php if( !$hit ) echo 'border:1px solid red;'; ?>">
			<?php if( $hit ){echo '<div id=memMarker style="left:'.$tlbpos.'%;width:5%;"></div>';} ?>
			</div><br><br>
		<div style="width:100px;">PAGE TABLE: </div>
			<div id=memBlock style="width:100%;">
			<div id=memMarker style="left:<?php echo $pagpos; ?>%;width:1%;<?php if( $hit ) echo "background:lightgray"; ?>"></div>
			</div><br><br>
		<div style="width:100px;">RAM: </div>
			<div id=memBlock style="width:100%;">
			<div id=memMarker style="left:<?php echo $rampos; ?>%;width:1%;"></div>
			</div><br><br>
		<form method=POST id=algSelect>
			<?php
				if( $tshow )
					echo '<input type=submit id=simButton name="TSHOW" value="Hide TLB" style="width:150px;height:20px;font-size:12px;"></input> ';
				else
					echo '<input type=submit id=simButton name="TSHOW" value="Show TLB" style="width:150px;height:20px;font-size:12px;"></input> ';
				if( $pshow )
					echo '<input type=submit id=simButton name="PSHOW" value="Hide Page Table" style="width:150px;height:20px;font-size:12px;"></input> ';
				else
					echo '<input type=submit id=simButton name="PSHOW" value="Show Page Table" style="width:150px;height:20px;font-size:12px;"></input> ';
			?>
			<input type=submit id=simButton name="MRESET" style="width:150px;height:20px;font-size:12px;"value="Reset"></input>
		</form>
		<table id=procTable>
		<?php
			if( $tshow )
			{
				echo '<tr>';
				echo '<th id=procHead>Page</th>';
				echo '<th id=procHead>Frame</th>';
				echo '</tr>';
				foreach( $tlb as $page => $frame )
				{
					echo '<tr>';
					echo '<td id=procCell>'.$page.'</td>';
					echo '<td id=procCell>'.$frame.'</td>';
					echo '</tr>';
				}
			}
		?>
		</table>
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
