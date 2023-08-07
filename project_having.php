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
    </head>
	
    <div class="topnav">
    <a href="AmusementPark.php">Amusement Park</a>
        <a href="insert_delete.php">Insert&DeleteRestaurants</a>
		<a href="project_having.php">Projection&HavingShows</a>
    </div>
	
    <body>

        <h2>Projection of Attributes of the Performs_Show_R2 Table</h2>
        <form method="POST" action="project_having.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="projectQueryRequest">

            Start Time: <input type="radio" checked="checked" name="starttime" value="YES" required> Yes
                       <input type="radio" name="starttime" value="NO"> No<br><br/>
			Title: <input type="radio" checked="checked" name="title" value="YES" required> Yes
                       <input type="radio" name="title" value="NO" > No<br><br/>
            Seats: <input type="radio" checked="checked" name="seats" value="YES" required> Yes
                       <input type="radio" name="seats" value="NO"> No<br><br/>
			Groupname: <input type="radio" checked="checked" name="groupname" value="YES" required> Yes
                       <input type="radio" name="groupname" value="NO"> No<br><br/>					   
            <input type="submit" value="Project" name="projectSubmit"></p>
			

			
        </form>
        <hr />

        <h2>Aggregation with Having (GROUP BY GENRE)</h2>
        <p>Please select number of showtimes per day</p>

        <form method="POST" action="project_having.php"> <!--refresh page when submitted-->
            <input type="hidden" id="havingQueryRequest" name="havingQueryRequest">
			At least: <input type="radio" checked="checked" name="having" value="1" required> 1
                   <input type="radio" name="having" value="2"> 2
				   <input type="radio" name="having" value="3"> 3
				   <input type="radio" name="having" value="4"> 4
				   <input type="radio" name="having" value="5"> 5
				   <br><br/>

            <input type="submit" value="Having" name="havingSubmit"></p>
        </form>

        <hr />

        <h2>Show Schedule</h2>
        <form method="GET" action="project_having.php"> <!--refresh page when submitted-->
            <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
            <input type="submit" value="Display" name="displayTuples"></p>
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

        function handleHavingRequest() {
            global $db_conn;

            $num = $_POST['having'];

			$p ="SELECT GENRE, COUNT(*)
				FROM PERFORMS_SHOW_R1 r1, PERFORMS_SHOW_R2 r2
				WHERE r1.TITLE=r2.TITLE
				GROUP BY GENRE
				HAVING COUNT(*) >= ".$num;
			echo $p;
			$result = executePlainSQL($p);
			printResult($result);
            OCICommit($db_conn);
        }

        function handleProjectRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table

			$p="";
			if($_POST['starttime']=="YES"){
				$p="starttime";
			}
			if($_POST['title']=="YES"){
				if($p!=""){
					$p= $p.", title";
				} else {
					$p= "title";
				}
			}
			if($_POST['seats']=="YES"){
				if($p!=""){
					$p= $p.", seats";
				} else {
					$p= "seats";
				}
			}
			if($_POST['groupname']=="YES"){
				if($p!=""){
					$p= $p.", groupname";
				} else {
					$p= "groupname";
				}
			}
			if($p==""){
				echo "<br>No Projection (missing expression).". "<br>";
			} else {
				$p ="SELECT ".$p." FROM PERFORMS_SHOW_R2";
				echo $p;
				$result = executePlainSQL($p);
				printResult($result);
			}
        }

        function handleDisplayRequest() {
            global $db_conn;

			$p = "SELECT STARTTIME, r1.TITLE, GENRE, SEATS, GROUPNAME FROM PERFORMS_SHOW_R1 r1, PERFORMS_SHOW_R2 r2
			WHERE r1.TITLE=r2.TITLE ORDER BY STARTTIME";
			$result = executePlainSQL($p);
			printResult($result);
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if(array_key_exists('havingQueryRequest', $_POST)) {
                    handleHavingRequest();
                } else if (array_key_exists('projectQueryRequest', $_POST)) {
                    handleProjectRequest();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('displayTuples', $_GET)) {
                    handleDisplayRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['havingSubmit']) || isset($_POST['projectSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['displayTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
