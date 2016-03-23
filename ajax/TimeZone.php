<?php 

/*
 * @package		Timezone Detect
 * @author      Pap Tamas
 * @copyright   (c) 2013 Pap Tamas
 * @website		https://github.com/paptamas/timezone-detect
 * @license		http://opensource.org/licenses/MIT
 *
 */

require $_SERVER['DOCUMENT_ROOT'].'/class/Timezone.class.php';

if ( ! isset($_POST['offset']) || ! isset($_POST['dst']))
{
	die('Invalid request.');
}

$offset = $_POST['offset'];
$dst = $_POST['dst'];


$usertimezone = json_encode(Timezone::detect_timezone_id($offset, $dst));
@session_start();
$_SESSION['usertimezone'] = $usertimezone;

echo $usertimezone;
?>