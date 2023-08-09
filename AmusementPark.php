<html>
    <head>
        <title>Amusement Park Planning</title>
        <link rel="stylesheet" href="hailey's styling.css">
    </head>

    <div class="topnav">
    <a href="AmusementPark.php">Amusement Park</a>
    <a href="insert_delete.php">Insert&DeleteRestaurants</a>
    <a href="project_having.php">Projection&HavingShows</a>
    <a href="updatejoin.php">Update&Join</a>
    </div>

    <body>
        <div>
        <!-- <h3>Select restaurants with specific capacity range</h3>
        <form method="GET" action="AmusementPark.php"> 
            <input type="hidden" id="getRestaurantsRequest" name="getRestaurantsRequest">
        <p> Capacity greater than: <input type="text" name="lowerBound"> and smaller than: <input type="text" name="upperBound"> </p> 
            <P> <input type="text" name="lowerBound"> < Capacity < <input type="text" name="upperBound"></p>
            <input id="submit-button" type="submit" name="getRestaurants" value="Search"></p>
        </form> -->

        <h3>Selection</h3>
        <form method="GET" action="AmusementPark.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getRestaurantsRequest" name="getRestaurantsRequest">
            <!-- <p> Capacity greater than: <input type="text" name="lowerBound"> and smaller than: <input type="text" name="upperBound"> </p> -->
            <!-- <P> <input type="text" name="lowerBound"> < Capacity < <input type="text" name="upperBound"></p> -->
            <p> 
            Select  
            <select name="attributeName" id="attributeName">
            <option value="RestaurantName">Restaurant name</option>
            <option value="RideName">Ride name</option>
            </select>

            From 
            <select name="tableName" id="tableName">
            <option value="Restaurant">Restaurant</option>
            <option value="Operates_Ride_R2">Ride</option>
            </select>
            </p>

            <p>
            Where
            Capacity > <input type="text" name="conditionName">
            </p>

            <input id="submit-button" type="submit" name="getRestaurants" value="Search"></p>
        </form>

        <hr />

        <h3>Show Operates_Ride_R2 names and capacity</h3>
        <form method="GET" action="AmusementPark.php"> <!--refresh page when submitted-->
            <input type="hidden" id="showRidesRequest" name="showRidesRequest">
            <input id="submit-button" type="submit" name="showRides" value="Display"></p>
        </form>

        <hr />

        <h3>Show restaurant names and capacity</h3>
        <form method="GET" action="AmusementPark.php"> <!--refresh page when submitted-->
            <input type="hidden" id="showRestaurantsRequest" name="showRestaurantsRequest">
            <input id="submit-button" type="submit" name="showRestaurants" value="Display"></p>
        </form>

        <hr />

        <h3>Show all the cheapest drinks in each restaurant in the park</h3>
        <form method="GET" action="AmusementPark.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getCheapestDrinksRequest" name="getCheapestDrinksRequest">
            <input id="submit-button" type="submit" name="getCheapestDrinks" value="Display"></p>
        </form>

        <hr />

        <h3>Find the visitors who went to all rides</h3>
        <form method="GET" action="AmusementPark.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getVisitorsOnAllRidesRequest" name="getVisitorsOnAllRidesRequest">
            <input id="submit-button" type="submit" name="getVisitorsOnAllRides" value="Search"></p>
        </form>

        </div>
        

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
            $db_conn = OCILogon("ora_mdow", "a75243949", "dbhost.students.cs.ubc.ca:1522/stu");

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


        function handleGetRestaurantsRequest() {
            // global $db_conn;

            // $c1 = $_GET['lowerBound'];
            // $c2 = $_GET['upperBound'];

            // $result = executePlainSQL("SELECT RestaurantName FROM Restaurant WHERE Capacity>$c1 AND Capacity<$c2");

            // echo "<tr>Retrieved data from table:</tr>";
            // echo "<table>";
            // echo "<tr><th>Restaurant name</th></tr>";

            // while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            //     echo "<tr><td>" . $row["RESTAURANTNAME"] . "</td></tr>";
            // }
            // echo "</table>";
            global $db_conn;

            $c1 = $_GET['attributeName'];
            $c2 = $_GET['tableName'];
            $c3 = $_GET['conditionName'];

            $result = executePlainSQL("SELECT $c1 FROM $c2 WHERE Capacity>$c3");

            echo "<tr>Retrieved data from table:</tr>";
            echo "<table>";
            // echo "<tr><th>Restaurant name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>";
            }
            echo "</table>";

        }


        function handleShowRestaurantsRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM Restaurant");

            echo "<tr>Retrieved data from table:</tr>";
            echo "<table>";
            echo "<tr><th>Restaurant Table</th><th>Capacity</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
        }
        

        function handleGetCheapestDrinksResquest() {
            global $db_conn;

            $result = executePlainSQL("SELECT RestaurantName, MIN(Price) FROM Provides_AlcoholicDrink GROUP BY RestaurantName");

            echo "<br>Retrieved data from table:<br>";
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

            echo "<br>Retrieved data from table:<br>";
            echo "<table>";
            echo "<tr><th>Visitors who have gone to all rides</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["VISITORNAME"] . "</td></tr>";
            }

            echo "</table>";
        }

        function handleShowRidesRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM Operates_Ride_R2");

            echo "<tr>Retrieved data from table:</tr>";
            echo "<table>";
            echo "<tr><th>Operates_Ride_R2 Table</th><th>Capacity</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertVisitorQueryRequest', $_POST)) {
                    handleInsertVisitorRequest();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('getRestaurants', $_GET)) {
                    handleGetRestaurantsRequest();
                } else if (array_key_exists('showRestaurants', $_GET)){
                    handleShowRestaurantsRequest();
                } else if (array_key_exists('getCheapestDrinks', $_GET)){
                    handleGetCheapestDrinksResquest();
                } else if (array_key_exists('getVisitorsOnAllRides', $_GET)){
                    handleGetVisitorsOnAllRides();
                } else if (array_key_exists('showRides', $_GET)){
                    handleShowRidesRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertVisitorSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['showRidesRequest']) || isset($_GET['getRestaurantsRequest']) || isset($_GET['showRestaurantsRequest']) || isset($_GET['getCheapestDrinksRequest']) || isset($_GET['getVisitorsOnAllRidesRequest'])) {
            handleGETRequest(); 
        }
		?>
	</body>
</html>
