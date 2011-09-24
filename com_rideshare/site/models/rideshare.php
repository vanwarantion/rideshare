<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

class rsTripList {
    public $usertrips;
    public $trips;
    
    public function __construct($userid, $from, $to, $early, $late){
        // get User's trips, UNION with queried trips
        $this->tripID = $tripid;
        $db =& JFactory::getDBO();
        
        if ($from == NULL) {$from = 0;}
        if ($to == NULL) {$to = 0;}
        
        $query='SELECT 
        tripID AS id,  
        regno, origin, destination, early, late, 
        NULL AS capacity, 
        NULL AS depart
        FROM `#__rsTrips`
        WHERE owner='.$userid;
        
        $query = $query.'
        UNION ALL
        SELECT 
        tripID AS id,  
        NULL AS regno, 
        origin, destination, early, late, capacity, depart
        FROM `#__rsTrips` ';
        
        $query = $query.'WHERE ';
        $query = $query.'origin=\''.htmlentities($from).'\' ';
        $query = $query.'AND ';
        $query = $query.'destination=\''.htmlentities($to).'\' ';
        
        if ($early != NULL) {
            $query = $query.'AND ';
            $query = $query.'early > \''.htmlentities($early).'\' ';
            }
        
        if ($late != NULL) {
            $query = $query.'AND ';
            $query = $query.'late < \''.htmlentities($late).'\' ';
            }
        
        $db->setQuery($query);
        $rs = $db->loadRowList();
        
        foreach ($rs as $retline) {
            if (is_null($retline[1])) {
                // results
                $this->trips[] = Array ("id"            =>  $retline[0], 
                                        "regno"         =>  $retline[1], 
                                        "origin"        =>  $retline[2], 
                                        "destination"   =>  $retline[3], 
                                        "early"         =>  $retline[4], 
                                        "late"          =>  $retline[5], 
                                        "capacity"      =>  $retline[6], 
                                        "depart"        =>  $retline[7]);
            } else {
                // user trips
                $this->usertrips[] = Array ("id"            =>  $retline[0], 
                                            "regno"         =>  $retline[1], 
                                            "origin"        =>  $retline[2], 
                                            "destination"   =>  $retline[3], 
                                            "early"         =>  $retline[4], 
                                            "late"          =>  $retline[5]);
                }
            }
        }
    }
class rsTrip {
    public $tripID;
    public $owner;
    public $regno;
    public $originID;
    public $destinationID;
    public $early;
    public $late;
    public $depart;
    public $capacity;
    public $userv;
    
    public function __construct($userid, $tripid = 0){
        $this->tripID = $tripid;
        $db =& JFactory::getDBO();
        
        $query='SELECT 
        t.owner, 
        t.regno, 
        t.origin, 
        t.destination, 
        t.early, 
        t.late, 
        TIME_FORMAT( t.depart, "%h:%i %p" ) as depart
        FROM #__rsTrips AS t
        WHERE t.tripID='.$tripid;
        $query = $query.' UNION ALL
        SELECT NULL, u.regno, u.seats, v.seats, NULL, NULL, NULL
        FROM #__rsUserVehicles as u
        LEFT JOIN #__rsVehicleTypes as v
        ON v.ID=u.vtypeID
        WHERE u.userID='.$userid;
        
        $db->setQuery( $query );
        $rs = $db->loadRowList();
        
        $this->owner =          $rs[0][0];
        $this->regno =          $rs[0][1];
        $this->originID =       $rs[0][2];
        $this->destinationID =  $rs[0][3];
        $this->early =          $rs[0][4];
        $this->late =           $rs[0][5];
        $this->depart =         $rs[0][6];
        
        foreach ($rs as $uv) {
            if ($uv[0] == NULL){
                $vv = Array (   "regno"  => $uv[1], 
                                "useats" => $uv[2], 
                                "vseats" => $uv[3]);
                $this->userv[] = $vv;
                //var_dump($uv);
                }
            }
        //var_dump($this->userv);
        }
    public function updateTrip(){
        $db =& JFactory::getDBO();
        //var_dump($this->tripID);
        if ($this->tripID == 0) {
            // insert
            $query =  'INSERT INTO `rideshare`.`#__rsTrips` (';
            $query = $query.'`tripID`, ';
            $query = $query.'`owner` , ';
            $query = $query.'`regno`  , ';
            $query = $query.'`origin`, ';
            $query = $query.'`destination`, ';
            $query = $query.'`early`, ';
            $query = $query.'`late`, ';
            $query = $query.'`capacity`, ';
            $query = $query.'`depart` ';
            $query = $query.') VALUES (';
            $query = $query.'NULL, ';
            $query = $query.$this->owner.', ';
            $query = $query.'\''.$this->regno.'\', ';
            $query = $query.$this->originID.', ';
            $query = $query.$this->destinationID.', ';
            $query = $query.'\''.$this->early.'\', ';
            $query = $query.'\''.$this->late.'\', ';
            $query = $query.$this->capacity.', ';
            $query = $query.'TIME(STR_TO_DATE(\'';
            $query = $query.$this->depart;
            $query = $query.'\', \'%h:%i %p\')) );';
        } else {
            // abort if owner is not current user
            // update
            $query = '';
            }
        //var_dump($query);
        $db->setQuery( $query );
        $db->query();
        
        return $db->insertid();
        }
    }
