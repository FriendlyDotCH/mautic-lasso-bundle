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
use Mautic\CampaignBundle\Entity\Campaign;
use Mautic\LeadBundle\Entity\Lead;

/**
 * This class processes payment requests from Webpayment
 * Class EmailData
 * @package MauticPlugin\MauticLassoBundle\Entity
 */
class LassoPayloadData extends FormEntity
{
    /**
     * @var int
     */
    private $id;

    private $email;

    private $tag;

    private $totalSpend;

    private $dateLastPurchase;

    private $staticData;
    
    private $staticDate;

    private $verification;

    private $lead;
    
    private $campaignId;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint(
            'email',
            new Assert\Email(
                [
                    'message' => 'mautic.multidomain.email.invalid',
                ]
            )
        );

        $metadata->addPropertyConstraint(
            'dateLastPurchase',
            new Assert\Date(
                [
                    'message' => 'mautic.multidomain.email.invalid',
                ]
            )
        );
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata (ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('lasso_payload_data')
            ->setCustomRepositoryClass(LassoPayloadDataRepository::class);

        // Helper functions
        $builder->addId();
        
        $builder->createField('email', 'string')
            ->columnName('email')
            ->build();

        $builder->createField('totalSpend', 'decimal')
            ->columnName('totalSpend')
            ->nullable()
            ->build();

        $builder->createField('tag', 'string')
        ->columnName('tag')
        ->nullable()
        ->build();    

        $builder->createField('dateLastPurchase', 'datetime')
        ->columnName('dateLastPurchase')
        ->nullable()
        ->build();    
 
        $builder->createOneToOne('campaignId', 'MauticPlugin\MauticLassoBundle\Entity\CampaignLasso')
            ->cascadePersist()
            ->cascadeMerge()
            ->cascadeDetach()
            ->addJoinColumn('campaignId', 'id', true, false, 'SET NULL')
            ->build();

        $builder->createField('verification', 'string')
        ->columnName('verification')
        ->nullable()
        ->build();  
        
        $builder->createField('staticData', 'string')
        ->columnName('staticData')
        ->nullable()
        ->build();  
        
        $builder->createField('staticDate', 'datetime')
        ->columnName('staticDate')
        ->nullable()
        ->build();

        $builder->createManyToOne('lead', 'Mautic\LeadBundle\Entity\Lead')
            ->cascadePersist()
            ->cascadeMerge()
            ->cascadeDetach()
            ->addJoinColumn('lead_id', 'id', true, false, 'SET NULL')
            ->build();

        //$builder->addLead(true, 'SET NULL');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getTotalSpend(): ?float
    {
        return $this->totalSpend;
    }

    public function setTotalSpend(float $amount): self
    {
        $this->totalSpend = $amount;
        return $this;
    }


    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function getDateLastPurchase(): ?\DateTime
    {
        return $this->dateLastPurchase;
    }

    public function setdateLastPurchase($dateLastPurchase): self
    {
        $this->dateLastPurchase = $dateLastPurchase;
        return $this;
    }

    public function getVerification(): ?string
    {
        return $this->verification;
    }

    public function setVerification(string $verification): self
    {
        $this->verification = $verification;
        return $this;
    }
    

    public function getStaticData(): ?string
    {
        return $this->staticData;
    }

    public function setStaticData(?string $staticData = null): self
    {
        $this->staticData = $staticData;
        return $this;
    }

    public function getStaticDate(): ?\DateTime
    {
        return $this->staticDate;
    }

    public function setStaticDate($staticDate = null): self
    {
        $this->staticDate = $staticDate;
        return $this;
    }

    public function getCampaignId()
    {
        return $this->campaignId;
    }

    public function setcampaignId($id = null): self
    {
        $this->campaignId = $id;
        return $this;
    }
    
    /**
     * Get Fake name to be compatable with getName of commonEntity. 
     */
    public function getName()
    {
        return $this->email;
    }

    public function setName(string $email) : self
    {
        $this->email = $email;
        return $this;
    }
    /**
     * Set Fake name to be compatable with getName of commonEntity. 
     * 
     */
    public function setLead(Lead $lead): self
    {
        $this->lead = $lead;
        return $this;
    }

    public function getLead(): ?Lead
    {
        return $this->lead;
    }

    public function getProperty($name)
    {
        return $this->{$name};
    }

    public function setProperty($name, $value): self
    {
        $this->{$name} = $value;
        return $this;
    }
}
