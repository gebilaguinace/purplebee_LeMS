<?php
//0xPathList==START
Route::post("/tryLogin", function($post){
	$connect = MySqlLeaf::getCon();

	//then start the session
	session_start();

	//set sa variable ang username and pass
	@$un = $post['username'];
	@$pw = $post['password'];

	// To be updated
	@$error = $_GET['error'];

	//if else kung may laman na or nka set na
	if (isset($un) && isset($pw)) {
		//query means check the database
		$query = mysqli_query($connect, "SELECT * FROM users WHERE username = '$un' ");
		$fetch = mysqli_fetch_array($query); //to use the data in the database


		// Bibilangun ang result
		if (mysqli_num_rows($query) > 0) {
			if ($pw == $fetch['password']) {
				$dbEmpType = $fetch["emptype"];
				$dbEmpId = $fetch["id"];

				$_SESSION["username"] = $un;
				$_SESSION["type"] = $dbEmpType;
				$_SESSION["id"] = $dbEmpId;

				// Cookie Insert
				setcookie( "username", $un, time() + (10 * 365 * 24 * 60 * 60));
				setcookie( "type", $dbEmpType, time() + (10 * 365 * 24 * 60 * 60));
				setcookie( "id", $dbEmpId, time() + (10 * 365 * 24 * 60 * 60));

			} else {
				FlashCard::setFlashCard("errorPassword");
			}
		} else{
			FlashCard::setFlashCard("errorUsername");
		}

		// Redirect the page
		header("location: /");
		exit;
	}
});

// Logout
Route::get("/logout", function(){

	if (LoginChecker::isLogin()){
		setcookie("username",null, -1,'/');
		setcookie("type",null, -1,'/');
		setcookie("id",null, -1,'/');
		session_destroy();
	}
	header("location: /");
	exit;
});

// Index Page
Route::get("/", function(){

	// Kapag naka login na ilipat sya sa nav interface
	if(LoginChecker::isLogin()){
		// Redirect to Login, kapag dae pa na login.
		LoginChecker::redirector();
		exit;
	}else{
		Route::render("index.leaf", array(
			"hasFlashCard" => FlashCard::hasFlashCard(),
			"flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : ""
		));
	}
});

Route::get("/admin/accounts", function(){
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else{
		// Restrict other user from accessing this area
		LoginChecker::thrower("ad");

		$query = mysqli_query(MySqlLeaf::getCon(), "SELECT * FROM `company_branches`");

		$branchList = array(); // Container kang data na hali sa database...
		while($row = mysqli_fetch_array($query)){
			// Add array inside the arrays
			array_push($branchList, $row);
		}

		// Render the View Interfce
		Route::render("account_list.leaf", array(
			"hasFlashCard" => FlashCard::hasFlashCard(),
			"flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
			"accountType" => LoginChecker::convertToText(LoginChecker::getAccountType()),
			"branchList" => $branchList
		));
	}
});

Route::post("/accountHandler", function($data){
	if (LoginChecker::isLogin() && LoginChecker::getAccountType() == "ad"){

		@ $username =   $data["username"];
		@ $password =   $data["password"];
		@ $fname =      $data["fname"];
		@ $lname =      $data["lname"];
		@ $emptype =    $data["emptype"];
		@ $branch =     $data["branch"];
		@ $deleteID =   $data["deleteID"];
		@ $action =     $data["action"];
		@ $id =         $data["id"];

		// Add/ Update Account
		if(isset($username) && isset($password) && isset($password) && isset($fname) && isset($lname) && isset($emptype) && isset($branch) && isset($id)){
			if ($id == ""){
				$sql = "INSERT INTO `users`(`username`, `password`, `emptype`) VALUES ('$username','$password','$emptype');";
				$query = mysqli_query(MySqlLeaf::getCon(), $sql);

				if($query === true){
					$user_id = mysqli_insert_id(MySqlLeaf::getCon());
					$sql = "INSERT INTO `employee_details`(`fname`, `lname`, `branch_id`, `user_id`) VALUES ('$fname','$lname', $branch, $user_id);";
					$query = mysqli_query(MySqlLeaf::getCon(), $sql);

					if($query === true)
						FlashCard::setFlashCard("successAdd");
					else
						FlashCard::setFlashCard("errorDetail");

				}else{
					FlashCard::setFlashCard("errorAdd");
				}

			}else{
				$sql = "UPDATE `users` SET `username`='$username',`password`='$password',`emptype`='$emptype' WHERE `id`='$id'";
				$query = mysqli_query(MySqlLeaf::getCon(), $sql);

				if($query === true){
					$sql = "UPDATE `employee_details` SET `fname`='$fname',`lname`='$lname',`branch_id`='$branch' WHERE `user_id`='$id'";
					$query = mysqli_query(MySqlLeaf::getCon(), $sql);

					if($query === true)
						FlashCard::setFlashCard("successUpdate");
					else
						FlashCard::setFlashCard("errorDetail");
				}else{
					FlashCard::setFlashCard("errorAdd");
				}

			}

			// Reload the webpage
			header("location: /admin/accounts");
			exit;

		}

		// Edit Account
		if (isset($action) && isset($id)){
			$sql = "SELECT u.id, u.username, u.password, u.emptype, b.id AS branch_id, emp.fname, emp.lname
                    FROM `users` u,
	                      `employee_details` emp,
	                      `company_branches` b
                	WHERE emp.branch_id = b.id
	                      AND u.emptype!='ad'
	                      AND u.id = $id";

			$query = mysqli_query(MySqlLeaf::getCon(), $sql);
			$result = mysqli_fetch_array($query);

			echo json_encode($result);
			mysqli_free_result($query);
			exit;

		}

		// Delete Account
		if(isset($deleteID)){
			$sql = "DELETE FROM `users` WHERE `id`='$deleteID';";
			$query = mysqli_query(MySqlLeaf::getCon(), $sql);

			if($query === true){
				FlashCard::setFlashCard("successDelete");
			}else{
				FlashCard::setFlashCard("errorDelete");
			}

			header("location: /admin/accounts");
			exit;
		}
	}
});

