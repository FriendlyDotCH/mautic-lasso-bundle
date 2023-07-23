<?php

namespace MauticPlugin\MauticLassoBundle\Service;

use Doctrine\ORM\EntityManager;
use MauticPlugin\MauticLassoBundle\Entity\Lasso;
use MauticPlugin\MauticLassoBundle\Entity\LassoPayloadData;
use Symfony\Component\HttpFoundation\Request;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\Tag;
use Mautic\CampaignBundle\Entity\Campaign;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticLassoBundle\Entity\CampaignLasso;
use Symfony\Component\DependencyInjection\Container;
use Mautic\MauticApi;

class LassoService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var Container
     */
    private $container;

    public function __construct(
        EntityManager $entityManager,
        LeadModel $leadModel,
        Container $container
    )
    {
        $this->em  = $entityManager;
        $this->leadModel = $leadModel;
        $this->container = $container;
    }

    public function processRawData(Request $request)
    {
        //By default call will be process if no api psw provide.
        $callFailed = false;
        $lassoConfigs = [];
        $processedData = [];
        
        $campaignId = $this->getCampaignId($request);
        $campaign = $this->em->getRepository(CampaignLasso::class)->find($campaignId);
        
        //Fetch all the Lasso config records from DB
        if($campaign){
            $lassoConfigs = $this->em->getRepository(Lasso::class)->getByCampaign($campaignId);
        }
        
        
        // Get raw json data from the webhook request
        $rawData = $request->getContent();
        $requestData = json_decode($rawData, true);
        
        if(is_array($requestData) && !empty($requestData)){
            
            foreach($lassoConfigs as $lassoConfig){
                // Get JSON index map 
                $payload = $lassoConfig->getPayLoad();

                // Perse all the payloads enclosed by []
                preg_match_all('/[^\[]+(?=\])/i', $payload, $matchs);
                $getData = $requestData;
                
                foreach($matchs[0] as $key => $match){
                   if(isset($getData["{$match}"])){
                        $getData = $getData["{$match}"];
                   }                    
                }
                
                // Get the value from the request param by index.
                $payloadData = isset($getData) ? $getData : '';
                $switcher = $lassoConfig->getSwitch();
                $mauticCoreField = $lassoConfig->getCoreFields();
                
                switch($switcher){
                    case 'key':
                        $processedData['email'] = $payloadData;
                        break;
                    case 'add':
                        if($mauticCoreField === 'tag'){                              
                            $processedData['tag'] = $payloadData;
                            
                        }else{
                            $processedData[$mauticCoreField] = $payloadData;
                        }
                        break;

                    case 'add_value':
                            $processedData['add_value'] = $payloadData;
                        break;    

                     case 'substract_value':
                            $processedData['substract_value'] = $payloadData;
                        break;
                     
                    case 'datetime':

                        $processedData[$mauticCoreField] = date('Y-m-d H:i:s');
                        break;
                     case 'date':
                        break;
                    case 'static':
                        $staticData = $lassoConfig->getStaticField() ? $lassoConfig->getStaticField() : null; 
                        $processedData['static'] = $staticData;
                        break;
                    case 'static_date':
                        $staticDate = $lassoConfig->getStaticField() ? $lassoConfig->getStaticField() : null; 
                        $processedData['static_date'] = $staticDate; 
                        break;
                        
                    case 'verification':                        
                        // If the switcher for verfication provide without []
                        if(!$matchs){
                            $awsApi = isset($requestData[$payload]) ? $requestData[$payload] : ''; 
                        }else{
                            $awsApi = $lassoConfig->getStaticField() ? $lassoConfig->getStaticField() : null;
                            //$lassoPayloadData->setVerification($awsApi);
                        }

                        // Request call will be failed if the api psw doesn't match with provided api psw.
                        if( $lassoConfig->getStaticField() && $lassoConfig->getStaticField() !== $awsApi){
                            $callFailed = true;
                            //$processedData['verification'] = false;
                        }
                        break; 
                }

                
            }
            if(!$callFailed){   
            // Process payload data
            $this->processPayloadData($processedData);

            }
        }
        
    }

    public function getRequest()
    {
        $rawData = file_get_contents("php://input");        
        return $rawData;
    }

    private function getCampaignId($request)
    {
        $requestedUrl = explode('/', $request->getPathInfo());
        return $requestedUrl[sizeof($requestedUrl) -1 ];
    }

    private function processPayloadData($data = [])
    {
        // Validate Basic auth to call the mautic API.
        $auth = $this->container->get('mautic.other.lasso.api_auth')->basicAuth();
        $api         = new MauticApi();
        $apiUrl      = 'http://'.$_SERVER['SERVER_NAME'].'/api';
        $contactApi = $api->newApi('contacts', $auth, $apiUrl);

        // Get all the core custom fields of a contact.
        $fields = $contactApi->getFieldList();
        
        // If email is provided on the request payload, check if either the email exist 
        // or it's not available in the contact. So then create a new contact by this email.
        if(isset($data['email'])){
            
            $lassoPayloadData = $this->em->getRepository(LassoPayloadData::class)->findOneByEmail($data['email']);
            
            if(!$lassoPayloadData){
                $lassoPayloadData =  new LassoPayloadData();
                
                // Set Campaign id
                $lassoPayloadData->setEmail($lassoPayloadData);
            }
            
            $data['static'] = $this->checkIsset($data, 'static');
            $data['static_date'] = $this->checkIsset($data, 'static_date');
            $tags = $data['tag'];
            // Remove tag values from the request data as there have no such custom field in contact core fields
            unset($data['tag']);
            
            // Retrieve contact with current email from payload
            $contactObj = $this->leadModel->getRepository()->findOneBy(['email' => $data['email']]);
            
            if($contactObj){
                // Get the contact.
                $contact = $contactApi->get($contactObj->getId());
                
                // Get all the core fields from the contact response.
                $fields = $contact[$contactApi->itemName()]['fields']['core'];
                
                // Generate new values for the existing custom field values.
                $data['add_value'] = $this->checkIsset($data, 'add_value')  + $this->checkIsset($fields['add_value'], 'value');
                $data['substract_value'] = $this->checkIsset($data, 'substract_value')  - $this->checkIsset($fields['substract_value'], 'value');
                
                // Edit existing contact with the provided email.
                $response = $contactApi->edit($contactObj->getId(), $data, true);
                $contact  = $response[$contactApi->itemName()];
            }else{                
                // Generate new values for the existing custom field values.
                $data['add_value'] = $this->checkIsset($data, 'add_value');
                $data['substract_value'] = $this->checkIsset($data, 'substract_value');
                
                // Create the contact
                $response = $contactApi->create($data);
                $contact  = $response[$contactApi->itemName()];

                $contactObj = $this->leadModel->getRepository()->findOneBy(['email' => $data['email']]);
                
            }

            // Add lead to the Lasso Payload Data.
            $lassoPayloadData->setLead($contactObj);
            $lassoPayloadData->setEmail($data['email']);
            $this->em->persist($lassoPayloadData);

            // Add tags to the current lead/contact
            $this->addTags($tags, $contactObj);

        }

        return ['status' => 'error', 'message' => 'No email has found!'];
    }

    public function addTags($tagDatas, $lead)
    {
        $tagDatas = explode(" ", $tagDatas);
                            
        foreach($tagDatas as $tagData){
            $tag = new Tag();
            $tag->setTag($tagData);
            $lead->addTag($tag);
            $this->em->persist($lead);
            $this->em->persist($tag);            
        }

        $this->em->flush();
    }

    private function checkIsset($data, $index)
    {
        return isset($data[$index]) ? $data[$index]  : null; 
    }
}