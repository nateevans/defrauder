<?php

namespace Defrauder\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Defrauder\Bundle\AppBundle\Entity\Transaction;

class LoadTransactionData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $transaction_data = array(
          array(
            'name'    => 'Shell Gas Station',
            'amount'  => 32.54,
            'date'    => '07/16/2012 05:30PM',
            'address' => '1234 Test Lane',
            'city'    => 'Detroit',
            'state'   => 'MI',
            'zip'     => '48226'
          ),
          array(
            'name'    => 'Starbucks Coffee',
            'amount'  => 4.85,
            'date'    => '07/17/2012 08:05AM',
            'address' => '5111 Maple Drive',
            'city'    => 'Royal Oak',
            'state'   => 'MI',
            'zip'     => '48067'
          ),
          array(
            'name'    => 'Olive Garden',
            'amount'  => 45.23,
            'date'    => '07/16/2012 07:45PM',
            'address' => '5523 Oak Street',
            'city'    => 'Royal Oak',
            'state'   => 'MI',
            'zip'     => '48067'
          ),
          array(
            'name'    => 'Target',
            'amount'  => 24.90,
            'date'    => '07/17/2012 10:13AM',
            'address' => '9913 Pinewood Avenue',
            'city'    => 'Ferndale',
            'state'   => 'MI',
            'zip'     => '48220'
          ),
        );

        foreach ($transaction_data as $data) {
            $transaction = new Transaction();
            $transaction->setName($data['name']);
            $transaction->setAmount($data['amount']);
            $transaction->setCreated(new \DateTime($data['date']));
            $transaction->setAddress($data['address']);
            $transaction->setCity($data['city']);
            $transaction->setState($data['state']);
            $transaction->setZip($data['zip']);

            $manager->persist($transaction);
        }

        $manager->flush();
    }
}