class rsUser {
    
    private $description;
    private $uid;
    //private $db;
    
    public function __construct($usrid = 0){
        // TODO: Also get trip count and ratings
        if ($usrid == 0) {
            return '';
            }
        $db =& JFactory::getDBO();
        $query = 'SELECT 
        uv.regno, 
        u.description
        FROM #__rsProfile AS u
        LEFT JOIN #__rsUserVehicles AS uv
        ON u.userID=uv.userID
        WHERE u.userID='.$usrid;
        
        $db->setQuery( $query );
        $rs = $db->loadRowList();
        // read description
        $this->description = $rs[0][1];
        $this->uid = $usrid;
        // read vehicles:
        foreach ($rs as $uv) {
            $vv = array("regno" => $uv[0]);
            $this->vlist[] = $vv;
            }
        }
    
    public function getVehicles(){
        return $this->vlist;
        }
    
    public function getDesc(){
        return $this->description;
        }
    
    public function setDesc($nDesc){
        $db =& JFactory::getDBO();
        $query = 'UPDATE #__rsProfile SET description=\''.$nDesc.'\' WHERE userID='.$this->uid;
        $db->setQuery( $query );
        $db->query();
        if ($db->getAffectedRows() < 1) {
            $data =new stdClass();
            $data->userID = $this->uid;
            $data->description = $nDesc;
            $db->insertObject( '#__rsProfile', $data, 'id' );
            }
        $this->description = $nDesc;
        return $db->getAffectedRows();
        }
    
    public function delVehicle($rno){
        $db =& JFactory::getDBO();
        $query = 'DELETE FROM #__rsUserVehicles WHERE regno=\''.$rno.'\'';
        $db->setQuery( $query );
        $db->query();
        return $db->getAffectedRows();
        }
    }
class rsVehicle {
    protected $regno;
    public $make;
    public $model;
    public $typeID;
    public $year;
    public $seats;
    public $aircon;
    public $description;
    protected $vTypes;
    protected $vtypeCount;
    
    public function getTypeCount(){
        return $this->vtypeCount;
        }
    
    public function getTypes(){
        return $this->vTypes;
        }
    
    public function updateVehicle(){
        $db =& JFactory::getDBO();
        $query = 'UPDATE `rideshare`.`rs_rsUserVehicles` SET ';
        $query = $query.'`vtypeID` = \''.$this->typeID.'\', ';
        $query = $query.'`vyear` = \''.$this->year.'\', ';
        $query = $query.'`seats` = \''.$this->seats.'\', ';
        $query = $query.'`aircon` = \''.$this->aircon.'\', ';
        $query = $query.'`description` = \''.$this->description.'\' ';
        $query = $query.'WHERE `rs_rsUserVehicles`.`regno` = \''.$this->regno.'\'';
        $db->setQuery( $query );
        $db->query();
        
        return $db->getAffectedRows();
        }
    
    public function addVehicle($rno){
        $db =& JFactory::getDBO();
        $user =& JFactory::getUser();
        $query = 'INSERT INTO `rideshare`.`#__rsUserVehicles` (';
        $query = $query.'`userID` , `vtypeID` , `regno` , `vyear` ,
         `seats` , `aircon` , `description` ';
        $query = $query.') VALUES ( ';
        $query = $query.'\''.$user->id.'\' , ';
        $query = $query.'\''.$this->typeID.'\' , ';
        $query = $query.'\''.strtoupper($rno).'\' , ';
        $query = $query.'\''.$this->year.'\' , ';
        $query = $query.'\''.$this->seats.'\' , ';
        $query = $query.'\''.$this->aircon.'\' , ';
        $query = $query.'\''.$this->description.'\' ';
        $query = $query.')';
        //var_dump($query);
        $db->setQuery( $query );
        $db->query();
        return $db->getAffectedRows();
        }
    public function __construct($rno){
        $this->regno = $rno;
        $db =& JFactory::getDBO();
        $query = 'SELECT 
        NULL AS ID, 
        tm.text AS make, 
        t.text AS model, 
        u.vyear, 
        u.seats, 
        t.seats AS orgSeats, 
        u.aircon, 
        t.aircon AS orgAircon, 
        u.description
        FROM rs_rsUserVehicles AS u
        LEFT JOIN `#__rsVehicleTypes` AS t
        ON t.ID=u.vtypeID
        INNER JOIN `#__rsVehicleTypes` AS tm
        ON t.catID=tm.ID
        WHERE u.regno="'.$rno.'" ';
        
        $query = $query.'UNION ALL
        SELECT DISTINCT 
        a.ID as ID, 
        b.text AS make, 
        a.text AS model,
        NULL, NULL, a.seats, NULL, a.aircon, NULL
        FROM `#__rsVehicleTypes` AS a
        INNER JOIN `#__rsVehicleTypes` AS b
        ON a.catID=b.ID
        ORDER BY ID, make, model';
        $db->setQuery( $query );
        $rs = $db->loadRowList();
        //0     1       2       3       4       5           6       7           8
        //ID 	make 	model 	vyear 	seats 	orgSeats 	aircon 	orgAircon 	description
        $editV = 1;
        if ($rno != '') {
            $this->make = $rs[0][1];
            $this->model = $rs[0][2];
            $this->year = $rs[0][3];
            if ( $rs[0][4] == NULL ) {
                $this->seats = $rs[0][5];
            } else {
                $this->seats = $rs[0][4];
                }
            if ( $rs[0][6] == NULL ) {
                $this->aircon = $rs[0][7];
            } else {
                $this->aircon = $rs[0][6];
                }
            $this->description = $rs[0][8];
            $editV = 1;
            }
        $this->vtypeCount = count($rs);
        //var_dump($rs);
        for ($i=$editV; $i < count($rs) ; $i++) {
            $curmake = array (  "id"        =>  $rs[$i][0], 
                                "model"     =>  $rs[$i][2], 
                                "seats"     =>  $rs[$i][5], 
                                "aircon"    =>  $rs[$i][7]);
            
            $this->vTypes[$rs[$i][1]][] = $curmake;
            }
        //var_dump($this->vTypes);
        }
    }
