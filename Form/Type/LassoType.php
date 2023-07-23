<?php

namespace MauticPlugin\MauticLassoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use MauticPlugin\MauticLassoBundle\Entity\Lasso;

class LassoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $switch = [
            'key' => 'Key',
            'add' => 'Add',
            'add_value' => 'Add Value',
            'substract_value' => 'Substract Value',
            'datetime' => 'Datetime(now)',
            //'date' => 'Date',
            'static' => 'Static',
            'static_date' => 'Static Date',
            'verification' => 'Verification',
        ];

        $coreFields = array_merge([            
            'tag' => 'Core tag',
            //'totalSpend' => 'Core total_spend',
            //'dateLastPurchase' => 'Core date_last_purchase',
        ], $options['core_fields']);

        $builder
            ->add('payload', TextType::class, [
                'label' => 'plugin.lasso.payload',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('switch', ChoiceType::class, [
                'label' => 'plugin.lasso.switch',
                'required' => true,
                'choices'           => array_flip($switch), // Choice type expects labels as keys
                'attr' => ['class' => 'form-control switch-payload-value']
            ])
            ->add('coreFields', ChoiceType::class, [
                'label' => 'plugin.lasso.coreFields',
                'required' => false,
                'choices'           => array_flip($coreFields), // Choice type expects labels as keys
                'attr' => ['class' => 'form-control core-mautic-field']
            ])
            
            
            
            ->add('staticField', TextType::class, [
                'label' => 'plugin.lasso.coreFields',
                'required' => false,
                'attr' => ['class' => 'form-control static-mautic-field lasso-hide']
            ])
            ;

            // In edit mode if the static switch is set then render the static Field 
            //textfield instead core filed choice box
            $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $data = $event->getData();
                    if (is_null($event->getData())) { return; }

                    if($data->getStaticField()){
                        $form->remove('staticField');
                        $form->add('staticField', TextType::class, [
                            'label' => 'plugin.lasso.coreFields',
                            'required' => false,
                            'attr' => ['class' => 'form-control static-mautic-field']
                        ]);
                    }
                }
            );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lasso_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lasso::class,
            'core_fields'       => [],
        ]);
    }

    public function getName()
    {
        return 'lasso_config';
    }
}