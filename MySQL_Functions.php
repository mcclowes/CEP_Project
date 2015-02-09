<?php 
#delete functions, edit damage reports , add current date, 

$mysqli = connect();

if (isset($_POST['form_type'])) {
	$type = $_POST['form_type'];
}
if (isset($_POST['form_data'])) {
	$data = $_POST['form_data'];
}


switch ($type) {
	case 'addTcMember':
		addTCMember($mysqli,$data);
		break;

	case 'addVehicle':
		addVehicle($mysqli,$data);
		break;

	case 'addDriver':
		addDriver($mysqli,$data);
		break;

	case 'addGroup';
		addGroup($mysqli,$data);
		break;

	case 'addDamageReport':
		addDamageReport($mysqli,$data);
		break;

	case 'addJourney':
		$Journey_ID = addJourney($mysqli,$data);
		$x = $data['Pickups']['No_Pickups']; 

		for ($xx = 0; $xx < $x; $xx++){
			addPickup($mysqli,$Journey_ID, $data['Pickups'][$xx]);
		}

		break;

	case 'addTCJourneyMember':
		addTCJourneyMember($mysqli,$data);
		break;

	case 'addVehicleCheckProblem':
		addVehicleCheckProblem($mysqli,$data);
		break;
	
	case 'getJourneys':
		$rdata = getJourneys($mysqli);
		echo json_encode($rdata);
		break;

	case 'getJourney':
		$rdata = getJourney($mysqli, $data['Journey_ID']);
		echo json_encode($rdata);
		break;

	default:
		echo json_encode("error");
		break;

}
//Connects to the database storing questions etc.

function connect(){
	$servername = "mysql.dur.ac.uk";
	$dbname = "Xgfjl56_WCT";
	$user = "gfjl56";
	$pass = "houston3";
	// Create connection
	$conn = new mysqli($servername, $user, $pass, $dbname);
	// Check connection
	if ($conn->connect_error) 
		{die("Connection failed: " . $conn->connect_error);}

	return $conn;
}


function outputDate($data){
	$s = str_split($data);
	0 1 0 4 / 2 0 / 1 6 
	$date = "$s[8]"."$s[9]"."/"."$s[5]"."$s[6]"."/"."$s[0]"."$s[1]"."$s[2]"."$s[3]";
	return $date;
}

function inputDate($data){
	$s = str_split($data);
	0 1 / 0 4 / 2 0 1 6 
	$date = "$s[6]"."$s[7]"."$s[8]"."$s[9]"."-"."$s[3]"."$s[4]"."-"."$s[0]"."$s[1]";
	return $date;
}


function getAddress($mysqli, $Address_ID){
	$Address = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (Line1, Line2, Line3, Line4, Line5, Post_Code) FROM Addresses WHERE Address_ID = ? ;")){
		$statement->bind_param("i", $Address_ID);
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Address['Line1'],$Address['Line2'],$Address['Line3'],$Address['Line4'],$Address['Line5'],$Address['Post_Code']);
		$statement->fetch();
	}
	return $Address;
}

function getAddresses($mysqli){
	$Addresses = array();
	$Address = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (Line1, Line2, Line3, Line4, Line5, Post_Code) FROM Addresses;")){
		$statement->bind_param("i", $Address_ID);
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Address['Line1'],$Address['Line2'],$Address['Line3'],$Address['Line4'],$Address['Line5'],$Address['Post_Code']);
		while($statement->fetch()){
			$Addresses[$i] = $Address;
			$i ++;
		}

	}
	return $Addresses;
}