Route::post("/list_accounts", function($requestData){
	// Restrict this area to be accessable by an admin user only.
	if (LoginChecker::isLogin() && LoginChecker::getAccountType() == "ad"){

		// datatable column index  => database column name
		$columns = array(
			0 => 'id',
			1 => 'fname',
			2 => 'lname',
			3 => 'username',
			4 => 'password',
			5 => 'emptype',
			6 => 'location'
		);

		// getting total number records without any search
		$query=mysqli_query(MySqlLeaf::getCon(),
			"SELECT u.id FROM `users` u, `employee_details` emp, `company_branches` cb
            	WHERE emp.branch_id = cb.id AND emp.user_id=u.id AND u.emptype!='ad'"
		);
		$totalData = mysqli_num_rows($query);

		$sql = "SELECT 	u.id,
                        emp.fname,
                        emp.lname,
                        u.username,
                        u.password,
                        u.emptype,
                        cb.location 
                FROM `users` u, `employee_details` emp, `company_branches` cb
                    WHERE emp.branch_id = cb.id AND emp.user_id=u.id AND u.emptype!='ad'";

		// Getting records as per search parameters
		if( !empty($requestData['search']['value']) )
			$sql.=" AND u.username LIKE '".$requestData['search']['value']."%'";

		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		$data = array();

		while( $row=mysqli_fetch_array($query) ) {  // preparing an array
			$nestedData=array();

			$nestedData[] = $row["id"];
			$nestedData[] = $row["fname"];
			$nestedData[] = $row["lname"];
			$nestedData[] = $row["username"];
			$nestedData[] = $row["password"];
			$nestedData[] = LoginChecker::convertToText($row["emptype"]);
			$nestedData[] = $row["location"];
			$nestedData[] = $row["id"];

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);
		mysqli_free_result($query);

	}else{
		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval(0),  // total number of records
			"recordsFiltered" => intval(0), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => array()   // total data array
		);
	}

	echo json_encode($json_data);
	exit;

});

Route::get("/admin/branches", function(){
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else{
		// Restrict other user from accessing this area
		LoginChecker::thrower("ad");

		// Render the View Interfce
		Route::render("branch_list.leaf", array(
			"hasFlashCard" => FlashCard::hasFlashCard(),
			"flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
			"accountType" => LoginChecker::convertToText(LoginChecker::getAccountType())
		));
	}
});
Route::get("/admin/password", function(){
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else{
		// Restrict other user from accessing this area
		LoginChecker::thrower("ad");

		// Render the View Interfce
		Route::render("adminpass.leaf", array(
			"hasFlashCard" => FlashCard::hasFlashCard(),
			"flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
			"accountType" => LoginChecker::convertToText(LoginChecker::getAccountType())
		));
	}
});

Route::post("/branchHandler", function($data){
	// Restrict this area to be accessable by an admin user only.
	if (LoginChecker::isLogin() && LoginChecker::getAccountType() == "ad"){

		@$location  = $data["location"];
		@$action    = $data["action"];
		@$id        = $data["id"];
		@$deleteID  = $data["deleteID"];

		// Select branches - For Edit Functionality
		if (isset($action) && isset($id)){
			$query = mysqli_query(MySqlLeaf::getCon(),"SELECT `location` FROM `company_branches` WHERE id='$id' LIMIT 1");
			$result = mysqli_fetch_array($query);

			echo json_encode($result);
			mysqli_free_result($query);

			exit;
		}

		// Delete Functionality
		if(isset($deleteID)){
			$sql = "DELETE FROM `company_branches` WHERE `id`='$deleteID'";
			$query = mysqli_query(MySqlLeaf::getCon(), $sql);

			if($query === true)
				FlashCard::setFlashCard("successDelete");
			else
				FlashCard::setFlashCard("errorDelete");

			// Reload the webPage
			header("location: /admin/branches");
			exit;
		}

		// Add/ Update Branch Functionality
		if (isset($location) && isset($id)){

			// Make sure the ID is defined from the form
			if ($id == ""){
				$sql = "INSERT INTO `company_branches`(`location`) VALUES ('$location')";
				$query = mysqli_query(MySqlLeaf::getCon(), $sql);

				if ($query === true)
					FlashCard::setFlashCard("successAdd");
				else
					FlashCard::setFlashCard("errorAdd");

			}else{
				$sql = "UPDATE `company_branches` SET `location`='$location' WHERE `id`='$id'";
				$query = mysqli_query(MySqlLeaf::getCon(), $sql);

				if($query === true)
					FlashCard::setFlashCard("successUpdate");
				else
					FlashCard::setFlashCard("errorAdd");
			}

			// Reload the webPage
			header("location: /admin/branches");
			exit;
		}

	}
});

