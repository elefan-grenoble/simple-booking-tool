<?php

$NB_MAX_OF_VOLUNTEER = 5;
$NB_MAX_OF_WORKED_HOURS = 3;
$NB_OF_VOLUNTEER_FIELDS = 3;
$HAS_HEADER = true;
$HAS_LABEL = true;

$csv_file = "csv/novembre.csv";
$csv_file_ressources = "csv/ressources.csv";
$to_email = "creneaux@lelefan.org";

$max_h = "Laisses donc des creneaux pour les autres, c'est ".$NB_MAX_OF_WORKED_HOURS."h max par mois :)";
$max_p = "Oups, il y bien assez de monde, essaie un autre creneau !";

$messages = array();
$messages["succes"] = array();
$messages["error"] = array();
$messages["warning"] = array();

//list all shift
$shifts = array();
$shifts[0] = '6h-7h30';
$shifts[1] = '7h30-10h30';
$shifts[2] = '10h30-13h30';
$shifts[3] = '13h30-16h30';
$shifts[4] = '16h30-19h30';
$shifts[5] = '19h30-21h';

//list all days
$days = array();
$days[0] = 'Ven 3 nov';
$days[1] = 'Sam 4 nov';
$days[2] = 'Ven 10 nov';
$days[3] = 'Sam 11 nov';
$days[4] = 'Jeu 16 nov';
$days[5] = 'Ven 17 nov';
$days[6] = 'Sam 18 nov';
$days[7] = 'Jeu 23 nov';
$days[8] = 'Ven 24 nov';
$days[9] = 'Sam 25 nov';
//$days[10] = 'Jeu 30 nov';
//$days[11] = 'Ven 1 dec';
//$days[12] = 'Sam 2 dec';

//list all shift
$shifts_values = array();
$shifts_values[0] = 1.5;
$shifts_values[1] = 3;
$shifts_values[2] = 3;
$shifts_values[3] = 3;
$shifts_values[4] = 3;
$shifts_values[5] = 1.5;

//jeudi
$blocked = array();
$blocked[4] = array();
$blocked[4][0] = true;
$blocked[4][1] = true;
$blocked[7] = array();
$blocked[7][0] = true;
$blocked[7][1] = true;

function readCSV($csvFile){
    $file_handle = fopen($csvFile, 'r');
    while (!feof($file_handle) ) {
        $line_of_text[] = fgetcsv($file_handle, 1024);
    }
    fclose($file_handle);
    return $line_of_text;
}
function writeCSV($csvFile,$list){
    $fp = fopen($csvFile, 'w');
	foreach ($list as $fields) {
	    fputcsv($fp, $fields);
	}
	fclose($fp);
}

function fixCSV(&$array){
    global $NB_OF_VOLUNTEER_FIELDS,$HAS_LABEL,$days;
    $shifted_col = ($HAS_LABEL) ? 1 : 0;
    $max = count($days)*$NB_OF_VOLUNTEER_FIELDS + $shifted_col;
    foreach ($array as $index => $line){
        for ($i=0;$i<$max;$i++){
            if (!isset($array[$index][$i]))
                $array[$index][$i] = '';
        }
        ksort($array[$index]);
    }
}

function singleLevelArray(&$element){
    if (is_array($element) && count($element)==1)
        $element = trim($element[0]);
}
        
$ressources = readCSV($csv_file_ressources);
array_walk($ressources,'singleLevelArray');

function isRessource($email){
    global $ressources;
    return in_array($email,$ressources);
}

class Volunteer {
    protected $_firstname;
    protected $_lastname;
    protected $_email;

    public function __construct($firstname,$lastname,$email)
    {
        $this->_firstname = $firstname;
        $this->_lastname = $lastname;
        $this->_email = trim($email);
    }

