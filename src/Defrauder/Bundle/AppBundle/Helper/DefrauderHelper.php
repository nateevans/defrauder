<?php

namespace Defrauder\Bundle\AppBundle\Helper;

use Defrauder\Validator;

class DefrauderHelper
{
    protected $valid_amount_multiplier = 10;

    /**
     * Check whether the given amount is greater than
     * a multiple of the average
     *
     * @param int|float $average Average value
     * @param int|float $amount Incoming value
     * @return bool
     */
    public function amountIsValid($average, $amount)
    {
        $validator = new Validator();

        return $validator->amountIsValid($average, $amount, $this->valid_amount_multiplier);
    }

    /**
     * Check whether the incoming zipcode is within a valid distance
     * from a previous set of zipcodes
     *
     * @param array $previous_zipcodes
     * @param int $incoming_zipcode
     * @return bool
     */
    public function locationIsValid($previous_zipcodes, $incoming_zipcode)
    {
        $validator = new Validator();

        return $validator->locationIsValid($previous_zipcodes, $incoming_zipcode);
    }

    /**
     * Generate a RFC 4122 compliant uuid v4 string
     *
     * @param mixed $data Data to seed the UUID with
     * @return string uuid
     */
    public static function guidv4($data = null)
    {
        if (!$data) {
            $data = openssl_random_pseudo_bytes(16);
        }

        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}