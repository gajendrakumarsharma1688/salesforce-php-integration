<?php

define("USERNAME", "<Username/Emailid>");
define("PASSWORD", "<enter your password>");
define("SECURITY_TOKEN", "<enter saleforce SECURITY TOKEN>");

require_once ('salesforce-toolkit/SforcePartnerClient.php');

$mySforceConnection = new SforcePartnerClient();
$mySforceConnection->createConnection("partner.wsdl.xml");
$mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);

$query = "SELECT Id, FirstName, LastName, Phone from Contact";
$response = $mySforceConnection->query($query);

foreach ($response->records as $record) {
    $id_array[] = $record->Id[0];
}

/*
 * Retrieving Records.
 * $id_array is an array of record ids built in the previous step
 */

$response_ids = $mySforceConnection->retrieve('Id, FirstName, LastName, Phone',
                'Contact', $id_array);
foreach ($response_ids as $record) {
    echo $record->Id . ": " . $record->fields->FirstName . " "
    . $record->fields->LastName . " " . $record->fields->Phone . "<br/>\n";
}

/*
 * Creating Records
 */

$records = array();

$records[0] = new SObject();
$records[0]->fields = array(
    'FirstName' => 'John',
    'LastName' => 'Smith',
    'Phone' => '(510) 555-5544',
    'BirthDate' => '1957-01-25'
);
$records[0]->type = 'Contact';

$records[1] = new SObject();
$records[1]->fields = array(
    'FirstName' => 'Mary',
    'LastName' => 'Smith',
    'Phone' => '(510) 486-2323',
    'BirthDate' => '1977-01-25'
);
$records[1]->type = 'Contact';

$response = $mySforceConnection->create($records);

$ids = array();
foreach ($response as $i => $result) {
    echo $records[$i]->fields["FirstName"] . " "
            . $records[$i]->fields["LastName"] . " "
            . $records[$i]->fields["Phone"] . " created with id "
            . $result->id . "<br/>\n";
    array_push($ids, $result->id);
}


/*
 * Delete Records
 * $ids is an array of record ids built in a previous step
 * example: $ids = ['0030o00002a5we9','0030o00006y5wy9']
 */

$response = $mySforceConnection->delete($ids);
foreach ($response as $result) {
    echo $result->id . " deleted<br/>\n";
}

/*
 * Update Records
 * $ids is an array of record ids built in a previous step
 * example: $ids = ['0030o00002a5we9','0030o00006y5wy9']
 */

$records[0] = new SObject();
$records[0]->Id = $ids[0];
$records[0]->fields = array(
    'Phone' => '(415) 555-5555',
);
$records[0]->type = 'Contact';

$records[1] = new SObject();
$records[1]->Id = $ids[0];
$records[1]->fields = array(
    'Phone' => '(415) 486-9969',
);
$records[1]->type = 'Contact';

$response = $mySforceConnection->update($records);
foreach ($response as $result) {
    echo $result->id . " updated<br/>\n";
}
