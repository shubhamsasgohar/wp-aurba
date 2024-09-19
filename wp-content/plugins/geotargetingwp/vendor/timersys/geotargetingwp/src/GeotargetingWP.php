<?php namespace GeotWP;
use GeotWP\Exception\AddressNotFoundException;
use GeotWP\Exception\GeotException;
use GeotWP\Exception\GeotRequestException;
use GeotWP\Exception\InvalidIPException;
use GeotWP\Exception\InvalidLicenseException;
use GeotWP\Exception\OutofCreditsException;
use GeotWP\Record\GeotRecord;
use GeotWP\Record\RecordConverter;
use IP2Location\Database;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use MaxMind\Db\Reader;
use stdClass;

class GeotargetingWP{

	private $api_args;

	private $license;
	private $api_secret;

	/**
	 * Constructor
	 *
	 * @param $acces_token
	 * @param $api_secret
	 *
	 * @throws InvalidLicenseException
	 */
	public function __construct( $acces_token = "", $api_secret = "" ) {
		$this->license = $acces_token;
		$this->api_secret = $api_secret;
	}

	/**
	 * Main function that return User data
	 *
	 * @param array $api_args
	 *
	 * @return mixed
	 * @throws AddressNotFoundException
	 * @throws GeotException
	 * @throws GeotRequestException
	 * @throws InvalidIPException
	 * @throws InvalidLicenseException
	 * @throws OutofCreditsException
	 */
	public function getData( $api_args = [] ){

		$this->api_args = $api_args;

		if( empty( $this->api_args ) || ! isset( $this->api_args['geolocation'] ) ) {
			throw new GeotRequestException(json_encode(['error' => 'No IP or coordinates set for user']));
		}
		// time to call api
		try{
			$request_params = $this->generateRequestParams();
			$res = self::client()->get( add_query_arg( $request_params['query'], self::api_url() . 'data' ), $request_params);
		} catch ( \Exception $e) {
			throw new GeotRequestException($e->getMessage());
		}
		$this->validateResponse( $res );
		return $this->getDecodedBody( $res );
	}

	/**
	 * Check returned response
	 *
	 * @param $res
	 *
	 * @throws AddressNotFoundException
	 * @throws GeotException
	 * @throws InvalidIPException
	 * @throws InvalidLicenseException
	 * @throws OutofCreditsException
	 */
	private function validateResponse( $res ) {
		if( is_wp_error( $res ) )
			throw new GeotException(json_encode(['error' => 'Null reponse from server']));
		
		$code = wp_remote_retrieve_response_code( $res );
		switch ($code) {
			case '404':
				throw new AddressNotFoundException(wp_remote_retrieve_body($res));
			case '500':
				throw new InvalidIPException(wp_remote_retrieve_body($res));
			case '401':
				throw new InvalidLicenseException(wp_remote_retrieve_body($res));
			case '403':
				throw new OutofCreditsException(wp_remote_retrieve_body($res));
			case '200':
				break;
			default:
				throw new GeotException(wp_remote_retrieve_body($res));
				break;
		}
	}

	/**
	 * Decode the response into object
	 * @param $res
	 *
	 * @return GeotRecord
	 */
	public function getDecodedBody( $res ) {
		$body = wp_remote_retrieve_body( $res );

		return json_decode( $body );

	}

	/**
	 * Helper function that let users check if license is valid
	 *
	 * @param $license
	 *
	 * @return false|string
	 */
	public static function checkLicense( $license ) {
		$response = self::client()->get( add_query_arg( [ 'license' => $license ],  self::api_url() .'check-license' ) );
		$body = wp_remote_retrieve_body($response);
		if( $code = wp_remote_retrieve_response_code( $response ) != '200')
			return json_encode(['error' => 'checkLicense Code:'. $code .' - Something wrong happened:' . strip_tags($body)]);

		return $body;
	}

	/**
	 * Helper function that let users check if license is valid
	 *
	 * @param $license
	 *
	 * @return false|string
	 */
	public static function checkSubscription( $license ) {
		$host = empty($_SERVER['HTTP_HOST']) ? parse_url( site_url(), PHP_URL_HOST ) : $_SERVER['HTTP_HOST'];
		$response = self::client()->get( add_query_arg( [ 'license' => $license,  'Geot-Origin' => $host ], self::api_url() .'check-subscription' ) );
		$body = wp_remote_retrieve_body($response);
		if( $code = wp_remote_retrieve_response_code( $response ) != '200')
			return json_encode(['error' => 'checkSubscription Code:'. $code .' - Something wrong happened:' . strip_tags($body)]);

		return $body;
	}

	/**
	 * Helper function that get cities for given country
	 *
	 * @param $iso_code
	 *
	 * @return array|mixed|\Psr\Http\Message\ResponseInterface
	 *
	 */
	public static function getCities( $iso_code ) {
		$response = self::client()->get( add_query_arg( [ 'iso_code' => $iso_code ], self::api_url() .'cities' ) );

		if( wp_remote_retrieve_response_code( $response ) != '200')
			return ['error' => 'Something wrong happened'];

		return wp_remote_retrieve_body($response);
	}

	/**
	 * Create a client instance
	 */
	private static function client() {
		return _wp_http_get_object();
	}

	/**
	 * Return API URL
	 * @return mixed
	 */
	public static function api_url() {
		return env('GEOT_ENDPOINT','https://geotargetingwp.com/api/v1/');
	}

	/**
	 * Generates signature
	 * @return array
	 */
	private function generateRequestParams() {
		$signature_params = [
			'query' => [
				'ip'        => $this->api_args['data']['ip'], // added for signature verification
				'license'	=> $this->license,
			],
			'headers' => [
				'Geot-Nonce'  => urlencode(base64_encode(makeRandomString())),
				'Geot-Origin' => $_SERVER['HTTP_HOST']
			]
		];
		$request_params = array_merge($signature_params , [
			'query' => [
				'type'		=> $this->api_args['geolocation'], // by_ip|by_html5
				'data'		=> $this->api_args['data'], // [ 'ip' => ''] || [ 'lat' => '', 'lng' => '' ]
				'license'	=> $this->license,
				'ip'        => $this->api_args['data']['ip'], // added for signature verification
			]
		] );

		$base_string = json_encode($signature_params);
		$request_params['query']['signature'] = urlencode(hash_hmac('sha256',$base_string, $this->api_secret ));
		return $request_params;
	}
}