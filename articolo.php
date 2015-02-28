<div class="container">
<div class="row">
<a href="index.php">Menù amministrazione</a>
<hr>
<?php
//Prelevo l' azione
$action = @$_GET['action'];

//Azione di default
if(strlen($action) == 0){
	//News per pagina
	$news_per_page = @$_GET['xpage'];
	if(strlen($news_per_page) == 0){
		$news_per_page = 10;
	}
	
	//Pagina di inizio
	if(!IsSet($_GET['start'])){
		$start = 0;
	}else{
		$start = $_GET['start'];
	}
	
	//Costruziona automatica della query per visualizzare gli articoli
	$strSQL = "SELECT intArticoloID, strTitolo, dtmPubblicazione FROM articolo";
	
	//Qui controllo quanti sono i records presenti nella tabella per effettuare la paginazione !
	$result = mysql_query($strSQL);
	$tot_rec = mysql_num_rows($result);
	//Numero pagine
	$num_page = ceil($tot_rec/$news_per_page);
	$current_page = ceil(($start/$news_per_page) + 1);
	//Libero la memoria
	mysql_free_result($result);
	//Fine questione paginazione :-)
	
	$strSQL .= " ORDER BY dtmPubblicazione DESC LIMIT $start, $news_per_page";
	
	//Eseguo la query visualizzo i risultati
	$result = mysql_query($strSQL);
	
	//Link per inserimento articolo
	echo "<a href=\"index.php?page=articolo&action=aggiungi\">Aggiungi Articolo</a><p>";
	
	//Visualizzo tutte le ultime News
	while($row = mysql_fetch_array($result))
	{
		//Prelevo i dati dall' array
		$intArticoloID = $row['intArticoloID'];
		$strTitolo = $row['strTitolo'];
		$dtmPubblicazione = $row['dtmPubblicazione'];
		
		echo "<ul><li><a href=\"index.php?page=articolo&action=aggiungi&id=$intArticoloID\" title=\"Modifica Articolo\">$strTitolo</a>\n</ul>
		      
		      
		
		";
	
	}
	
	//Libero la memoria
	mysql_free_result($result);
	
	//Paginazione
	//Visualizzo i numeri di pagina
	if($num_page > 1)
	{
		echo "<p align=\"center\">Altre pagine:";
		//Ciclo FOR per elencare i numeri pagina
		for($page = 1; $page <= $num_page; $page++){
			if($page == $current_page){
				echo "&nbsp;<b>$page</b>";
			}else{
				echo "&nbsp;<a href=\"index.php?page=articolo&start=".($page - 1) * $news_per_page."\">$page</a>";
			}
		}
	}
}



//Form elimina articolo
if($action == "elimina") { 
    $intArticoloID = @$_GET['id']; 
    if(strlen($intArticoloID) > 0){   
        $strSQL = "DELETE FROM articolo WHERE id = $intArticoloID";
    
	$result = mysql_query($strSQL);
    $row = mysql_fetch_array($result);
	}
	if(mysql_query($strSQL)){
		echo "Articolo aggiunto/modificato con successo !<br>";
		echo "<a href=\"index.php?page=articolo\">Torna alla gestione articoli</a>";
	}
	else{ 
        echo "Errore di cancellazione"; 
}
mysql_free_result($result);
} 