    public function getFirstname(){
        return $this->_firstname;
    }
    public function getLastname(){
        return $this->_lastname;
    }
    public function getEmail(){
        return $this->_email;
    }
    public function __toString()
    {
        if (!isRessource($this->getEmail()))
            return $this->getFirstname() . '&nbsp;' . strtoupper(substr($this->getLastname(),0,1));
        else
            return '<i><b>'.$this->getFirstname() . '&nbsp;' . strtoupper(substr($this->getLastname(),0,1)).' (R)</b></i>';
    }
}

function countVolunteers($booked,$index,$index_j){
    global $NB_MAX_OF_VOLUNTEER,$NB_OF_VOLUNTEER_FIELDS,$HAS_HEADER,$HAS_LABEL;
    $nb = 0;
    $shifted_row = ($HAS_HEADER) ? 1 : 0;
    $shifted_col = ($HAS_LABEL) ? 1 : 0;
    for($i=0;$i<$NB_MAX_OF_VOLUNTEER;$i++){
        if (filter_var($booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+2+$shifted_col], FILTER_VALIDATE_EMAIL)) {
            $nb++;
        }
    }
    return $nb;
}
function getScheduledHours($booked,$email){
    global $days,$shifts,$shifts_values;
    global $NB_MAX_OF_VOLUNTEER,$NB_OF_VOLUNTEER_FIELDS,$HAS_HEADER,$HAS_LABEL;
    $nb = 0;
    $shifted_row = ($HAS_HEADER) ? 1 : 0;
    $shifted_col = ($HAS_LABEL) ? 1 : 0;
    foreach ($days as $index_j => $day){
        foreach ($shifts as $index => $shift) {
            for ($i = 0; $i < $NB_MAX_OF_VOLUNTEER; $i++) {
                if (($booked[$index * $NB_MAX_OF_VOLUNTEER + $i + $shifted_row][$index_j * $NB_OF_VOLUNTEER_FIELDS + 2 + $shifted_col]) == $email) {
                    $nb += $shifts_values[$index];
                }
            }
        }
    }
    return $nb;
}

function getVolunteers($booked,$index,$index_j){
    global $NB_MAX_OF_VOLUNTEER,$NB_OF_VOLUNTEER_FIELDS,$HAS_HEADER,$HAS_LABEL;
    $volunteers = array();
    $shifted_row = ($HAS_HEADER) ? 1 : 0;
    $shifted_col = ($HAS_LABEL) ? 1 : 0;
    for($i=0;$i<$NB_MAX_OF_VOLUNTEER;$i++){
        if (filter_var($booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+2+$shifted_col], FILTER_VALIDATE_EMAIL)) {
            $volunteers[] = new Volunteer(strtolower($booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+$shifted_col]),strtolower($booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+1+$shifted_col]),strtolower($booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+2+$shifted_col]));
        }
    }
    return $volunteers;
}
function addVolunteer(&$booked,$index,$index_j,$volunteer){
    global $NB_MAX_OF_VOLUNTEER,$NB_OF_VOLUNTEER_FIELDS,$HAS_HEADER,$HAS_LABEL;
    $shifted_row = ($HAS_HEADER) ? 1 : 0;
    $shifted_col = ($HAS_LABEL) ? 1 : 0;
    for($i=0;$i<$NB_MAX_OF_VOLUNTEER;$i++){
        if (!filter_var($booked[$index*$NB_MAX_OF_VOLUNTEER+$shifted_row+$i][$index_j*$NB_OF_VOLUNTEER_FIELDS+2+$shifted_col], FILTER_VALIDATE_EMAIL)) {
            $booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+0+$shifted_col] = $volunteer->getFirstname();
            $booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+1+$shifted_col] = $volunteer->getLastname();
            $booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+2+$shifted_col] = $volunteer->getEmail();
            $i = $NB_MAX_OF_VOLUNTEER; //exit
        }
    }
}
function removeVolunteer(&$booked,$index,$index_j,$email){
    global $NB_MAX_OF_VOLUNTEER,$NB_OF_VOLUNTEER_FIELDS,$HAS_HEADER,$HAS_LABEL;
    $shifted_row = ($HAS_HEADER) ? 1 : 0;
    $shifted_col = ($HAS_LABEL) ? 1 : 0;
    for($i=0;$i<$NB_MAX_OF_VOLUNTEER;$i++){
        $current_email = $booked[$index*$NB_MAX_OF_VOLUNTEER+$shifted_row+$i][$index_j*$NB_OF_VOLUNTEER_FIELDS+2+$shifted_col];
        if ((filter_var($current_email, FILTER_VALIDATE_EMAIL))
        && ($email == $current_email)){
            $booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+0+$shifted_col] = '';
            $booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+1+$shifted_col] = '';
            $booked[$index*$NB_MAX_OF_VOLUNTEER+$i+$shifted_row][$index_j*$NB_OF_VOLUNTEER_FIELDS+2+$shifted_col] = '';
            $i = $NB_MAX_OF_VOLUNTEER; //exit
        }
    }
}