function getVehicles($mysqli){	
	$Vehicles = array();
	$Vehicle = array();
	$i = 0;

	if($statement = $mysqli->prepare(" SELECT (Vehicle_ID, Nickname, Registration, Make, Model, Colour, Capacity_Passengers, Tax_Due, MOT_Due, Inspection_Due,
										 Service_Due, Tail_Service_Due, Section_19_No, Section_19_Due, Seating_Configurations) FROM Vehicles;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Vehicle['Vehicle_ID'], $Vehicle['Nickname'],$Vehicle['Registration'],$Vehicle['Make'],$Vehicle['Model'],$Vehicle['Colour'],$Vehicle['Capacity_Passengers'],$Vehicle['Tax_Due'],$Vehicle['MOT_Due'],$Vehicle['Inspection_Due,'],
								$Vehicle['Service_Due'], $Vehicle['Tail_Service_Due'], $Vehicle['Section_19_No'], $Vehicle['Section_19_Due'], $Vehicle['Seating_Configurations']);
		while($statement->fetch()){

			$Vehicle['Tax_Due'] = outputDate($Vehicle['Tax_Due']);
			$Vehicle['MOT_Due'] = outputDate($Vehicle['MOT_Due']);
			$Vehicle['Inspection_Due,'] = outputDate($Vehicle['Inspection_Due']);
			$Vehicle['Service_Due'] = outputDate($Vehicle['Service_Due']);
			$Vehicle['Tail_Service_Due'] = outputDate($Vehicle['Tail_Service_Due']);
			$Vehicle['Section_19_Due'] = outputDate($Vehicle['Section_19_Due']);

			$Vehicles[$i] = $Vehicle;
			$i ++;
		}
	}	
	return $Vehicles;
	
}


function getDrivers($mysqli){
	$Drivers = array();
	$Driver = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (Driver_ID, fName, sName, Address_ID, Tel_No, Mobile_No, BOD, Licence_No, Licence_Expires, Licence_Points, DBS_No, DBS_Issued, 
										Emergency_Name, Emergency_Tel, Emergency_Relationship, Is_Volunteer) FROM Drivers ;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Driver['Driver_ID'], $Driver['fName'],$Driver['sName'],$Address_ID,$Driver['Tel_No'], $Driver['Mobile_No'], $Driver['BOD'], $Driver['Licence_No'], $Driver['Licence_Expires'], $Driver['Licence_Points'], $Driver['DBS_No'], $Driver['DBS_Issued'], 
										$Driver['Emergency_Name'],$Driver['Emergency_Tel'],$Driver['Emergency_Relationship'],$Driver['Is_Volunteer']);
		while($statement->fetch()){

			$Driver['BOD'] = outputDate($Driver['BOD']);
			$Driver['Licence_Expires'] = outputDate($Driver['Licence_Expires']);
			$Driver['DBS_Issued'] = outputDate($Driver['DBS_Issued']);

			$Drivers[$i] = $Driver;
			$i++;
		}

	}
	return $JourneyMembers;
}


function getDamageReports($mysqli){
	$Damage_Report = array();
	$Damage_Reports = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (Vehicle_ID, Damage_description, Date_Added, Date_Resolved) FROM Damage_Reports;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Damage_Report['Vehicle_ID'],$Damage_Report['Damage_description'],$Damage_Report['Date_Added'],$Damage_Report['Date_Resolved']);
		while($statement->fetch()){

			$Damage_Report['Date_Added'] = outputDate($Damage_Report['Date_Added']);
			$Damage_Report['Date_Resolved'] = outputDate($Damage_Report['Date_Resolved']);

			$Damage_Reports[$i] = $Damage_Report;
			$i++;
		}

	}
	return $Damage_Reports;

}


function getVehicleDamageReports($mysqli,$Vehicle_ID){
	$Damage_Report = array();
	$Damage_Reports = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT ( Damage_description, Date_Added, Date_Resolved) FROM Damage_Reports WHERE Vehicle_ID = ?;")){
		$statement->bind_param('i', $Vehicle_ID);
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Damage_Report['Damage_description'],$Damage_Report['Date_Added'],$Damage_Report['Date_Resolved']);
		while($statement->fetch()){
			
			$Damage_Report['Date_Added'] = outputDate($Damage_Report['Date_Added']);
			$Damage_Report['Date_Resolved'] = outputDate($Damage_Report['Date_Resolved']);

			$Damage_Reports[$i] = $Damage_Report;
			$i++;
		}

	}
	return $Damage_Reports;

}


function getGroups($mysqli){
	$Group = array();
	$Groups = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (Group_ID, Name, Tel) FROM Groups;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Group['Group_ID'],$Group['Name'],$Group['Tel']);
		while($statement->fetch()){
			$Groups[$i] = $Group;
			$i++;
		}

	}
	return $Groups;

}