//Form aggiunta/modifica Articolo
if($action == "aggiungi"){
	$intArticoloID = @$_GET['id'];
	//Controllo se prelevare informazioni
	//relative a news da aggiornare...
	if(strlen($intArticoloID) != 0){
		//Query SQL
		$strSQL = "SELECT * FROM articolo"
				. " WHERE intArticoloID = $intArticoloID";
		//Eseguo la query e recupero i dati
		$result = mysql_query($strSQL);
		$row = mysql_fetch_array($result);
		
		//recupero dati...
		$intArticoloSezioneID = $row['intSezioneID'];
		$intArticoloAutoreID = $row['intAutoreID'];
		$strTitolo = $row['strTitolo'];
		$strIntroduzione = $row['strIntroduzione'];
		$strArticolo = $row['strArticolo'];
		
		//Libero la memoria
		mysql_free_result($result);
	}
	?>
	<script language="javascript">
	function CheckForm(form){
		//Avvio il controllo dei campi
		var booReturn = true;
		//strMessage
		var strMessage = "Attenzione, compilare i seguenti campi:\n"
		
		//Controllo
		if(form.strTitolo.value == ""){
			strMessage += "*Titolo\n";
			booReturn = false;
		}
		
		if(form.strIntroduzione.value == ""){
			strMessage += "*Introduzione\n";
			booReturn = false;
		}
		
		if(form.strArticolo.value == ""){
			strMessage += "*Articolo\n";
			booReturn = false;
		}
		
		//Ritorno booReturn
		if(booReturn == false){
			alert (strMessage);
		}
		return booReturn;
	}
	</script>
	<h1>Aggiungi/Modifica Articolo</h1>
	<form method="post" action="index.php?page=articolo&action=aggiungi_2" onsubmit="return CheckForm(this);">
		<input type="hidden" name="intArticoloID" value="<?php echo @$intArticoloID;?>">
		Titolo:&nbsp;<input type="text" name="strTitolo" value="<?php echo @$strTitolo;?>" size="50" maxlength="100"><p>
		Introduzione:<br><textarea name="strIntroduzione" cols="70" rows="5"><?php echo @$strIntroduzione;?></textarea><p>
		Articolo Completo:<br><textarea name="strArticolo" cols="70" rows="20"><?php echo @$strArticolo;?></textarea><p>
		Sezione:&nbsp;<select name="intSezioneID">
		<?php
		//Recupero tutte le sezioni disponibili
		$strSQL = "SELECT intSezioneID, strNome FROM sezione ORDER BY strNome ASC";
		$result = mysql_query($strSQL);
		
		//Popolo la select
		while($row = mysql_fetch_array($result)){
			$intSezioneID = $row['intSezioneID'];
			$strNome = $row['strNome'];
			//Mantengo la sezione originaria
			if($intSezioneID == $intArticoloSezioneID){
				echo "<option value=\"$intSezioneID\" SELECTED>$strNome</option>\n";
			}else{
				echo "<option value=\"$intSezioneID\">$strNome</option>\n";
			}
		}
		
		//Libero la memoria
		mysql_free_result($result);
		?>
		</select><p>
		Autore:&nbsp;<select name="intAutoreID">
		<?php
		//Recupero tutte gli autori disponibili
		$strSQL = "SELECT intAutoreID, strNome, strCognome FROM autore ORDER BY strCognome, strNome ASC";
		$result = mysql_query($strSQL);
		
		//Popolo la select
		while($row = mysql_fetch_array($result)){
			$intAutoreID = $row['intAutoreID'];
			$strNome = $row['strNome'];
			$strCognome = $row['strCognome'];
			//Mantengo l'autore originario
			if($intAutoreID == $intArticoloAutoreID){
				echo "<option value=\"$intAutoreID\" SELECTED>$strCognome $strNome</option>\n";
			}else{
				echo "<option value=\"$intAutoreID\">$strCognome $strNome</option>\n";
			}
		}
		
		//Libero la memoria
		mysql_free_result($result);
		?>
		</select><p>
		<input type="submit" value="Inserisci Articolo">&nbsp;<input type="reset" value="Annulla Modifiche"> 
		<?php echo
		"<a href=\"index.php?page=articolo&action=elimina&id=$intArticoloID\">
		Elimina
		</a>\n"
		?>
	</form>
	<?php
}

if($action == "aggiungi_2"){
	//Recupero tutti dati
	$intArticoloID = $_POST['intArticoloID'];
	$intSezioneID = $_POST['intSezioneID'];
	$intAutoreID = $_POST['intAutoreID'];
	$strTitolo = $_POST['strTitolo'];
	$strIntroduzione = $_POST['strIntroduzione'];
	$strArticolo = $_POST['strArticolo'];
	$dtmPubblicazione = time();
	
	//controllo se aggiornare o aggiungere l'articolo
	if(strlen($intArticoloID) == 0){ //Aggiungo l'articolo
		$strSQL = "INSERT INTO articolo ("
				. " intSezioneID,"
				. " intAutoreID,"
				. " strTitolo,"
				. " strIntroduzione,"
				. " strArticolo,"
				. " dtmPubblicazione) VALUES("
				. " $intSezioneID,"
				. " $intAutoreID,"
				. " '$strTitolo',"
				. " '$strIntroduzione', "
				. " '$strArticolo', "
				. " $dtmPubblicazione)";
	}else{ //Modifico l'articolo
		$strSQL = "UPDATE articolo SET"
				. " intSezioneID = $intSezioneID,"
				. " intAutoreID = $intAutoreID, "
				. " strTitolo = '$strTitolo', "
				. " strIntroduzione = '$strIntroduzione', "
				. " strArticolo = '$strArticolo'"
				. " WHERE intArticoloID = $intArticoloID";
	}
	
	
	//Eseguo la query SQL
	if(mysql_query($strSQL)){
		echo "Articolo aggiunto/modificato con successo !<br>";
		echo "<a href=\"index.php?page=articolo\">Torna alla gestione articoli</a>";
	}else{
		echo "Errori riscontrati durante l'inserimento<br>";
		echo "Errore: ".mysql_error();
	}
}
?>
</div></div>
