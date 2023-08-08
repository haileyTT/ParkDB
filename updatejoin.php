<html>
    <head>
        <title>Amusement Park Planning</title>
    </head>
    <div class="topnav">
    <a href="AmusementPark.php">Amusement Park</a>
        <a href="insert_delete.php">Insert&DeleteRestaurants</a>
		<a href="project_having.php">Projection&HavingShows</a>
	    <a href="updatejoin.php">Update&Join</a>
    </div>
    <body>

        <h3>Update Attribute of Performs Show</h3>
        <form method="POST" action="updatejoin.php"> <!--refresh page when submitted-->
        <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
        Number of Old Seats: <input type="text" name="oldName"> <br /><br />
        Number of New Seats : <input type="text" name="newName"> <br /><br />
        Old Genre: <input type="text" name="oldGenre"> <br /><br />
        New Genre: <input type="text" name="newGenre"> <br /><br />
        <input type="submit" value="Update" name="updateSubmit"></p>
</form>

        <h3>Show Performs_Show_R1 Table</h3>
        <form method="GET" action="updatejoin.php"> <!--refresh page when submitted-->
            <input type="hidden" id="showGroupsRequest" name="showGroupsRequest">
            <input id="submit-button" type="submit" name="showGroups" value="Display"></p>
        </form>

        <h3>Show Performs_Show_R2 Table</h3>
        <form method="GET" action="updatejoin.php"> <!--refresh page when submitted-->
            <input type="hidden" id="showGroups2Request" name="showGroups2Request">
            <input id="submit-button" type="submit" name="showGroups2" value="Display"></p>
        </form>

        <h3>Find the types of rides with capacity thats greater than the average capacity of all the ride types </h3>
        <form method="POST" action="updatejoin.php"> <!--refresh page when submitted-->
            <input type="hidden" id="minSeatsRequest" name="minSeatsRequest">
            <input id="submit-button" type="submit" name="minSeats" value="Display"></p>
        </form>

    
        <h3>Find the visitors who have been on the inputted ride</h3>
        <form method="POST" action="updatejoin.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getVisitorsOnAllRidesRequest" name="getVisitorsOnAllRidesRequest">
             RideName: <input type="text" name="rideName"> <br /><br />
            <input id="submit-button" type="submit" name="getVisitorsOnAllRides" value="Search"></p>
        </form>
        

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement. Referred to oracle-test.php and user ihciem on GitHub's code printResult.php
			$header = false;

			echo "<table>";
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				$numKeys = array_filter(array_keys($row), function($numKey) {return is_int($numKey);});
				$assocKeys = array_filter(array_keys($row), function($assocKey) {return is_string($assocKey);});

				// output header/column/attribute names
				if (!$header) {
					echo "<thead><tr>";
					foreach ($assocKeys as $key) {
						echo '<th>' . ($key !== null ? htmlentities($key, ENT_QUOTES) : '') . str_repeat("&nbsp;", 5) . '</th>';
					}
					echo "</tr></thead>";
					$header = true;
				}

				// output all the data rows.
				echo '<tr>';
				foreach ($numKeys as $index) {
					echo "<td>" . $row[$index] . str_repeat("&nbsp;", 5) . "</td>";
				}
				echo '</tr>';
			}
			echo "</table>";
		}

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_sophia54", "a55661094", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }
        

        function handleUpdateRequest() {
            global $db_conn;
            
            
            $oldN = $_POST['oldName'];
            $newN = $_POST['newName'];

            $oldGenre = $_POST['oldGenre'];
            $newGenre = $_POST['newGenre'];
            
            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE Performs_Show_R2 SET Seats =" . $newN . " WHERE Seats=" . $oldN . "");
            executePlainSQL("UPDATE Performs_Show_R1 SET Genre ='" . $newGenre . "'WHERE Genre='" . $oldGenre . "'");
        
            
            
            OCICommit($db_conn);
            }
            

            function handleGetVisitorsOnAllRides() {
                global $db_conn;
                
                
                $rn = $_POST['rideName'];
                
                $result = executePlainSQL("SELECT VisitorName
                FROM Visitor v, GoesOn g
                WHERE RideName = '" . $rn . "' AND v.TicketNumber = g.TicketNumber");

            echo "<h2>Visitors who have gone on a ride with entered Ride Name:</h2>";

          printResult($result);
             
                }
            
        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM Groups");

            echo "<tr>Retrieved data from table:</tr>";
            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in Groups: " . $row[0] . "</br>";
            }
        }

        function handleShowShowsRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM Performs_Show_R1");

           printResult($result);
        }

        function handleShowShowsRequest2() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM Performs_Show_R2");

           printResult($result);
        }
        function minSeats() {
            global $db_conn;

                $result = executePlainSQL("SELECT RideType
                FROM Operates_Ride_R2 r2
                GROUP BY RideType
                HAVING avg(Capacity) > (SELECT avg(Capacity)
                                         FROM Operates_Ride_R2)
                ");
                printResult($result);
}

       

            
        
     

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('getVisitorsOnAllRides', $_POST)) {
                    handleGetVisitorsOnAllRides();
                }
                else if (array_key_exists('minSeats', $_POST)) {
                    minSeats();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                } else if (array_key_exists('showGroups', $_GET)){
                    handleShowShowsRequest();
                } 
                else if (array_key_exists('showGroups2', $_GET)){
                    handleShowShowsRequest2();
                } 

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['getVisitorsOnAllRidesRequest']) ||  isset($_POST['minSeatsRequest'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['showGroupsRequest'])||isset($_GET['showGroups2Request']) ) {
            handleGETRequest(); 
        }
		?>
	</body>
</html>
