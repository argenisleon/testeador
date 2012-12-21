<!-- 
	Testeador
	Desarrollado por Argenis Leon. 
	argenisleon@gmail.com. 
	Twitter: @argenisleon.
	
	Mood Agency. Todos Los Derechos Reservados http://mood.com.ve
	
	Todo. 

	Detectar Doctype
	icono de carga al momento de hacer el yslow page speed
	javascript dentro de la pagina
	htaccess que este abilitada la compresion y los expiration date

	Joomla
	Try to detect this http://www.hostknox.com/tutorials/joomla/optimization
	Verificar si el administrador esta protegido por plugin OSE
    Verificar que el cache de joomla este actido

 -->
<?php

    define("YSLOWTRESHOLD", 90);
    define("PAGESPEEDTRESHOLD", 90);
    define("USERNAME_GTM", 'argenisleon@gmail.com');
    define("PASSWORD_GTM", '05224202ae927d4145d8693c24e0fccf');

    $scoreParam = $_GET['score'];
	$validateParam = $_GET['validate'];
	$firewallParam = $_GET['validate'];
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Testeador </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="css/bootstrap.css" rel="stylesheet">
        <style>
        iframe {
            width: 100%;
            height: 600px;
        }
        </style>
        <link href="css/bootstrap-responsive.css" rel="stylesheet">


    </head>

  <body>
	<div class="container">
		
		<h1>
			Testeador v0.613
		</h1>
		<!--<small><a href='http://mood.com.ve'>By mood agency</a></small>-->
		
		<form action="index.php" method='get'>
		
			<div class="input">
			  <input name="host" class="span2" id="appendedInputButton" value="<?php echo $_GET['host'] ?>"  type="text" placeholder="Type your host..."/>
			</div>
			<p>
			  <button class="btn btn-large btn-primary" type="submit">Go!</button>
			  
			</p>
			<label class="checkbox">
				<input type="checkbox" name="score" value="pSyS" <?echo ( $scoreParam=='pSyS' ? ' checked="checked"' : '') ?>>Get pageSpeed & ySlow Score<br>
			</label>
			<label class="checkbox">
				<input type="checkbox" name="validate" value="w3c" <? echo ($validateParam=='w3c' ? ' checked="checked"' : '') ?>>Validate W3C 
			</label>
			<label class="checkbox">
				<input type="checkbox" name="firewall" value="test" <? echo ($firewallParam=='test' ? ' checked="checked"' : '') ?>>Test Firewall
			</label>
			
			
		</form>
		
		<small><a href='changelog.txt' >Change Log </a></small>
	
		