if (!file_exists($csv_file)){ //create empty
    $fp = fopen($csv_file, 'w');
    $data = array();
    $data[] = '';
    foreach ($days as $day){
        $data[] = $day;
        for ($i = 1; $i < $NB_OF_VOLUNTEER_FIELDS; $i++)
            $data[] = '';
    }
    fputcsv($fp, $data); //head
    foreach ($shifts as $creneau) {
        $data = array();
        $data[] = $creneau; //first column
        foreach ($days as $day){
            for ($i = 0; $i < $NB_OF_VOLUNTEER_FIELDS; $i++)
                $data[] = '';
        }
        for ($i =0 ; $i < $NB_MAX_OF_VOLUNTEER; $i++)
            fputcsv($fp, $data);
    }
    fclose($fp);
}
if ($_POST && isset($_POST["remove"]) && $_POST["remove"]) {
    if (isset($_POST["email"])&&$_POST["email"]){
        $booked = readCSV($csv_file);
        if (isset($_POST['creneau'])&&isset($_POST['jour'])){
            if (!isRessource($_POST["email"])){
                removeVolunteer($booked,$_POST['creneau'],$_POST['jour'],$_POST["email"]);
                fixCSV($booked);
                writeCSV($csv_file,$booked);
                $subject = '[ONLINE FORM] nouveau creneau supprimé';
                $message = $_POST["lastname"].' '.$_POST["firstname"].' // '.$_POST["email"].' '.$_POST['remove'];
                $headers = 'From: creneaux@lelefan.org' . "\r\n" .
                    'Reply-To: '. $_POST["email"] . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
                mail($to_email, $subject, $message, $headers);
                //success, redirect same file
                header('Location: '.$_SERVER['PHP_SELF']."?success=remove");
                exit;
            }else{
                $messages['error'][] = "<i class=\"large material-icons\">warning</i> Tu es bénévole ressource ! Merci d'envoyer un mail à creneaux@lelefan.org pour te désincrire.</a> :)";
            }
        }else{
            $messages['error'][] = "formulaire incomplet";
        }
    }
}elseif ($_POST && isset($_POST["ok"]) && $_POST["ok"]){
	if (isset($_POST["lastname"])&&$_POST["lastname"]&&isset($_POST["firstname"])&&$_POST["firstname"]){
		if (isset($_POST["email"])&&$_POST["email"]){
			if (isset($_POST["raison"])){
			    $subject = '[ONLINE FORM] Impossible de reserver un creneau';
			    $message = $_POST["lastname"].' '.$_POST["firstname"].' '.$_POST["email"].' '.$_POST["raison"].' '.$_POST["message"];
			    $headers = 'From: creneaux@lelefan.org' . "\r\n" .
			     'Reply-To: '. $_POST["email"] . "\r\n" .
			     'X-Mailer: PHP/' . phpversion();
			    mail($to_email, $subject, $message, $headers);
				header('Location: '.$_SERVER['PHP_SELF']."?success=true");
				exit;
			}else{
                $booked = readCSV($csv_file);
				if (getScheduledHours($booked,$_POST["email"])<$NB_MAX_OF_WORKED_HOURS){
					if (isset($_POST['creneau'])&&isset($_POST['jour'])){
						if (!isRessource($_POST["email"])){
                            $countV = countVolunteers($booked,$_POST['creneau'],$_POST['jour']);
                            if ($countV<$NB_MAX_OF_VOLUNTEER){
                                $volunteer = new Volunteer($_POST["firstname"],$_POST["lastname"],$_POST["email"]);
                                addVolunteer($booked,$_POST['creneau'],$_POST['jour'],$volunteer);
                                fixCSV($booked);
                                writeCSV($csv_file,$booked);
                                $subject = '[ONLINE FORM] nouveau creneau reservé';
                                $message = $_POST["lastname"].' '.$_POST["firstname"].' // '.$_POST["email"].' '.$_POST['ok'];
                                $headers = 'From: creneaux@lelefan.org' . "\r\n" .
                                    'Reply-To: '. $_POST["email"] . "\r\n" .
                                    'X-Mailer: PHP/' . phpversion();
                                mail($to_email, $subject, $message, $headers);
                                //success, redirect same file
                                header('Location: '.$_SERVER['PHP_SELF']."?success=true");
                                exit;
                            }else{
                                $messages['error'][] = $max_p;
                            }
                        }else{
                            $messages['warning'][] = "<i class=\"large material-icons\">favorite</i> Tu es bénévole ressource ! Afin de combler au mieux l'intégralité des creneaux avec au moins un volontaire comme toi, merci de remplir le <a href=\"https://framadate.org/bj92OEFnmo5VrN3R\">framadate</a> :)";
                        }
					 }else{
					 	$messages['error'][] = "formulaire incomplet";
					 }
				}else{
					$messages['error'][] = $max_h;
				}
			}
		}else{
			$messages['error'][] = "Ton email est necessaire pour pouvoir te recontacter ;)";
		}
	}else{
		$messages['error'][] = "Merci de bien spécifier ton nom et prénom :)";
	}
}
if (isset($_GET['success'])&&$_GET['success']=='true'){
	$messages['success'][] = "Merci, ta demande a bien été prise en compte. <br/>A bientôt dans ton épicerie";
}
if (isset($_GET['success'])&&$_GET['success']=='remove'){
	$messages['success'][] = "Merci, la suppression a bien été prise en compte. <br/>Tu peux choisir un autre créneau. <br/>A bientôt dans ton épicerie";
}

