<?php
use com\google\i18n\phonenumbers\PhoneNumberUtil;
use com\google\i18n\phonenumbers\PhoneNumberFormat;
use com\google\i18n\phonenumbers\NumberParseException;

require_once 'PhoneNumberUtil.php';

$swissNumberStr = "044 668 18 00";
$phoneUtil = PhoneNumberUtil::getInstance();
try {
	$swissNumberProto = $phoneUtil->parseAndKeepRawInput($swissNumberStr, "CH");
	var_dump($swissNumberProto);
} catch (NumberParseException $e) {
	echo $e;
}
$isValid = $phoneUtil->isValidNumber($swissNumberProto);//return true
var_dump($isValid);
// Produces "+41446681800"
echo $phoneUtil->format($swissNumberProto, PhoneNumberFormat::INTERNATIONAL) . PHP_EOL;
echo $phoneUtil->format($swissNumberProto, PhoneNumberFormat::NATIONAL) . PHP_EOL;
echo $phoneUtil->format($swissNumberProto, PhoneNumberFormat::E164) . PHP_EOL;

echo $phoneUtil->formatOutOfCountryCallingNumber($swissNumberProto, "US") . PHP_EOL;