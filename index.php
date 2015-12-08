<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="ISO-8859-1">
		<title>WordDef</title>
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
			$dataToSearch1 = str_replace(' ', '', $dataToSearch);
			$dataToSearch2 = str_replace('\t', '', $dataToSearch1);
			$dataToSearch3 = str_replace('\n', '', $dataToSearch2);
			$dataToSearch4 = str_replace('\r', '', $dataToSearch3);
			$dataToSearch5 = str_replace('\0', '', $dataToSearch4);
			$dataToSearch = str_replace('\x0B', '', $dataToSearch5);
			$dataToSearch = strtolower($dataToSearch);
			
			$dataToSearchWithFirstUpperCase = ucfirst($dataToSearch);
			echo "<h1><b>".$dataToSearchWithFirstUpperCase."</b></h1>";
			
			// On va regarder si le mot a déjà été recherché (si un fichier du même nom existe déjà)
			$i = 0;
			$fileToSearch2 = str_replace("é", "e", $dataToSearch);
			$fileToSearch3 = str_replace("à", "a", $fileToSearch2);
			$fileToSearch4 = str_replace("è", "e", $fileToSearch3);
			$fileToSearch5 = str_replace("û", "u", $fileToSearch4);
			$fileToSearch6 = str_replace("ô", "o", $fileToSearch5);
			$fileToSearch = $fileToSearch6.".html";
			$caches = scandir("caches");
			$wordAlreadySearch = false;
			
			for ($i; $i < sizeof($caches); $i++) {
				if ($caches[$i] == $fileToSearch) {
					$wordAlreadySearch = true;
					break;
				}
			}
			
			if ($wordAlreadySearch) {
				$contenuFichier = file_get_contents("caches/".$fileToSearch);
				$i = 0;
				$matches;
				preg_match("~<def>(.*)</def>~i", $contenuFichier, $matches);
				if ($matches != null) {
					echo "<h1><b><font color = 'red'> Definitions :</font></b></h1> ".$matches[1];
					preg_match("#<sortant>(.*)</sortant>#Us", $contenuFichier, $matches2);
					if ($matches2 != null) {
						echo "<h1><b><font color = 'red'> Relations sortantes :</font></b></h1> ";
						$rel = explode(">", $matches2[1]);
						
						$relSortantes = [];
						for ($i; $i < sizeof($rel); $i++) {
							if ($rel[$i] != " " && $rel[$i] != $dataToSearch) {
								$dataToDisplay = str_replace("0", "", $rel[$i]);
								$dataToDisplay = str_replace("1", "", $dataToDisplay);
								$dataToDisplay = str_replace("2", "", $dataToDisplay);
								$dataToDisplay = str_replace("3", "", $dataToDisplay);
								$dataToDisplay = str_replace("4", "", $dataToDisplay);
								$dataToDisplay = str_replace("5", "", $dataToDisplay);
								$dataToDisplay = str_replace("6", "", $dataToDisplay);
								$dataToDisplay = str_replace("7", "", $dataToDisplay);
								$dataToDisplay = str_replace("8", "", $dataToDisplay);
								$dataToDisplay = str_replace("9", "", $dataToDisplay);
								$dataToDisplay = str_replace(":", "", $dataToDisplay);
								$dataToDisplay = str_replace("  ", " ", $dataToDisplay);
								if (in_array($dataToDisplay, $relSortantes) == false) {
									
									$containsAppostrophes = strstr($dataToDisplay , "\"");
									$containsUnderscores = strstr($dataToDisplay , "_");
									$containsRCrochet = strstr($dataToDisplay , "r [");
									
									if ($containsAppostrophes == null && $containsUnderscores == null && $containsRCrochet == null) {
										echo $dataToDisplay."<br><br>";
										array_push($relSortantes, $rel[$i]);
									}
								}
							}
						}
					}
				}
			}
			else {
				$site = "http://www.jeuxdemots.org/rezo-xml.php?gotermsubmit=Chercher&gotermrel=".$dataToSearch."&output=onlyxml";
				$data  = file_get_contents($site);
				$monfichier = fopen("caches/".$fileToSearch, 'w+');
				fputs($monfichier, $data);

				if (strstr($data, 'OVERLOAD') == NULL) {
					$i = 0;
					$matches;
					preg_match("~<def>(.*)</def>~i", $data, $matches);
					if ($matches != null) {
						echo "<h1><b><font color = 'red'> Definitions :</font></b></h1> ".$matches[1];
						preg_match("#<sortant>(.*)</sortant>#Us", $data, $matches2);
						if ($matches2 != null) {
							echo "<h1><b><font color = 'red'> Relations sortantes :</font></b></h1> ";
							$rel = explode(">", $matches2[1]);
							
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
						}
					}
				}
				else {
					header("Refresh: 1; URL=index.php?mot=".$dataToSearch);
				}
			}
		}
		?>

	</body>
	
	<script type="text/javascript" src="./jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
</html>