class rsLocations {
    public $locationList;   // [ID, name]
    public $map;
    
    // return array of directly connected locations and distances
    // eg: ([...], ["cityB", 356], [...])
    public function getConnected($lid) {
        
        }
    
    // fill $locationList and wholeMap
    public function __construct(){
        $this->regno = $rno;
        $db =& JFactory::getDBO();
/*
        $query = 'SELECT lo.name AS origin, ld.name AS destination, p.distance
        FROM `rs_rsPaths` AS p
        LEFT JOIN `rs_rsLocations` AS lo
        ON lo.locationID=p.org
        LEFT JOIN `rs_rsLocations` AS ld
        ON ld.locationID=p.dst
        UNION ALL
        SELECT locationID, name, NULL, NULL 
        FROM `rs_rsLocations`';
*/
        $query = 'SELECT locationID , NULL AS destination, NULL AS distance, name
        FROM `rs_rsLocations`
        UNION ALL
        SELECT org AS origin, dst AS destination, distance, NULL
        FROM `rs_rsPaths`
        ORDER BY name';
        $db->setQuery( $query );
        $rs = $db->loadRowList();
        //  0           1               2           3
        // lid/org 	    destination 	distance 	name
        //var_dump($rs);
        foreach ($rs as $rr) {
            if ($rr[1] == NULL) {
                // locations list
                $this->locationList[$rr[0]] = $rr[3];
            } else {
                // map
                $this->map[] = Array (  "origin"        => $rr[0], 
                                        "destination"   => $rr[1], 
                                        "distance"      => $rr[2]);
                }
            }
        }
    }
