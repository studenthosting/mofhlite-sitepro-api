<?php

include __DIR__.'/SiteProApiClient.php';
$sql = mysqli_query($connect,"SELECT * FROM `hosting_account` WHERE `account_username`='".$_GET['account_id']."' AND `account_for`='".$ClientInfo['hosting_client_key']."'");
	if(mysqli_num_rows($sql)>0){
		include __DIR__.'/hostingInfo.php';
	}

use Profis\SitePro\SiteProApiClient;

$builderError = null;
if (isset($_GET['editWebsite']) && $_GET['editWebsite']) {
	// get "your_api_username" and "our_api_password" from your license and enter them here
	// use this for premium/free licenses
	$api = new SiteProApiClient('http://site.pro/api/', 'your_api_username', 'your_api_password');
	// use this for enterprise licenses and change 'your-bulder-domain.com' to your builder domain
	//$api = new SiteProApiClient('http://your-bulder-domain.com/api/', 'your_api_username', 'your_api_password');

	try {
		// this call is used to open builder, so you need to set correct parameters to represent users website you want to open
		// this data usually comes from your user/hosting manager system
		$res = $api->remoteCall('requestLogin', array(
			'type' => 'external',				// (required) 'external'
			'domain' => $data['hosting_domain'],			// (required) domain of the user website you want to edit
			'lang' => 'en',						// (optional) 2-letter language code, set language code you whant builder to open in
			'username' => $data['hosting_username'],		// (required) user websites FTP username
			'password' => $data['hosting_password'],	// (required) user websites FTP password
			'apiUrl' => '0.0.0.0',				// (required) user websites FTP server IP address
			'uploadDir' => '/htdocs',		// (required) user websites FTP folder where website files need to be uploaded to
			'hostingPlan' => 'free',	// (optional) hosting plan that user uses
			'panel' => 'Site Builder'			// (optional) user/hosting management panel name that this script will be run in
		));
		if (!$res || !is_object($res)) {
			// handle errors
			throw new ErrorException('Response format error');
		} else if (isset($res->url) && $res->url) {
			// on success redirect to builder URL
			header('Location: '.$res->url, true);
			exit();
		} else {
			// handle errors
			throw new ErrorException((isset($res->error->message) && $res->error->message) ? $res->error->message : 'Unknown error');
		}
	} catch(ErrorException $ex) {
		// handle errors
		$builderError = 'Request error: '.$ex->getMessage();
	}
}


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Example Panel</title>
		<style type="text/css">
			body {
				font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
				color: #333;
				font-size: 14px;
				line-height: 16px;
			}
			.container {
				width: 600px;
				margin: 100px auto 0px auto;
			}
			.error-alert {
				padding: 15px;
				background-color: #F2DEDE;
				border: 1px solid #EBCCD1;
				border-radius: 4px;
				color: #A94442;
				margin: 10px 0;
			}
			.tbl-websites {
				border: none;
				border-collapse: collapse;
				width: 100%;
			}
			.tbl-websites th,
			.tbl-websites td {
				border: 1px solid #DDD;
				border-collapse: collapse;
				padding: 8px;
				text-align: left;
			}
		</style>
    </head>
    <body>
		<div class="container">
			<h1>Select Websites</h1>
			<?php if ($builderError): ?>
			<div class="error-alert"><?php echo $builderError; ?></div>
			<?php endif; ?>
			<table class="tbl-websites">
				<thead>
					<tr>
						<th>Website</th>
						<th>Functions</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>$data['hosting_domain']</td>
						<td><a href="?editWebsite=1">Edit Website</a></td>
					</tr>
				</tbody>
			</table>
		</div>
    </body>
</html>
