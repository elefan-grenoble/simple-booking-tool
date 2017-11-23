<?php
$ADMIN = false;
if (md5($_COOKIE["babar"]) == 'd82d5af0d6f21d63ecac7799d88ea864')
    $ADMIN = true;
if ($ADMIN){
    $_SESSION['messages']['warning'][] = 'YOU ARE ADMIN';
}

$NB_MAX_OF_VOLUNTEER = array(7,2);
$NB_OF_BOOKABLE_ROOM = array(4,1);
if ($ADMIN) //admin can fill everithing
    $NB_OF_BOOKABLE_ROOM = $NB_MAX_OF_VOLUNTEER;
$NB_MAX_OF_WORKED_HOURS = 3;
$NB_OF_VOLUNTEER_FIELDS = 3;
$HAS_HEADER = true;
$HAS_LABEL = true;
$FRAMAURL = "https://framadate.org/bj92OEFnmo5VrN3R"; //pour les ressources
$FRAMAURL = "#"; //pour les ressources

$PERIOD_NAME = "mois de decembre";
$csv_file = "csv/decembre.csv";
$csv_file_ressources = "csv/ressources.csv";
$to_email = "creneaux@lelefan.org";

$max_h = "Laisses donc des creneaux pour les autres, c'est ".$NB_MAX_OF_WORKED_HOURS."h max par mois :)";
$max_p = "Oups, il y bien assez de monde, essaie un autre creneau !";

$messages = array();
$messages["succes"] = array();
$messages["error"] = array();
$messages["warning"] = array();

$JOB_TYPES = array();
$JOB_TYPES[0] = "Epicerie";
$JOB_TYPES[1] = "Accueil - Bureau des membres";
$NB_OF_JOB_TYPE = count($JOB_TYPES);

//list all shift
$SHIFTS = array();
$SHIFTS[0] = array();
$SHIFTS[0][0] = '6h-7h30';
$SHIFTS[0][1] = '7h30-10h30';
$SHIFTS[0][2] = '10h30-13h30';
$SHIFTS[0][3] = '13h30-16h30';
$SHIFTS[0][4] = '16h30-19h30';
$SHIFTS[0][5] = '19h30-21h';
$SHIFTS[1] = array();
$SHIFTS[1][0] = '9h-12h';
$SHIFTS[1][1] = '12h-13h30';
$SHIFTS[1][2] = '13h30-16h30';
$SHIFTS[1][3] = '16h30-19h30';

//list all days
$DAYS = array();
$DAYS[0] = 'Jeu 30 nov';
$DAYS[1] = 'Ven 1 dec';
$DAYS[2] = 'Sam 2 dec';
$DAYS[3] = 'Jeu 7 dec';
$DAYS[4] = 'Ven 8 dec';
$DAYS[5] = 'Sam 9 dec';
$DAYS[6] = 'Jeu 14 dec';
$DAYS[7] = 'Ven 15 dec';
$DAYS[8] = 'Sam 16 dec';
$DAYS[9] = 'Jeu 21 dec';
$DAYS[10] = 'Ven 22 dec';
$DAYS[11] = 'Sam 23 dec';

//list all shift
$SHIFTS_VALUES = array();
$SHIFTS_VALUES[0] = array();
$SHIFTS_VALUES[0][0] = 1.5;
$SHIFTS_VALUES[0][1] = 3;
$SHIFTS_VALUES[0][2] = 3;
$SHIFTS_VALUES[0][3] = 3;
$SHIFTS_VALUES[0][4] = 3;
$SHIFTS_VALUES[0][5] = 1.5;
$SHIFTS_VALUES[1] = array();
$SHIFTS_VALUES[1][0] = 3;
$SHIFTS_VALUES[1][1] = 1.5;
$SHIFTS_VALUES[1][2] = 3;
$SHIFTS_VALUES[1][3] = 3;

$SCHEDULED_HOURS=array();