function getGroup($mysqli, $Group_ID){
	$Group = array();
	if($statement = $mysqli->prepare(" SELECT (Name, Address, Tel, Invoice_Email, Invoice_Address, Invoice_Tel, Emergency_Name, Emergency_Tel, Profitable, Community, 
										Social, Statutory, Charity_No, Org_Aim, Activities_Education, Activities_Recreation, Activities_Health, Activities_Religion, Activities_Social, Activities_Inclusion, 
										Activities_Other, Concerned_Physical, Concerned_Learning, Concerned_Mental_Health, Concerned_Ethnic, Concerned_Alcohol, Concerned_Drug, Concerned_HIV_AIDS, 
										Concerned_Socially_Isolated, Concerned_Dementia, Concerned_Elderly, Concerned_Pre_School, Concerned_Young, Concerned_Women, Concerned_Health, 
										Concerned_Rurally_Isolated, Concerned_Other) FROM Group WHERE Group_ID = ?;")){
		$statement->bind_param("i", $Group_ID);
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Group['Name'],$Address_ID1,$Group['Address_Tel'],$Group['Invoice_Email'], $Address_ID2,$Group['Invoice_Tel'],$Group['Emergency_Name'],$Group['Emergency_Tel'], $Group['Profitable'], $Group['Community'], 
								$Group['Social'], $Group['Statutory'], $Group['Charity_No'], $Group['Org_Aim'], $Group['Activities_Education'], $Group['Activities_Recreation'], $Group['Activities_Health'], $Group['Activities_Religion'], $Group['Activities_Social'], $Group['Activities_Inclusion'],
								$Group['Activities_Other'], $Group['Concerned_Physical'], $Group['Concerned_Learning'], $Group['Concerned_Mental_Health'], $Group['Concerned_Ethnic'], $Group['Concerned_Alcohol'], $Group['Concerned_Drug'], $Group['Concerned_HIV_AIDS'], 
								$Group['Concerned_Socially_Isolated'], $Group['Concerned_Dementia'], $Group['Concerned_Elderly'], $Group['Concerned_Pre_School'], $Group['Concerned_Young'], $Group['Concerned_Women'], $Group['Concerned_Health'], 
								$Group['Concerned_Rurally_Isolated'], $Group['Concerned_Other']);
		$statement->fetch();
		
		$Group['Address'] = getAddress($mysqli, $Address_ID1);
		$Group['Destination'] = getAddress($mysqli, $Address_ID2);

	}

	return $Group;

}


function getJourneys($mysqli){
	$Journeys = array();
	$rdata = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (Journey_ID, Journey_Description, Jouney_Date, Return_Time) FROM Journeys;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($rdata['Journey_ID'], $rdata['Journey_Description'], $rdata['Jouney_Date'], $rdata['Return_Time']);
		while($statement->fetch()){

			if($stm = $mysqli->prepare(" SELECT MAX(Time) FROM Pickups WHERE Journey_ID = ?;")){

				$stm->bind_param("i", $rdata['Journey_ID']);
				$stm->execute();
				$stm->store_result();
				$stm->bind_result($rdata['Pickup_Time']);
				$stm->fetch();

				}

			$rdata['Jouney_Date'] = outputDate($rdata['Jouney_Date']);

			$Journeys[$i] = $rdata;
			$i ++;
			}
	}	
	return $Journeys;
}

