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
        'username'          => 'your-username',
        'password'          => 'your-password',
        'customerNumber'    => 'your-customerNumber',
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



