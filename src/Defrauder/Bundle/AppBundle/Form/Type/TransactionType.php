<?php

namespace Defrauder\Bundle\AppBundle\Form\Type;

use ReCaptchaSecureToken\ReCaptchaToken;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Tests\ContainerAwareEventDispatcherTest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\DependencyInjection\ContainerAware;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class TransactionType extends AbstractType
{
    /** @var Container */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('name', 'text', array('label' => 'Business Name'))
          ->add('amount')
          ->add('address')
          ->add('city')
          ->add('state')
          ->add('zip')
          ->add(
            'recaptcha',
            'ewz_recaptcha',
            array(
              'label'       => false,
              'attr'        => array(
                'options' => array(
                  'theme'       => 'light',
                  'type'        => 'image',
                  'secureToken' => $this->getRecaptchaSecureToken()
                )
              ),
              'mapped'      => false,
              'constraints' => array(
                new RecaptchaTrue()
              )
            )
          )
          ->add('validate', 'submit');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
          array(
            'data_class' => 'Defrauder\Bundle\AppBundle\Entity\Transaction',
          )
        );
    }

    /**
     * Get a secure token for reCAPTCHA so we aren't restricted to a specific domain...
     * @see https://developers.google.com/recaptcha/docs/secure_token
     *
     * @return string
     */
    public function getRecaptchaSecureToken()
    {
        $public_key = $this->container->getParameter('recaptcha.public_key');
        $private_key = $this->container->getParameter('recaptcha.private_key');

        $recaptchaToken = new ReCaptchaToken(array('site_key' => $public_key, 'site_secret' => $private_key));

        return $recaptchaToken->secureToken(uniqid('recaptcha'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'defrauder_transaction';
    }
}