Route::post("/list_branches", function($requestData){
	// Restrict this area to be accessable by an admin user only.
	if (LoginChecker::isLogin() && LoginChecker::getAccountType() == "ad"){

		// datatable column index  => database column name
		$columns = array(
			0 => 'location',
			1 => 'emp_count',
			2 => 'cust_count',
			3 => 'loan_proceed',
			4 => 'gross_loan',
		);

		// getting total number records without any search
		$query=mysqli_query(MySqlLeaf::getCon(),
			"SELECT cb.id FROM company_branches cb;"
		);
		$totalData = mysqli_num_rows($query);

		$sql = "SELECT 	cb.id as id,
                    cb.location as location,
                    COUNT(DISTINCT  ed.id) as emp_count,
                    COUNT(DISTINCT cust.id)  as cust_count,
                    IFNULL(SUM(DISTINCT lend.amount), 0) as loan_proceed,
                    IFNULL(SUM(DISTINCT pay.amount), 0) as gross_loan
                FROM company_branches cb
                    LEFT JOIN employee_details ed
                        ON(cb.id=ed.branch_id)
                    LEFT JOIN customer cust
                        ON(cb.id=cust.branch_id)
                    LEFT JOIN lending lend
                        ON(cust.id=lend.customer_id)
                    LEFT JOIN payment_history pay
                        ON(lend.id=pay.lending_id)
                    GROUP BY cb.id";

// getting records as per search parameters
		if( !empty($requestData['search']['value']) )
			$sql.=" WHERE location LIKE '".$requestData['search']['value']."%'";

		$query = mysqli_query(MySqlLeaf::getCon(), $sql);
		$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		$data = array();

		while( $row=mysqli_fetch_array($query) ) {  // preparing an array
			$nestedData=array();

			$nestedData[] = $row["location"];
			$nestedData[] = $row["emp_count"];
			$nestedData[] = $row["cust_count"];
			$nestedData[] = $row["loan_proceed"];
			$nestedData[] = $row["gross_loan"];
			$nestedData[] = $row["id"];

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);
		mysqli_free_result($query);

	}else{
		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval(0),  // total number of records
			"recordsFiltered" => intval(0), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => array()   // total data array
		);
	}

	echo json_encode($json_data);
	exit;

});

Route::get("/bm/customers", function(){
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else{
		// Restrict other user from accessing this area
		LoginChecker::thrower("bm");

		// Render the View Interfce
		Route::render("customer_list.leaf", array(
			"hasFlashCard" => FlashCard::hasFlashCard(),
			"flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
			"accountType" => LoginChecker::convertToText(LoginChecker::getAccountType()),
			"branchName" => LoginChecker::getBranchName()
		));
	}
});

