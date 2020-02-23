<?php

namespace App\Form;

use App\Entity\BillScan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BillScanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('billFile', FileType::class, [
                "mapped" => false,
                'required' => false,
                'auto_initialize' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF or IMAGE document',
                    ])
                ],
            ])
            ->add("fileName", HiddenType::class, [
                'required' => false
            ])
            ->add("fileNameOrig", HiddenType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BillScan::class,
        ]);
    }
}
