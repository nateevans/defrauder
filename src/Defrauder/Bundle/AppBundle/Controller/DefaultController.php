<?php

namespace Defrauder\Bundle\AppBundle\Controller;

use Defrauder\Bundle\AppBundle\Entity\Transaction;

use Defrauder\Bundle\AppBundle\Helper\DefrauderHelper;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $trasaction = new Transaction();
        $form = $this->createForm(
          'defrauder_transaction',
          $trasaction,
          array(
            'constraints' => array(
              new Assert\Callback(array($this, 'validateTransaction'))
            )
          )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $defrauderHelper = $this->get('app.defrauder.helper');

            if ($defrauderHelper->transactionIsValid($trasaction)) {
                // seems legit...
                /** @var $em \Doctrine\Common\Persistence\ObjectManager */
                $em = $this->getDoctrine()->getManager();

                $em->persist($trasaction);
                $em->flush();

                return $this->redirectToRoute('success', array('uuid' => $trasaction->getUuid()));
            } else {
                // sketchy transaction!
                return $this->redirectToRoute('fail');
            }
        }

        return $this->render(
          'AppBundle:Default:index.html.twig',
          array(
            'form' => $form->createView()
          )
        );
    }

    /**
     * @Route("/success/{uuid}", name="success")
     * @Method({"GET"})
     * @param string $uuid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successAction($uuid)
    {
        /** @var $em \Doctrine\Common\Persistence\ObjectManager */
        $em = $this->getDoctrine()->getManager();

        $transactionRepo = $em->getRepository('AppBundle:Transaction');
        $transaction = $transactionRepo->findOneBy(array('uuid' => $uuid));

        return $this->render(
          'AppBundle:Default:success.html.twig',
          array(
            'transaction' => $transaction
          )
        );
    }

    /**
     * @Route("/fail", name="fail")
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function failAction()
    {
        return $this->render('AppBundle:Default:fail.html.twig');
    }

    /**
     * Validate the transaction datas
     *
     * @todo This should probably be pulled out into a service
     * @param \Defrauder\Bundle\AppBundle\Entity\Transaction $transaction
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     */
    public function validateTransaction(Transaction $transaction, ExecutionContextInterface $context)
    {
        $name = $transaction->getName();
        if (empty($name)) {
            $context->buildViolation('Name is required!')->atPath('name')->addViolation();
        }

        $amount = $transaction->getAmount();
        if (!is_int($amount) && !is_float($amount)) {
            // make sure number like...
            $context->buildViolation('Amount must be a number!')->atPath('amount')->addViolation();
        } elseif ((int) $amount != $amount) {
            // validate decimal places
            $decimals = strlen($amount) - strrpos($amount, '.') - 1;
            if ($decimals > 2) {
                $context->buildViolation('Amount must have no more than 2 decimal places!')
                  ->atPath('amount')
                  ->addViolation();
            }
        }

        //todo: actually validate the address...
        $address = $transaction->getAddress();
        if (empty($address)) {
            $context->buildViolation('Address is required!')->atPath('address')->addViolation();
        }

        $city = $transaction->getCity();
        if (empty($city)) {
            $context->buildViolation('City is required!')->atPath('city')->addViolation();
        }

        $state = $transaction->getState();
        if (empty($state)) {
            $context->buildViolation('State is required!')->atPath('state')->addViolation();
        }

        $zip = $transaction->getZip();
        if (!is_int($zip)) {
            // make sure zip number like...
            $context->buildViolation('Zip must be a number!')->atPath('zip')->addViolation();
        } elseif (strlen((string) $zip) != 5) {
            $context->buildViolation('Zip must be 5 digits!')->atPath('zip')->addViolation();
        }
    }
}
