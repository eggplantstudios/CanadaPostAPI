<?php
/**
 * The CanadaPostShipment Class represents a parcel or package, and is used to estimate rates.
 * Accepts an array of arguments as per the Input Requirements for the getRates endpoint
 * Found here: http://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/rating/getrates/default.jsf
 *
 *
 * PHP version 5
 *
 * @package    CanadaPost
 * @author     Shawn Wernig <shawn@eggplantstudios.ca>
 * @version    1.0.0
 */

class CanadaPostShipment
{
    protected $args;

    /**
     *
     * Accepts arguments to create the Parcel/Package, see:
     * http://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/rating/getrates/default.jsf
     *
     * @param $args
     */
    public function __construct( $args )
    {
        $this->args = $args;
        $this->args['@attributes'] = array('xmlns' => "http://www.canadapost.ca/ws/ship/rate-v3");
    }

    /**
     *
     * Converts the $args array into XML for the getRates request.
     *
     * @param CanadaPost $canadaPost
     * @return string
     */
    public function toXML( CanadaPost $canadaPost )
    {
        $this->args['customer-number'] = $canadaPost->customerNumber();
        $xml = Array2XML::createXML('mailing-scenario', $this->args);
        echo $xml->saveXML();
        return $xml->saveXML();
    }

    /**
     *
     * Validates a postal code.
     *
     * @param $postal
     * @return bool
     */
    public static function validatePostal( $postal )
    {
        if ( preg_match("/[a-ceghj-npr-tv-z][0-9][a-ceghj-npr-tv-z][- ]?[0-9][a-ceghj-npr-tv-z][0-9]$/i", $postal ) == false )
        {
            return false;
        }
        return $postal;
    }
}