<?php
require_once(__DIR__.'/vendor/autoload.php');
use paliwalnitin\voluumwrap\voluumwrap;

$vrc = new voluumwrap();
$vrc->login('voluum_reg_email_id', 'voluum_password');

#get detail of lander
$lander = $vrc->landerReport('Lander_id');
print_r( $lander );
/*
Output:
Array
(
    [id] => Lander_id
    [name] => lander_name
    [namePostfix] => lander_postfix_name
    [createdTime] => created_time
    [updatedTime] => updated_time
    [deleted] => 
    [url] => lander_url
    [numberOfOffers] => 1
    [tags] => Array
        (
        )
)
*/

#update lander info
$lander['url']='https://example.com';
$res = $vrc->updateLander('lander_id', $lander);

#get all active campaigns
$vrc->getActiveCampaigns();

#get data of specified campign
$stats = $vrc->campaignReport('campaignId', $dateRange = 'last-30-days', $groupBy = 'day');
print_r($stats);

//$campaignId = '3d38aa4f-db62-4aef-958d-fac71fe936b2';
//$stats = $vrc->campaignReport($campaignId, 'last-30-days', '1');
//print_r($stats);