function getJourney($mysqli, $Journey_ID){
	$rdata = array();
	if($statement = $mysqli->prepare(" SELECT (Journey_ID, Journey_Description, Booking_Date, fName, sName, Address_ID, Tel_No, Group_ID, Journey_Date, Destination, Return_Time,
										No_Passengers, Passengers_Note, Wheelchairs, Transferees, Other_Access, Booked_By, Driver_ID, Vehicle, 
										Keys_To_Collect, Quote, Invoice_Sent, Invoice_Paid) FROM Journeys WHERE Journey_ID = ?;")){
		$statement->bind_param("i", $Journey_ID);
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($rdata['Journey_ID'], $rdata['Journey_Description'], $rdata['Booking_Date'],$rdata['fName'],$rdata['sName'], $Address_ID1, $rdata['Tel_No'], $rdata['Group_ID'],$rdata['Journey_Date'], $Address_ID2, $rdata['Return_Time'], 
								$rdata['No_Passengers'], $rdata['Passengers_Note'], $rdata['Wheelchairs'], $rdata['Transferees'], $rdata['Other_Access'], $rdata['Booked_By'], $rdata['Driver_ID'], $rdata['Vehicle'], 
								$rdata['Keys_To_Collect'], $rdata['Quote'], $rdata['Invoice_Sent'], $rdata['Invoice_Paid']);
		$statement->fetch();

		$rdata['Booking_Date'] = outputDate($rdata['Booking_Date']);
		$rdata['Jouney_Date'] = outputDate($rdata['Jouney_Date']);
		$rdata['Invoice_Sent'] = outputDate($rdata['Invoice_Sent']);
		$rdata['Invoice_Paid'] = outputDate($rdata['Invoice_Paid']);


		$rdata['Address'] = getAddress($mysqli, $Address_ID1);
		$rdata['Destination'] = getAddress($mysqli, $Address_ID2);

	}

	return $rdata;

}


function getJourneyMembers($mysqli,$Journey_ID){
	$JourneyMembers = array();
	$JourneyMember = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (TC_Member_ID) FROM TC_Members WHERE Journey_ID = ?;")){
		$statement->bind_param('i', $Journey_ID);
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($TC_Member_ID);
		while($statement->fetch()){
			$JourneyMember = getTCMember($mysqli, $TC_Member_ID);
			$JourneyMembers[$i] = $JourneyMember;
			$i++;
		}

	}
	return $JourneyMembers;
}



function getTCMember($mysqli, $TC_Member_ID){
	$TC_Member = array();
	if($statement = $mysqli->prepare(" SELECT (fName, sName, Address_ID, Tel_No, Emergency_Name, Emergency_Tel, Emergency_Relationship, DOB,
										Details_Wheelchair, Details_Wheelchair_Type, Details_Wheelchair_Seat, Details_Scooter, Details_Mobility_Aid, Details_Shopping_Trolley, 
										Details_Guide_Dog, Details_People_Carrier, Details_Assistant, Details_Travelcard, Reasons_Transport, Reasons_Bus_Stop, Reasons_Anxiety,
										Reasons_Door, Reasons_Handrails, Reasons_Lift, Reasons_Level_Floors, Reasons_Low_Steps, Reasons_Assistance, Reasons_Board_Time,
										Reasons_Wheelchair_Access, Reasons_Other) FROM TC_Members;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($TC_Member['TC_Member_ID'], $TC_Member['fName'],$TC_Member['sName'], $Address_ID,$TC_Member['Tel_No'],$TC_Member['Emergency_Name'],$TC_Member['Emergency_Tel'], $TC_Member['Emergency_Relationship'], $TC_Member['DOB'],
								$TC_Member['Details_Wheelchair'], $TC_Member['Details_Wheelchair_Type'], $TC_Member['Details_Wheelchair_Seat'], $TC_Member['Details_Scooter'], $TC_Member['Details_Mobility_Aid'], $TC_Member['Details_Shopping_Trolley'], 
								$TC_Member['Details_Guide_Dog'], $TC_Member['Details_People_Carrier'], $TC_Member['Details_Assistant'], $TC_Member['Details_Travelcard'],$TC_Member['Reasons_Transport'],$TC_Member['Reasons_Bus_Stop'],$TC_Member['Reasons_Anxiety'],
								$TC_Member['Reasons_Door'],$TC_Member['Reasons_Handrails'],$TC_Member['Reasons_Lift'],$TC_Member['Reasons_Level_Floors'],$TC_Member['Reasons_Low_Steps'], $TC_Member['Reasons_Assistance'], $TC_Member['Reasons_Board_Time'],
								$TC_Member['Reasons_Wheelchair_Access'], $TC_Member['Reasons_Other']);
		$statement->fetch();

		$TC_Member['DOB'] = outputDate($TC_Member['DOB']);


	}
	return $TC_Member;
} 


