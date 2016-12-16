<?php

namespace AppBundle\Service;

use Firebase\JWT\JWT;

/**
 * Description of JwtAuth
 *
 * @author anonymous
 */
class JwtAuth {
    public $manager;
    public $key;
    
    public function __construct($manager) 
    {
        $this->manager = $manager;
        $this->key = "clave-secreta-pidia";
    }
    
    public function checkToken($jwt,$getIdentity=false)
    {
        $key = $this->key;
        $auth = false;
        
        try{
            $decoded = JWT::decode($jwt, $key, array("HS256"));
            
        } catch (\UnexpectedValueException $ex) {
            $auth = false;
        }  catch (\DomainException $ex){
            $auth = false;
        }
        
        if(isset($decoded->sub)){
            $auth = true;
        }
        else{
            $auth = false;
        }
        
        if($getIdentity == TRUE){
            return $decoded;
        }
        else{
            return $auth;
        }
    }
}