//colors / nb of booking
$colors = array();
$colors[0] = 'red lighten-4';
$colors[1] = 'amber lighten-3';
$colors[2] = 'yellow lighten-4';
$colors[3] = 'lime lighten-2';
$colors[4] = 'light-green lighten-3';
$colors[5] = 'green lighten-4';

//$boder_color = array('lightblue','lightgreen','yellow','Violet');
$boder_color = array('#e3f2fd','#bbdefb','#90caf9','#64b5f6','#42a5f5','#2196f3','#1e88e5','#1976d2','#1565c0','#0d47a1');

$booked = readCSV($csv_file);

?><!DOCTYPE html>
  <html>
    <head>
		<!--Import Google Icon Font-->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<!--Import materialize.css-->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
		<title>L'éléfàn Gestion des Creneaux</title>
		<!--Let browser know website is optimized for mobile-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body>
    	<?php foreach ($messages["success"] as $value) { ?>
    		<div class="card-panel green lighten-2"><?php echo $value ?></div>
    	<?php } ?>
    	<?php foreach ($messages["error"] as $value) { ?>
    		<div class="card-panel red lighten-2"><?php echo $value ?></div>
    	<?php } ?>
    	<?php foreach ($messages["warning"] as $value) { ?>
    		<div class="card-panel orange lighten-2"><?php echo $value ?></div>
    	<?php } ?>
    	<div class="container">
    		<div class="section">
      			<div class="row">
      				<h2>
				    	Gestion du bénévolat
				    </h2>
				    <p>
				    	Clique sur une case pour t'inscrire sur le créneau associé.
				    </p>
					<table class="bordered" id="tab"><!--responsive-table-->
						<thead>
							<tr>
								<td>
								</td>
								<?php foreach ($days as $index_j => $day) { ?>
									<td class="center-align"
                                        style="border-left:2px solid <?php echo $boder_color[$index_j%count($boder_color)] ?>" width="<?php echo intval(100/count($days))?>%"><?php echo $day; ?></td>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($shifts as $index => $creneau) { ?>
							<tr>
								<td class="right-align"><b><?php echo $creneau; ?></b></td>
								<?php foreach ($days as $index_j => $day) :
								    if (isset($blocked[$index_j])&&isset($blocked[$index_j][$index])) { ?>
                                        <td class="creneau blocked"
                                            style="border-left:2px solid <?php echo $boder_color[$index_j%count($boder_color)] ?>;background: lightgray;text-align: center;color: gray;">
                                            Epicerie fermée
                                        </td>
                                    <?php } else {
                                        $nb = countVolunteers($booked,$index,$index_j);
                                        $volunteers = getVolunteers($booked,$index,$index_j);
                                        $r = array();
                                        $v = array();
                                        foreach ($volunteers as $volunteer){
                                            if (isRessource($volunteer->getEmail()))
                                                $r[] = $volunteer;
                                            else
                                                $v[] = $volunteer;
                                        }
                                        ?>
                                        <td class="creneau <?php echo $colors[$nb]; ?>"
                                            style="border-left:2px solid <?php echo $boder_color[$index_j%count($boder_color)] ?>"
                                            data-nb="<?php echo count($v); ?>"
                                            data-nb-r="<?php echo count($r); ?>"
                                            data-lig="<?php echo $index; ?>"
                                            data-col="<?php echo $index_j; ?>"
                                            data-fr="je m'inscris pour le <?php echo $day; ?> de <?php echo $creneau; ?>"
                                            data-fr-nok="je me désinscrit du <?php echo $day; ?> de <?php echo $creneau; ?>">
                                            <small>
                                                <?php echo implode('<br>',$r) ?><br>
                                                <?php echo implode('<br>',$v) ?>
                                            </small>
                                        </td>
								    <?php } ?>
								<?php endforeach; ?>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<br>
					<br>
					<a class="btn waves-effect waves-light deep-purple modal-trigger" href="#impossible"><i class="material-icons left">mood_bad</i>Oups, je ne peux pas m'inscrire</a>
					<a class="btn waves-effect waves-light red modal-trigger" href="#desinscription"><i class="material-icons left">warning</i>Désinscription</a>
				</div>
			</div>
		</div>
		


<div id="inscription" class="modal">
    <form  action="" method="POST">
    	<div class="modal-content">
    		<input type="text" name="lastname" placeholder="Nom" />
			<input type="text" name="firstname" placeholder="Prenom" />
			<input type="email" name="email" placeholder="mon@email.fr" />
			<input type="hidden" name="creneau" />
			<input type="hidden" name="jour" />
    	</div>
    	<div class="modal-footer">
			<input type="submit" name="ok" value="M'inscrire" class="btn" />
		</div>
	</form>
</div>
<div id="remove" class="modal">
    <form  action="" method="POST">
        <div class="modal-content">
            <input type="email" name="email" placeholder="mon@email.fr" />
            <input type="hidden" name="creneau" />
            <input type="hidden" name="jour" />
            <p><i>Hum... est-ce que quelqu'un va pouvoir prendre ta place ? ... pas sûr ! <br/> N'abuses pas de cette fonction ...</i></p>
        </div>
        <div class="modal-footer">
            <input type="submit" name="remove" value="M'inscrire" class="btn red" />
        </div>
    </form>
</div>
<div id="desinscription" class="modal">
    <div class="modal-content">
        <div class="show-touch">
            Reste appuyé une seconde sur le créneau où tu souhaites te désinscrire puis entre ton courriel.
        </div>
        <div class="hide-touch">
            Clique avec le botton droit de la souris sur le créneau où tu souhaites te désinscrire puis entre ton courriel.
        </div>
    </div>
</div>
<div id="impossible" class="modal">
    <form  action="" method="POST">
    	<div class="modal-content">
    		<p>
		      <input name="raison" type="radio" id="raison1" value="pas disponible sur les créneaux" />
		      <label for="raison1">Je ne suis pas disponible sur les créneaux qui restent à pourvoir</label>
		    </p>
		    <p>
		      <input name="raison" type="radio" id="raison2" value="aucun créneau disponible" />
		      <label for="raison2">Il ne reste aucun créneau disponible</label>
		    </p>
		    <p>
		      <input name="raison" type="radio" id="raison3" checked="checked" value="problème technique" />
		      <label for="raison3">J'ai un problème technique</label>
		    </p>
    		<input type="text" name="lastname" placeholder="Nom" />
			<input type="text" name="firstname" placeholder="Prenom" />
			<input type="email" name="email" placeholder="mon@email.fr" />
			<div class="input-field col s12">
	          <textarea id="message" class="materialize-textarea" name="message"></textarea>
	          <label for="message">Un message facultatif</label>
	        </div>
    	</div>
    	<div class="modal-footer">
			<input type="submit" name="ok" value="Prévenir" class="btn" />
		</div>
	</form>
</div>

      	<!--Import jQuery before materialize.js-->
      	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
  		<?php if (true ){?>
    	<script type="text/javascript">
            function is_touch_device() {
                return !!('ontouchstart' in window);
            }
    		jQuery(function(){
    			$('.modal').modal();

    			if (is_touch_device()){
    			    $(".show-touch").show();
    			    $(".hide-touch").hide();
                }else{
                    $(".show-touch").hide();
                    $(".hide-touch").show();
                }

    			$("#tab").on("click","tbody td.creneau:not(.blocked)",function(){
    				if ((parseInt($(this).attr("data-nb"))+parseInt($(this).attr("data-nb-r")))<<?php echo $NB_MAX_OF_VOLUNTEER; ?>){
    					$("#inscription").find("input[name=ok]").val($(this).attr("data-fr"));
	    				$("#inscription").find("input[name=creneau]").val($(this).attr("data-lig"));
	    				$("#inscription").find("input[name=jour]").val($(this).attr("data-col"));
	    				$("#inscription").modal('open');
    				}else{
    					Materialize.toast('<?php echo addslashes($max_p); ?>', 3000, 'rounded');
    				}
    				
    			});
                $("#tab").on("contextmenu","tbody td.creneau:not(.blocked)",function(event){
                    event.preventDefault();
                    $("#remove").find("input[name=remove]").val($(this).attr("data-fr-nok"));
                    $("#remove").find("input[name=creneau]").val($(this).attr("data-lig"));
                    $("#remove").find("input[name=jour]").val($(this).attr("data-col"));
                    $("#remove").modal('open');
                });
                $("#tab").on("click","tbody td.creneau.blocked",function(){
                    Materialize.toast('L&rsquo;épicerie est fermée le jeudi matin :)', 3000, 'rounded');
                });
    		});
    	</script>
    	<?php /*}else{ ?>
    	<script type="text/javascript">
    		jQuery(function(){
    			$('.modal').modal();
    			$("#tab").on("click","tbody td.creneau",function(){
    				 Materialize.toast('<?php echo addslashes($max_h); ?>', 3000, 'rounded');
    			});
    		});
    	</script>
    	<?php*/ } ?>
    </body>
  </html>
