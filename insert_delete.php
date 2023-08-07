<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  delete values, and then query for values

  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

  <html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
        <link rel="stylesheet" href="hailey's styling.css">
    </head>

    <div class="topnav">
        <a href="AmusementPark.php">Amusement Park</a>
        <a href="insert_delete.php">insert_delete</a>
	<a href="project_having.php">Projection&HavingShows</a>
    </div>

    <body>

        <h3>Insert Values into the RESTAURANT Table</h3>
        <form method="POST" action="insert_delete.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
			<p> Restaurant Name: <input type="text" name="insName"> </p>
            <p> Capacity: <input type="text" name="insNo"> </p>
            
            <input id="submit-button" type="submit" value="Insert" name="insertSubmit"></p>
        </form>

        <hr />

        <h3>Delete a RESTAURANT by name</h3>
        <p>The values are case sensitive and if you enter in the wrong case, the delete statement will not do anything.</p>

        <form method="POST" action="insert_delete.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
            <p> Restaurant Name: <input type="text" name="restaurantname"> </p>

            <input id="submit-button" type="submit" value="Delete" name="deleteSubmit"></p>
        </form>

        <hr />

        <h3>RESTAURANT and PROVIDES_ALCOHOLICDRINK</h3>
        <form method="GET" action="insert_delete.php"> <!--refresh page when submitted-->
            <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
            <input id="submit-button" type="submit" value="Display" name="displayTuples"></p>
        </form>

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP
		include 'printResult.php';

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

        function handleDeleteRequest() {
            global $db_conn;

            $name = $_POST['restaurantname'];
            //$new_name = $_POST['newName'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("DELETE FROM RESTAURANT WHERE restaurantname='" . $name . "'");
            OCICommit($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insName'],
                ":bind2" => $_POST['insNo']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into RESTAURANT values (:bind1, :bind2)", $alltuples);
            OCICommit($db_conn);
        }

        function handleDisplayRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM RESTAURANT");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in RESTAURANT: " . $row[0] . "<br>";
            }

			$result = executePlainSQL("SELECT * FROM RESTAURANT");
			printResult($result);
			
			$result = executePlainSQL("SELECT Count(*) FROM PROVIDES_ALCOHOLICDRINK");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in PROVIDES_ALCOHOLICDRINK: " . $row[0] . "<br>";
            }
			$result = executePlainSQL("SELECT * FROM PROVIDES_ALCOHOLICDRINK");
			printResult($result);
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if(array_key_exists('deleteQueryRequest', $_POST)) {
                    handleDeleteRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('displayTuples', $_GET)) {
                    handledisplayRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['deleteSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['displayTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
