<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

class rsTrip {
    
    public function __construct($tripid = 0){
        
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
    
    private function getLocationDD($ddname) {
        $locs = new rsLocations();
        $rv = '<select name="'.$ddname.'">';
        foreach (array_keys($locs->locationList) as $locKey) {
            $rv = $rv.'<option value="'.$locKey.'">';
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
        // call our class for RideShare related stuff:
        $rsp = new rsUser($user->id);
        // if has vehicle:
        $uvl = $rsp->getVehicles();
        if (count($uvl) > 0) {
            $adr = substr(JURI::current(), 0, strrpos(JURI::current(), '/')).'/rstrip';
            $rv = $rv.'<a href="'.$adr.'">Click to add your trip here!</a><br><br>';
            }
        // begin filter form
        $rv = $rv.'<div id="rideFilter"><table><tbody>';
        $rv = $rv.'<tr><td>From City:<br></td>';
        $rv = $rv.'<td>To City:<br></td>';
        $rv = $rv.'<td>Depart no earlier than:<br></td>';
        $rv = $rv.'<td>Arrive no later than:<br></td>';
        $rv = $rv.'<td colspan="1" rowspan="2" style="vertical-align: center;">Button<br></td>';
        $rv = $rv.'</tr><tr>';
        // From
        $rv = $rv.'<td>'.$this->getLocationDD('fromL').'</td>';
        // To
        $rv = $rv.'<td>'.$this->getLocationDD('toL').'</td>';
        // Earliest
        $rv = $rv.'<td>'.JHTML::_( 'calendar',$startdate,'dateEarliest','dateE','%Y-%m-%d').'</td>';
        // Latest
        $rv = $rv.'<td>'.JHTML::_( 'calendar',$enddate,'dateLatest','dateL','%Y-%m-%d').'</td>';
        $rv = $rv.'</tr>';
        // end filter form
        $rv = $rv.'</tbody></table></div>';
        
        // begin listings
        $rv = $rv.'<div id="rideListings"><table><tbody>';
        
        $rv = $rv.'<br>Listings goes here<br>';
        
        // end listings
        $rv = $rv.'</tbody></table></div>';
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
                return 'You need to have a vehicle before create a trip.';
                }
            
            }
        // check delete
        
        // edit & create
        $rv = '<H1>';
        if ($edit == 0) {
            $rv = $rv.'New';
        } else {
            $rv = $rv.'Edit';
            }
        $rv = $rv.' Trip</H1>';
        // begin form:
        $rv = $rv.'<table style="text-align: left; width: 100%;" border="1" ><tbody>';
        $rv = $rv.'<tr><td>From<br></td><td>';
        $rv = $rv.$this->getLocationDD('fromL');
        $rv = $rv.'</td></tr><tr><td>To<br></td><td>';
        $rv = $rv.$this->getLocationDD('toL');
        $rv = $rv.'</td></tr><tr><td>Depart no earlier than<br></td><td>';
        $rv = $rv.JHTML::_( 'calendar',$startdate,'dateEarliest','dateE','%Y-%m-%d');
        $rv = $rv.'</td></tr><tr><td>Arrive no later than<br></td><td>';
        $rv = $rv.JHTML::_( 'calendar',$finishdate,'dateLatest','dateL','%Y-%m-%d');
        $rv = $rv.'</td></tr><tr><td>Select Vehicle<br></td><td>';
        $rv = $rv.'dropdown';
        $rv = $rv.'<br></td></tr><tr><td>Passanger capacity<br></td><td>';
        $rv = $rv.'dropdown';
        $rv = $rv.'<br></td></tr><tr><td>delete-button<br></td><td>';
        $rv = $rv.'done_button';
        $rv = $rv.'<br></td></tr>';
        $rv = $rv.'</tbody></table>';
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
