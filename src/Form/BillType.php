<?php

namespace App\Form;

use App\Entity\Bill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shop', TextType::class)
            ->add('date', DateType::class, [
                'label' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false
            ])
            ->add('summary_brutto', NumberType::class)
            ->add('summary_netto', NumberType::class)
            ->add('positions', CollectionType::class, [
                'label' => false,
                'entry_type' => PositionType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
        ;
        return $builder;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bill::class,
        ]);
    }
}
