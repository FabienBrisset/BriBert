<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="ISO-8859-1">
		<title>WordDef</title>
		<link href="./bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" />
		
		<link href="WordDef.css" rel="stylesheet" />
		
		<script type="text/javascript" src="./jquery-2.1.4.min.js"></script>
		<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	</head>

	<body bgcolor = "azure">

			<form class = "form-search" action="" method = "POST">
			<?php 
			if (isset($_POST['dataToSearch']) || isset($_GET['mot'])) { // Si l'on doit afficher le formulaire en haut de la page ?>
				<div id = 'formOnTop' class = "input-append">
			<?php
			}
			else { // Sinon, si l'on doit l'afficher au milieu de la page ?>
				<div id = 'form' class = "input-append">
			<?php 
			}
					if (isset($_POST['dataToSearch'])) { // Si le mot a été cherché lors du chargement précédent
						$dataToSearch = $_POST['dataToSearch'];
						$dataToSearch = strtolower($dataToSearch);
						$dataToSearchWithFirstUpperCase = ucfirst($dataToSearch);
						
						?><input class="span2 search-query" id = "inputForm" type = "text" name = "dataToSearch" value = "<?php echo stripslashes(htmlspecialchars($dataToSearchWithFirstUpperCase));?>" /><?php
					}
					else if (isset($_GET['mot'])) { // Si le mot a été cherché lors des chargements précédents (accessibilité du site JeuxDeMots)
						$dataToSearch = $_GET['mot'];
						$dataToSearch = $_POST['dataToSearch'];
						$dataToSearch = strtolower($dataToSearch);
						$dataToSearchWithFirstUpperCase = ucfirst($dataToSearch);
						
						?><input class="span2 search-query" id = "inputForm" type = "text" name = "dataToSearch" value = "<?php echo stripslashes(htmlspecialchars($dataToSearchWithFirstUpperCase));?>" /><?php
					}
					else { // Sinon, si c'est le premier appel
						echo "<input class='span2 search-query' id = 'inputForm' type = 'text' name = 'dataToSearch' placeholder = 'Mot a chercher sur Diko' />";
					}
					?>
					<button type = "submit" class="btn" id = 'validForm' name = "valider" onclick = "putOnTop()" /><span class="glyphicon icon-search"></span> Search</button>
				</div>
			</form>
		
			<?php 
			if (isset($_POST['dataToSearch']) || isset($_GET['mot'])) {
				// Nettoyage du mot cherché
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
						if ($matches[1] != "") {
							echo "<div id = 'titleDef' onclick = 'displayDefinition()'><h1><b><font color = 'midnightblue'> Definitions</font></b></h1></div><div id = 'definition'>".$matches[1]."</div>";
						}
						preg_match("#<sortant>(.*)</sortant>#Us", $contenuFichier, $matches2);
						if ($matches2 != null) {
							echo "<div id = 'titleSortantes' onclick = 'displaySortantes()'><h1><b><font color = 'red'> Relations sortantes</font></b></h1></div>";
							echo "<div id = 'relationsSortantes'>";
							$rel = explode(">", $matches2[1]);
							$arrayTypesRel = array();
							for ($i; $i < sizeof($rel); $i++) {
								$typesRel = explode("type=\"", $rel[$i]);
								$j = 0;
								for ($j; $j < sizeof($typesRel); $j++) {
									preg_match("~r_(.*)\"~i", $typesRel[$j], $matches2bis);
									if ($matches2bis != null) {
										if (isset($matches2bis[1])) {
											if ($matches2bis != null) {
												$posInterestingDataMatches2bis = strpos($matches2bis[1], "\"");
												$interestingDataMatches2Bis = substr($matches2bis[1], 0, $posInterestingDataMatches2bis);
												if ($arrayTypesRel != null) {
													$k = 0;
													$found = false;
													for ($k; $k < sizeof($arrayTypesRel); $k++) {
														$containsTypeRel = strstr($arrayTypesRel[$k] , $interestingDataMatches2Bis);
														if ($containsTypeRel == $interestingDataMatches2Bis) {
															$found = true;
														}
													}
													if (!$found) {
														array_push($arrayTypesRel, $interestingDataMatches2Bis);
													}
												}
												else {
													array_push($arrayTypesRel, $interestingDataMatches2Bis);
												}
											}
										}
									}
								}
							}
							
							$i = 0;
							$relSortantes = array(array());
							
							for ($i; $i < sizeof($arrayTypesRel); $i++) {
								$relSortantes[$i][0] = "";
							}
							
							$i = 0;
			
							for ($i; $i < sizeof($rel); $i++) {
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
								$dataToDisplay = str_replace("</rel", "", $dataToDisplay);

								if ($rel[$i] != " " && $dataToDisplay != "") {
									$RelIAfterFirstQuote = strstr($rel[$i], "r_");
									$posInterestingDataRelI = strpos($RelIAfterFirstQuote, "\"");
									$interestingDataRelI = substr($RelIAfterFirstQuote, 0, $posInterestingDataRelI);
									if ($interestingDataRelI == null) {
										$RelIAfterFirstQuote = strstr($rel[($i - 1)], "r_");
										$posInterestingDataRelI = strpos($RelIAfterFirstQuote, "\"");
										$interestingDataRelI = substr($RelIAfterFirstQuote, 0, $posInterestingDataRelI);
										$interestingDataRelI = str_replace("r_", "", $interestingDataRelI);
									}
									
									$containsAppostrophes = strstr($dataToDisplay, "\"");
									$containsUnderscores = strstr($dataToDisplay, "_");
									$containsRCrochet = strstr($dataToDisplay, "r [");
									$containsCrochetOuvrant = strstr($dataToDisplay, "[");
									$containsCrochetFermant = strstr($dataToDisplay, "]");
									
									if ($containsAppostrophes == null && $containsUnderscores == null && $containsRCrochet == null) {
										if (($containsCrochetOuvrant != null && $containsCrochetFermant != null) || ($containsCrochetOuvrant == null && $containsCrochetFermant == null) ) {
											if ($dataToDisplay != null) {
												if ($dataToDisplay != " ") {
													$x = 0;
													$posInRelSortantes = 0;
													for ($x; $x < sizeof($arrayTypesRel); $x++) {
														if ($arrayTypesRel[$x] == $interestingDataRelI) {
															$posInRelSortantes = $x;
															break;
														}
													}
													
													if (in_array($dataToDisplay, $relSortantes[$posInRelSortantes]) == false) {
														array_push($relSortantes[$posInRelSortantes], $dataToDisplay);
													}
												}
											}
										}
									}
								}
							}
							
							for ($a = 0; $a < sizeof($arrayTypesRel); $a++) {
								if (sizeof($relSortantes[$a]) > 1) {
									echo "<div class = 'row'>";
									echo "<div class = 'col-md-12'>";
									echo "<b>".$arrayTypesRel[$a]."</b><br>";
									echo "</div>";
									echo "</div>";
									
									$cpt = 0;

									for ($b = 0; $b < sizeof($relSortantes[$a]); $b++) {	
										if ($relSortantes[$a][$b] != "")  {
											if ($cpt == 0) {
												echo "<div class = 'row'>";
													echo "<div class = 'col-md-3'>".$relSortantes[$a][$b]."</div>";
													
													if ($b == (sizeof($relSortantes[$a]) - 1)) {
														echo "</div>";
													}
												$cpt++;
											}
											else if ($cpt < 3 && $b != (sizeof($relSortantes[$a]) - 1)) {
												echo "<div class = 'col-md-3'>".$relSortantes[$a][$b]."</div>";
												$cpt++;
											}
											else {
												echo "<div class = 'col-md-3'>".$relSortantes[$a][$b]."</div>";
												echo "</div>";
												$cpt = 0;
											}
											
										}
									}
								}
							}
							echo "</div>";
						}
						preg_match("#<entrant>(.*)</entrant>#Us", $contenuFichier, $matches3);
						if ($matches3 != null) {
							echo "<div id = 'titleEntrantes' onclick = 'displayEntrantes()'><h1><b><font color = 'red'> Relations entrantes</font></b></h1></div>";
							echo "<div id = 'relationsEntrantes'>";
							$rel = explode(">", $matches3[1]);
							$arrayTypesRel = array();
						
							$i = 0;
							for ($i; $i < sizeof($rel); $i++) {
								$typesRel = explode("type=\"", $rel[$i]);
								$j = 0;
								for ($j; $j < sizeof($typesRel); $j++) {
									preg_match("~r_(.*)\"~i", $typesRel[$j], $matches3bis);
									if ($matches3bis != null) {
										if (isset($matches3bis[1])) {
											if ($matches3bis != null) {
												$posInterestingDataMatches3bis = strpos($matches3bis[1], "\"");
												$interestingDataMatches3Bis = substr($matches3bis[1], 0, $posInterestingDataMatches3bis);
												if ($arrayTypesRel != null) {
													$k = 0;
													$found = false;
													for ($k; $k < sizeof($arrayTypesRel); $k++) {
														$containsTypeRel = strstr($arrayTypesRel[$k] , $interestingDataMatches3Bis);
														if ($containsTypeRel == $interestingDataMatches3Bis) {
															$found = true;
														}
													}
													if (!$found) {
														array_push($arrayTypesRel, $interestingDataMatches3Bis);
													}
												}
												else {
													array_push($arrayTypesRel, $interestingDataMatches3Bis);
												}
											}
										}
									}
								}
							}
							
							$i = 0;
							$relEntrantes = array(array());
							
							for ($i; $i < sizeof($arrayTypesRel); $i++) {
								$relEntrantes[$i][0] = "";
							}
							
							$i = 0;
							for ($i; $i < sizeof($rel); $i++) {
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
								$dataToDisplay = str_replace("</rel", "", $dataToDisplay);
								if ($rel[$i] != " " && $dataToDisplay != "") {
									$RelIAfterFirstQuote = strstr($rel[$i], "r_");
									$posInterestingDataRelI = strpos($RelIAfterFirstQuote, "\"");
									$interestingDataRelI = substr($RelIAfterFirstQuote, 0, $posInterestingDataRelI);
									if ($interestingDataRelI == null) {
										$RelIAfterFirstQuote = strstr($rel[($i - 1)], "r_");
										$posInterestingDataRelI = strpos($RelIAfterFirstQuote, "\"");
										$interestingDataRelI = substr($RelIAfterFirstQuote, 0, $posInterestingDataRelI);
										$interestingDataRelI = str_replace("r_", "", $interestingDataRelI);
									}

									$containsAppostrophes = strstr($dataToDisplay, "\"");
									$containsUnderscores = strstr($dataToDisplay, "_");
									$containsRCrochet = strstr($dataToDisplay, "r [");
									$containsCrochetOuvrant = strstr($dataToDisplay, "[");
									$containsCrochetFermant = strstr($dataToDisplay, "]");
									
									if ($containsAppostrophes == null && $containsUnderscores == null && $containsRCrochet == null) {
										if (($containsCrochetOuvrant != null && $containsCrochetFermant != null) || ($containsCrochetOuvrant == null && $containsCrochetFermant == null) ) {
											if ($dataToDisplay != null) {
												if ($dataToDisplay != " ") {
													$x = 0;
													$posInRelEntrantes = 0;
													for ($x; $x < sizeof($arrayTypesRel); $x++) {
														if ($arrayTypesRel[$x] == $interestingDataRelI) {
															$posInRelEntrantes = $x;
															break;
														}
													}
													
													if (in_array($dataToDisplay, $relEntrantes[$posInRelEntrantes]) == false) {
														array_push($relEntrantes[$posInRelEntrantes], $dataToDisplay);
													}
												}
											}
										}
									}
								}
							}
							
							for ($a = 0; $a < sizeof($arrayTypesRel); $a++) {
								if (sizeof($relEntrantes[$a]) > 1) {
									echo "<div class = 'row'>";
									echo "<div class = 'col-md-12'>";
									echo "<b>".$arrayTypesRel[$a]."</b><br>";
									echo "</div>";
									echo "</div>";
									
									$cpt = 0;

									for ($b = 0; $b < sizeof($relEntrantes[$a]); $b++) {	
										if ($relEntrantes[$a][$b] != "")  {
											if ($cpt == 0) {
												echo "<div class = 'row'>";
													echo "<div class = 'col-md-3'>".$relEntrantes[$a][$b]."</div>";
													
													if ($b == (sizeof($relEntrantes[$a]) - 1)) {
														echo "</div>";
													}
												$cpt++;
											}
											else if ($cpt < 3 && $b != (sizeof($relEntrantes[$a]) - 1)) {
												echo "<div class = 'col-md-3'>".$relEntrantes[$a][$b]."</div>";
												$cpt++;
											}
											else {
												echo "<div class = 'col-md-3'>".$relEntrantes[$a][$b]."</div>";
												echo "</div>";
												$cpt = 0;
											}
											
										}
									}
								}
							}
							echo "</div>";
						}
					}
				}
				else {
					$encodedSearch = urlencode($dataToSearch);
					$site = "http://www.jeuxdemots.org/rezo-xml.php?gotermsubmit=Chercher&gotermrel=".$encodedSearch."&output=onlyxml";
					$data  = file_get_contents($site);
					if (strstr($data, '<def>') != NULL) {
						$monfichier = fopen("caches/".$fileToSearch, 'w+');
						fputs($monfichier, $data);
					}
					
					$currentColonne = 0;
					$ligne = 0;
					$currentLigne = 0;

					if (strstr($data, 'OVERLOAD') == NULL) {
						$i = 0;
						$matches;
						preg_match("~<def>(.*)</def>~i", $data, $matches);
						if ($matches != null) {
							echo "<div id = 'title'><h1><b>".$dataToSearchWithFirstUpperCase."</b></h1></div>";
							if ($matches[1] != "") {
								echo "<div id = 'titleDef' onclick = 'displayDefinition()'><h1><b><font color = 'midnightblue'> Definitions</font></b></h1></div><div id = 'definition'>".$matches[1]."</div>";
							}
							preg_match("#<sortant>(.*)</sortant>#Us", $data, $matches2);
							if ($matches2 != null) {
								echo "<div id = 'titleSortantes' onclick = 'displaySortantes()'><h1><b><font color = 'red'> Relations sortantes</font></b></h1></div>";
								echo "<div id = 'relationsSortantes'>";
								$rel = explode(">", $matches2[1]);
								$arrayTypesRel = array();
								for ($i; $i < sizeof($rel); $i++) {
									$typesRel = explode("type=\"", $rel[$i]);
									$j = 0;
									for ($j; $j < sizeof($typesRel); $j++) {
										preg_match("~r_(.*)\"~i", $typesRel[$j], $matches2bis);
										if ($matches2bis != null) {
											if (isset($matches2bis[1])) {
												if ($matches2bis != null) {
													$posInterestingDataMatches2bis = strpos($matches2bis[1], "\"");
													$interestingDataMatches2Bis = substr($matches2bis[1], 0, $posInterestingDataMatches2bis);
													if ($arrayTypesRel != null) {
														$k = 0;
														$found = false;
														for ($k; $k < sizeof($arrayTypesRel); $k++) {
															$containsTypeRel = strstr($arrayTypesRel[$k] , $interestingDataMatches2Bis);
															if ($containsTypeRel == $interestingDataMatches2Bis) {
																$found = true;
															}
														}
														if (!$found) {
															array_push($arrayTypesRel, $interestingDataMatches2Bis);
														}
													}
													else {
														array_push($arrayTypesRel, $interestingDataMatches2Bis);
													}
												}
											}
										}
									}
								}
								
								$i = 0;
								$relSortantes = array(array());
								
								for ($i; $i < sizeof($arrayTypesRel); $i++) {
									$relSortantes[$i][0] = "";
								}
								
								$i = 0;
				
								for ($i; $i < sizeof($rel); $i++) {
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
									$dataToDisplay = str_replace("</rel", "", $dataToDisplay);

									if ($rel[$i] != " " && $dataToDisplay != "") {
										$RelIAfterFirstQuote = strstr($rel[$i], "r_");
										$posInterestingDataRelI = strpos($RelIAfterFirstQuote, "\"");
										$interestingDataRelI = substr($RelIAfterFirstQuote, 0, $posInterestingDataRelI);
										if ($interestingDataRelI == null) {
											$RelIAfterFirstQuote = strstr($rel[($i - 1)], "r_");
											$posInterestingDataRelI = strpos($RelIAfterFirstQuote, "\"");
											$interestingDataRelI = substr($RelIAfterFirstQuote, 0, $posInterestingDataRelI);
											$interestingDataRelI = str_replace("r_", "", $interestingDataRelI);
										}
										
										$containsAppostrophes = strstr($dataToDisplay, "\"");
										$containsUnderscores = strstr($dataToDisplay, "_");
										$containsRCrochet = strstr($dataToDisplay, "r [");
										$containsCrochetOuvrant = strstr($dataToDisplay, "[");
										$containsCrochetFermant = strstr($dataToDisplay, "]");
										
										if ($containsAppostrophes == null && $containsUnderscores == null && $containsRCrochet == null) {
											if (($containsCrochetOuvrant != null && $containsCrochetFermant != null) || ($containsCrochetOuvrant == null && $containsCrochetFermant == null) ) {
												if ($dataToDisplay != null) {
													if ($dataToDisplay != " ") {
														$x = 0;
														$posInRelSortantes = 0;
														for ($x; $x < sizeof($arrayTypesRel); $x++) {
															if ($arrayTypesRel[$x] == $interestingDataRelI) {
																$posInRelSortantes = $x;
																break;
															}
														}
														
														if (in_array($dataToDisplay, $relSortantes[$posInRelSortantes]) == false) {
															array_push($relSortantes[$posInRelSortantes], $dataToDisplay);
														}
													}
												}
											}
										}
									}
								}
								
								for ($a = 0; $a < sizeof($arrayTypesRel); $a++) {
									if (sizeof($relSortantes[$a]) > 1) {
										echo "<div class = 'row'>";
										echo "<div class = 'col-md-12'>";
										echo "<b>".$arrayTypesRel[$a]."</b><br>";
										echo "</div>";
										echo "</div>";
										
										$cpt = 0;

										for ($b = 0; $b < sizeof($relSortantes[$a]); $b++) {	
											if ($relSortantes[$a][$b] != "")  {
												if ($cpt == 0) {
													echo "<div class = 'row'>";
														echo "<div class = 'col-md-3'>".$relSortantes[$a][$b]."</div>";
														
														if ($b == (sizeof($relSortantes[$a]) - 1)) {
															echo "</div>";
														}
													$cpt++;
												}
												else if ($cpt < 3 && $b != (sizeof($relSortantes[$a]) - 1)) {
													echo "<div class = 'col-md-3'>".$relSortantes[$a][$b]."</div>";
													$cpt++;
												}
												else {
													echo "<div class = 'col-md-3'>".$relSortantes[$a][$b]."</div>";
													echo "</div>";
													$cpt = 0;
												}
												
											}
										}
									}
								}
								echo "</div>";
							}
							preg_match("#<entrant>(.*)</entrant>#Us", $data, $matches3);
							if ($matches3 != null) {
								echo "<div id = 'titleEntrantes' onclick = 'displayEntrantes()'><h1><b><font color = 'red'> Relations entrantes</font></b></h1></div>";
								echo "<div id = 'relationsEntrantes'>";
								$rel = explode(">", $matches3[1]);
								$arrayTypesRel = array();
							
								$i = 0;
								for ($i; $i < sizeof($rel); $i++) {
									$typesRel = explode("type=\"", $rel[$i]);
									$j = 0;
									for ($j; $j < sizeof($typesRel); $j++) {
										preg_match("~r_(.*)\"~i", $typesRel[$j], $matches3bis);
										if ($matches3bis != null) {
											if (isset($matches3bis[1])) {
												if ($matches3bis != null) {
													$posInterestingDataMatches3bis = strpos($matches3bis[1], "\"");
													$interestingDataMatches3Bis = substr($matches3bis[1], 0, $posInterestingDataMatches3bis);
													if ($arrayTypesRel != null) {
														$k = 0;
														$found = false;
														for ($k; $k < sizeof($arrayTypesRel); $k++) {
															$containsTypeRel = strstr($arrayTypesRel[$k] , $interestingDataMatches3Bis);
															if ($containsTypeRel == $interestingDataMatches3Bis) {
																$found = true;
															}
														}
														if (!$found) {
															array_push($arrayTypesRel, $interestingDataMatches3Bis);
														}
													}
													else {
														array_push($arrayTypesRel, $interestingDataMatches3Bis);
													}
												}
											}
										}
									}
								}
								
								$i = 0;
								$relEntrantes = array(array());
								
								for ($i; $i < sizeof($arrayTypesRel); $i++) {
									$relEntrantes[$i][0] = "";
								}
								
								$i = 0;
								for ($i; $i < sizeof($rel); $i++) {
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
									$dataToDisplay = str_replace("</rel", "", $dataToDisplay);
									if ($rel[$i] != " " && $dataToDisplay != "") {
										$RelIAfterFirstQuote = strstr($rel[$i], "r_");
										$posInterestingDataRelI = strpos($RelIAfterFirstQuote, "\"");
										$interestingDataRelI = substr($RelIAfterFirstQuote, 0, $posInterestingDataRelI);
										if ($interestingDataRelI == null) {
											$RelIAfterFirstQuote = strstr($rel[($i - 1)], "r_");
											$posInterestingDataRelI = strpos($RelIAfterFirstQuote, "\"");
											$interestingDataRelI = substr($RelIAfterFirstQuote, 0, $posInterestingDataRelI);
											$interestingDataRelI = str_replace("r_", "", $interestingDataRelI);
										}

										$containsAppostrophes = strstr($dataToDisplay, "\"");
										$containsUnderscores = strstr($dataToDisplay, "_");
										$containsRCrochet = strstr($dataToDisplay, "r [");
										$containsCrochetOuvrant = strstr($dataToDisplay, "[");
										$containsCrochetFermant = strstr($dataToDisplay, "]");
										
										if ($containsAppostrophes == null && $containsUnderscores == null && $containsRCrochet == null) {
											if (($containsCrochetOuvrant != null && $containsCrochetFermant != null) || ($containsCrochetOuvrant == null && $containsCrochetFermant == null) ) {
												if ($dataToDisplay != null) {
													if ($dataToDisplay != " ") {
														$x = 0;
														$posInRelEntrantes = 0;
														for ($x; $x < sizeof($arrayTypesRel); $x++) {
															if ($arrayTypesRel[$x] == $interestingDataRelI) {
																$posInRelEntrantes = $x;
																break;
															}
														}
														
														if (in_array($dataToDisplay, $relEntrantes[$posInRelEntrantes]) == false) {
															array_push($relEntrantes[$posInRelEntrantes], $dataToDisplay);
														}
													}
												}
											}
										}
									}
								}
								
								for ($a = 0; $a < sizeof($arrayTypesRel); $a++) {
									if (sizeof($relEntrantes[$a]) > 1) {
										echo "<div class = 'row'>";
										echo "<div class = 'col-md-12'>";
										echo "<b>".$arrayTypesRel[$a]."</b><br>";
										echo "</div>";
										echo "</div>";
										
										$cpt = 0;

										for ($b = 0; $b < sizeof($relEntrantes[$a]); $b++) {	
											if ($relEntrantes[$a][$b] != "")  {
												if ($cpt == 0) {
													echo "<div class = 'row'>";
														echo "<div class = 'col-md-3'>".$relEntrantes[$a][$b]."</div>";
														
														if ($b == (sizeof($relEntrantes[$a]) - 1)) {
															echo "</div>";
														}
													$cpt++;
												}
												else if ($cpt < 3 && $b != (sizeof($relEntrantes[$a]) - 1)) {
													echo "<div class = 'col-md-3'>".$relEntrantes[$a][$b]."</div>";
													$cpt++;
												}
												else {
													echo "<div class = 'col-md-3'>".$relEntrantes[$a][$b]."</div>";
													echo "</div>";
													$cpt = 0;
												}
												
											}
										}
									}
								}
								echo "</div>";
							}
						}
					}
					else {
						header("Refresh: 1; URL=default.php?mot=".$dataToSearchWithFirstUpperCase);
					}
				}
			}

			?>
			<script>
				function putOnTop() {
					document.getElementById('form').style.marginTop = "10px";
				}
				function displayDefinition() {
					var def = document.getElementById('definition');
					if (def.style.display == "inline") {
						def.style.display = "none";
					}
					else {
						def.style.display = "inline";
					}
				}
				function displaySortantes() {
					var sortantes = document.getElementById('relationsSortantes');
					if (sortantes.style.display == "inline") {
						sortantes.style.display = "none";
					}
					else {
						sortantes.style.display = "inline";
					}
				}
				function displayEntrantes() {
					var entrantes = document.getElementById('relationsEntrantes');
					if (entrantes.style.display == "inline") {
						entrantes.style.display = "none";
					}
					else {
						entrantes.style.display = "inline";
					}
				}
			</script>

	</body>
</html>