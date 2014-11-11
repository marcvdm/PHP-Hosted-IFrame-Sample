<?PHP 

if (isset($_POST['responseCode'])) {
	echo '<p><strong>Cardstream Response</strong></p>';
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
	exit;
}

$action = "https://gateway.cardstream.com/hosted/";
$sig_key = "Circle4Take40Idea";

$fields = array(	
	"merchantID" => '100001', 
	"action" => "SALE",
	"type" => 1,
	"amount" => 1001,
	"transactionUnique" => uniqid(),
	"orderRef" => "Test purchase",
	"currencyCode" => 826,
	"countryCode" => 826,
	"redirectURL" => ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 
);
	

function createSignature(array $data, $key, $algo = null) {

	$algos = array(
		'SHA512' => true,
		'SHA256' => true,
		'SHA1' => true,
		'MD5' => true,
		'CRC32' => true,
	);

	if ($algo === null) {
		$algo = 'SHA512';
	}
	
	ksort($data);

	// Create the URL encoded signature string
	$ret = http_build_query($data, '', '&');

	// Normalise all line endings (CRNL|NLCR|NL|CR) to just NL (%0A)
	$ret = preg_replace('/%0D%0A|%0A%0D|%0A|%0D/i', '%0A', $ret);
	
	// Hash the signature string and the key together
	$ret = hash($algo, $ret . $key);

	// Prefix the algorithm if not the default
	if ($algo !== 'SHA512') {
		$ret = '{' . $algo . '}' . $ret;
	}

	return $ret;	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Integration > Hosted Forms Integration | Card Processing | Payment Gateway - Cardstream&reg;</title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta http-equiv="Content-Language" content="en-us" />
	</head>
	<body>
	<script>
		window.onload = function() {
			document.getElementById('frm').submit();
		}
	</script>	
	
	<form target="myIframe" id="frm" action="<?= $action ?>" method="post">
		<?	foreach ($fields as $key => $value) { ?>
				<input type="hidden" name="<?= $key ?>" value="<?= $value ?>">			
		<?	}
		
			if (isset($sig_key)) { ?>
				<input type="hidden" name="signature" value="<?= createSignature($fields, $sig_key, 'SHA512') ?>" />
		<?	} ?>
	</form>
	<iFrame style="background-color:#ffffff; text-align: center;height: 1050px; display: block; width: 100%; border: 0; margin: 20px auto 0;" src="" name="myIframe" id="myIframe" onload="scroll(0,0);"></iFrame>

	</body>
</html>
