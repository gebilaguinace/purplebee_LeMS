<?php

class Route {
	private static $uri;
	private static $pathParts;
	public static $pathExist = false;
	public static $requestMethod = "GET";
	public static $domainName = "";
	public static $error = false;

	/**
	 * @return string
	 */
	public static function getDomainName(): string {
		return self::$domainName;
	}

	/**
	 * @return string
	 */
	public static function getRequestMethod(): string {
		return self::$requestMethod;
	}

	/**
	 * @param string $requestMethod
	 */
	public static function setRequestMethod(string $requestMethod) {
		self::$requestMethod = $requestMethod;
	}

	/**
	 * @param bool $pathExist
	 */
	public static function setPathExist(bool $pathExist) {
		self::$pathExist = $pathExist;
	}

	/**
	 * @return mixed
	 */
	public static function getPathExist(): bool {
		return self::$pathExist;
	}

	/**
	 * @return bool
	 */
	public static function isError(): bool {
		return self::$error;
	}

	/**
	 * @param bool $error
	 */
	public static function setError(bool $error) {
		self::$error = $error;
	}

	public function __construct($requestDetails) {
		// Get the HTTP REQUEST METHOD
		self::$requestMethod = $requestDetails["REQUEST_METHOD"];

		// domain name of the server.
		self::$domainName = $requestDetails["SERVER_NAME"];

		// /path1/path2/path3
		self::$uri = $requestDetails["REQUEST_URI"];

		// path1/path2/pathN
		$uriTrimmed = urldecode(trim($requestDetails["REQUEST_URI"], '/'));

		/**
		 * Convert a string into array
		 * Array (
		 *      [0] => Param1
		 *      [1] => Param2
		 * )
		 */
		self::$pathParts = explode('/', $uriTrimmed);
	}

	public static function get_($url, $callback){
		if (!self::$pathExist){
			// Execute only if the method is GET
			if (self::$requestMethod == "GET"){

				// path1/path2/pathN
				$urlTrimmed = urldecode(trim($url, '/'));
				$urlParts = explode('/', $urlTrimmed);

				// Check if the parts is possible match or has an additional parameter
				if (count(self::$pathParts) > count($urlParts)){
					/**
					 * Get the array of the possible matched array
					 * Output should be...
					 * array (
					 *    0 => "path1",
					 *    1 => "path2"
					 * )
					 */
					$slicedURI = array_slice(self::$pathParts, 0, count($urlParts));

					// Check if the two arrays matched
					if ($slicedURI === $urlParts){
						/**
						 * Create a new array that doesn't include the matched value
						 * Output should be...
						 * array (
						 *    0 => "par1",
						 *    1 => "par2"
						 * )
						 */
						$usableParams = array_slice(self::$pathParts, count($urlParts));
						$callback($usableParams);

						self::setPathExist(true);
					}
				}
			}
		}

	}

	public static function get($url, $callback){
		if (!self::$pathExist) {
			// Execute only if the method is GET
			if (self::$requestMethod == "GET") {

				// Check if it is super matched :))
				if (self::$uri == $url) {
					$callback();
					self::setPathExist(true);
				}
			}
		}
	}


	public static function post($url, $callback){
		if (!self::$pathExist) {
			// Execute only if the method is POST
			if (self::$requestMethod == "POST") {

				// Check if it is super matched :))
				if (self::$uri == $url) {
					$callback($_POST);
					self::setPathExist(true);
				}
			}
		}
	}

	public static function notFoundStr(string $str){
		if (!self::$pathExist && !self::isError()) {
			echo $str;
			self::setError(true);
		}
	}

	public static function notFoundRender($file, array $arrVariables = array()){
		if (!self::$pathExist && !self::isError()) {

			self::render($file, $arrVariables);
			self::setError(true);
		}
	}


	public static function view($url, $file, array $arrVariables = array()){
		if (!self::$pathExist) {
			// Execute only if the method is GET
			if (self::$requestMethod == "GET") {

				// Check if it is super matched :))
				if (self::$uri == $url) {
					self::render($file, $arrVariables);
					self::setPathExist(true);
				}
			}
		}
	}


	/** This is the view method of the twig templating engine
	 * @param $file
	 * @param array $arrVariables
	 */
	public static function render($file, array $arrVariables = array()){
		// Load the Vendor Files Automatically when called
		require_once VENDORS.DS.'autoload.php';

		$loader = new Twig_Loader_Filesystem(VIEW_PATH);

		$twig = new Twig_Environment($loader, array(
			// "cache" => CACHE_PATH
		));

		// Put the Config Variables in the Twig Template
		$twig->addGlobal('config', Config::array());

		echo $twig->render($file, $arrVariables);
	}

	public static function redirect($uri){
		header("location: ". $uri);
		exit;
	}

}