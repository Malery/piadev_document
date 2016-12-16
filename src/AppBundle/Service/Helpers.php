<?php

namespace AppBundle\Service;

class Helpers{
    
    public $jwt_auth;
    
    public function __construct($jwt_auth) {
        $this->jwt_auth = $jwt_auth;
    }
    
    public function checkAuth($hash, $getIdentity = false)
    {
        $jwt_auth = $this->jwt_auth;
        $auth = false;
        
        if($hash != NULL){
            if($getIdentity == false){
                $check_token = $jwt_auth->checkToken($hash);
                if($check_token == true){
                    $auth = true;
                }
            }
            else{
                $check_token = $jwt_auth->checkToken($hash,true);
                if(is_object($check_token)){
                    $auth = $check_token;
                }
            }
        }
        return $auth;
    }

    public function json($data)
    {
        // http://jmsyst.com/bundles/JMSSerializerBundle // Ampliar la funcionalidad
        
        $normalizer = new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer();
        $encoder =  new \Symfony\Component\Serializer\Encoder\JsonEncoder();
        
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        
        $serializer = new \Symfony\Component\Serializer\Serializer(array($normalizer),array($encoder));
        $json = $serializer->serialize($data, 'json');
        
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($json);
        $response->headers->set("Content-Type", "application/json");
        
        return  $response;
    }
    
    public function msgData($msg,$status='error',$code=400, $my_data=null)
    {
        $data = array(
            "status" => $status,
            "code" => $code,
            "msg" =>$msg,
            "data" => $my_data
        );

        return  $data;
    }
}

