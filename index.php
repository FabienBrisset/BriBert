<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="ISO-8859-1">
		<title>DicoBriBert</title>
		<link href="./bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
	</head>

	<body bgcolor = "lightblue">
		<form action="" method = "POST">
			Mot a rechercher :
			<?php 
				if (isset($_POST['dataToSearch'])) {
					$dataToSearch = $_POST['dataToSearch'];
					echo "<input type = 'text' name = 'dataToSearch' value = ".$dataToSearch." />";
				}
				else if (isset($_GET['mot'])) {
					$dataToSearch = $_GET['mot'];
					echo "<input type = 'text' name = 'dataToSearch' value = ".$dataToSearch." />";
				}
				else { ?>
					<input type = "text" name = "dataToSearch" placeholder = "Mot a chercher sur Diko" />
				<?php
				}
				?>
			<input type = "submit" name = "valider" value = "Valider" />
		</form>
	
		<?php 
		if (isset($_POST['dataToSearch']) || isset($_GET['mot'])) {
			$site = "http://www.jeuxdemots.org/rezo-xml.php?gotermsubmit=Chercher&gotermrel=".$dataToSearch."&output=onlyxml";
			$data  = file_get_contents($site);

			if (strstr($data, 'OVERLOAD') == NULL) {
				$matches;
				preg_match("~<def>([^{]*)</def>~i", $data, $matches);
				echo "<h1><b><font color = 'red'> Definitions :</font></b></h1> ".$matches[1];
				preg_match("~<sortant>([^{]*)</sortant>~i", $data, $matches);
				echo "<h1><b><font color = 'red'> Relations sortantes :</font></b></h1> ";
				$rel = explode(">", $matches[1]);
				$i = 0;
				$relSortantes = [];
				for ($i; $i < sizeof($rel); $i++) {
					if (preg_match("<^[A-Za-z]*$>", $rel[$i])) {
						if ($rel[$i] != " " && $rel[$i] != $dataToSearch) {
							if (in_array($rel[$i], $relSortantes) == false) {
								echo $rel[$i]."<br>";
								array_push($relSortantes, $rel[$i]);
							}
						}
					}
				}
				

				//echo $relSortantes[1];
			}
			else {
				header("Refresh: 1; URL=index.php?mot=".$dataToSearch);
				//header('Refresh: 1; URL=http://www.jeuxdemots.org/rezo-xml.php?gotermsubmit=Chercher&gotermrel=ordinateur&output=onlyxml');
			}
		}
		?>

	</body>
	
	<script type="text/javascript" src="./jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
</html>