Route::post("/customerHandler", function($data){
	if (LoginChecker::isLogin() && LoginChecker::getAccountType() == "bm"){
		@ $makerName    = $data["makerName"];
		@ $comakerName  = $data["comakerName"];
		@ $company      = $data["company"];
		@ $address      = $data["address"];
		@ $id           = $data["id"];
		@ $action       = $data["action"];
		@ $deleteID     = $data["deleteID"];

		// Lending Variables
		@ $lend_id = $data["lend_id"];
		@ $amount = $data["amount"];
		@ $loanType = $data["loanType"];
		@ $monthTerm = $data["monthTerm"];
		@ $firstDueDate = $data["firstDueDate"];
		@ $payrollDates = $data["payrollDates"];

		// Lend Action and Monthly Debt Computation
		if (isset($lend_id) && isset($amount) && isset($loanType) && isset($monthTerm) && isset($firstDueDate) && isset($payrollDates)){
			function getInterestRate($lt){
				switch ($lt){
					case 'bl':  $interestRate = 10;  break; // Business Loan
					case 'sl':  $interestRate = 6;   break; // Salary Loan
					case 'cl':  $interestRate = 8;   break; // Cash Loan
					case 'hl':  $interestRate = 2.5; break; // Honorarium Loan
					case 'gl':  $interestRate = 2.5; break; // Government Loan
					case 'pl':  $interestRate = 2.5; break; // Pension Loan
					default:    $interestRate = 0;   break; // No Selected
				}

				return $interestRate;
			}

			$date = date_parse_from_format("Y-m-d", $firstDueDate);
			$month = $date["month"];
			$year = $date["year"];

			// Computation
			$monthlyDue = ceil($amount/$monthTerm);
			$decimalInterestRate = getInterestRate($loanType) * 0.01;
			$additionalCost = ceil($monthlyDue * $decimalInterestRate);
			$monthlyDuewInterest = $monthlyDue + $additionalCost;

			$sql = "INSERT INTO `lending`(`amount`, `loantype`, `monthterm`, `fdd`, `payroll_dates`, `customer_id`)
					VALUES ('$amount','$loanType','$monthTerm','$firstDueDate','$payrollDates','$lend_id')";

			$query = mysqli_query(MySqlLeaf::getCon(), $sql);

			if ($query === true){
				FlashCard::setFlashCard("successLend");
				$parentID =  mysqli_insert_id(MySqlLeaf::getCon());

				$sql = "INSERT INTO `debt`(`amount`, `balance`, `month`, `year`, `lending_id`)
						VALUES ";

				$lastMonth = $month;
				for($i=0; $i < $monthTerm; $i++){
					if ($i != 0){
						if ($lastMonth < 12){
							$lastMonth++;
						} else{
							$lastMonth = 1;
							$year++;
						}
					}

					$sql .= "('$monthlyDuewInterest', '$monthlyDuewInterest', '$lastMonth', '$year', '$parentID')";

					if ($i+1 < $monthTerm) 	$sql .= ",";
					else                    $sql .= ";";
				}

				$query = mysqli_query(MySqlLeaf::getCon(), $sql);

				// Update Customer List
				$sql_ = "UPDATE `customer` SET `status`='Unpaid' WHERE `id`='$lend_id'";
				mysqli_query(MySqlLeaf::getCon(), $sql_);

				if ($query === false){
					FlashCard::setFlashCard("errorCompute");
				}

			} else{
				FlashCard::setFlashCard("errorLend");
			}
			header("location: /bm/customers");
			exit;



		}

		// Add/ Update Customer
		if (isset($makerName) && isset($comakerName) && isset($company) && isset($address) && isset($id)) {
			// Make sure the ID is defined from the form
			if ($id == "") {
				$sql = "INSERT INTO `customer`	(`maker_name`, `comaker_name`, `company`, `address`, `branch_id`)
								VALUES 			('$makerName','$comakerName','$company','$address'," .LoginChecker::getBranchId(). ")";
				$query = mysqli_query(MySqlLeaf::getCon(), $sql);

				if ($query === true)
					FlashCard::setFlashCard("successAdd");
				else
					FlashCard::setFlashCard("errorAdd");

				header("location: /bm/customers");
				exit;

			}else{
				$sql = "UPDATE `customer` SET 	`maker_name`='$makerName',`comaker_name`='$comakerName',
												`company`='$company',`address`='$address'
										  WHERE `id`='$id'";
				$query = mysqli_query(MySqlLeaf::getCon(), $sql);

				if ($query === true)
					FlashCard::setFlashCard("successEdit");
				else
					FlashCard::setFlashCard("errorAdd");

				header("location: /bm/customers");
				exit;
			}
		}

		// Edit Customer
		if (isset($action) && isset($id)){
			$sql = "SELECT  `maker_name`, `comaker_name`, `company`, `address` FROM `customer` WHERE `id`='$id'";
			$query = mysqli_query(MySqlLeaf::getCon(), $sql);
			$result = mysqli_fetch_array($query);

			echo json_encode($result);
			mysqli_free_result($query);

			exit;
		}

		// Delete Customer
		if(isset($deleteID)){
			$sql = "DELETE FROM `customer` WHERE `id`='$deleteID'";
			$query = mysqli_query(MySqlLeaf::getCon(), $sql);

			if($query === true)
				FlashCard::setFlashCard("successDelete");
			else
				FlashCard::setFlashCard("errorDelete");

			// Reload the webPage
			header("location: /admin/branches");
			exit;
		}


		// Lend Customer

	}
});