//jeudi
$BLOCKED = array();
$BLOCKED[0] = array();
$BLOCKED[0][0] = array();
$BLOCKED[0][0][0] = true;
$BLOCKED[0][0][1] = true;
$BLOCKED[0][3] = array();
$BLOCKED[0][3][0] = true;
$BLOCKED[0][3][1] = true;
$BLOCKED[0][6] = array();
$BLOCKED[0][6][0] = true;
$BLOCKED[0][6][1] = true;
$BLOCKED[0][9] = array();
$BLOCKED[0][9][0] = true;
$BLOCKED[0][9][1] = true;
$BLOCKED[1] = array();
$BLOCKED[1][0] = array();
$BLOCKED[1][0][0] = true;
$BLOCKED[1][3] = array();
$BLOCKED[1][3][0] = true;
$BLOCKED[1][6] = array();
$BLOCKED[1][6][0] = true;
$BLOCKED[1][9] = array();
$BLOCKED[1][9][0] = true;

//colors / nb of booking
$COLORS = array();
$COLORS[0] = array();
$COLORS[0][0] = 'free';
$COLORS[0][1] = 'amber lighten-3';
$COLORS[0][2] = 'yellow lighten-4';
$COLORS[0][3] = 'lime lighten-2';
$COLORS[0][4] = 'light-green lighten-3';
$COLORS[0][5] = 'green lighten-4';
$COLORS[1] = array();
$COLORS[1][0] = 'free';
$COLORS[1][1] = 'lime lighten-2';
$COLORS[1][2] = 'green lighten-4';