function getTCMembers($mysqli){
	$TC_Member = array();
	$TC_Members = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (TC_Member_ID, fName, sName, Address_ID) FROM TC_Members;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($TC_Member['TC_Member_ID'],$TC_Member['fName'],$TC_Member['sName'],$Address_ID);
		while($statement->fetch()){
			$Address = getAddress($mysqli, $Address_ID);
			$TC_Member['Post_Code'] = $Address['Post_Code'] ;
			$TC_Members[$i] = $TC_Member;
			$i++;
		}

	}
	return $TC_Members;

}


function getPickups($mysqli,$Journey_ID){
	$Pickup = array();
	$Pickups = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (Note, Address_ID, Time, Return_Note) FROM Pickups WHERE Journey_ID = ?;")){
		$statement->bind_param($Journey_ID);
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Pickup['Note'], $Address_ID, $Pickup['Time'], $Pickup['Return_Note']);
		while($statement->fetch()){
			$
			$Pickups[$i] = $Pickup;
			$i++;
		}

	}
	return $Pickups;

}


function getVehicleCheckProblems($mysqli){	
	$Problem= array();
	$Problems = array();
	$i = 0;
	if($statement = $mysqli->prepare(" SELECT (Vehicle_ID, Problem_Description) FROM Vehicle_Check_Problems ;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Problem['Vehicle_ID'], $Problem['Problem_Description']);
		while($statement->fetch()){
			$
			$Problems[$i] = $Problem;
			$i++;
		}

	}
	return $Pickups;

}








 
function addAddress($mysqli,$Address){
	

	if( $statement = $mysqli->prepare("INSERT INTO Addresses (Line1, Line2, Line3, Line4, Line5, Post_Code) VALUES  (?, ?, ?, ?, ?, ?);")){
		
		$statement->bind_param("ssssss",$Address['Line1'],$Address['Line2'],$Address['Line3'],$Address['Line4'],$Address['Line5'],$Address['Post_Code']);

		$statement->execute();
	}
	if($statement = $mysqli->prepare(" SELECT MAX(Address_ID) FROM Addresses;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Address_ID);
		$statement->fetch();

		
		return $Address_ID;
	}

}




function addVehicle($mysqli,$Vehicle){

	if( $statement = $mysqli->prepare("INSERT INTO Vehicles ( Nickname, Registration, Make, Model, Colour, Capacity_Passengers, Tax_Due, MOT_Due, Inspection_Due,
										 Service_Due, Tail_Service_Due, Section_19_No, Section_19_Due, Seating_Configurations) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);") ){

		$statement->bind_param("sssssissssssss",$Vehicle['Nickname'],$Vehicle['Registration'],$Vehicle['Make'],$Vehicle['Model'],$Vehicle['Colour'],$Vehicle['Capacity_Passengers'],$Vehicle['Tax_Due'],$Vehicle['MOT_Due'],$Vehicle['Inspection_Due'],
								$Vehicle['Service_Due'], $Vehicle['Tail_Service_Due'], $Vehicle['Section_19_No'], $Vehicle['Section_19_Due'], $Vehicle['Seating_Configurations']);
		$statement->execute();
		$statement->store_result();
	}

}




function addDriver($mysqli,$Driver){
	$Address_ID = addAddress($mysqli,$Driver['Address']);
	
	if( $statement = $mysqli->prepare("INSERT INTO Drivers (fName, sName, Address_ID, Tel_No, Mobile_No, BOD, Licence_No, Licence_Expires, Licence_Points, DBS_No, DBS_Issued, 
										Emergency_Name, Emergency_Tel, Emergency_Relationship, Is_Volunteer) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);") ){
		
		$statement->bind_param("ssisssssissssss",$Driver['fName'],$Driver['sName'],$Address_ID,$Driver['Tel_No'], $Driver['Mobile_No'], $Driver['BOD'], $Driver['Licence_No'], $Driver['Licence_Expires'], $Driver['Licence_Points'], $Driver['DBS_No'], $Driver['DBS_Issued'], 
										$Driver['Emergency_Name'],$Driver['Emergency_Tel'],$Driver['Emergency_Relationship'],$Driver['Is_Volunteer']);
		$statement->execute();
		$statement->store_result();
		
	}

}