Route::post("/list_customer", function($requestData){
	// Restrict this area to be accessable by an admin user only.
	if (LoginChecker::isLogin() && LoginChecker::getAccountType() == "bm"){

		// datatable column index  => database column name
		$columns = array(
			0 => 'id',
			1 => 'maker_name',
			2 => 'comaker_name',
			3 => 'company',
			4 => 'address',
			5 => 'status'
		);

		// getting total number records without any search
		$query=mysqli_query(MySqlLeaf::getCon(),
			"SELECT `id` FROM `customer` WHERE `branch_id`='" .LoginChecker::getBranchId(). "';"
		);
		$totalData = mysqli_num_rows($query);

		$sql = "SELECT `id`, `maker_name`, `comaker_name`, `company`, `address`, `status` FROM `customer` WHERE `branch_id`='" .LoginChecker::getBranchId(). "'";

		// getting records as per search parameters
		if( !empty($requestData['columns'][0]['search']['value']) ){   // ID
			$sql.=" AND `" .$columns[0]. "` LIKE '".$requestData['columns'][0]['search']['value']."%' ";
		}
		if( !empty($requestData['columns'][1]['search']['value']) ){  // Maker Name
			$sql.=" AND `" .$columns[1]. "` LIKE '".$requestData['columns'][1]['search']['value']."%' ";
		}
		if( !empty($requestData['columns'][2]['search']['value']) ){  // Maker Name
			$sql.=" AND `" .$columns[2]. "` LIKE '".$requestData['columns'][2]['search']['value']."%' ";
		}
		if( !empty($requestData['columns'][3]['search']['value']) ){  // Maker Name
			$sql.=" AND `" .$columns[3]. "` LIKE '".$requestData['columns'][3]['search']['value']."%' ";
		}
		if( !empty($requestData['columns'][4]['search']['value']) ){  // Maker Name
			$sql.=" AND `" .$columns[4]. "` LIKE '".$requestData['columns'][4]['search']['value']."%' ";
		}


		$query = mysqli_query(MySqlLeaf::getCon(), $sql);
		$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ;";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		$data = array();

		while( $row=mysqli_fetch_array($query) ) {  // preparing an array
			$nestedData=array();

			$nestedData[] = $row["id"];
			$nestedData[] = $row["maker_name"];
			$nestedData[] = $row["comaker_name"];
			$nestedData[] = $row["company"];
			$nestedData[] = $row["address"];
			$nestedData[] = $row["status"];
			$nestedData[] = $row["id"];

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);
		mysqli_free_result($query);

	}else{
		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval(0),  // total number of records
			"recordsFiltered" => intval(0), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => array()   // total data array
		);
	}

	echo json_encode($json_data);
	exit;

});