class RideshareModelRideshare extends JModelItem {
    protected $msg;
    private function getEditDesc($oldText = ''){
        $rv = '<form method="post" action="'.htmlentities($_SERVER['PHP_SELF']).'"';
        $rv = $rv.' name="editDesc">';
        $rv = $rv.'<table><tbody><tr><td>
        <textarea cols="50" rows="4" name="newDesc">'.$oldText;
        $rv = $rv.'</textarea></td></tr><tr align="right"><td>
        <input value="Done" name="UpdateDesc" type="submit"><br><br></td></tr></tbody></table>';
        $rv = $rv.'</form>';
        return $rv;
        }
    private function getVehDelConfirm($vehReg = ''){
        $rv = '<div id="vehDelConf'.$vehReg.'" style="display:none;">
        <form method="post" action="'.htmlentities($_SERVER['PHP_SELF']).'"';
        $rv = $rv.' name="delVeh'.$vehReg.'"><input type="hidden" 
        name="vehReg" value="'.$vehReg.'">Are you sure? <input value="Yes!" 
        name="vehDel" type="submit"></form></div>';
        return $rv;
        }
    public function getVehicle(){
        // TODO: Add custom vehicle make and model
        $vModify = 0;
        if(isset($_POST['veadded'])){
            $rv = '<H1>New vehicle has been posted</H1>';
            $vModify = 1;
            $vobj = new rsVehicle();
            }
        if(isset($_POST['vedone'])){
            //var_dump($_POST);
            $vModify = 2;
            $rv = '<H1>Updated vehicle:</H1>';
            $vobj = new rsVehicle($_POST["regTxt"]);
            }
        
        if ($vModify > 0){
            $vobj->typeID = htmlentities( $_POST["sl".$_POST["vmakeList"]]);
            $vobj->year = htmlentities($_POST["yearTxt"]);
            $vobj->seats = htmlentities($_POST["seatsTxt"]);
            if ($_POST["airconSel"] == 'Yes'){
                $acselval = 1;
            } else {
                $acselval = 0;
                }
            $vobj->aircon = $acselval;
            $vobj->description = htmlentities($_POST["newDesc"]);
            
            if ($vModify == 1){
                $vobj->addVehicle(htmlentities( strtoupper($_POST["regTxt"]) ));
                
            } elseif ($vModify == 2) {
                if ($vobj->updateVehicle() < 1) {
                    $rv = 'Something went wrong!';
                    }
                }
            $rv = $rv.'<br>RegNo: '.$_POST["regTxt"];
            $rv = $rv.'<br>Model: '.$_POST["sl".$_POST["vmakeList"]];
            $rv = $rv.'<br>Year: '.$_POST["yearTxt"];
            $rv = $rv.'<br>Seats: '.$_POST["seatsTxt"];
            $rv = $rv.'<br>AirCon: '.$_POST["airconSel"];
            $rv = $rv.'<br>Description: '.$_POST["newDesc"];
            return $rv;
            }
        
        $rv = '<h1>';
        $vtoEdit = '';
        if(isset($_GET['add'])){
            $rv = $rv.'New ';
        } else {
            $rv = $rv.'Edit ';
            if(isset($_GET['v'])){
                $vtoEdit = $_GET['v'];
            } else {
                return 'Invalid URL';
                }
            }
        $vobj = new rsVehicle($vtoEdit);
        $rv = $rv.'Vehicle</h1>';
        
        $rv = $rv.'<form method="post" action="'.htmlentities($_SERVER['PHP_SELF']);
        $rv = $rv.'?view=rideshare&id=2"';
        $rv = $rv.' name=vehform">';
        
        $rv = $rv.'<table style="text-align: left;"><tbody>';
        $rv = $rv.'<tr><td style="width: 140px;">Licence Plate</td>';
        $rv = $rv.'<td><input type="text" name="regTxt" value="';
        // Fill regno if exists:
        $rv = $rv.$vtoEdit;
        $rv = $rv.'"><br></td></tr>';
        
        // Make:
        $rsv = new rsVehicle($vtoEdit);
        $rv = $rv.'<script type="text/javascript">';
        if ($vobj->make == NULL) {
            $rv = $rv.'mmseldiv = "slistnomakesel";';
        } else {
            $rv = $rv.'mmseldiv = "slist'.$vobj->make.'";';
            }
        $rv = $rv.'yearaircon = new Array('.$rsv->getTypeCount().');</script>';
        $rv = $rv.'<tr><td>Make</td><td><select name="vmakeList" 
        onchange="javascript:ChangeDisplayDiv(mmseldiv, \'slist\' + this.value)">';
        // DataFill:
        $maketree = $rsv->getTypes();
        $rv = $rv.'<option ';
        if ($vobj->make == NULL) {
            $rv = $rv.'selected="yes" ';
            }
        $rv = $rv.'value="nomakesel">Select Make</option>';
        foreach (array_keys($maketree) as $vt) {
            $rv = $rv.'<option value="'.$vt.'"';
            if ($vobj->make == $vt) {
                $rv = $rv.' selected="yes"';
                }
            $rv = $rv.'>'.$vt.'</option>';
            }
        $rv = $rv.'</select><br></td></tr>';
        
        // Model: TODO: Populate with jQuery
        $rv = $rv.'<tr><td>Model</td><td><div id="slistnomakesel"';
        if ($vobj->make != NULL) {
            $rv = $rv.' style="display: none;"';
            }
        $rv = $rv.'><i>Select a Make</i></div>';
        foreach (array_keys($maketree) as $vt) {
            // special named hidden div
            $rv = $rv.'<div id="slist'.$vt.'"';
            if ($vt == $vobj->make) {
                $rv = $rv.' style="display: block;">';
            } else {
                $rv = $rv.' style="display: none;">';
                }
            $models = $maketree[$vt];
            $rv = $rv.'<select name="sl'.$vt.'">';
            //var_dump($vobj->model);
            foreach ( $models as $curmodel ) {
                $rv = $rv.'<option value="'.$curmodel["id"].'"';
                if ($vobj->model == $curmodel["model"]) {
                    $rv = $rv.' selected="yes"';
                    }
                $rv = $rv.'>';
                $rv = $rv.$curmodel["model"].'</option>';
                }
            $rv = $rv.'</select>';
            $rv = $rv.'</div>';
            }
        $rv = $rv.'</tr>';
        // Year:
        $rv = $rv.'<tr><td>Year</td><td><input type="text" name="yearTxt" value="';
        // Fill year if exists:
        if ($vobj->model != NULL) {
            $vtoEditYear = $vobj->year;
            }
        $rv = $rv.$vtoEditYear;
        $rv = $rv.'"></td></tr>';
        // Seats:
        $rv = $rv.'<tr><td>Seats</td><td><input type="text" name="seatsTxt" value="';
        // Fill seats if exists:
        if (!$vobj->model == NULL) {
            $vtoEditSeats = $vobj->seats;
            }
        $rv = $rv.$vtoEditSeats;
        $rv = $rv.'"></td></tr>';
        // Aircon:
        $rv = $rv.'<tr><td>Aircon</td><td>';
        $rv = $rv.'<input name="airconSel" value="Yes" ';
        // if checked: checked="checked"
        if ($vobj->model != NULL) {
            if ($vobj->aircon == 1) {
                $rv = $rv.' checked="checked"';
                }
            }
        $rv = $rv.' type="radio">Yes<br>';
        $rv = $rv.'<input name="airconSel" value="No" ';
        // if checked: checked="checked"
        if (($vobj->model != NULL) || (isset($_GET['add']))) {
            if ($vobj->aircon == 0) {
                $rv = $rv.' checked="checked"';
                }
            }
        $rv = $rv.' type="radio">No<br>';
        $rv = $rv.'</td></tr>';
        // Description
        $rv = $rv.'<tr><td colspan="2">Description<br><textarea cols="36" rows="3" name="newDesc">';
        // Fill year if exists:
        if ($vobj->model != NULL) {
            $vtoDesc = $vobj->description;
            }
        $rv = $rv.$vtoDesc;
        $rv = $rv.'</textarea></td></tr>';
        // Submit button:
        $rv = $rv.'<tr><td colspan="2"><div style="text-align: right;">';
        $rv = $rv.'<input type="submit" name="';
        
        if (isset($_GET['add'])) {
            $rv = $rv.'veadded';
        } else {
            $rv = $rv.'vedone';
            }
        
        $rv = $rv.'" value="Done"><br>';
        // Done
        $rv = $rv.'</div></td></tr></tbody></table>';
        
        return $rv;
        }
    public function getProfile(){
        $user =& JFactory::getUser();
        // check authorization:
        if ($user->id == 0) {
            return 'Please login or register';
            }
        // check requested user ID
        $selfProfile = False;
        if(isset($_GET['uid'])){
            // TODO: Check if client authorized
            $quser = JFactory::getUser($_GET['uid']);
            if ($user->id == $_GET['uid']) {
                $selfProfile = True;
                }
        } else {
            $quser = $user;
            $selfProfile = True;
            }
        // call our class for RideShare related stuff:
        $rsp = new rsUser($quser->id);
        
        // See if updated
        if ($selfProfile == True) {
            if(isset($_POST['UpdateDesc'])){
                $rsp->setDesc($_POST['newDesc']);
                }
            }
        
        // delete vehicle
        if(isset($_POST['vehDel'])){
            if ($rsp->delVehicle(htmlentities($_POST['vehReg'])) != 1) {
                return 'Error on deleting vehicle';
                }
            }
        
        $rv = '<div id="profile"><table style="width: 100%;"><tbody><tr><td><h1>';
        
        $rv = $rv.$quser->name.' ('.$quser->username.')';
        
        $rv = $rv.'</h1></td><td style="width: 140px;">
            Trip count: 35<br>
            General Rating: 666<br>
            Driver Rating: 333<br>
        </td></tr></tbody></table></div><br>';
        
        $dsc = $rsp->getDesc();
        
        $rv = $rv.'<div id="pSection"><h3>Description:</h3></div>';
        // Edit box:
        $rv = $rv.'<div id="popEdit" style="display:none;">'.$this->getEditDesc($dsc).'</div>';
        // Normal text:
        $rv = $rv.'<div id="pDescr"><p>'.$dsc;
        if ($selfProfile == True) { // hide pDescr, shows popEdit:
            $rv = $rv.' (<a  href="javascript:ChangeDisplayDiv(\'pDescr\', \'popEdit\');">Edit</a>)';
            }
        $rv = $rv.'</p></div><br><br>';
        
        if ($selfProfile == True) {
            // if has vehicle:
            $uvl = $rsp->getVehicles();
            if (count($uvl) > 0) {
                $rv = $rv.'<div id="pSection"><h3>Your Vehicles:</h3></div>';
                
                $rv = $rv.'<table><tbody>';
                foreach ($uvl as $uvi) {
                    $rv = $rv.'<tr><td style="vertical-align: top;">';
                    $rv = $rv.'<a href="index.php?option=com_rideshare&view=rideshare&id=2&v=';
                    $rv = $rv.$uvi["regno"];    // licence plate
                    $rv = $rv.'">'.$uvi["regno"].'</a>';
                    $rv = $rv.'<br></td><td style="width: 140px; text-align: center;">';
                    // delete link:
                    $rv = $rv.'<div id="vehDel'.$uvi["regno"].'">';
                    $rv = $rv.'<a href="javascript:ChangeDisplayDiv
                    (\'vehDel'.$uvi["regno"].'\', \'vehDelConf'.$uvi["regno"].'\');">';
                    $rv = $rv.'delete</a></div>';
                    // confirm link:
                    $rv = $rv.$this->getVehDelConfirm($uvi["regno"]);
                    // close row
                    $rv = $rv.'<br></td></tr>';
                    }
                // Link to add new vehicle:
                $rv = $rv.'</tbody></table><a 
                href="index.php?option=com_rideshare&view=rideshare&id=2&add=yes">
                Add new vehicle</a><br><br>';
                }
            }
            
        // comments section
        $rv = $rv.'<div id="pSection"><h3>Comments:</h3></div>';
        $rv = $rv.'<table style="width: 100%;"><tbody>';
        // for each comment:
        $commenter = 'someuser';
        $score = 'g5 / d2';
        $comment = 'Curabitur quis varius quam. Nullam cursus facilisis 
        mi ut bibendum. Duis arcu elit, interdum non scelerisque eget, 
        laoreet id justo. Curabitur auctor urna ut turpis iaculis 
        consequat. Morbi dolor justo, vulputate non porta sit amet, 
        luctus posuere ligula. Nullam dolor dolor, aliquam a vestibulum 
        eget, venenatis vitae tortor.';
        
        $rv = $rv.'<tr><td style="vertical-align: top;">';
        $rv = $rv.$commenter;
        $rv = $rv.'<br></td><td style="width: 140px;">Score: ';
        $rv = $rv.$score;
        $rv = $rv.'<br></td></tr><tr>';
        $rv = $rv.'<td colspan="2" rowspan="1" style="vertical-align: top;"><p>';
        $rv = $rv.$comment;
        $rv = $rv.'</p></td></tr>';
        // end of comments
        $rv = $rv.'</tbody></table>';
        
        // Temp part:
        $db =& JFactory::getDBO();
        $query = 'SELECT id, username FROM #__users';
        $db->setQuery( $query );
        $liste = $db->loadRowList();
        $rv = $rv.'<br>';
        foreach ($liste as $adam) {
            $rv = $rv.'<a href="'.JURI::current().'?uid='.$adam[0];
            $rv = $rv.'">'.$adam[1].'</a><br>';
            }
        
        return $rv;
        }
    private function getLocationDD($ddname, $locsel = 0, $locs = NULL) {
        if (is_null($locs)) {
            $locs = new rsLocations();
            }
        $rv = '<select name="'.$ddname.'">';
        foreach (array_keys($locs->locationList) as $locKey) {
            $rv = $rv.'<option value="'.$locKey.'"';
            if ($locKey == $locsel) {
                $rv = $rv.' selected="yes"';
                }
            $rv = $rv.'>';
            $rv = $rv.$locs->locationList[$locKey].'</option>';
            }
        $rv = $rv.'</select><br>';
        return $rv;
        }
    public function getRides(){
        $rv = '<H1>List of Rides</H1>';
        // check authorization:
        $user =& JFactory::getUser();
        if ($user->id == 0) {
            return 'Please login or register to see this page.';
            }
        // check previous post
        if(isset($_POST['fromL'])){
            $filtering = True;
            //var_dump($_POST);
        }
        $results = new rsTripList($user->id, $_POST['fromL'], $_POST['toL'], $_POST['dateEarliest'], $_POST['dateLatest']);
        
        
        // call our class for RideShare related stuff:
        $rsp = new rsUser($user->id);
        // if has vehicle:
        $uvl = $rsp->getVehicles();
        if (count($uvl) > 0) {
            $adr = substr(JURI::current(), 0, strrpos(JURI::current(), '/')).'/rstrip';
            $rv = $rv.'<a href="'.$adr.'">Click to add your trip here!</a><br><br>';
            }
        // begin filter form
        $rv = $rv.'<div id="rideFilter">';
        $rv = $rv.'<form method="post" action="'.htmlentities($_SERVER['PHP_SELF']);
        $rv = $rv.'" name=ridefilter"><table><tbody>';

        $rv = $rv.'<tr><td>From City:<br></td>';
        $rv = $rv.'<td>To City:<br></td>';
        $rv = $rv.'<td>Depart no earlier than:<br></td>';
        $rv = $rv.'<td>Arrive no later than:<br></td>';
        $rv = $rv.'<td colspan="1" rowspan="2" style="vertical-align: center;">';
        $rv = $rv.'<input type="submit" name="filterdone" value="done"></td>';
        $rv = $rv.'</tr><tr>';
        // From
        $llist = new rsLocations();
        $rv = $rv.'<td>'.$this->getLocationDD('fromL', $_POST['fromL'], $llist).'</td>';
        // To
        $rv = $rv.'<td>'.$this->getLocationDD('toL', $_POST['toL'], $llist).'</td>';
        // Earliest
        $rv = $rv.'<td>'.JHTML::_( 'calendar',$_POST['dateEarliest'],'dateEarliest','dateE','%Y-%m-%d').'</td>';
        // Latest
        $rv = $rv.'<td>'.JHTML::_( 'calendar',$_POST['dateLatest'],'dateLatest','dateL','%Y-%m-%d').'</td>';
        $rv = $rv.'</tr>';
        // end filter form
        $rv = $rv.'</tbody></table></div>';
        
        //var_dump($results->trips);
        $rv = $rv.'<br>';
        if (count($results->usertrips) > 0) {
            // "Your trips"
            $rv = $rv.'<div id="UserRideListings"><table><tbody>';
            $rv = $rv.'<tr><td colspan="5" rowspan="1"><h4>Your Trips</h4><br></td></tr>';
            foreach ($results->usertrips as $ut) {
                $rv = $rv.'<tr><td>'.$ut["regno"].'<br></td>';
                $rv = $rv.'<td>'.$llist->locationList[$ut["origin"]].'<br></td>';
                $rv = $rv.'<td>'.$llist->locationList[$ut["destination"]].'<br></td>';
                $rv = $rv.'<td>'.$ut["early"].'<br></td>';
                $rv = $rv.'<td>'.$ut["late"].'<br></td>';
                $rv = $rv.'</tr>';
                }
            $rv = $rv.'</tbody></table></form></div>';
            }
        $rv = $rv.'<br>';
        // begin listings
        if (count($results->trips) > 0) {
            $rv = $rv.'<div id="rideListings"><table><tbody>';
            $rv = $rv.'<tr><td colspan="5" rowspan="1"><h4>Search Results</h4><br></td></tr>';
            $rv = $rv.'<tr><td><b>Origin</b><br></td>';
            $rv = $rv.'<td><b>Destination</b><br></td>';
            $rv = $rv.'<td><b>Earliest Depart</b><br></td>';
            $rv = $rv.'<td><b>Latest Arrival</b><br></td>';
            $rv = $rv.'<td><b>Capacity</b><br></td>';
            $rv = $rv.'</tr>';
            foreach ($results->trips as $ut) {
                $rv = $rv.'<tr><td>'.$llist->locationList[$ut["origin"]].'<br></td>';
                $rv = $rv.'<td>'.$llist->locationList[$ut["destination"]].'<br></td>';
                $rv = $rv.'<td>'.$ut["early"].'<br></td>';
                $rv = $rv.'<td>'.$ut["late"].'<br></td>';
                $rv = $rv.'<td>'.$ut["capacity"].'<br></td>';
                $rv = $rv.'</tr>';
                }
            $rv = $rv.'</tbody></table></form></div>';
        } else {
            $rv = $rv.'<br>Nothing found!<br>';
            }
        // end listings
        return $rv;
        }
    public function getTrip(){
        // check edit
        $edit = 0;
        $modify = False;
        $user =& JFactory::getUser();
        $rsp = new rsUser($user->id);
        $uvl = $rsp->getVehicles();
        
        
        if(isset($_GET['trip'])){
            // check authorization to edit
            $edit = $_GET['trip'];
        } else {
            // NEW: check if user has vehicle
            if (count($uvl) == 0) {
                return 'You need to have a vehicle before you create a trip.';
                }
            }
        
        if(isset($_POST['sluv'])){
            $rtr = new rsTrip($user->id, $edit);
            $rtr->regno = $_POST['sluv'];
            $rtr->owner = $user->id;
            $rtr->capacity = $_POST['seatsel'.$_POST['sluv']];
            $rtr->originID = $_POST['fromL'];
            $rtr->destinationID = $_POST['toL'];
            $rtr->early = $_POST['dateEarliest'];
            $rtr->late = $_POST['dateLatest'];
            $rtr->depart = $_POST['hoursel'].':'.$_POST['minutesel'].' '.$_POST['ampm'];
            // insert & get ID
            $lastid = $rtr->updateTrip();
            // $edit=ID
            //var_dump($rtr);
            $rv = '<H1>Post successful</H1>';
            $rv = $rv.'Last insert id: '.$lastid.'<br>';
            $rv = $rv.'[Link to go back home] [<a href="'.htmlentities($_SERVER['PHP_SELF']).'?trip='.$lastid.'">Link to edit last post</a>]';
            return $rv;
            }
        
        $rtr = new rsTrip($user->id, $edit);
        //var_dump($rtr);
        // check delete
        
        // edit & create
        $rv = '<H1>';
        if ($edit == 0) {
            $rv = $rv.'New';
        } else {
            $rv = $rv.'Edit';
            }
        $rv = $rv.' Trip</H1>';
        // begin form here:
        $rv = $rv.'<form method="post" action="'.htmlentities($_SERVER['PHP_SELF']);
        $rv = $rv.'" name=tripform">';
        
        $rv = $rv.'<table style="text-align: left;" border="1" ><tbody>';
        $rv = $rv.'<tr><td>From<br></td><td>';
        $rv = $rv.$this->getLocationDD('fromL', $rtr->originID);
        $rv = $rv.'</td></tr><tr><td>To<br></td><td>';
        $rv = $rv.$this->getLocationDD('toL', $rtr->destinationID);
        $rv = $rv.'</td></tr><tr><td>Depart no earlier than<br></td><td>';
        $rv = $rv.JHTML::_('calendar',$rtr->early,'dateEarliest','dateE','%Y-%m-%d');
        $rv = $rv.'</td></tr><tr><td>Arrive no later than<br></td><td>';
        $rv = $rv.JHTML::_( 'calendar',$rtr->late,'dateLatest','dateL','%Y-%m-%d');
        $rv = $rv.'</td></tr><tr><td>Select Vehicle<br></td><td>';
        
        // first car is selected if edit=0
        $rv = $rv.'<script type="text/javascript">';
        if ($edit == 0) {
            $selectedvehicle = $rtr->userv[0]["regno"];
        } else {
            $selectedvehicle = $rtr->regno;
            }
        $rv = $rv.'mmseldiv = "seats'.$selectedvehicle.'";';
        $rv = $rv.'</script>';
        // vehicle selection
        $rv = $rv.'<select name="sluv" ';
        $rv = $rv.'onchange="javascript:ChangeDisplayDiv(mmseldiv, \'seats\' + this.value)">';
        
        foreach ($rtr->userv as $uv) {
            $rv = $rv.'<option value="'.$uv["regno"].'"';
            if ($uv["regno"] == $selectedvehicle){
                $rv = $rv.' selected="yes"';
                }
            $rv = $rv.'>'.$uv["regno"].'</option>';
            $curveh = Array ("regno" => $uv["regno"]);
            if ($uv["useats"] == NULL) {
                $curveh["seats"] = $uv["vseats"];
            } else {
                $curveh["seats"] = $uv["useats"];
                }
            $curveh["html"] = '<div id="seats'.$uv["regno"].'"';
            if ($uv["regno"] == $selectedvehicle){
                $curveh["html"] = $curveh["html"].' style="display: block;"';
            } else {
                $curveh["html"] = $curveh["html"].' style="display: none;"';
                }
            $curveh["html"] = $curveh["html"].'>';
            $curveh["html"] = $curveh["html"].'<select name="seatsel';
            $curveh["html"] = $curveh["html"].$uv["regno"].'">';
            
            for ($i = $curveh["seats"]; $i>0; $i--) {
                $curveh["html"] = $curveh["html"].'<option value="'.$i.'">';
                // select if selected
                $curveh["html"] = $curveh["html"].$i.'</option>';
                }
            $curveh["html"] = $curveh["html"].'</select></div>';
            $usrvehsel[] = $curveh;
            }
        $rv = $rv.'</select>';
        $rv = $rv.'<br></td></tr><tr><td>Passanger capacity<br></td><td>';
        // capacity selection
        foreach ($usrvehsel as $seatdiv) {
            $rv = $rv.$seatdiv["html"];
            }
        $rv = $rv.'</td></tr><tr><td>Desired time of day to depart<br>';
        $rv = $rv.'</td><td>';
        // time to depart
        //var_dump($rtr->depart);
        $depampm = explode(' ', $rtr->depart);
        $dephm = explode(':', $depampm[0]);
        $rv = $rv.'<select name="hoursel">';
        for ($i = 1; $i < 13; $i++){
            $rv = $rv.'<option value="'.$i.'"';
            if ($i == $dephm[0]) {
                $rv = $rv.' selected="yes"';
                }
            $rv = $rv.'>'.$i.'</option>';
            }
        $rv = $rv.'</select> : ';
        $rv = $rv.'<select name="minutesel">';
        for ($i = 0; $i < 60; $i++){
            $rv = $rv.'<option value="'.$i.'"';
            if ($i == $dephm[1]) {
                $rv = $rv.' selected="yes"';
                }
            $rv = $rv.'>'.$i.'</option>';
            }
        $rv = $rv.'</select>   ';
        $rv = $rv.'<input type="radio" name="ampm" value="AM" ';
        if ($depampm[1] == 'AM') {
            $rv = $rv.'checked="checked"';
            }
        $rv = $rv.'/>AM ';
        $rv = $rv.'<input type="radio" name="ampm" value="PM" ';
        if ($depampm[1] == 'PM') {
            $rv = $rv.'checked="checked"';
            }
        $rv = $rv.'/>PM';
        $rv = $rv.'</td></tr><tr><td>';
        if ($edit > 0) {
            $rv = $rv.'<div id="dellink">';
            $rv = $rv.'<a  href="javascript:ChangeDisplayDiv(\'dellink\', \'delconfirm\');">Delete This Trip</a>';
            $rv = $rv.'</div>';
            $rv = $rv.'<div id="delconfirm" style="display: none;">';
            $rv = $rv.'Are you sure? [Yes]</div>';
            }
        $rv = $rv.'<br></td><td>';
        $rv = $rv.'<input type="submit" name="trdone" value="done">';
        $rv = $rv.'<field name="view" type="hidden" default="rideshare" />';
        $rv = $rv.'<field name="id" type="hidden" default="3" />';
        $rv = $rv.'<br></td></tr>';
        $rv = $rv.'</tbody></table></form>';
        return $rv;
        }
    
    /**
     * Get the message
     * @return string The message to be displayed to the user
     */
    public function getMsg() {
        // include javascript
        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_rideshare/css/rideshare.css');
        $document->addScript('components/com_rideshare/js/showhide.js');
        
        if (!isset($this->msg)) {
            $id = JRequest::getInt('id');
            switch ($id) {
                case 1:
                    $this->msg = $this->getProfile();
                    break;
                default:
                case 2:
                    $this->msg = $this->getVehicle();
                    break;
                case 3:
                    $this->msg = $this->getTrip();
                    break;
                case 4:
                    $this->msg = $this->getRides();
                    break;
                default:
                    $this->msg = 'Page ID: '.$id;
                }
            }
        return $this->msg;
        }
    }
