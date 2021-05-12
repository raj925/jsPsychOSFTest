<?php
/*
* secrets.php should contain the definition of the variable $OSF_PAT
* It should look like:
* <?php
* $OSF_PAT="osf_personal_access_token_with_osf.nodes.data_write_scope";
*/
include('secrets.php');

// Your OSF data component id (https://osf.io/osf_repository_id/)
$WHERE_TO_SAVE="atq9z";

$content = $_POST['public_data']; // Data sent from the participant's machine
$participant_id = $_POST['participant_id']; // Participant id sent from participant's machine

// Because the above came from the participant's machine, we sanitize the input
if(!preg_match("/^[0-9a-zA-Z]+$/", $participant_id)) {
    http_response_code(400);
    die("{error: 'Invalid participant Id'}");
}

// This may need changing if you don't use Frankfurt (DE) for your data storage
$url = "https://files.de-1.test.osf.io/v1/resources/$WHERE_TO_SAVE/providers/osfstorage/?kind=file&name=$participant_id.csv";

/*
 * The cURL request below is adapted from https://stackoverflow.com/a/5676572
 * We create a request to the $url above, with the following key properties:
 * - PUT method (because we're creating a file)
 * - Authorization header with the token
 * - Body content of the file we want to create
 */

// open connection
$ch = curl_init();

// set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $content);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

// set the request headers, used to authenticate using the PAT
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer $OSF_PAT"
));

// have curl_exec return the contents of the cURL; rather than echoing it
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

// execute post
$result = curl_exec($ch);

// Inform the participant that we saved the data successfully
echo $result;