Route::post("/getBalance", function($requestData){

    // Restrict this area to be accessable by an admin user only.
    if (LoginChecker::isLogin() && (LoginChecker::getAccountType() == "bm" || LoginChecker::getAccountType() == "fc")){
        @ $customer_id = $requestData["customer_id"];
        @ $history = $requestData["history"];
		@ $collectionType = $requestData["collection_type"];
        function numToMonthName($num){
            switch ($num){
                case 1: $monthName = "January"; break;
                case 2: $monthName = "February"; break;
                case 3: $monthName = "March"; break;
                case 4: $monthName = "April"; break;
                case 5: $monthName = "May"; break;
                case 6: $monthName = "June"; break;
                case 7: $monthName = "July"; break;
                case 8: $monthName = "August"; break;
                case 9: $monthName = "September"; break;
                case 10: $monthName = "October"; break;
                case 11: $monthName = "November"; break;
                case 12: $monthName = "December"; break;
                default: $monthName = "NaN"; break;
            }

            return $monthName;
        }

        if (isset($customer_id) && isset($history)){
        	if ($history == "lending"){
        		$query = mysqli_query(MySqlLeaf::getCon(),
			        "SELECT `id` FROM `lending` WHERE `customer_id`='$customer_id'"
		        );

		        $totalData = mysqli_num_rows($query);

		        $sql = "SELECT `id`, `loantype`, `amount`, `monthterm` FROM `lending` WHERE `customer_id`='$customer_id'";


		        $query = mysqli_query(MySqlLeaf::getCon(), $sql);
		        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		        $sql .= "ORDER BY `id` DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ;";

		        $query = mysqli_query(MySqlLeaf::getCon(), $sql);

		        $data = array();

		        while( $row=mysqli_fetch_array($query) ) {  // preparing an array
			        $nestedData=array();

			        $nestedData[] = $row["id"];
			        $nestedData[] = $row["loantype"];
			        $nestedData[] = $row["amount"];
			        $nestedData[] = $row["monthterm"];

			        $data[] = $nestedData;
		        }

		        $json_data = array(
			        "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			        "recordsTotal"    => intval( $totalData ),  // total number of records
			        "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			        "data"            => $data   // total data array
		        );
		        mysqli_free_result($query);
	        }

	        if ($history == "payment"){

		        $query = mysqli_query(MySqlLeaf::getCon(),
			        "SELECT pay.id FROM `customer` cust, `lending` lend, `payment_history` pay
                	WHERE cust.id = lend.customer_id AND pay.lending_id = lend.id AND cust.id='$customer_id'"
		        );

		        $totalData = mysqli_num_rows($query);

		        $sql = "SELECT pay.user_id, pay.lending_id, pay.amount, pay.date FROM `customer` cust, `lending` lend, `payment_history` pay
                	WHERE cust.id = lend.customer_id AND pay.lending_id = lend.id AND cust.id='$customer_id'";


		        $query = mysqli_query(MySqlLeaf::getCon(), $sql);
		        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		        $sql .= "ORDER BY `date` DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ;";

		        $query = mysqli_query(MySqlLeaf::getCon(), $sql);

		        $data = array();

		        while( $row=mysqli_fetch_array($query) ) {  // preparing an array
			        $nestedData=array();

			        $nestedData[] = $row["user_id"];
			        $nestedData[] = $row["lending_id"];
			        $nestedData[] = $row["amount"];
			        $nestedData[] = $row["date"];

			        $data[] = $nestedData;
		        }

		        $json_data = array(
			        "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			        "recordsTotal"    => intval( $totalData ),  // total number of records
			        "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			        "data"            => $data   // total data array
		        );
		        mysqli_free_result($query);
	        }
        }
        elseif (isset($collectionType)){
        	if ($collectionType == "this_month"){
		        // datatable column index  => database column name
		        $columns = array(
			        0 => 'lending_id',
			        1 => 'maker_name',
			        2 => 'comaker_name',
			        3 => 'loantype',
			        4 => 'amount',
			        5 => 'balance',
			        6 => 'payroll_dates'
		        );

		        // getting total number records without any search
		        $query=mysqli_query(MySqlLeaf::getCon(),
			        "SELECT debt.lending_id
 						FROM `customer` cust, `lending` lend, `debt` debt
                	WHERE cust.id = lend.customer_id AND debt.lending_id = lend.id AND debt.month<=month(now()) AND debt.year=YEAR(now()) AND debt.status != 'PAID' 
                	GROUP BY debt.lending_id"
		        );

		        $totalData = mysqli_num_rows($query);

		        $sql = "SELECT debt.lending_id, cust.maker_name, cust.comaker_name, lend.loantype, debt.amount, SUM(debt.balance) as balance, lend.payroll_dates
 						FROM `customer` cust, `lending` lend, `debt` debt
                	WHERE cust.id = lend.customer_id AND debt.lending_id = lend.id AND (debt.month<=month(now()) AND debt.year=YEAR(now()) OR debt.year<YEAR(now())) AND debt.status != 'PAID' 
                	GROUP BY debt.lending_id";

		        $query = mysqli_query(MySqlLeaf::getCon(), $sql);


		        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		        $sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ;";
		        $query = mysqli_query(MySqlLeaf::getCon(), $sql);

		        $data = array();

		        while( $row=mysqli_fetch_array($query) ) {  // preparing an array
			        $nestedData=array();

			        $nestedData[] = $row["lending_id"];
			        $nestedData[] = $row["maker_name"];
			        $nestedData[] = $row["comaker_name"];
			        $nestedData[] = $row["loantype"];
			        $nestedData[] = $row["amount"];
			        $nestedData[] = $row["balance"];
			        $nestedData[] = $row["payroll_dates"];
			        $nestedData[] = $row["lending_id"];

			        $data[] = $nestedData;
		        }
		        $json_data = array(
			        "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			        "recordsTotal"    => intval( $totalData ),  // total number of records
			        "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			        "data"            => $data   // total data array
		        );
		        mysqli_free_result($query);
	        }

	        if ($collectionType == "all_pending"){
		        // datatable column index  => database column name
		        $columns = array(
			        0 => 'lending_id',
			        1 => 'maker_name',
			        2 => 'comaker_name',
			        3 => 'loantype',
			        4 => 'amount',
			        5 => 'balance',
			        6 => 'month',
			        7 => 'payroll_dates',
			        8 => 'status'
		        );

		        // getting total number records without any search
		        $query=mysqli_query(MySqlLeaf::getCon(),
			        "SELECT debt.lending_id
							  FROM `customer` cust, `lending` lend, `debt` debt 
						WHERE cust.id = lend.customer_id AND debt.lending_id = lend.id AND debt.status != 'PAID'"
		        );

		        $totalData = mysqli_num_rows($query);

		        $sql = "SELECT debt.lending_id, cust.maker_name, cust.comaker_name, lend.loantype, debt.amount, debt.balance, debt.month, debt.year, lend.payroll_dates, debt.status 
							FROM `customer` cust, `lending` lend, `debt` debt 
						WHERE cust.id = lend.customer_id AND debt.lending_id = lend.id AND debt.status != 'PAID'";

		        $query = mysqli_query(MySqlLeaf::getCon(), $sql);


		        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		        $sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ;";
		        $query = mysqli_query(MySqlLeaf::getCon(), $sql);

		        $data = array();

		        while( $row=mysqli_fetch_array($query) ) {  // preparing an array
			        $nestedData=array();

			        $nestedData[] = $row["lending_id"];
			        $nestedData[] = $row["maker_name"];
			        $nestedData[] = $row["comaker_name"];
			        $nestedData[] = $row["loantype"];
			        $nestedData[] = $row["amount"];
			        $nestedData[] = $row["balance"];
			        $nestedData[] = numToMonthName($row["month"]) . " ". $row["year"]; // Due date
			        $nestedData[] = $row["payroll_dates"];
			        $nestedData[] = $row["status"];
			        $nestedData[] = $row["lending_id"];

			        $data[] = $nestedData;
		        }
		        $json_data = array(
			        "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			        "recordsTotal"    => intval( $totalData ),  // total number of records
			        "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			        "data"            => $data   // total data array
		        );
		        mysqli_free_result($query);
	        }

        }
        elseif (isset($customer_id)){

        	$sql = "SELECT SUM(debt.balance) as bal FROM `customer` cust, `lending` lend, `debt` debt
                	WHERE cust.id = lend.customer_id AND debt.lending_id = lend.id AND cust.id='$customer_id'";

        	$query = mysqli_query(MySqlLeaf::getCon(), $sql);

        	if (mysqli_fetch_array($query)["bal"] == 0){
				$sql = "UPDATE `customer` SET `status`='PAID' WHERE `id`='$customer_id'";
				mysqli_query(MySqlLeaf::getCon(), $sql);
	        }

            // getting total number records without any search
            $query = mysqli_query(MySqlLeaf::getCon(),
                "SELECT lend.id FROM `customer` cust, `lending` lend, `debt` debt
                	WHERE cust.id = lend.customer_id AND debt.lending_id = lend.id AND cust.id='$customer_id';"
            );
            $totalData = mysqli_num_rows($query);

            $sql = "SELECT debt.amount, debt.balance, debt.month, debt.year, debt.status FROM `customer` cust, `lending` lend, `debt` debt
                	WHERE cust.id = lend.customer_id AND debt.lending_id = lend.id AND cust.id='$customer_id'";


            $query = mysqli_query(MySqlLeaf::getCon(), $sql);
            $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
            $sql .= "ORDER BY `debt`.`year` DESC, debt.month DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ;";
            $query = mysqli_query(MySqlLeaf::getCon(), $sql);

            $data = array();

            while( $row=mysqli_fetch_array($query) ) {  // preparing an array
                $nestedData=array();

                $nestedData[] = $row["amount"];
                $nestedData[] = $row["balance"];
                $nestedData[] = numToMonthName($row["month"]) . " ". $row["year"];
                $nestedData[] = $row["status"];

                $data[] = $nestedData;
            }

            $json_data = array(
                "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => intval( $totalData ),  // total number of records
                "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data"            => $data   // total data array
            );
            mysqli_free_result($query);
        }
        else{
            $json_data = array(
                "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => intval(0),  // total number of records
                "recordsFiltered" => intval(0), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data"            => array()   // total data array
            );
        }
    }else{
        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval(0),  // total number of records
            "recordsFiltered" => intval(0), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => array()   // total data array
        );
    }

    echo json_encode($json_data);
    exit;
});

