<?php

class ImagenController extends Hw_Controller_Action
{
  public function init()
  {
      parent::init();
      $options = array(
         Zend_Db::ALLOW_SERIALIZATION => false
      );
      $params = array(
          'host'           => '127.0.0.1',
          'username'       => 'hwarch_gcasado',
          'password'       => 'yodo33',
          'dbname'         => 'hwarch_db',
          'options'        => $options
      );
      
     $this->db = Zend_Db::factory('Pdo_Mysql', $params);
     $this->db->getConnection();
      
  }
  
  public function indexAction()
  {
    
  	$request = $this->getRequest();
    $form  = new Forms_Image();
    
    $type = $this->_getParam('fold');
    $id = $this->_getParam('id');
    $objeto_id = $this->_getParam('objeto_id');
    
    $this->view->type= $type;
    $this->view->id= $id;
    
	$form->addAll(array(
		'fold' => $type,
		'n' =>$this->_getParam('n'),
		'id' => $id,
		'objeto_id' => $objeto_id
	));

    if ($this->getRequest()->isPost()) {
	    if ($form->isValid($request->getPost())) {
      		if (!$form->image->receive()) {
             	print "Error receiving the file";
   			}
   			
   			$data = $form->getValues();
   			
      		$file_info=array(
      		'path_small'=>DOCROOT.$this->_dataConfig['itemView'][$type]["list"]["thumb"]["path_small"],
      		'path_medium'=>DOCROOT.$this->_dataConfig['itemView'][$type]["list"]["thumb"]["path_medium"],
      		'path_original'=>DOCROOT.$this->_dataConfig['itemView'][$type]["list"]["thumb"]["path_original"],
   			'prefix_small'=>$this->_dataConfig['itemView'][$type]["list"]["thumb"]["prefix_small"],
   			'prefix_medium'=>$this->_dataConfig['itemView'][$type]["list"]["thumb"]["prefix_medium"],
   			'prefix_original'=>$this->_dataConfig['itemView'][$type]["list"]["thumb"]["prefix_original"]);  
   			
   			$img = new Image(array('file_info'=>$file_info));
   			$img->setCode($data['image']);
   			$img->setSrc($form->getPath());
   			//Upload de la imagen
   			$this->view->message = $img->save();
   			
            $name=$data['image'];
            
   			try {
   			  
	          //busco si existe la imagen
              $sql = "SELECT * FROM imagen where type='{$type}' and name='{$name}'";
              $resulset = $this->db->fetchAll($sql);
              $imagen_id = $resulset[0]['id'];
              
              if (!$imagen_id) { 
		        //inserto registro en imagen
       		    $registro = array(
      				'type' => $type,
                  	'name' => $name, 
    	    		'user'      => 'andrea',
                	'created' =>  date('Y-m-d H:i:s'),
                );
                $this->db->insert('imagen', $registro);
                $imagen_id = $this->db->lastInsertId();
              }
              //busco si ya existe la relacion
              $sql = "SELECT * FROM objeto_imagen where objeto_id={$objeto_id} and imagen_id={$imagen_id}";
              $resulset = $this->db->fetchAll($sql);
                  
              if (!$resulset[0]) {
     		    //guardo relacion con objeto	
     		    $registro = array(
        			'imagen_id' => $imagen_id,
        			'objeto_id' => $objeto_id,
                 	'user'      => 'andrea',
                 	'created' =>  date('Y-m-d H:i:s'),
                );
                $this->db->insert('objeto_imagen', $registro);
              }
              
              //redirijo a pagina del objeto
              $this->_redirect("objeto/edit?type={$type}&id={$id}");
              
   			} catch (Zend_Db_Adapter_Exception $e) {
    
                //print "Error: perhaps a failed login credential, or perhaps the RDBMS is not running";
                $this->view->message = $e->getMessage();
    
            } catch (Zend_Exception $e) {
        
                //print "Error: perhaps factory() failed to load the specified Adapter class";
                $this->view->message = $e->getMessage();
            }
   			
	    }
    }
        
    $this->view->form = $form;
  }
  
  public function editAction()
  {
    //edito imagen
    $type=$this->_getParam('type');
    $id=$this->_getParam('id');
    $objeto_id=$this->_getParam('objeto_id');
    $imagen_id=$this->_getParam('imagen_id');
    
    $accion=$this->_getParam('accion');
    
    $this->view->id=$id;
    $this->view->imagen_id=$imagen_id;
    $this->view->objeto_id=$objeto_id;
    $this->view->type=$type;
    
     try {
       
        //consulto la relacion objeto_imagen y la imagen
        
        $sql = "SELECT imagen.*, objeto_imagen.orden FROM imagen, objeto_imagen where imagen.id=objeto_imagen.imagen_id and objeto_id={$objeto_id} and imagen_id={$imagen_id}";
        $imagenes = $this->db->fetchAll($sql);
        $imagen= $imagenes[0];
        
        $this->view->imagen=$imagen;
        
        $config=Zend_Registry::get('dataConfig');
        $itemConfig=$config['itemView'][$type]['list'];
        $size='medium';
          
        $pref=$itemConfig['thumb']['prefix_'.$size];

        $path=$itemConfig['thumb']['path_'.$size];

        $this->view->path =$path.$pref ;
       
        $sql = "SELECT * FROM photographer order by name";
        $photographers = $this->db->fetchAll($sql);
        $this->view->photographers = $photographers;
            
        } catch (Zend_Db_Adapter_Exception $e) {
              print "perhaps a failed login credential, or perhaps the RDBMS is not running";
        } catch (Zend_Exception $e) {
              print "perhaps factory() failed to load the specified Adapter class";
        }
        
      if ($accion=='Save') {
        
          $orden=$this->_getParam('orden');
          $photographer_id=$this->_getParam('photographer');
          $copyright=$this->_getParam('copyright');
          $description_en=$this->_getParam('description_en');
          $description_ge=$this->_getParam('description_ge');
          
          //FIXME: Valido los campos y escapo los campos de entrada
          try {
            //Guardo los cambios
            $data = array(
  			'orden' => $orden,
            );
            $this->db->update("objeto_imagen", $data, "objeto_id={$objeto_id} and imagen_id={$imagen_id}");
            
            $data = array(
  				'photographer_id' => ($photographer_id)? $photographer_id : null,
            	'copyright' => $copyright,
                'description_en' => $description_en,
            	'description_ge' => $description_ge,
            );
            $this->db->update("imagen", $data, "id={$imagen_id}");
            
          } catch (Zend_Db_Adapter_Exception $e) {
            print "perhaps a failed login credential, or perhaps the RDBMS is not running";
          } catch (Zend_Exception $e) {
            print "perhaps factory() failed to load the specified Adapter class";
          }
  
          //Vuelvo a la lista de imagenes
          $this->_redirect("objeto/edit?type={$type}&id={$id}");
      } else if ($accion=='Cancel') {
        //Vuelvo a la lista de imagenes
        $this->_redirect("objeto/edit?type={$type}&id={$id}");
        
      }
     
    
  }
  
}