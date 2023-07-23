<?php

namespace MauticPlugin\MauticLassoBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Mautic\CampaignBundle\Entity\Campaign;
use MauticPlugin\MauticLassoBundle\Entity\CampaignLasso;
use Symfony\Component\Validator\Constraints\Valid;

class CampaignLassoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('name', TextType::class,[
            'label' => 'plugin.lasso.campaign',
            'required' => true,
        ]);
        /*$builder
        ->add('campaignId', EntityType::class, [
            'label' => 'plugin.lasso.campaign',
            'required' => true,
            'attr' => ['class' => 'form-control'],
            'class' => Campaign::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC');
            },
            'choice_label' => 'name',
        ]);*/

        /*$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $campaignLasso = $event->getData();
            $form = $event->getForm();
    
            // checks if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$campaignLasso || null === $campaignLasso->getId()) {
                $form->add('name', TextType::class,[
                    'label' => 'plugin.lasso.campaign',
                    'required' => true,
                ]);
            }else{
               $form->add('campaignId', EntityType::class, [
                'label' => 'plugin.lasso.campaign',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'class' => Campaign::class,
                'query_builder' => function (EntityRepository $er) {

        */            
        $builder->add(
            'lassos',
            CollectionType::class,
            [
                'label' => false,
                'allow_add'     => true,
                'allow_delete'  => true,
                'entry_type'    => LassoType::class,
                'by_reference'  => false,
                'constraints' => [
                    new Valid(),
                ],
                'entry_options' => [
                    'core_fields' => $options['core_fields'],
                    'label' => false
                ],
                
            ]
        );    

        $builder->add(
            'buttons',
            FormButtonsType::class
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'          => CampaignLasso::class,
            'remove_onclick'      => 'Mautic.removeFormListOption(this);',
            'option_required'     => true,
            'option_notblank'     => true,
            'constraint_callback' => false,
            'remove_icon'         => 'fa fa-times',
            'with_labels'         => false,
            'entry_type'          => LassoType::class,
            'add_value_button'    => 'mautic.core.form.list.additem',
            // Stores as [label => value] array instead of [list => [[label => the label, value => the value], ...]]
            'key_value_pairs'     => false,
            'option_constraint'   => [],
            'core_fields' => [],
        ]);

        $resolver->setDefined(
            [
                'remove_onclick',
                'option_required',
                'option_notblank',
                'remove_icon',
            ]
        );
    }
}