Route::get("/bm/pay", function (){
    // ASDASDasdasd asdas d asASD as
    if (!LoginChecker::isLogin()){
        header("location: /");
        exit;
    }else{
        // Restrict other user from accessing this area
        LoginChecker::thrower("bm");

        // Render the View Interfce
        Route::render("pay.leaf", array(
            "hasFlashCard" => FlashCard::hasFlashCard(),
            "flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
            "accountType" => LoginChecker::convertToText(LoginChecker::getAccountType()),
            "branchName" => LoginChecker::getBranchName()
        ));
    }
});

Route::post("/payHandler", function ($data){
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else{
		if (LoginChecker::getAccountType() == "bm" || LoginChecker::getAccountType() == "fc"){

			@ $customerID = $data["customerID"];
			@ $amount = $data["amount"];

			if (isset($customerID) && isset($amount)) {
				if ($amount > 0) {
					$sql = "SELECT debt.id, debt.balance
 						FROM `customer` cust, `lending` lend, `debt` debt
                	WHERE cust.id = lend.customer_id AND debt.lending_id = lend.id AND debt.status != 'PAID'  AND debt.lending_id='$customerID'";
					$query = mysqli_query(MySqlLeaf::getCon(), $sql);
					$dbBalance = array();
					$newBalance = array();


					// Put the database balance into an array
					while ($row = mysqli_fetch_array($query)){
						$nData = array();
						$nData["id"] = $row["id"];
						$nData["balance"] = $row["balance"];
						$dbBalance[] = $nData;
					}

					function deductAmount($dbBalance,$amount) {
						$debtList = $dbBalance;
						$remainingAmount = $amount;
						$newValues = array();
						// Work on...

						for ($i = 0; $i < count($debtList); $i++) {
							if ($remainingAmount > 0) {
								$remainingAmount = $debtList[$i]["balance"] - $remainingAmount;

								if ($remainingAmount < 0) {
									$remainingBalance = 0;
									$remainingAmount = abs($remainingAmount);
								} else {
									$remainingBalance = $remainingAmount;
									$remainingAmount = 0;
								}

								$newValues[] = array(
									"id" => $debtList[$i]["id"],
									"balance" => $remainingBalance
								);
							} else {
								break;
							}
						}

						return $newValues;
					}

					$newBalance = deductAmount($dbBalance, $amount);

					// Update the database with the new balance
					foreach ($newBalance as $n){
						$id = $n["id"]; $balance = $n["balance"];

						$status = ($balance == 0) ? ", `status`='PAID'" : "";
						$sql = "UPDATE `debt` SET `balance`='$balance' $status WHERE `id`='$id';";
						mysqli_query(MySqlLeaf::getCon(), $sql);
					}

					// Add to Payment History
					$sql = "INSERT INTO `payment_history`(`user_id`, `lending_id`, `amount`, `date`) VALUES ('" .LoginChecker::getAccountId(). "','$customerID','$amount', now())";
					mysqli_query(MySqlLeaf::getCon(), $sql);

					FlashCard::setFlashCard("paymentProcessed");
					header("location: /bm/pay");
					exit;
				}
			}

			exit;
		}

	}
});