<?php

    if (!isset($_GET['host'])) return;

	// Load the favicon test class.
	require_once('php/favicon.inc.php'); // Thanks to http://www.controlstyle.com/articles/programming/text/php-favicon/

	// Load the web test framework class.
	require_once("php/Services_WTF_Test.php"); // Thanks to http://gtmetrix.com 
	
	// Load the W3C validatino test class.
	require_once("php/api_w3cvalidation.class.php"); // Thanks to http://www.phpclasses.org/package/5712-PHP-Validate-an-HTML-page-using-the-W3C-validator.html
	
	$url = $_GET['host'];
	$url = "http://".$_GET['host'];
    $errorURL = $url."/asdhgfakdsgfaksdgfasgdfghk.html";
    $firewallTestURL = $url."/index.php?%20union";

    $errorCount = 0;
    $warningCount = 0;
    $passCount = 0;

    str_replace("www.", "", $url); // strip www. from the url so we can check that the returned URL www

	// Verify the domain can be resolved
	$dataReturned = getDataFromURL ($url);
	$html = $dataReturned['html'];
	
	if (isset($dataReturned['error'])) {
		//Domain can not be resolved;
		echo $dataReturned['error'] ;
		fatal();
		exit;
	}
	//----------------------------------------------- Start Testing
	echo "<h3>Host</h3>";
	echo  "<p>".$url."</p>";

	//------------------------------------------------ Check Favicon
	echo "<h3>Favicon</h3>";

	$favicon = new favicon($url, 0);
	$fv = $favicon->get_ico_url();
	echo "<img src='". $fv ."'/>";

	//------------------------------------------------ Canonicalization
	echo "<h3>Canonicalization</h3>";

	$parsedURL = parse_url ($dataReturned['effectiveURL']);
	
	echo "Check if the user is redirect to the www. domain";

	if (preg_match("/www./i", $parsedURL['host'])) {
		pass();
	} else {
		fatal("Users was not redirected");
	}
	
	//------------------------------------------------ Check Title and metadata
	echo "<h3>Title and Metadata</h3>";
	echo "Looking for the 'Joomla' word or empty tags...";
	
	// Parse the html into a DOMDocument
	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	
	print_r ($dom->doctype);

	$xpath = new DOMXPath($dom);	
	$nodelist = $xpath->query("//title"); 
	
	
	//------------------------ Doctype
	/*echo "<h4>doctype</h4>";
	$attrs = $xpath->query("//doctype");
	print_r ($attrs);
	*/
	//------------------------ Encoding
	
	echo "<h4>Encoding</h4>";
	$attrs = $xpath->query("//meta");
	
	$encodingFound = false;
	for ($i = 0; $i < $attrs->length; $i++) {
		$attr = $attrs->item($i);
		
		$val = $attr->getAttribute('http-equiv');	
		
		if (strcasecmp ($val, "content-type" )==0) {
			echo "<p>http-equiv='".$val."'</p>";
			echo "<p>content='".$attr->getAttribute('content')."'</p>";
			$encodingFound = 'HTML';
		}

		if (strcasecmp($val, 'utf-8')==0) {
			$encodingFound = 'HTML5';
		}
	}
	
	if ($encodingFound) 
		pass($encodingFound.' encoding found!');
	else
		warning("HTML encoding not found");
	
	//------------------------ Title
	echo "<h4>Title</h4>";	 	
		
	foreach ($nodelist as $n){
		$val = $n->nodeValue;
		echo $val;
		if (!findString($val, "joomla" ) || ($val==""))
			pass();
		 else 
			warning();
	}
	
	$attrs = $xpath->query("//meta"); 
	$descriptionFound = false;
	$keywordsFound = false;
	
	//------------------------ Description
	
	echo "<h4>Description</h4>";
	
	for ($i = 0; $i < $attrs->length; $i++) {
		$attr = $attrs->item($i);
		
		if ($attr->getAttribute('name')=='description') {
			$descriptionFound = true;
			
			$val = $attr->getAttribute('content');
			echo $val;
			if (!findString($val, "joomla" ) || ($val==""))
				pass();
			else 
				warning();
			
		}
	}
	
	if (!$descriptionFound) 
		fatal("meta description tag not found!");
	
	//------------------------ Keywords
	
	echo "<h4>Keywords</h4>";

	for ($i = 0; $i < $attrs->length; $i++) {
		$attr = $attrs->item($i);
	
		if ($attr->getAttribute('name')=='keywords') {
			$keywordsFound = true;
			
			$val = $attr->getAttribute('content');
			echo $val;
			if (!findString($val, "joomla" ) || ($val==""))
				pass();
			else 
				warning();
		}
	}

	if (!$keywordsFound)
		fatal("meta keywords tag not found!");

	//------------------------------------------------ W3C Validation
	if ($validateParam == 'w3c') {
		echo "<h3>W3C Validation</h3>";
		$validate = new W3cValidateApi;
		
		$a = $validate->validate('http://google.com/');
		if($a){
			pass();
		} else {
			fatal($validate->ValidErrors." error(s) found");
			echo "<a target='_blank' href='".$validate->urlLink()."'>View more details</a>";
			
		}
	}

	//------------------------------------------------ Check Google Analytics
	echo "<h3>Google Analytics</h3>";
	echo "<p>Looking for 'UA-' string in the page... </p>";
	
	if (findString($html, "UA-" ))
			pass();
		 else 
			fatal("UA- not found");

	//------------------------------------------------ Check Google Webmaster Tools
	echo "<h3>Google Webmaster Tools</h3>";
	echo "<p>Looking for 'google-site-verification' string in the page... </p>";
	
	if (findString($html, "google-site-verification" ))
			pass();
		 else 
			fatal("google-site-verification tag not found!");

	//------------------------------------------------ Check SEF
	echo "<h3>SEF</h3>";

	// grab all the a tags on the page	
	$attrs = $xpath->query("//a");

	$NotSEFCount = 0;
	
	if ($attrs->length>0)
		echo "Not SEF URL's"; 
	for ($i = 0; $i < $attrs->length; $i++) {
		$attr = $attrs->item($i);
		$linkURL = $attr->getAttribute('href');	
		$parsedURL = parse_url ($linkURL);
		//print_r (isset($parsedURL['query']));
		
		if (isset($parsedURL['query'])) {
			$NotSEFCount++;
			echo "<br /><a href='".$linkURL."'>$linkURL</a>";
		}
	}
	if ($NotSEFCount==0) {
		pass();
	} else {
		warning($NotSEFCount. " Not SEF link of ".$attrs->length." detected");
	}
	
	//------------------------------------------------ PageSpeed & Yslow
	if ($scoreParam == 'pSyS') {
		echo "<h3>PageSpeed & Yslow</h3>";
		pSyS($url); // Check Pagespeed and yslow
	}
	//------------------------------------------------ Firewall
	
	if ($firewallParam=='test') {
		echo "<h3>Firewall</h3>";
		echo "<p>We made and attack! Verify that the site respond accordingly!";
		echo "<div><iframe class='iframe' src='".$firewallTestURL."'></iframe></div>"; 
	}
	
	//------------------------------------------------ CSS in home
	echo "<h3>CSS style in file</h3>";
	$nodes = $xpath->query("//style");
	
	$styleFound= false;
	$encodingFound = false;
	for ($i = 0; $i < $attrs->length; $i++) {
		$node = $nodes->item($i);
		echo $node->nodeValue;
		$styleFound = true;
	}
	if ($styleFound)
		warning ('Put styles in a external file');
	//------------------------------------------------ Old Browser detection
	echo "<h3>Old Browser detection</h3>";
	echo "<p>Looking for 'BrowserUpdateWarning.js' </p>";
	
	if (findString($html, "BrowserUpdateWarning.js" ))
			pass();
		 else 
			fatal("BrowserUpdateWarning.js not found!");
	
	//------------------------------------------------ Check 404
	echo "<h3>404 Page</h3>";
	echo "<p>We are trying to reach the 404 page! Verify that the site respond accordingly!";
	
	echo "<div><iframe class='iframe' src='".$errorURL."'></iframe></div>"; 
	// Do you have to check visually if the 404 page is what you expect

	//------------------------------------------------ HotLinking
	echo "<h3>Hotlinking</h3>";
	
	//Trying to get thefirst image of the page
	$attrs = $xpath->query("//img"); 

	if (!findString($url, getHost() )) {

		if ($attrs->length>0); {
			$attr = $attrs->item(0);
			$val = $attr->getAttribute('src');
			echo "<img src='"."$url$val' alt='Hotlinking Detection'  />"; 
		}
	} else {
		fatal('We can not test hotlinking. The script domain and the test domain are the same');
	}

	//------------------------------------------------ Done
	echo "<h3>Done!</h3>";
	if ($errorCount>0) 
		fatal($errorCount. " errors found!");
		
	if ($warningCount>0) 
		warning($warningCount. " warnings found!");
	
	pass($passCount. " tests success!");
	
	if ($warningCount==0 && $errorCount==0) 
		pass("Success! Everything looks OK!");