function readCSV($csvFile){
    $file_handle = fopen($csvFile, 'r');
    $line_of_text = array();
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
//    global $NB_OF_VOLUNTEER_FIELDS,$HAS_LABEL,$DAYS;
//    $shifted_col = ($HAS_LABEL) ? 1 : 0;
//    $max = count($DAYS)*$NB_OF_VOLUNTEER_FIELDS + $shifted_col;
//    foreach ($array as $index => $line){
//        for ($i=0;$i<$max;$i++){
//            if (!isset($array[$index][$i]))
//                $array[$index][$i] = '';
//        }
//        ksort($array[$index]);
//    }
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
        $this->_firstname = strtolower($firstname);
        $this->_lastname = strtolower($lastname);
        $this->_email = strtolower(trim($email));
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

function getIndexes($job_type,$index,$index_j){
    global $HAS_HEADER,$HAS_LABEL,$SHIFTS,$NB_MAX_OF_VOLUNTEER,$NB_OF_VOLUNTEER_FIELDS;
    $shifted_row = ($HAS_HEADER) ? 1 : 0;
    $shifted_col = ($HAS_LABEL) ? 1 : 0;

    $line_index = $shifted_row;
    for ($job = 0; $job < $job_type; $job++) {
        $line_index += count($SHIFTS[$job])*$NB_MAX_OF_VOLUNTEER[$job]+ $shifted_row;
    }
    $line_index += $index*$NB_MAX_OF_VOLUNTEER[$job_type];
    $column_index = $index_j*$NB_OF_VOLUNTEER_FIELDS+$shifted_col;

    return array($line_index,$column_index);
}

function countVolunteers($booked,$job_type,$index,$index_j){
    global $NB_MAX_OF_VOLUNTEER;
    $nb = 0;
    list($line_index ,$column_index) = getIndexes($job_type,$index,$index_j);
    for($i=0;$i<$NB_MAX_OF_VOLUNTEER[$job_type];$i++){ //for all lines in this shift
        if (filter_var($booked[$line_index+$i][$column_index+2], FILTER_VALIDATE_EMAIL)) { // if email is valid
            $nb++;
        }
    }
    return $nb;
}
function getScheduledHours($booked,$email){
    global $DAYS,$SHIFTS,$SHIFTS_VALUES;
    global $NB_MAX_OF_VOLUNTEER,$NB_OF_JOB_TYPE,$HAS_HEADER,$HAS_LABEL;
    if (!isset($_SESSION['scheduled_hours'][$email])){
        foreach ($DAYS as $index_j => $day){
            for ($j = 0; $j < $NB_OF_JOB_TYPE; $j++) {
                foreach ($SHIFTS[$j] as $index => $shift) {
                    for ($i = 0; $i < $NB_MAX_OF_VOLUNTEER[$j]; $i++) {
                        list($line_index ,$column_index) = getIndexes($j,$index,$index_j);
                        if ((strtolower($booked[$line_index + $i ][$column_index + 2 ])) == strtolower($email)) {
                            $_SESSION['scheduled_hours'][$email] += $SHIFTS_VALUES[$i][$index];
                        }
                    }
                }
            }
        }
    }
    return $_SESSION['scheduled_hours'][$email];
}

function getVolunteers($booked,$job_type,$index,$index_j){
    global $NB_MAX_OF_VOLUNTEER;
    $volunteers = array();

    list($line_index ,$column_index) = getIndexes($job_type,$index,$index_j);

    for($i=0;$i<$NB_MAX_OF_VOLUNTEER[$job_type];$i++){
        if (filter_var($booked[$line_index+$i][$column_index+2], FILTER_VALIDATE_EMAIL)) {
            $volunteers[] = new Volunteer(
                    strtolower($booked[$line_index+$i][$column_index]),
                    strtolower($booked[$line_index+$i][$column_index+1]),
                    strtolower($booked[$line_index+$i][$column_index+2])
            );
        }
    }
    return $volunteers;
}
function addVolunteer(&$booked,$job_type,$index,$index_j,$volunteer){
    global $NB_MAX_OF_VOLUNTEER,$SHIFTS_VALUES,$SCHEDULED_HOURS;

    list($line_index ,$column_index) = getIndexes($job_type,$index,$index_j);

    for($i=0;$i<$NB_MAX_OF_VOLUNTEER[$job_type];$i++){
        if (!filter_var($booked[$line_index+$i][$column_index+2], FILTER_VALIDATE_EMAIL)) { //email is not valid == room is available
            $booked[$line_index+$i][$column_index+0] = $volunteer->getFirstname();
            $booked[$line_index+$i][$column_index+1] = $volunteer->getLastname();
            $booked[$line_index+$i][$column_index+2] = strtolower($volunteer->getEmail());
            $_SESSION['scheduled_hours'][$volunteer->getEmail()] += $SHIFTS_VALUES[$job_type][$index];
            return true;
        }elseif ($booked[$line_index+$i][$column_index+2] == $volunteer->getEmail()){ //oups already subscribe here
            $_SESSION['messages']['error'][] = "Oups, tu es déjà inscrit sur ce créneau ! Je sais que tu es super, mais de là à tenir deux postes en parallèle, quand même... :)";
            return false;
        }
    }
    return true;
}
function removeVolunteer(&$booked,$job_type,$index,$index_j,$email){
    global $NB_MAX_OF_VOLUNTEER;

    $returnValue = false;
    list($line_index ,$column_index) = getIndexes($job_type,$index,$index_j);

    for($i=0;$i<$NB_MAX_OF_VOLUNTEER[$job_type]&&!$returnValue;$i++){
        $current_email = $booked[$line_index+$i][$column_index+2];
        if ((filter_var($current_email, FILTER_VALIDATE_EMAIL)) //room is not empty and emails matche
        && (strtolower($email) == strtolower($current_email))){
            $booked[$line_index][$column_index+0] = '';
            $booked[$line_index][$column_index+1] = '';
            $booked[$line_index][$column_index+2] = '';
            $returnValue = true; //exit
        }
    }
    return $returnValue;
}

if (!file_exists($csv_file)) { //create empty
    $fp = fopen($csv_file, 'w');
    for ($job = 0; $job < $NB_OF_JOB_TYPE; $job++) {
        $head = array();
        $head[] = $JOB_TYPES[$job];
        foreach ($DAYS as $day) {
            $head[] = $day;
            for ($d = 1; $d < $NB_OF_VOLUNTEER_FIELDS; $d++)
                $head[] = '';
        }
        fputcsv($fp, $head); //head
        foreach ($SHIFTS[$job] as $shift) {
            $data = array();
            $data[] = $shift; //first column
            foreach ($DAYS as $day) {
                for ($vf = 0; $vf < $NB_OF_VOLUNTEER_FIELDS; $vf++)
                    $data[] = '';
            }
            for ($i = 0; $i < $NB_MAX_OF_VOLUNTEER[$job]; $i++)
                fputcsv($fp, $data);
        }
    }
    fclose($fp);
}
if ($_POST && isset($_POST["remove"]) && $_POST["remove"]) {
    if (isset($_POST["email"])&&$_POST["email"]){
        $booked = readCSV($csv_file);
        if (isset($_POST['creneau'])&&isset($_POST['jour'])){
            if (!isRessource($_POST["email"]) || $ADMIN){
                $remove = removeVolunteer($booked,$_POST['job_type'],$_POST['creneau'],$_POST['jour'],$_POST["email"]);
                fixCSV($booked);
                writeCSV($csv_file,$booked);
                if ($remove){
                    $subject = '[ONLINE FORM] nouveau creneau supprimé';
                    $message = $_POST["lastname"].' '.$_POST["firstname"].' // '.$_POST["email"].' '.$_POST['remove'];
                    $headers = 'From: creneaux@lelefan.org' . "\r\n" .
                        'Reply-To: '. $_POST["email"] . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                    mail($to_email, $subject, $message, $headers);
                    //todo mail the user too
                    //success, redirect same file
                    $_SESSION['messages']['success'][] =  "Merci, la suppression a bien été prise en compte. <br/>Tu peux choisir un autre créneau. <br/>A bientôt dans ton épicerie";
                }else{
                    $_SESSION['messages']['error'][] = "Oups, l'opération a mal tourné...";
                }
            }else{
                $_SESSION['messages']['error'][] = "<i class=\"large material-icons\">warning</i> Tu es bénévole ressource ! Merci d'envoyer un mail à creneaux@lelefan.org pour te désincrire.</a> :)";
            }
        }else{
            $_SESSION['messages']['error'][] = "formulaire incomplet";
        }
    }
}
elseif ($_POST && isset($_POST["ok"]) && $_POST["ok"]){
	if (isset($_POST["lastname"])&&$_POST["lastname"]&&isset($_POST["firstname"])&&$_POST["firstname"]){
		if (isset($_POST["email"])&&$_POST["email"]){
			if (isset($_POST["raison"])){
			    $subject = '[ONLINE FORM] Impossible de reserver un creneau';
			    $message = $_POST["lastname"].' '.$_POST["firstname"].' '.$_POST["email"].' '.$_POST["raison"].' '.$_POST["message"];
			    $headers = 'From: creneaux@lelefan.org' . "\r\n" .
			     'Reply-To: '. $_POST["email"] . "\r\n" .
			     'X-Mailer: PHP/' . phpversion();
			    mail($to_email, $subject, $message, $headers);
                $_SESSION['messages']['success'][] = "Merci, ta demande a bien été prise en compte. <br/>A bientôt dans ton épicerie";
			}else{
                $booked = readCSV($csv_file);
				if ((getScheduledHours($booked,$_POST["email"])<$NB_MAX_OF_WORKED_HOURS) || $ADMIN){
					if (isset($_POST['creneau'])&&isset($_POST['jour'])){
						if (!isRessource($_POST["email"]) || $ADMIN){
                            $countV = countVolunteers($booked,$_POST['job_type'],$_POST['creneau'],$_POST['jour']);
                            if ($countV<$NB_MAX_OF_VOLUNTEER[$_POST['job_type']]){
                                $volunteer = new Volunteer($_POST["firstname"],$_POST["lastname"],$_POST["email"]);
                                if (addVolunteer($booked,$_POST['job_type'],$_POST['creneau'],$_POST['jour'],$volunteer)){
                                    fixCSV($booked);
                                    writeCSV($csv_file,$booked);
                                    $subject = '[ONLINE FORM] nouveau creneau reservé';
                                    $message = $_POST["lastname"].' '.$_POST["firstname"].' // '.$_POST["email"].' '.$_POST['ok'];
                                    $headers = 'From: creneaux@lelefan.org' . "\r\n" .
                                        'Reply-To: '. $_POST["email"] . "\r\n" .
                                        'X-Mailer: PHP/' . phpversion();
                                    mail($to_email, $subject, $message, $headers);
                                    //success, redirect same file
                                    $_SESSION['messages']['success'][] = "Merci, ta demande a bien été prise en compte. <br/>A bientôt dans ton épicerie";
                                    if (getScheduledHours($booked,$_POST["email"])<$NB_MAX_OF_WORKED_HOURS) { // some more to do
                                        $_SESSION['messages']['warning'][] = "Il te restera ".($NB_MAX_OF_WORKED_HOURS-getScheduledHours($booked,$_POST["email"]))."h à choisir pour combler ce cycle";
                                    }
                                }
                            }else{
                                $_SESSION['messages']['error'][] = $max_p;
                            }
                        }else{
                            $_SESSION['messages']['warning'][] = "<i class=\"large material-icons\">favorite</i> Tu es bénévole ressource ! Afin de combler au mieux l'intégralité des creneaux avec au moins un volontaire comme toi, merci de remplir le <a href=\"".$FRAMAURL."\">framadate</a> :)";
                        }
					 }else{
                        $_SESSION['messages']['error'][] = "formulaire incomplet";
					 }
				}else{
                    $_SESSION['messages']['error'][] = $max_h;
				}
			}
		}else{
            $_SESSION['messages']['error'][] = "Ton email est necessaire pour pouvoir te recontacter ;)";
		}
	}else{
        $_SESSION['messages']['error'][] = "Merci de bien spécifier ton nom et prénom :)";
	}
}

//$BORDER_COLOR = array('lightblue','lightgreen','yellow','Violet');
$BORDER_COLOR = array('#e3f2fd','#bbdefb','#90caf9','#64b5f6','#42a5f5','#2196f3','#1e88e5','#1976d2','#1565c0','#0d47a1','#1565c0','#1976d2','#1e88e5','#2196f3','#42a5f5','#64b5f6','#90caf9','#bbdefb');

$booked = readCSV($csv_file);

?><!DOCTYPE html>
  <html>
    <head>
		<!--Import Google Icon Font-->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<!--Import materialize.css-->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
		<title>L'éléfàn <?php echo $PERIOD_NAME; ?></title>
		<!--Let browser know website is optimized for mobile-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <style>
            td.free:before{
                content: 'libre';
                text-align: center;
                width: 100%;
                display: block;
                font-style: italic;
                color: lightblue;
                height: 100%;
                margin: auto;
            }
            .job{
                display: none;
            }
            .card-panel{
                color: white;
            }
            .table-container{
                overflow-x: scroll;
                margin-bottom: 20px;
            }
            .modal .title{
                text-align: center;
                text-transform: uppercase;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
    	<div class="container">
    		<div class="section">
                <?php foreach ($_SESSION['messages']["success"] as $value) { ?>
                    <div class="card-panel green lighten-1"><?php echo $value ?></div>
                <?php } ?>
                <?php foreach ($_SESSION['messages']["error"] as $value) { ?>
                    <div class="card-panel red lighten-1"><?php echo $value ?></div>
                <?php } ?>
                <?php foreach ($_SESSION['messages']["warning"] as $value) { ?>
                    <div class="card-panel orange lighten-1"><?php echo $value ?></div>
                <?php } ?>
                <?php unset($_SESSION['messages']["success"]); ?>
                <?php unset($_SESSION['messages']["error"]); ?>
                <?php unset($_SESSION['messages']["warning"]); ?>
      			<div class="row">
      				<h2>
				    	Créneaux <?php echo $PERIOD_NAME; ?>
				    </h2>
                    <div class="input-field col s12">
                        <select id="job_type">
                            <option value="" disabled selected>Choisir un poste</option>
                            <?php for($i=0;$i<$NB_OF_JOB_TYPE;$i++): ?>
                                <option value="job<?php echo $i ?>"><?php echo $JOB_TYPES[$i]; ?></option>
                            <?php endfor; ?>
                        </select>
                        <label for="job_type">A quel poste souhaites tu participer ?</label>
                    </div>
                    <p class="job<?php for($i=0;$i<$NB_OF_JOB_TYPE;$i++): ?> job<?php echo $i; endfor; ?>">
                        Clique sur une case pour t'inscrire sur le créneau associé.
                    </p>
                    <p class="job job0">
                        Il y a <?php echo $NB_OF_BOOKABLE_ROOM[0]; ?> places réservables par créneau.
                    </p>
                    <p class="job job1">
                        Il y a <?php echo $NB_OF_BOOKABLE_ROOM[1]; ?> place réservable par créneau.
                    </p>
                    <?php for ($job=0;$job<$NB_OF_JOB_TYPE;$job++) : ?>
                        <div class="col s12 table-container job job<?php echo $job ?>">
                            <table class="bordered">
                                <thead>
                                <tr>
                                    <td>
                                    </td>
                                    <?php foreach ($DAYS as $index_j => $day) { ?>
                                        <td class="center-align"
                                            style="border-left:2px solid <?php echo $BORDER_COLOR[$index_j%count($BORDER_COLOR)] ?>" width="<?php echo intval(100/count($DAYS))?>%"><?php echo $day; ?></td>
                                    <?php } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($SHIFTS[$job] as $index => $shift) { ?>
                                    <tr>
                                        <td class="right-align"><b><?php echo $shift; ?></b></td>
                                        <?php foreach ($DAYS as $index_j => $day) :
                                            if (isset($BLOCKED[$job][$index_j])&&isset($BLOCKED[$job][$index_j][$index])) { ?>
                                                <td class="creneau blocked"
                                                    style="border-left:2px solid <?php echo $BORDER_COLOR[$index_j%count($BORDER_COLOR)] ?>;background: lightgray;text-align: center;color: gray;">
                                                    Epicerie fermée
                                                </td>
                                            <?php } else {
                                                $nb = countVolunteers($booked,$job,$index,$index_j);
                                                $volunteers = getVolunteers($booked,$job,$index,$index_j);
                                                $r = array();
                                                $v = array();
                                                foreach ($volunteers as $volunteer){
                                                    if (isRessource($volunteer->getEmail()))
                                                        $r[] = $volunteer;
                                                    else
                                                        $v[] = $volunteer;
                                                }
                                                ?>
                                                <td class="creneau <?php echo $COLORS[$job][$nb]; ?>"
                                                    style="border-left:2px solid <?php echo $BORDER_COLOR[$index_j%count($BORDER_COLOR)] ?>"
                                                    data-nb="<?php echo count($v); ?>"
                                                    data-nb-r="<?php echo count($r); ?>"
                                                    data-lig="<?php echo $index; ?>"
                                                    data-col="<?php echo $index_j; ?>"
                                                    data-job="<?php echo $job; ?>"
                                                    data-fr="je m'inscris pour le <?php echo $day; ?> de <?php echo $shift; ?>"
                                                    data-fr-nok="je me désinscris du <?php echo $day; ?> de <?php echo $shift; ?>">
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
                        </div>
                    <?php endfor; ?>
					<a class="btn waves-effect waves-light deep-purple modal-trigger" href="#impossible"><i class="material-icons left">mood_bad</i>Oups, je ne peux pas m'inscrire</a>
					<a class="btn waves-effect waves-light red modal-trigger" href="#desinscription"><i class="material-icons left">warning</i>Désinscription</a>
				</div>
			</div>
		</div>
		


<div id="inscription" class="modal">
    <form  action="" method="POST">
    	<div class="modal-content">
            <div class="title"></div>
            <div class="row">
                <div class="input-field col s6 m12">
                    <input id="firstname" type="text" name="firstname" class="validate">
                    <label for="firstname">Ton prénom</label>
                </div>
                <div class="input-field col s6 m12">
                    <input id="lastname" type="text" name="lastname" class="validate">
                    <label for="lastname">Ton nom</label>
                </div>
                <div class="input-field col s12">
                    <input id="email" type="email" name="email" class="validate">
                    <label for="email" data-error="Mauvais format" data-success="">Ton courriel</label>
                </div>
            </div>
            <button type="submit" class="btn ok hide show-on-small">Je m'inscris</button>
			<input type="hidden" name="creneau" />
			<input type="hidden" name="jour" />
			<input type="hidden" name="job_type" />
			<input type="hidden" name="ok" />
    	</div>
    	<div class="modal-footer">
            <button type="submit" class="btn ok hide-on-small-only">Je m'inscris</button>
		</div>
	</form>
</div>
<div id="remove" class="modal">
    <form  action="" method="POST">
        <div class="modal-content">
            <div class="title"></div>
            <input type="email" name="email" placeholder="mon@email.fr" />
            <input type="hidden" name="creneau" />
            <input type="hidden" name="jour" />
            <input type="hidden" name="job_type" />
            <input type="hidden" name="remove" />
            <p><i>Hum... est-ce que quelqu'un va pouvoir prendre ta place ? ... pas sûr ! <br/> N'abuses pas de cette fonction ...</i></p>
        </div>
        <div class="modal-footer">
            <button type="submit" class="remove btn red">Je me désinscris</button>
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
            $(document).ready(function() {
                $('select').material_select();
            });
    		jQuery(function(){
    			if (is_touch_device()){
    			    $(".show-touch").show();
    			    $(".hide-touch").hide();
    			    $(".modal").addClass("bottom-sheet").modal();
    			    console.log("touch_device detected");
                }else{
                    $(".show-touch").hide();
                    $(".hide-touch").show();
                    $('.modal').modal();
                }

                $("#job_type").change(function () {
                    $(".job").hide();
                    $("."+$(this).val()).show();
                });

                var max_nb_of_volunteer = <?php echo json_encode($NB_OF_BOOKABLE_ROOM); ?>;

    			$(".job").on("click","tbody td.creneau:not(.blocked)",function(){
    				if ((parseInt($(this).attr("data-nb"))+parseInt($(this).attr("data-nb-r")))<max_nb_of_volunteer[$(this).attr("data-job")]){
    					$("#inscription").find(".title").html($(this).attr("data-fr"));
    					$("#inscription").find("input[name=ok]").val($(this).attr("data-fr"));
	    				$("#inscription").find("input[name=creneau]").val($(this).attr("data-lig"));
	    				$("#inscription").find("input[name=jour]").val($(this).attr("data-col"));
	    				$("#inscription").find("input[name=job_type]").val($(this).attr("data-job"));
	    				$("#inscription").modal('open');
    				}else{
    					Materialize.toast('<?php echo addslashes($max_p); ?>', 3000, 'rounded');
    				}
    				
    			});
                $(".job").on("contextmenu","tbody td.creneau:not(.blocked)",function(event){
                    event.preventDefault();
                    $("#remove").find(".title").html($(this).attr("data-fr-nok"));
                    $("#remove").find("input[name=remove]").val($(this).attr("data-fr-nok"));
                    $("#remove").find("input[name=creneau]").val($(this).attr("data-lig"));
                    $("#remove").find("input[name=jour]").val($(this).attr("data-col"));
                    $("#remove").find("input[name=job_type]").val($(this).attr("data-job"));
                    $("#remove").modal('open');
                });
                $(".job").on("click","tbody td.creneau.blocked",function(){
                    Materialize.toast('L&rsquo;épicerie est fermée le jeudi matin :)', 3000, 'rounded');
                });
    		});
    	</script>
    	<?php  } ?>
    </body>
  </html>
