<?php
/**
 * The CanadaPostCredentials Class contains your credentials.
 *
 * PHP version 5
 *
 * @package    CanadaPost
 * @author     Shawn Wernig <shawn@eggplantstudios.ca>
 * @version    1.0.0
 */

class CanadaPostCredentials
{
    protected $username;
    protected $password;
    protected $customerNumber;
    protected $mode;
    private $accepted_modes = array('live','test');

    /**
     *
     * Requires an array of credentials:
     *  mode
     *  password
     *  username
     *  customerNumber
     *
     * @param $credentials
     */
    public function __construct( $credentials )
    {
        try
        {
            $credentials = $this->validate( $credentials );
        }
        catch(Exception $e)
        {
            die($e);
        }

        $this->mode = $credentials['mode'];
        $this->password = $credentials['password'];
        $this->username = $credentials['username'];
        $this->customerNumber = $credentials['customerNumber'];

    }

    /**
     *
     * Validates the credentials - specifically the 'mode'
     *
     * @param $credentials
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function validate( $credentials )
    {
        foreach( get_class_vars( get_class( $this ) ) as $name => $var )
        {
            if( array_key_exists( $name, $credentials ) )
            {
                $this->$name = $credentials[$name];
            }
        }

        if( ! in_array( strtolower($credentials['mode']), $this->accepted_modes ) )
        {
            throw new InvalidArgumentException( $credentials['mode'] . " is not a valid mode.");
        }

        return $credentials;
    }

    public function username()
    {
        return $this->username;
    }

    public function customerNumber()
    {
        return $this->customerNumber;
    }

    public function password()
    {
        return $this->password;
    }

    public function mode()
    {
        return $this->mode;
    }

}