  <html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Count the number of tuples in Groups table</h2>
        <form method="GET" action="Groups.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
        </form>

        <h2>Show all the group names in the Groups table</h2>
        <form method="GET" action="Groups.php"> <!--refresh page when submitted-->
            <input type="hidden" id="showGroupsRequest" name="showGroupsRequest">
            <input type="submit" name="showGroups"></p>
        </form>

        <h2>Show all the cheapest drink in each restaurant from the Provides_AlcoholicDrink table</h2>
        <form method="GET" action="Groups.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getCheapestDrinksRequest" name="getCheapestDrinksRequest">
            <input type="submit" name="getCheapestDrinks"></p>
        </form>

        <h2>Find the visitors who went to all rides</h2>
        <form method="GET" action="Groups.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getVisitorsOnAllRidesRequest" name="getVisitorsOnAllRidesRequest">
            <input type="submit" name="getVisitorsOnAllRides"></p>
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

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table demoTable:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_wuyy2001", "a72671456", "dbhost.students.cs.ubc.ca:1522/stu");

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

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM Groups");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in Groups: " . $row[0] . "<br>";
            }

        }

        function handleShowGroupsRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM Groups");

            echo "<br>Retrieved data from Groups Table:<br>";
            echo "<table>";
            echo "<tr><th>Group name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["GROUPNAME"] . "</td></tr>";
            }

            echo "</table>";

        }

        function handleGetCheapestDrinksResquest() {
            global $db_conn;

            $result = executePlainSQL("SELECT RestaurantName, MIN(Price) FROM Provides_AlcoholicDrink GROUP BY RestaurantName");

            echo "<br>Retrieved data from Provides_AlcoholicDrink Table:<br>";
            echo "<table>";
            echo "<tr><th>Restaurant Name</th><th>Cheapest Drink</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["RESTAURANTNAME"] . "</td><td>" . $row["MIN(PRICE)"] . "</td></tr>";
            }

            echo "</table>";
        }

        function handleGetVisitorsOnAllRides() {
            global $db_conn;

            $result = executePlainSQL("SELECT VisitorName FROM Visitor V
                                    WHERE NOT EXISTS ((SELECT R.RideName
                                    FROM Operates_Ride_R2 R) 
                                    MINUS
                                    (SELECT S.RideName
                                    FROM GoesOn S            
                                    WHERE S.TicketNumber = V.TicketNumber))");

            echo "<br>Retrieved data from tables:<br>";
            echo "<table>";
            echo "<tr><th>Visitors who have gone to all rides</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["VISITORNAME"] . "</td></tr>";
            }

            echo "</table>";
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        // function handlePOSTRequest() {
        //     if (connectToDB()) {
        //         if (array_key_exists('resetTablesRequest', $_POST)) {
        //             handleResetRequest();
        //         } else if (array_key_exists('updateQueryRequest', $_POST)) {
        //             handleUpdateRequest();
        //         } else if (array_key_exists('insertQueryRequest', $_POST)) {
        //             handleInsertRequest();
        //         }

        //         disconnectFromDB();
        //     }
        // }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                } else if (array_key_exists('showGroups', $_GET)){
                    handleShowGroupsRequest();
                } else if (array_key_exists('getCheapestDrinks', $_GET)){
                    handleGetCheapestDrinksResquest();
                } else if (array_key_exists('getVisitorsOnAllRides', $_GET)){
                    handleGetVisitorsOnAllRides();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['showGroupsRequest']) || isset($_GET['getCheapestDrinksRequest']) || isset($_GET['getVisitorsOnAllRidesRequest'])) {
            handleGETRequest(); 
        }
		?>
	</body>
</html>
