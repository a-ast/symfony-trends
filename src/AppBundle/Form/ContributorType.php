<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContributorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('githubId')
            ->add('githubLogin')
            ->add('email')
            ->add('gitEmails')
            ->add('name')
            ->add('gitNames')
            ->add('country')
            ->add('githubLocation')
            ->add('isCoreMember', CheckboxType::class, [
                'label' => 'Core member'
            ])
            ->add('createdAt', DateType::class, array('widget' => 'single_text'))
            ->add('updatedAt', DateType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contributor'
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'contributor';
    }


}