function addDamageReport($mysqli,$Damage_Report){


	if( $statement = $mysqli->prepare("INSERT INTO Damage_Reports (Vehicle_ID, Damage_description, Date_Added, Date_Resolved) VALUES ( ?, ?, ?, ?);") ){

		$statement->bind_param("isss",$Damage_Report['Vehicle_ID'],$Damage_Report['Damage_description'],$Damage_Report['Date_Added'],$Damage_Report['Date_Resolved']);
		$statement->execute();
		$statement->store_result();
	}
}


function addGroup($mysqli,$Group){

	$Address_ID1 = addAddress($mysqli,$Group['Address']);
	$Address_ID2 = addAddress($mysqli,$Group['Invoice_Address']);

	if( $statement = $mysqli->prepare("INSERT INTO Groups ( Name, Address, Tel, Invoice_Email, Invoice_Address, Invoice_Tel, Emergency_Name, Emergency_Tel, Profitable, Community, 
										Social, Statutory, Charity_No, Org_Aim, Activities_Education, Activities_Recreation, Activities_Health, Activities_Religion, Activities_Social, Activities_Inclusion, 
										Activities_Other, Concerned_Physical, Concerned_Learning, Concerned_Mental_Health, Concerned_Ethnic, Concerned_Alcohol, Concerned_Drug, Concerned_HIV_AIDS, 
										Concerned_Socially_Isolated, Concerned_Dementia, Concerned_Elderly, Concerned_Pre_School, Concerned_Young, Concerned_Women, Concerned_Health, 
										Concerned_Rurally_Isolated, Concerned_Other) VALUES ( ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,? ,?, ?, ?, ?, ?);") ){


		$statement->bind_param("sississsiiiisssssssssssssssssssssssss",
								$Group['Name'],$Address_ID1,$Group['Address_Tel'],$Group['Invoice_Email'], $Address_ID2,$Group['Invoice_Tel'],$Group['Emergency_Name'],$Group['Emergency_Tel'], $Group['Profitable'], $Group['Community'], 
								$Group['Social'], $Group['Statutory'], $Group['Charity_No'], $Group['Org_Aim'], $Group['Activities_Education'], $Group['Activities_Recreation'], $Group['Activities_Health'], $Group['Activities_Religion'], $Group['Activities_Social'], $Group['Activities_Inclusion'],
								$Group['Activities_Other'], $Group['Concerned_Physical'], $Group['Concerned_Learning'], $Group['Concerned_Mental_Health'], $Group['Concerned_Ethnic'], $Group['Concerned_Alcohol'], $Group['Concerned_Drug'], $Group['Concerned_HIV_AIDS'], 
								$Group['Concerned_Socially_Isolated'], $Group['Concerned_Dementia'], $Group['Concerned_Elderly'], $Group['Concerned_Pre_School'], $Group['Concerned_Young'], $Group['Concerned_Women'], $Group['Concerned_Health'], 
								$Group['Concerned_Rurally_Isolated'], $Group['Concerned_Other']);
		$statement->execute();
		$statement->store_result();
	}
}

function addJourney($mysqli,$Journey){

		$Address_ID1 = addAddress($mysqli,$Journey['Address']);
		$Address_ID2 = addAddress($mysqli,$Journey['Destination']);

	if( $statement = $mysqli->prepare("INSERT INTO Journeys (Journey_Description, Booking_Date, fName, sName, Address_ID, Tel_No, Group_ID, Journey_Date, Destination, Return_Time,
										No_Passengers, Passengers_Note, Wheelchairs, Transferees, Other_Access, Booked_By, Driver_ID, Vehicle, 
										Keys_To_Collect, Quote, Invoice_Sent, Invoice_Paid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);") ){

		$statement->bind_param("ssssisisisisiisssssdss",
								$Journey['Journey_Description'],$Journey['Booking_Date'],$Journey['fName'],$Journey['sName'], $Address_ID1,$Journey['Tel_No'],$Journey['Group_ID'],$Journey['Journey_Date'], $Address_ID2, $Journey['Return_Time'], 
								$Journey['No_Passengers'], $Journey['Passengers_Note'], $Journey['Wheelchairs'], $Journey['Transferees'], $Journey['Other_Access'], $Journey['Booked_By'], $Journey['Driver_ID'], $Journey['Vehicle'], 
								$Journey['Keys_To_Collect'], $Journey['Quote'], $Journey['Invoice_Sent'], $Journey['Invoice_Paid']);
		$statement->execute();
		$statement->store_result();
	}
	if($statement = $mysqli->prepare(" SELECT MAX(Journey_ID) FROM Journeys;")){
		$statement->execute();
		$statement->store_result();
		$statement->bind_result($Journey_ID);
		$statement->fetch();
	}
		return $Journey_ID;
	
}





