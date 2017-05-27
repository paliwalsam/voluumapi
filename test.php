<?php
require_once(__DIR__.'/vendor/autoload.php');
use nitin\voluumapi\voluumwrap;

$vrc = new voluumwrap();
$vrc->login('contact@mytokri.com', '@>#:e\\3&AZQqNN_k');

//$campaigns = $vrc->getActiveCampaigns();
//print_r( $campaigns );

$campaignId = '3d38aa4f-db62-4aef-958d-fac71fe936b2';
$stats = $vrc->campaignReport($campaignId, 'last-30-days', '1');
print_r($stats);
