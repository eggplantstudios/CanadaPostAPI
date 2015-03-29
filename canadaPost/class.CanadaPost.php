<?php
/**
 * The CanadaPost Class handles the API requests for Canada Posts' API.
 *
 * PHP version 5
 *
 * @package    CanadaPost
 * @author     Shawn Wernig <shawn@eggplantstudios.ca>
 * @version    1.0.0
 */

require('class.CanadaPostCredentials.php');
require('class.CanadaPostShipment.php');
include('lib/Array2XML.php');
include('lib/XML2Array.php');


/**
 * Class CanadaPost
 */
class CanadaPost
{

    /**
     * @var CanadaPostCredentials
     */
    protected $credentials;
    /**
     * @var service_urls
     *
     * The different endpoints.
     *
     */
    protected $service_urls = array(
        'getRates'          => 'https://ct.soa-gw.canadapost.ca/rs/ship/price',
        'discoverServices'  => 'https://ct.soa-gw.canadapost.ca/rs/ship/service',
        'getOption'         => 'https://ct.soa-gw.canadapost.ca/rs/ship/option/DC',
        'getService'        => 'https://ct.soa-gw.canadapost.ca/rs/ship/service/DOM.EP?country=CA',
    );
    /**
     * @var string
     *
     * The Certificate location.
     */
    protected $cert_url = '/cert/cacert.pem';

    /**
     *
     * Requires a CanadaPostCredentials object to instantiate this class.
     *
     * @param CanadaPostCredentials $credentials
     *
     */
    public function __construct( CanadaPostCredentials $credentials )
    {
        $this->credentials = $credentials;
    }

    /**
     *
     * The GetRates service returns a list of shipping services, prices and transit times
     * for a given item to be shipped.
     *
     * @param CanadaPostShipment $shipping
     * @return bool|DOMDocument
     */
    public function getRates(CanadaPostShipment $shipping )
    {
        return $this->dispatchRequest( __FUNCTION__, $shipping->toXML($this) );
    }

    /**
     *
     * The DiscoverServices service returns the list of available postal services for shipment
     * of a parcel to a particular destination.
     *
     * @return bool|DOMDocument
     */
    public function discoverServices( )
    {
        return $this->dispatchRequest( __FUNCTION__ );
    }

    /**
     *
     * The GetOptions service  returns information about a given add-on option such
     * as how it is used and whether it requires or conflicts with other options.
     *
     * @return bool|DOMDocument
     */
    public function getOption( )
    {
        return $this->dispatchRequest( __FUNCTION__ );

    }

    /**
     *
     * The GetService service  returns details of a given postal service in
     * terms of the min/max weight and dimensions offered by the postal service.
     * Also returned are details about the available add-on options.
     *
     * @return bool|DOMDocument
     */
    public function getService( )
    {
        return $this->dispatchRequest( __FUNCTION__ );
    }

    /**
     *
     * The main request dispatcher.
     *
     * @param $service
     * @param bool $xml
     * @param array $options
     * @return bool|DOMDocument
     */
    function dispatchRequest(  $service, $xml = false, $options = array() )
    {
        return $this->request( $service, $xml, $options );

    }

    /**
     *
     * Performs the cURL request.
     *
     *
     * @param $service
     * @param bool $xml
     * @param array $options
     * @return bool|DOMDocument
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function request( $service, $xml = false, $options = array() )
    {
        $options[CURLOPT_SSL_VERIFYPEER]    = true;
        $options[CURLOPT_SSL_VERIFYHOST]    = 2;
        $options[CURLOPT_CAINFO]            = $this->certUrl();
        $options[CURLOPT_RETURNTRANSFER]    = true;
        $options[CURLOPT_HTTPAUTH]          = CURLAUTH_BASIC;
        $options[CURLOPT_USERPWD]           = $this->credentials();
        $options[CURLOPT_HTTPHEADER]        = array('Content-Type: application/vnd.cpc.ship.rate-v3+xml', 'Accept: application/vnd.cpc.ship.rate-v3+xml');

        // If we are supplying data;
        if( $xml )
        {
            $options[CURLOPT_POST]              = true;
            $options[CURLOPT_POSTFIELDS]        = $xml;
        }

        // Create the request
        if( isset($this->service_urls[ $service ]) )
        {
            $curl = curl_init($this->service_urls[ $service ]);
        }
        else
        {
            throw new InvalidArgumentException( "$service is not a valid service." );
        }

        // Add the options
        curl_setopt_array( $curl, $options );
        // Execute
        $curl_response = curl_exec($curl);
        // Check for errors
        if( curl_errno($curl) ){
            curl_close($curl);
            throw new Exception( 'cURL error: ' . curl_error($curl) );
        }
        // Check HTTP status
        if( curl_getinfo($curl,CURLINFO_HTTP_CODE) == 200 )
        {
            curl_close($curl);
            // Return data
            return XML2Array::createArray($curl_response);
        }
        else
        {
            throw new Exception( $curl_response . ' : ' . curl_getinfo($curl,CURLINFO_HTTP_CODE) );
        }
        curl_close($curl);
        return false;
    }


    /**
     *
     * Formats the Credentials for the cURL request.
     * @return string
     */
    private function credentials()
    {
        return $this->credentials->username() . ':' . $this->credentials->password();
    }

    /**
     *
     * Returns the Certificate URI
     *
     * @return string
     */
    protected function certUrl()
    {
        return realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . $this->cert_url;
    }

    /**
     *
     * Return the mode of the request.
     *
     * @return mixed
     */
    public function mode()
    {
        return $this->credentials->mode();
    }

    /**
     *
     * Return the customer number.
     *
     * @return mixed
     */
    public function customerNumber()
    {
        return $this->credentials->customerNumber();
    }


}