function addTCMember($mysqli,$TC_Member){

	$Address_ID = addAddress($mysqli,$TC_Member['Address']);

	if( $statement = $mysqli->prepare("INSERT INTO TC_Members ( fName, sName, Address_ID, Tel_No, Emergency_Name, Emergency_Tel, Emergency_Relationship, DOB,
										Details_Wheelchair, Details_Wheelchair_Type, Details_Wheelchair_Seat, Details_Scooter, Details_Mobility_Aid, Details_Shopping_Trolley, 
										Details_Guide_Dog, Details_People_Carrier, Details_Assistant, Details_Travelcard, Reasons_Transport, Reasons_Bus_Stop, Reasons_Anxiety,
										Reasons_Door, Reasons_Handrails, Reasons_Lift, Reasons_Level_Floors, Reasons_Low_Steps, Reasons_Assistance, Reasons_Board_Time,
										Reasons_Wheelchair_Access, Reasons_Other) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)") ){

		$statement->bind_param('ssisssssssssssssssssssssssssss',
								$TC_Member['fName'],$TC_Member['sName'], $Address_ID,$TC_Member['Tel_No'],$TC_Member['Emergency_Name'],$TC_Member['Emergency_Tel'], $TC_Member['Emergency_Relationship'], $TC_Member['DOB'],
								$TC_Member['Details_Wheelchair'], $TC_Member['Details_Wheelchair_Type'], $TC_Member['Details_Wheelchair_Seat'], $TC_Member['Details_Scooter'], $TC_Member['Details_Mobility_Aid'], $TC_Member['Details_Shopping_Trolley'], 
								$TC_Member['Details_Guide_Dog'], $TC_Member['Details_People_Carrier'], $TC_Member['Details_Assistant'], $TC_Member['Details_Travelcard'],$TC_Member['Reasons_Transport'],$TC_Member['Reasons_Bus_Stop'],$TC_Member['Reasons_Anxiety'],
								$TC_Member['Reasons_Door'],$TC_Member['Reasons_Handrails'],$TC_Member['Reasons_Lift'],$TC_Member['Reasons_Level_Floors'],$TC_Member['Reasons_Low_Steps'], $TC_Member['Reasons_Assistance'], $TC_Member['Reasons_Board_Time'],
								$TC_Member['Reasons_Wheelchair_Access'], $TC_Member['Reasons_Other'] );

		

		$statement->execute();
		$statement->store_result();
		
	}
}

function addPickup($mysqli,$Journey_ID,$Pickup){

	$Address_ID = addAddress($mysqli,$Pickup['Address']);

	if( $statement = $mysqli->prepare("INSERT INTO Pickups ( Journey_ID, Note, Address_ID, Time, Return_Note) VALUES ( ?, ?, ?, ?, ?);") ){

		$statement->bind_param("isiss",$Journey_ID, $Pickup['Note'], $Address_ID, $Pickup['Time'], $Pickup['Return_Note']);
		$statement->execute();
		$statement->store_result();
	}
}





function addTCJourneyMember($mysqli,$TC_Journey_Members){

	if( $statement = $mysqli->prepare("INSERT INTO TC_Journey_Members (Journey_ID, TC_Member_ID) VALUES (?, ?);") ){
		$statement->bind_param("ii",$TC_Journey_Members['Journey_ID'],$TCJourneyMember['TC_Member_ID']);
		$statement->execute();
		$statement->store_result();
	}
}


function addVehicleCheckProblem($mysqli,$Vehicle_Check_Problem){

	if( $statement = $mysqli->prepare("INSERT INTO Vehicle_Check_Problems (Vehicle_ID, Problem_Description) VALUES (?, ?);") ){
		$statement->bind_param("is",$Vehicle_Check_Problem['Vehicle_ID'],$Vehicle_Check_Problem['Problem_Description']);
		$statement->execute();
		$statement->store_result();
	}
}
?>