
<html>
    <head>
        <title>WCT Tool</title>
        <link rel="icon" type="image/png" href="./img/wct_icon.png">
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="table_sorter_api/themes/blue/style.css" type="text/css" media="screen" />
        
		<script type="text/javascript" src="jQuery/jquery-2.1.3.min.js"></script> 
		<script type="text/javascript" src="table_sorter_api/jquery.tablesorter.js"></script> 
		<script type="text/javascript" src="table_sorter_api/jquery.tablesorter.widgets.js"></script>
        
        <script type="text/javascript">
        
            function submit(id){
                window.location = 'show_member_info.php?id=' + id;
            }
            
            function populateMembers(){
                $.ajax({
                    type: "POST",
                    url:"MySQL_Functions.php",
                    data: {
                        'form_type': 'getTCMembers'
                    },
                    dataType: "json",
                    success: function(returned_data) {
                    	var table_body = document.getElementById('table_body');
                        for(var i = 0; i < returned_data.length; i++) {
							var id = returned_data[i]['TC_Member_ID'];
							
							var row = table_body.insertRow();
							row.id = ('TC_Member_ID' + id);
							
							var cell = row.insertCell();
							cell.innerHTML = returned_data[i]['fName'];
							var cell = row.insertCell();
							cell.innerHTML = returned_data[i]['sName'];
							var cell = row.insertCell();
							cell.innerHTML = returned_data[i]['Post_Code'];

 							var button = row.insertCell();
 							button.innerHTML = '<div class="button" id="view-' + id + '" onclick="submit(' + id + ')">View</div>';
                        }
                        $("table").tablesorter(); 
                        $("table").trigger("update"); 
                    }
                });
            }
        </script>
    </head>
    <body onload='populateMembers()'>
        <div id="wctLogo"></div>
        <?php include 'nav.php' ?>
        <div id="page_wrapper">
			<div id="main">
				<div>
					<table cellspacing="1" class="tablesorter">
						<thead>
							<tr>
								<th>Forename</th>
								<th>Surname</th>
								<th>Post Code</th>
							</tr>
						</thead>
						<tbody id='table_body'>
						</tbody>
					</table>
				</div>
			</div>
        </div>
    </body>
</html>