?>
    </div>
  </body>
</html>

<?php
	function fatal($text="This is a fatal error.") {
		echo '<div class="alert alert-error">  
			<a class="close" data-dismiss="alert">�</a>  
			<strong>Error! </strong>'. $text.'</div>';
		global $errorCount;
		$errorCount++;
	}

	function pass($text="You have successfully done it.") {
		echo '<div class="alert alert-success">  
			<a class="close" data-dismiss="alert">�</a>  
			<strong>Success! </strong>'.$text.'</div>';
		global $passCount;
		$passCount++;
	}
	
	function warning ($text = "Best check yorself, you're not looking too good") {
		echo "<div class='alert'>  
			<a class='close' data-dismiss='alert'>�</a>  
			<strong>Warning! </strong>".$text."</div>";
		global $warningCount;
		$warningCount++;
	}
	
	// thanks gtmetrix.com for the service
	function pSyS($url) {
		
		$test = new Services_WTF_Test(USERNAME_GTM, PASSWORD_GTM);
		
		$testid = $test->test(array(
			'url' => $url
		));

		if (!$testid) {
			die("Test ALERTed: " . $test->error() . "\n");
		}

		$test->get_results();

		if ($test->error()) {
			die($test->error());
		}
		
		$results = $test->results();
		
		$pageSpeed = $results['pagespeed_score'];
		$ySlow = $results['yslow_score'];
		
		
		echo "<p>Page Load Time:".($results['page_load_time']/1000)." seconds"."</p>";
		echo "<p>Page Bytes:".($results['page_bytes']/1024)." Kb</p>";
		
		if ($pageSpeed <= PAGESPEEDTRESHOLD)
			fatal("pageSpeed score is ". $pageSpeed." must be greater than ".PAGESPEEDTRESHOLD );
		else
			pass ("pageSpeed score is ".$pageSpeed );
		
		
		if ($ySlow <= YSLOWTRESHOLD)
			fatal("ySlow score is ".$ySlow ." must be greater than ".YSLOWTRESHOLD);
		else
			pass("ySlow score is ".$ySlow);
			
		$reportURL = $results['report_url'];
		echo "<a href='".$reportURL."'>View more details</a>";
		
		
		// If you no longer need a test, you can delete it:
		//echo "Deleting test id $testid\n";
		//$result = $test->delete();
		//if (! $result) { die("error deleting test: " . $test->error()); }
	}

	function findString($text, $cadena) {
		return strpos($text, $cadena);
	}

	function getDataFromURL($url) {

		$userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
		// make the cURL request to $target_url
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		
		$dataReturned['html'] = curl_exec($ch);
		$dataReturned['effectiveURL'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		
		
		if (!$dataReturned['html']) {
			//echo "<br />cURL error number:" .curl_errno($ch);
			//echo "<br />cURL error:" . curl_error($ch);
			$dataReturned['error']=  curl_error($ch);
		}
		
		return $dataReturned;
	}
	// http://stackoverflow.com/questions/1459739/php-serverhttp-host-vs-serverserver-name-am-i-understanding-the-ma
	function getHost() {
        if ($host = $_SERVER['HTTP_X_FORWARDED_HOST']) {
            $elements = explode(',', $host);
            $host = trim(end($elements));
        } else {
            if (!$host = $_SERVER['HTTP_HOST'])
            {
                if (!$host = $_SERVER['SERVER_NAME'])
                {
                    $host = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
                }
            }
        }
        // Remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);

    return trim($host);
}
?>
