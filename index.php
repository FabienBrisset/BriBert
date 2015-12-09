<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="ISO-8859-1">
		<title>WordDef</title>
		<link href="./bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
		<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" />
		<link href="WordDef.css" rel="stylesheet" />
		
		<script type="text/javascript" src="./jquery-2.1.4.min.js"></script>
		<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
	</head>

	<body bgcolor = "azure">
		<form class = "form-search" action="" method = "POST">
			<div id = 'form' class = "input-append">
				<?php 
					if (isset($_POST['dataToSearch'])) {
						$dataToSearch = $_POST['dataToSearch'];
						echo "<input class='span2 search-query' id = 'inputForm' type = 'text' name = 'dataToSearch' value = ".$dataToSearch." />";
					}
					else if (isset($_GET['mot'])) {
						$dataToSearch = $_GET['mot'];
						echo "<input class='span2 search-query' id = 'inputForm' type = 'text' name = 'dataToSearch' value = ".$dataToSearch." />";
					}
					else { 
						echo "<input class='span2 search-query' id = 'inputForm' type = 'text' name = 'dataToSearch' placeholder = 'Mot a chercher sur Diko' />";
					}
				?>
				<button type = "submit" class="btn" id = 'validForm' name = "valider" onclick = "putOnTop()" /><span class = "glyphicon icon-search"></span></button>
			</div>
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
				preg_match("~<def>(.*)</def>~i", $contenuFichier, $matches);
				if ($matches != null) {
					echo "<div id = 'title'><h1><b>".$dataToSearchWithFirstUpperCase."</b></h1></div>";
					echo "<h1><b><font color = 'midnightblue'> Definitions</font></b></h1><div id = 'definition'>".$matches[1]."</div>";
					preg_match("#<sortant>(.*)</sortant>#Us", $contenuFichier, $matches2);
					if ($matches2 != null) {
						echo "<div id = 'relationsSortantes'>";
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
									$containsCrochetOuvrant = strstr($dataToDisplay , "[");
									$containsCrochetFermant = strstr($dataToDisplay , "]");
									
									if ($containsAppostrophes == null && $containsUnderscores == null && $containsRCrochet == null) {
										if (($containsCrochetOuvrant != null && $containsCrochetFermant != null) || ($containsCrochetOuvrant == null && $containsCrochetFermant == null) ) {
											if ($dataToDisplay != null) {
												if ($dataToDisplay != " ") {
													echo $dataToDisplay."<br><br>";
													array_push($relSortantes, $rel[$i]);
												}
											}
										}
									}
								}
							}
						}
						echo "</div>";
					}
					preg_match("#<entrant>(.*)</entrant>#Us", $contenuFichier, $matches3);
					if ($matches3 != null) {
						echo "<h1><b><font color = 'red'> Relations entrantes :</font></b></h1> ";
						$rel = explode(">", $matches3[1]);
						$relEntrantes = [];
						$i = 0;
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
									$containsCrochetOuvrant = strstr($dataToDisplay , "[");
									$containsCrochetFermant = strstr($dataToDisplay , "]");
									
									if ($containsAppostrophes == null && $containsUnderscores == null && $containsRCrochet == null) {
										if (($containsCrochetOuvrant != null && $containsCrochetFermant != null) || ($containsCrochetOuvrant == null && $containsCrochetFermant == null)) {
											if ($dataToDisplay != null) {
												if ($dataToDisplay != " ") {
													echo $dataToDisplay."<br><br>";
													array_push($relSortantes, $rel[$i]);
												}
											}
										}
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
				
				$currentColonne = 0;
				$ligne = 0;
				$currentLigne = 0;

				if (strstr($data, 'OVERLOAD') == NULL) {
					$i = 0;
					$matches;
					preg_match("~<def>(.*)</def>~i", $data, $matches);
					if ($matches != null) {
						echo "<div id = 'title'><h1><b>".$dataToSearchWithFirstUpperCase."</b></h1></div>";
						echo "<h1><b><font color = 'red'> Definitions :</font></b></h1> ".$matches[1];
						preg_match("#<sortant>(.*)</sortant>#Us", $data, $matches2);
						if ($matches2 != null) {
							echo "<h1><b><font color = 'red'> Relations sortantes :</font></b></h1> ";
							echo "<table border = '0' align = 'center'>";
							$rel = explode(">", $matches2[1]);
							
							$relSortantes = [];
							echo "<tr>";
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
											if ($dataToDisplay != null) {
												if ($dataToDisplay != " ") {
													if ($currentLigne != $ligne) {
														echo "<tr><td>";
														$ligne++;
														$currentColonne = 0;
													}
													else {
														echo "<td>";
													}
													echo $dataToDisplay."<br><br>";
													array_push($relSortantes, $rel[$i]);
													if ($currentColonne == 29) {
														echo "</td></tr>";
														$currentColonne++;
														$currentLigne++;
													}
												}
											}
										}
									}
								}
							}
						}
						preg_match("#<entrant>(.*)</entrant>#Us", $data, $matches3);
						if ($matches3 != null) {
							echo "<h1><b><font color = 'red'> Relations sortantes :</font></b></h1> ";
							$rel = explode(">", $matches3[1]);
							
							$relEntrantes = [];
							$i = 0;
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
										$containsCrochetOuvrant = strstr($dataToDisplay , "[");
										$containsCrochetFermant = strstr($dataToDisplay , "]");
										
										if ($containsAppostrophes == null && $containsUnderscores == null && $containsRCrochet == null) {
											if (($containsCrochetOuvrant != null && $containsCrochetFermant != null) || ($containsCrochetOuvrant == null && $containsCrochetFermant == null)) {
												if ($dataToDisplay != null) {
													if ($dataToDisplay != " ") {
														echo $dataToDisplay."<br><br>";
														array_push($relSortantes, $rel[$i]);
													}
												}
											}
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
		<script>
			if (document.getElementById('definition') != null) {
					document.getElementById('form').style.marginTop = "0px";
				}
			function putOnTop() {
				document.getElementById('form').style.marginTop = "0px";
				checkedOnce = true;
			}
		</script>
	</body>
</html>