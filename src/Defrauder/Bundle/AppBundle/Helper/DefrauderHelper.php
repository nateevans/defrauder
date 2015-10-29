<?php

namespace Defrauder\Bundle\AppBundle\Helper;

use Defrauder\Bundle\AppBundle\Entity\Transaction;
use Defrauder\Validator;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefrauderHelper extends ContainerAware
{
    protected $valid_amount_multiplier = 10;

    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * Check if the transaction is valid against the Defrauder Validator
     *
     * @param Transaction $transaction
     * @return bool Whether or not the transaction is valid
     */
    public function transactionIsValid(Transaction $transaction)
    {
        /** @var $em \Doctrine\Common\Persistence\ObjectManager */
        $em = $this->container->get('doctrine')->getManager();

        $transactionRepo = $em->getRepository('AppBundle:Transaction');

        $average = $transactionRepo->getAvgAmount();
        $amountIsValid = $this->amountIsValid($average, $transaction->getAmount());

        $zipcodes = $transactionRepo->getAllZips();
        $locationIsValid = $this->locationIsValid($zipcodes, $transaction->getZip());

        return $amountIsValid && $locationIsValid;
    }

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