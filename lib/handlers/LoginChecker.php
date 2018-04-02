<?php

/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 3/6/2018
 * Time: 11:56 PM
 */

if (!isset($_SESSION)){
	session_start();
}

class LoginChecker {

	protected static $logUser;
	protected static $logAccountType;
	protected static $logAccountId;


	public static function getUsername(): string {
		return self::$logUser;
	}

	public static function getAccountId(): string {
		return self::$logAccountId;
	}

	public static function getAccountType(): string {
		return self::$logAccountType;
	}
	public static function getBranchId(): int {
		$sql = "SELECT cb.id FROM `users` u, `employee_details` emp, `company_branches` cb
            	WHERE emp.branch_id = cb.id AND emp.user_id=u.id AND u.id=". self::$logAccountId;
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);
		return mysqli_fetch_array($query)["id"];
	}

	public static function getBranchName(): string {
		$sql = "SELECT cb.location FROM `users` u, `employee_details` emp, `company_branches` cb
            	WHERE emp.branch_id = cb.id AND emp.user_id=u.id AND u.id=". self::$logAccountId;
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);
		return mysqli_fetch_array($query)["location"];
	}

	// Start Function List
	public static function isLogin(): bool {

		if (isset($_SESSION["username"]) && isset($_SESSION["type"]) && isset($_SESSION["id"])) {
			self::$logUser = $_SESSION['username'];
			self::$logAccountType = $_SESSION['type'];
			self::$logAccountId = $_SESSION['id'];
			return true;
		}elseif (isset($_COOKIE['username'])&& isset($_COOKIE["type"]) && isset($_COOKIE["id"])) {
			$_SESSION['username'] = $_COOKIE['username'];
			$_SESSION['type'] = $_COOKIE['type'];
			$_SESSION['id'] = $_COOKIE['id'];

			self::$logUser = $_SESSION['username'];
			self::$logAccountType = $_SESSION['type'];
			self::$logAccountId = $_SESSION['id'];
			return true;
		}else{
			return false;
		}
	}

	public static function convertToText(string $str): string {
		switch ($str){
			case 'fc':
				return "Field Collector";
				break;
			case 'bm':
				return "Branch Manager";
				break;
			case 'os':
				return "Office Staff";
				break;
			case 'ad':
				return "Administrator";
				break;
			default:
				return $str;
				break;
		}
	}

	public static function redirector(){
		switch (self::$logAccountType){
			case 'fc':
				header("location: /fc/pay");
				break;
			case 'bm':
				header("location: /bm/customers");
				break;
			case 'os':
				header("location: /os/print");
				break;
			case 'ad':
				header("location: /admin/branches");
				break;
		}
		exit;
	}

	public static function thrower($currentType){
		if (self::$logAccountType != $currentType)
			self::redirector();
	}
}