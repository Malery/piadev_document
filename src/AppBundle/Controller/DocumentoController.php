<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BackendBundle\Entity\Documento;
use Symfony\Component\HttpFoundation\Request;

class DocumentoController extends Controller
{
    public function indexAction()
    {
        return $this->render('BackendBundle:Default:index.html.twig');
    }
    
    public function newAction(Request $request) {
	$helpers = $this->get("app.helpers");
        
        $hash = $request->get("authorization",null);
        $checkAuth = $helpers->checkAuth($hash);
        
        if($checkAuth == true){
            $json = $request->get("json", null);
            //validado la autorización y halla recuperado los parámetros, que haya enviado en nombre del documento
            if($json!=null){
                $params = json_decode($json);
                $nombre = (isset($params->nombre))? $params->nombre:null;
                $numero = (isset($params->numero))? $params->numero:null;
                
                if($nombre != null && $numero!=null){
                    $fechaRegistro = new \DateTime("now");
                    $descripcion = (isset($params->descripcion))? $params->descripcion:null;
                    
                    $em = $this->getDoctrine()->getManager();
                    
                    $documento_aux = $em->getRepository("BackendBundle:Documento")
                                        ->findOneBy(array("numero" => $numero));
                    
                    if($documento_aux == null){
                        $documento = new Documento();
                    
                        $documento->setNombre($nombre);
                        $documento->setNumero($numero);
                        $documento->setFechaRegistro($fechaRegistro);
                        $documento->setDescripcion($descripcion);


                        $em->persist($documento);
                        $em->flush();

                        $data = $helpers->msgData("Documento ha sido creado!!","success",200,$documento);
                                              
                    }else{
                        $data = $helpers->msgData("Número de documento duplicado");   
                    }
   
                }
                else{
                    $data = $helpers->msgData("Falta el número y el nombre");
                }
            }
            else{
                $data = $helpers->msgData("No se enviaron los datos");
            }
        } else {
                $data = $helpers->msgData("No autorizado a esta sección");
        }
        return $helpers->json($data);
    }
    
    public function listAction(Request $request)
    {
        $helpers = $this->get("app.helpers");
        
        $hash = $request->get("authorization",null);
        $checkAuth = $helpers->checkAuth($hash);
        
        if($checkAuth == true){
            $em = $this->getDoctrine()->getManager();
            $documentos = $em->getRepository("BackendBundle:Documento")
                                     ->findBy(array("activo" => TRUE));
            if(count($documentos)>0){
                $data = $helpers->msgData("Exito","success",200,$documentos);
            }else{
                $data = $helpers->msgData("No hay registros");
            }
            
        }
        else{
            $data = $helpers->msgData("No autorizado a esta sección");
        }
        
        return $helpers->json($data);
        //return $helpers->json($request);
    }
    
    public function deleteAction(Request $request, $id=null)
    {
        $helpers = $this->get("app.helpers");
        
        $hash = $request->get("authorization",null);
        $checkAuth = $helpers->checkAuth($hash);
        //si la acturización es válida
        if($checkAuth == true){
            $em = $this->getDoctrine()->getManager();
            $documento = $em->getRepository("BackendBundle:Documento")
                            ->findOneBy(array("id" => $id));
            if($documento!=null){
                
                $documento->setActivo(false);
                $em->persist($documento);
                $em->flush();
                
                $data = $helpers->msgData("Documento ha sido eliminado","success",200,$documento);
            }else{
                $data = $helpers->msgData("Documento no existe");
            }
        }
        else{
            $data = $helpers->msgData("No autorizado a esta sección");
        }
        
        return $helpers->json($data);
    }
    
    public function editAction(Request $request, $id=null)
    {
        $helpers = $this->get("app.helpers");
        
        $hash = $request->get("authorization",null);
        $checkAuth = $helpers->checkAuth($hash);
        
        if($checkAuth == true){
            
            $em = $this->getDoctrine()->getManager();
            $documento = $em->getRepository("BackendBundle:Documento")
                            ->findOneBy(array("id" => $id));
            if($documento!=null){
                $json = $request->get("json", null);
                if($json != null){
                    $params = json_decode($json);
                    
                    $nombre = (isset($params->nombre))? $params->nombre:null;
                    $numero = (isset($params->numero))? $params->numero:null;
                    
                    if($nombre != null && $numero!=null){
                        
                        $descripcion = (isset($params->descripcion))? $params->descripcion:null;
                        

                        $documento_aux = $em->getRepository("BackendBundle:Documento")
                                            ->findOneBy(array("numero" => $numero));

                        if($documento_aux == null){                           

                            $documento->setNombre($nombre);
                            $documento->setNumero($numero);
                            $documento->setDescripcion($descripcion);

                            $em->persist($documento);
                            $em->flush();

                            $data = $helpers->msgData("Documento ha sido actualizado!!","success",200,$documento);

                        }else{
                            $data = $helpers->msgData("Número de documento duplicado");   
                        }

                    }
                    else{
                        $data = $helpers->msgData("Falta el número y el nombre");
                    }
                    
                    
                    
                    
                    
                    
                }
                else{
                    $data = $helpers->msgData("Datos no enviados");
                }              
                   
            }
            else{
                $data = $helpers->msgData("Documento no existe");
            }
            
        }
        else{
            $data = $helpers->msgData("No autorizado a esta sección");
        }
        
        return $helpers->json($data);
    }
}
