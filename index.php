<?php

// CONFIGURATION

define('FLOWERPOT_ROOT_URL', 'XXXXX');

// SETUP

function isURL($e) {
	return substr($e, 0, 4) === 'http';
}

$template = array_values(array_filter([
	$_ENV['FLOWERPOT_ROOT_URL'] ?? '',
	$_SERVER['FLOWERPOT_ROOT_URL'] ?? '',
	FLOWERPOT_ROOT_URL,
	], 'isURL'))[0] ?? '';

if (!$template) { ?>
	<p><strong>Missing root URL.</strong></p>

	<p>Please replace <strong><code><?php echo FLOWERPOT_ROOT_URL ?></code></strong> in index.php with your root URL</p>

	<p>Alternatively, set the <code>FLOWERPOT_ROOT_URL</code> environment variable.</p>
<?php 
	die();
}

define('FLP_INDEX', '/index.html');

// FETCH

$base = str_replace(FLP_INDEX, '', $template);
$path = $_SERVER['REQUEST_URI'] == '/' ? FLP_INDEX : $_SERVER['REQUEST_URI'];

$outputData = str_replace($base, '', str_replace($template, '/', file_get_contents($base . $path, false, stream_context_create([
	'http' => [
		'ignore_errors' => true,
	],
]))));

// HEADERS

array_map('header', array_filter($http_response_header, function ($e) {
	if (strpos($e, 'Connection:') === 0) {
		return false;
	}

	if (strpos($e, 'Content-Length') === 0) {
		return false;
	}
	
	return true;
}));

// OUTPUT

echo $outputData;

?>