Route::get("/bm/password", function (){
    // ASDASDasdasd asdas d asASD as
    if (!LoginChecker::isLogin()){
        header("location: /");
        exit;
    }else {
        // Restrict other user from accessing this area
        LoginChecker::thrower("bm");

        // Render the View Interfce
        Route::render("bmpass.leaf", array(
            "hasFlashCard" => FlashCard::hasFlashCard(),
            "flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
            "accountType" => LoginChecker::convertToText(LoginChecker::getAccountType()),
            "branchName" => LoginChecker::getBranchName()
        ));
    }
});

Route::post("/changePassword", function($data){
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else {
		$pass = $data["currentPass"];
		$pass1 = $data["newPass1"];
		$pass2 = $data["newPass2"];

		if (isset($pass) && isset($pass1) && isset($pass2)){

			if (strlen($pass1) < 8 || strlen($pass2) < 8){
				FlashCard::setFlashCard("8characters");
			}else{
				if ($pass1 != $pass2){
					FlashCard::setFlashCard("passNotMatch");
				}else {
					// Check the database if the password is correct
					$sql = "SELECT `id` FROM `users` WHERE `id`=" .LoginChecker::getAccountId(). " AND `password`='$pass';";
					$query = mysqli_query(MySqlLeaf::getCon(), $sql);

					if (mysqli_num_rows($query) > 0){

						// Update the password in the database
						$sql = "UPDATE `users` SET `password`='$pass1' WHERE `id`='" .LoginChecker::getAccountId(). "';";
						$query = mysqli_query(MySqlLeaf::getCon(), $sql);

						if ($query === true) {
							FlashCard::setFlashCard("successSave");
						}else{
							FlashCard::setFlashCard("errorSave");
						}


					}else{
						FlashCard::setFlashCard("wrongPass");
					}
				}
			}
			if (LoginChecker::getAccountType() == "bm"){
				header("location: /bm/password");
			}elseif (LoginChecker::getAccountType() == "fc"){
				header("location: /fc/password");
			}elseif (LoginChecker::getAccountType() == "ad"){
				header("location: /admin/password");
			}
			exit;
		}else{
			echo "Missing data";
			exit;
		}
	}

});


Route::get("/fc/pay", function (){
	// ASDASDasdasd asdas d asASD as
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else {
		// Restrict other user from accessing this area
		LoginChecker::thrower("fc");

		// Render the View Interfce
		Route::render("pay_fc.leaf", array(
			"hasFlashCard" => FlashCard::hasFlashCard(),
			"flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
			"accountType" => LoginChecker::convertToText(LoginChecker::getAccountType()),
			"branchName" => LoginChecker::getBranchName()
		));
	}
});

Route::get("/fc/password", function (){
	// ASDASDasdasd asdas d asASD as
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else {
		// Restrict other user from accessing this area
		LoginChecker::thrower("fc");

		// Render the View Interfce
		Route::render("fcpass.leaf", array(
			"hasFlashCard" => FlashCard::hasFlashCard(),
			"flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
			"accountType" => LoginChecker::convertToText(LoginChecker::getAccountType()),
			"branchName" => LoginChecker::getBranchName()
		));
	}
});

Route::get("/fc/report", function (){
	// ASDASDasdasd asdas d asASD as
	if (!LoginChecker::isLogin()){
		header("location: /");
		exit;
	}else {
		// Restrict other user from accessing this area
		LoginChecker::thrower("fc");

		$sql = "SELECT cust.maker_name, lend.loantype, pay.amount 
					FROM `customer` cust, `lending` lend, `payment_history` pay 
					WHERE cust.id = lend.customer_id AND DATE(pay.date)=DATE(now()) AND pay.lending_id = lend.id AND pay.user_id='" .LoginChecker::getAccountId(). "';";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);
		$arr = array();

		while ($row = mysqli_fetch_array($query)){
			array_push($arr, $row);
		}

		$getName = mysqli_fetch_array(mysqli_query(MySqlLeaf::getCon(),
			"SELECT `fname`, `lname` FROM `employee_details` WHERE `user_id`='" .LoginChecker::getAccountId(). "'"
		));

		// Render the View Interfce
		Route::render("fcreport.leaf", array(
			"hasFlashCard" => FlashCard::hasFlashCard(),
			"flashCard" => FlashCard::hasFlashCard() ? FlashCard::getFlashCard() : "",
			"accountType" => LoginChecker::convertToText(LoginChecker::getAccountType()),
			"branchName" => LoginChecker::getBranchName(),
			"data" => $arr,
			"name" => $getName["fname"] ." ". $getName["lname"]
		));
	}
});
//0xPathList==END

//0xNotFound==START
Route::notFoundStr("404 Error - The requested URL was not found on the server");

Route::notFoundRender("error.leaf", array(
"error_number" => "404",
"error_title" => "File Not Found",
"error_description" => "The requested URL was not found on the server"
));

////0xNotFound==END