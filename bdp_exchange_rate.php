<?php
/**
 * @package    bdp_exchange_rate
 * @author     Octávio Filipe Pereira Gonçalves<octavio.filipe.pereira@gmail.com>
 * @copyright  Octávio Filipe Pereira
 * @license    AGPLv3 :: https://www.gnu.org/licenses/agpl-3.0.en.html
 * @link       https://github.com/octaviofilipepereira/
 * @version    v1.0.2
 */

// Include Simple Html Dom
include_once('simple_html_dom.php');

// Choose the output of the exchange rates
// Output to the browser -> $output = '';
// Output to file -> $output = 'file';
$output = '';

// If output to file, define file name and absolute location path
$fileoutput = '';

// Get Country Name, Currency, Exchange Rate, from BDP (Banco de Portugal))
function scraping_bdp_exr() {
    // Create HTML DOM   
	$html = file_get_html('https://www.bportugal.pt/taxas-cambio');

	// Get BDP rates from div content
	foreach($html->find('div[class="rates-row"]') as $coinrate) {
		// Get coin name div content
		$coin['rates-country'] = trim($coinrate->find('div[class="rates-country-name"]', 0)->plaintext);
		// Get coin name div content
		$coin['rates-coin'] = trim($coinrate->find('div[class="rates-coin"]', 0)->plaintext);
		// Get coin value div content
		$coin['rates-rate'] = trim($coinrate->find('div[class="rates-rate"]', 0)->plaintext);

		$retcoin[] = $coin;
	}

	// Clear HTML DOM Memory
	$html->clear();
	unset($html);

	// Return Array
	return $retcoin;
}

$retcoin = scraping_bdp_exr();

// Create output file, if defined
if ($output == "file") {
	if (file_exists($fileoutput)) {
		unlink($fileoutput);
	}
	$exrout = fopen($fileoutput, "w") or die("Unable to open file!");
}

// Display BDP Exchange Rate
foreach($retcoin as $exr) {
	if (!empty($exr['rates-country'])) {
		// Output to Browser or to txt file 
		if ($output == "file") {
			$exrin = "" . $exr['rates-country'] . " | " . $exr['rates-coin'] . " | " . $exr['rates-rate'] . "\r\n";
			fwrite($exrout, $exrin);
		} else {
			echo "<p>" . $exr['rates-country'] . " | " . $exr['rates-coin'] . " | " . $exr['rates-rate'] . "</p>";
		}
	}
}

// Close output file
if ($output == "file") {
	fclose($exrout);
	echo "File $fileoutput created";
}
?>
