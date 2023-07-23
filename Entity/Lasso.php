<?php

namespace MauticPlugin\MauticLassoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Mautic\CategoryBundle\Entity\Category;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Mautic\FormBundle\Entity\Form;

/**
 * This class processes payment requests from Webpayment
 * Class Lasso
 * @package MauticPlugin\MauticLassoBundle\Entity
 */
class Lasso extends FormEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * 
     * @var string
     */
    private $payload;

    /**
     * @var string
     */
    private $switch;

    /**
     * @var string
     */
    private $coreFields;

    /**
     * @var string
     */
    private $staticField;

    /**
     * @var CampaignLasso
     */
    private $campaignLasso;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint(
            'payload',
            new Assert\NotBlank(
                [
                    'message' => 'mautic.lasso.payload.required',
                ]
            )
        );

        $metadata->addPropertyConstraint(
            'switch',
            new Assert\NotBlank(
                ['message' => 'mautic.lasso.switch.required']
            )
        );

        /*$metadata->addPropertyConstraint(
            'coreFields',
            new Assert\NotBlank(
                ['message' => 'mautic.lasso.coreFields.required']
            )
        );*/
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata (ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('lasso_config')
            ->setCustomRepositoryClass(LassoRepository::class);

        // Helper functions
        $builder->addId();
        
        $builder->createField('payload', 'string')
            ->columnName('payload')
            ->build();

        $builder->createField('switch', 'string')
            ->columnName('switch')
            ->build();

        $builder->createField('coreFields', 'string')
            ->columnName('core_fields')
            ->nullable()
            ->build();    

        $builder->createField('staticField', 'string')
        ->columnName('staticField')
        ->nullable()
        ->build();    

        $builder->createManyToOne('campaignLasso', 'CampaignLasso')
            ->inversedBy('lassos')
            ->addJoinColumn('campaign_id', 'id', false, false, 'CASCADE')
            ->build();
    
 
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): Lasso
    {
        $this->payload = $payload;
        return $this;
    }

    public function getSwitch(): ?string
    {
        return $this->switch;
    }

    public function setSwitch(string $text): Lasso
    {
        $this->switch = $text;
        return $this;
    }


    public function getCoreFields(): ?string
    {
        return $this->coreFields;
    }

    public function setCoreFields(?string $text): Lasso
    {
        $this->coreFields = $text;
        return $this;
    }

    public function getStaticField(): ?string 
    {
        return $this->staticField;
    }

    public function setStaticField(?string $staticField): self 
    {
        $this->staticField = $staticField;
        return $this;
    }


    /**
     * Get Fake name to be compatable with getName of commonEntity. 
     */
    public function getName()
    {
        return $this->payload;
    }

    /**
     * Set Fake name to be compatable with getName of commonEntity. 
     * 
     */
    public function setName(string $payload): Lasso
    {
        $this->payload = $payload;
        return $this;
    }

    public function getcampaignLasso(): CampaignLasso
    {
        return $this->campaignLasso;
    }

    public function setcampaignLasso(CampaignLasso $campaignLasso): self
    {
        $this->campaignLasso = $campaignLasso;
        return $this;
    }
}