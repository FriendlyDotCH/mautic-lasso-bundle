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
use Doctrine\Common\Collections\ArrayCollection;
use Mautic\CampaignBundle\Entity\Campaign;

/**
 * This class processes payment requests from Webpayment
 * Class CampaignLasso
 * @package MauticPlugin\MauticLassoBundle\Entity
 */

class CampaignLasso  extends FormEntity
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $campaignId;

    /**     
     * @var string
     */
    private $compaignUrl;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ArrayCollection
     */
    private $lassos;

    public function __construct()
    {
        $this->lassos = new ArrayCollection();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint(
            'compaignUrl',
            new Assert\Url(
                ['message' => 'mautic.lasso.url.invalid']
            )
        );
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata (ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('compaign_lasso')
            ->setCustomRepositoryClass(LassoPayloadDataRepository::class);

        // Helper functions
        $builder->addId();

        $builder->createField('name', 'string')
            ->columnName('name')
            ->build();

        $builder->createOneToMany('lassos', 'Lasso')
        ->setIndexBy('id')
        ->mappedBy('campaignLasso')
        ->fetchExtraLazy()
        ->cascadePersist()
        ->cascadeMerge()
        ->build();
        
        $builder->createOneToOne('campaignId', 'Mautic\CampaignBundle\Entity\Campaign')
            ->cascadePersist()
            ->cascadeMerge()
            ->cascadeDetach()
            ->addJoinColumn('campaignId', 'id', true, false, 'SET NULL')
            ->build();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCampaignId(): ?Campaign 
    {
        return $this->campaignId;
    }

    public function setcampaignId(Campaign $id): self
    {
        $this->campaignId = $id;
        return $this;
    }

    public function getCompaignUrl() : ?string
    {
        return $this->compaignUrl;
    }

    public function setCompaignUrl(string $url): self
    {
        $this->compaignUrl = $url;
        return $this;
    }

    public function getLassos()
    {
        return $this->lassos;
    }

    public function addLasso($lasso): self
    {
        $this->lassos->add($lasso);
        $lasso->setcampaignLasso($this);
        return $this;

        /*if(!$this->lassos->contains($lasso)){
            $this->lassos->add($lasso);
            $lasso->setcampaignLasso($this);
            return $this;
        }*/
    }

    public function removeLasso($lasso): self
    {
        //if($this->lassos->contains($lasso)){
            $this->lassos->removeElement($lasso);
        //}
        return $this;
    }
    
     /**
     * Get Campaign name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *  Set Campaign name
     * 
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

}