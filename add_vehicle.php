
<html>
    <head>
        <title>WCT Tool | Add Vehicle</title>
        <link rel="icon" type="image/png" href="./img/wct_icon.png">
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

        <script type="text/javascript">
            function submit() {
                form_data = {
                    'Nickname':						document.getElementById('input-Nickname').value,
                    'Licence':						document.getElementById('input-Licence').value,
                    'Make':							document.getElementById('input-Make').value,
                    'Model':						document.getElementById('input-Model').value,
                    'Colour':						document.getElementById('input-Colout').value,
                    'Capacity_Passengers':			document.getElementById('number-Capacity_Passengers').value,
                    'Capacity_Note':				document.getElementById('input-Capacity_Note').value,
                    'Tax_Due':						document.getElementById('date-Tax_Due').value,
                    'MOT_Due':						document.getElementById('date-MOT_Due').value,
                    'Safety_Due':					document.getElementById('date-Safety_Due').value,
                    'Service_Due':					document.getElementById('date-Service_Due').value,
                    'Lift_Service_Due':				document.getElementById('date-Lift_Service_Due').value,
                    'Permit_Number':				document.getElementById('input-Permit_Number').value,
                    'Permit_Expiry':				document.getElementById('date-Permit_Expiry').value
                };
                
                $.ajax({
                    type: "POST",
                    url:"MySQL_Functions.php",
                    data: {
                        'form_type': 'addVehicle',
                        'form_data': form_data
                    },
                    dataType: "json",
                    success: function(returned_data) {
                        $('#result').replaceWith('<div id="result">'+returned_data+'</div>');
                    }
                });
            }

            var i = 0;

            function next(){
                var pages = ['#page1', '#page2', '#page3'];
                var currentPage = $(pages[i]);
                var nextPage = $(pages[i+1]);

                currentPage.animate({
                    height: "toggle"
                }, 500, function(){
                    currentPage.hide();
                    nextPage.animate({
                        height: "toggle"
                    });  
                });

                i = i + 1;

                if (i == pages.length - 1){
                    $('#nextButton').animate({
                        height : "toggle"
                    }, 250, function(){
                        $('#submitButton').show();
                    }); 
                }
            }

            function cancel(){
                window.location.href = "";
            }

            function startScreen(){
                $('#page2').hide();
                $('#page3').hide();
                $('#submitButton').hide();
                var i = 0;
            }

        </script>
    </head>
    <body onload="startScreen()">
        <div id="wctLogo"></div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="journeys.php">Journeys</a>
                    <ul>
                        <li><a href="journeys.php">Manage Journeys</a></li>
                        <li><a href="add_journey.php">Add Journey</a></li>
                    </ul>
                </li>
                <li><a href="groups.php">Groups</a>
                    <ul>
                        <li><a href="manage_groups.php">Manage Groups</a></li>
                        <li><a href="add_group.php">Add group</a></li>
                    </ul>
                </li>
                <li><a href="members.php">Members</a>
                    <ul>
                        <li><a href="manage_members.php">Manage Members</a></li>
                        <li><a href="add_member.php">Add Member</a></li>
                    </ul>
                </li>
                <li><a href="vehicles.php">Vehicles</a>
                    <ul>
                        <li><a href="manage_vehicles.php">Manage Vehicles</a></li>
                        <li><a href="add_vehicle.php">Add vehicle</a></li>
                    </ul>
                </li>
                <li><a href="drivers.php">Drivers</a>
                    <ul>
                        <li><a href="manage_drivers.php">Manage Drivers</a></li>
                        <li><a href="add_driver.php">Add Driver</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="page_wrapper">
            <div id="main">
                <form method="POST" action="">
					<div id="page1">
						<fieldset id="personalDetails">
							<legend>Vehicle Details</legend>
							<table>
								<tr><td><label>Nickname: </label></td><td><input type="text" id="input-Nickname"/> <td></tr>
								<tr><td><label>Registration Number: </label></td><td><input type="text" id="input-Licence"/> <td></tr>
								<tr><td><label>Make: </label></td><td><input type="text" id="input-Make"/> <td></tr>
								<tr><td><label>Model: </label></td><td><input type="text" id="input-Model"/> <td></tr>
								<tr><td><label>Colour: </label></td><td><input type="text" id="input-colour"/> <td></tr>
								<tr><td><label>Passenger seating capacity: </label></td><td><input type="text" id="input-Capacity_Passengers"/> </td></tr>
                                <tr><td><label>Capacity note (other configurations): </label></td><td><input type="text" id="input-Capacity_Note"/> </td></tr>
							</table>
						</fieldset>
						<fieldset id="emergencyContact">
							<legend>Maintentance Details</legend>
							<table>
								<tr><td><label>Tax Due: </label></td><td><input type="date" id="date-Tax_Due"/></td></tr>
								<tr><td><label>MOT Due: </label></td><td><input type="date" id="date-MOT_Due"/></td></tr>
								<tr><td><label>Safety Inspection Due:</label></td><td><input type="text" id="date-Safety_Due"/></td></tr>
		                        <tr><td><label>Service Due: </label></td><td><input type="text" id="date-Service_Due"/> </td></tr>
                                <tr><td><label>Tail Lift Service Due: </label></td><td><input type="text" id="date-Tail_Lift_Service_Due"/> </td></tr>
                                <tr><td><label>Section 19 Permit Number: </label></td><td><input type="text" id="input-Permit_Number"/> </td></tr>
                                <tr><td><label>Section 19 Expiry Date: </label></td><td><input type="date" id="date-Permit_Expiry"/> </td></tr>
                            </table>
						</fieldset>
					</div>
                    <input type="text" name="mobility" id="mobility" style='display:none;'/>
                    </br>
                </form>
				<div id="nextButton" onclick="next()">Next</div>
				<div id="submitButton" onclick="submit();">Submit</div>
				<div id="cancelButton" onclick="cancel()">Cancel</div>
				<div id='result'></div> 
            </div>
        </div>
    </body>
</html>