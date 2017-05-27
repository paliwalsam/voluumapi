<?php 
namespace paliwalnitin\voluumwrap;
use GuzzleHttp\Client;
use paliwalnitin\voluumwrap\VoluumHelper;

class voluumwrap 
{
	const VOLUUM_REPORTING_BASE_URI = 'https://reports.voluum.com/report';
	const VOLUUM_LOGIN_BASE_URI = 'https://security.voluum.com/login';
	
	private $client;
	private $username;
	private $password;

	private $authToken;
	
	function __construct($username = null, $password = null)
	{
		$this->client = new Client([
			'base_uri' => self::VOLUUM_REPORTING_BASE_URI
		]);

		$this->username = $username;
		$this->password = $password;
	}

	public function login($username = null, $password = null){
		if ($username && $password){
			$this->username = $username;
			$this->password = $password;
		}
		return $this->obtainAuthToken();
	}

	private function obtainAuthToken (){

		if (!$this->username && !$this->password){
			return false;
		}

		//Use Basic Auth to retrieve the Auth Token for further requests
		$res = $this->client->get(self::VOLUUM_LOGIN_BASE_URI, [
    		'auth' => [
        		$this->username, 
        		$this->password
    		]
		]);

		$data = json_decode($res->getBody(), true);

		if ( $data['loggedIn'] && isset($data['token']) ){
			$this->authToken = $data['token'];
			return true;	
		}

		return false;
	}

	function query($params, $decodeJson = true){
		$res = $this->client->request('GET', '', [
			'headers' => ['cwauth-token' => $this->authToken],
			'query' => $params
		]);

		if ($decodeJson) {
			return json_decode($res->getBody(), true);
		} else {
			return $res->getBody();
		}
		
	}

	function campaignReport($campaignId, $dateRange = 'last-30-days', $groupBy = 'day'){
		$params = [
			'sort' => 'visits',
			'direction' => 'desc',
			'groupBy' => $groupBy,
			'offset' => '0',
			'limit' => '1000',
			'filter1' => 'campaign',
			'filter1Value' => $campaignId
		];
		// Add To / From and TZ to params
		$params = array_merge(VoluumHelper::dateRangeFromSlug($dateRange), $params);
		$result = $this->query($params);
		return $result['rows'];
	}
	

	/**
	* @return an array with 'campaginId' => 'campaignName'
	*/
	function getActiveCampaigns(){
		
		$params = [
			'columns' => 'campaignName',
			'columns' => 'campaignUrl',
			'groupBy' => 'campaign',
			'offset' => '0',
			'limit' => '1000'
		];
		$params = array_merge(VoluumHelper::dateRangeFromSlug('today'), $params);

		$result = $this->query($params);
		$tempresult=[];
		foreach($result['rows'] as $row)
		{
			$tempresult[ $row['campaignId'] ] = [ 
				'campaignId' => $row['campaignId'],
				'campaignName'=> $row['campaignName'], 
				'campaignUrl'=>$row['campaignUrl'] ,
				'trafficSourceName'=>$row['trafficSourceName'] 
				] ;
		}
		$tempresult=json_decode(json_encode($tempresult), FALSE);
		return $tempresult;
	}
}