<?php
/**
 * Created by PhpStorm.
 * User: shawnwernig
 * Date: 15-03-27
 * Time: 11:13 AM
 */


require('canadaPost/class.CanadaPost.php');


$cp = new CanadaPost(
    new CanadaPostCredentials(
        array(
        'username'          => '5a149634026803ec',
        'password'          => '5f26d3e2c903aa888f93fe',
        'customerNumber'    => '0008119085',
        'mode'              => 'test'
        )
    )
);

try
{
    $origin = CanadaPostShipment::validatePostal('V0A1K0');
    $destination = CanadaPostShipment::validatePostal('K1K4T3');
    if( $origin !== false && $destination !== false )
    {
        $result = $cp->getRates( new CanadaPostShipment(
            array(
                'destination' => array(
                    'domestic' => array(
                        'postal-code' => $origin
                    )
                ),
                'origin-postal-code' => $destination,
                'parcel-characteristics' => array(
                    'weight' => 1,
                    'dimensions' => array(
                        'width' => 10,
                        'length' => 10,
                        'height' => 10
                    )
                )
            )
        ));

        echo '<pre>';
        print_r( $result );
    }
    else
    {
        throw new Exception('your postal code sucks');
    }
}
catch( Exception $e )
{
    die($e);
}



