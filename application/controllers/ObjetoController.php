<?php

class ObjetoController extends Hw_Controller_Action
{
  public function init()
  {
    
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
    //muestro la lista de objetos
  }
  
  public function searchAction()
  {
    //muestro la lista de objetos
    
    $workNumber=$this->_getParam("work_number");
    $typeParam=$this->_getParam('type');
    
    $objectType=$typeParam[0];
    
    if ($workNumber) {
      $sql = "SELECT * FROM kb_trans k where term_arg1='{$objectType}' and term_arg2 like '{$workNumber}%' and term_active=1 and term_head='object' order by term_arg2";
      $items = $this->db->fetchAll($sql);
      $this->view->items=$items;
      $this->view->type=$objectType;
    }        
    
    
  }
  
  private function trim_text($string, $word_count=30)
	{
	    $trimmed = "";
	    $string = preg_replace("/\040+/"," ", trim($string));
	    $stringc = explode(" ",$string);
	    //echo sizeof($stringc);
	    if($word_count >= sizeof($stringc))
	    {
	        // nothing to do, our string is smaller than the limit.
	      return $string;
	    }
	    elseif($word_count < sizeof($stringc))
	    {
	        // trim the string to the word count
	        for($i=0;$i<$word_count;$i++)
	        {
	            $trimmed .= $stringc[$i]." ";
	        }
	       
	        if(substr($trimmed, strlen(trim($trimmed))-1, 1) == '.')
	          return trim($trimmed).'..';
	        else
	          return trim($trimmed).'...';
	    }
	}
  
  public function editAction()
  {
    
    //muestro las propiedades de un objeto y la lista de imagenes
    
    $id=$this->_getParam('id');
    $type=$this->_getParam('type');
    
    $this->view->id= $id;
    $this->view->type= $type;
    
    $sql = "SELECT * FROM objeto where tipo='work' and subtipo='{$type}' and codigo='{$id}'";
    $resulset = $this->db->fetchAll($sql);
    $objeto_id = $resulset[0]['id'];
    
    if ($objeto_id) {
      
      $this->view->objeto_id=$objeto_id;
      
        try {
            //Muestro datos del objeto de la tabla kb_trans
           
            $sql = "SELECT * FROM kb_trans where term_arg1='{$type}' and term_arg2='{$id}' and term_active=1 and term_head='object'";
            $items = $this->db->fetchAll($sql);
            $this->view->titulo = $items[0]['term_arg3'];
            $this->view->descripcion = $this->trim_text($items[0]['term_list']);
            
            //Muestro lista de imagenes
            
            $sql = "SELECT imagen.*, objeto_imagen.orden, photographer.name as photographer_name FROM imagen inner join objeto_imagen on imagen.id=objeto_imagen.imagen_id  left outer join photographer on photographer.id = photographer_id where objeto_id={$objeto_id} order by objeto_imagen.orden, imagen.id ";
            $imagenes = $this->db->fetchAll($sql);
            $this->view->imagenes = $imagenes;
            
            $config=Zend_Registry::get('dataConfig');
            $itemConfig=$config['itemView'][$type]['list'];
            $size='small';
              
            $pref=$itemConfig['thumb']['prefix_'.$size];
    
            $path=$itemConfig['thumb']['path_'.$size];
    
            $this->view->path =$path.$pref ;
      
        } catch (Zend_Db_Adapter_Exception $e) {
    
            print "perhaps a failed login credential, or perhaps the RDBMS is not running";
    
        } catch (Zend_Exception $e) {
    
            print "perhaps factory() failed to load the specified Adapter class";
    
        }
    } 
  }
  
   
  
  public function deleteAction()
  {
      
    //elimino imagen
    $type=$this->_getParam('type');
    $id=$this->_getParam('id');
    $objeto_id=$this->_getParam('objeto_id');
    $imagen_id=$this->_getParam('imagen_id');
    
     try {
       
          //elimino la relacion objeto_imagen
          $this->db->delete("objeto_imagen","objeto_id={$objeto_id} and imagen_id={$imagen_id}");
          
            
        } catch (Zend_Db_Adapter_Exception $e) {
    
            print "perhaps a failed login credential, or perhaps the RDBMS is not running";
    
        } catch (Zend_Exception $e) {
    
            print "perhaps factory() failed to load the specified Adapter class";
    
        }
     
      $this->_redirect("objeto/edit?type={$type}&id={$id}");
    
  }
  
  public function loadAction()
  {
    
     try {
       
            $objectType=$this->_getParam('type');
            $size = 'original';
            
            $sql = "SELECT * FROM kb_trans k where term_arg1='{$objectType}' and term_active=1 and term_head='object' order by term_arg2";
            $items = $this->db->fetchAll($sql);
            
            $i=0;
            $cantidad_imagenes=0;
            
            foreach ($items as $i=>$item){
                
                //busco si existe el objeto
                $codigo= $item['term_arg2'];
                
                $sql = "SELECT * FROM objeto where tipo='work' and subtipo='{$objectType}' and codigo='{$codigo}'";
                $resulset = $this->db->fetchAll($sql);
                $objeto_id = $resulset[0]['id'];
                    
                if (!$objeto_id) {
                  //sino existe agrego el objeto
                  $data = array(
                        'tipo' => 'work',
                        'subtipo' => $objectType,
            			'codigo' => $codigo,
          	    		'user' => 'andrea',
                      	'created' =>  date('Y-m-d H:i:s'),
                   );
                  $this->db->insert('objeto', $data);
                  $objeto_id = $this->db->lastInsertId();
                 
                }
                
                // si es hwg tengo que usar el work-id
                if ($objectType=='hwg') {
                  $sql = "SELECT * FROM graph where hwg='{$codigo}';";
                  $resultset = $this->db->fetchAll($sql);
                  $work_id = $resultset[0]['work_id'];
                } elseif ($objectType=='tap') { 
                  $sql = "SELECT * FROM tap where tap='{$codigo}';";
                  $resultset = $this->db->fetchAll($sql);
                  $work_id = $resultset[0]['work_id'];                
                } else {
                  $work_id = $codigo;
                }
              
                if (!work_id) continue;
                
                 // Recupero la lista de imágenes que esta mostrando ahora
                
                 $hwImage = new Hw_Image();
                 $imagenes = $hwImage->workImage2($objectType,$size,$work_id,'list',true,'asc',0,true,array('check_visibility'=>0));
                 
                 if (!$imagenes) 
                    continue;
                 
                 foreach ($imagenes as $j=>$imagen) {
                   //busco si existe la imagen
                   
                    $sql = "SELECT * FROM imagen where type='{$objectType}' and name='{$imagen}'";
                    $resulset = $this->db->fetchAll($sql);
                    $imagen_id = $resulset[0]['id'];
                    
                    if (!$imagen_id) {
                      //si no existe la agrego
                      $data = array(
                        'type' => $objectType,
            			'name' => $imagen,
          	    		'user' => 'andrea',
                      	'created' =>  date('Y-m-d H:i:s'),
                      );
                      $this->db->insert('imagen', $data);
                      $imagen_id = $this->db->lastInsertId();
                      $cantidad_imagenes++;
                    }   
                   
                    $sql = "SELECT * FROM objeto_imagen where objeto_id={$objeto_id} and imagen_id={$imagen_id}";
                    $resulset = $this->db->fetchAll($sql);
                    $relacion = $resulset[0]['objeto_id'];
                    
                    if (!$relacion) {
                      //creo la relacion con el objeto
                      $data = array(
            			'imagen_id' => $imagen_id,
            			'objeto_id' => $objeto_id,
                      	'user'      => 'andrea',
                      	'created' =>  date('Y-m-d H:i:s'),
                      );
                      $this->db->insert('objeto_imagen', $data);
                    }
                 }
                 
                
            }
            $this->view->cantidad = $i+1;
            $this->view->cantidad_imagenes = $cantidad_imagenes;
            
           
        } catch (Zend_Db_Adapter_Exception $e) {
    
            print "perhaps a failed login credential, or perhaps the RDBMS is not running";
    
        } catch (Zend_Exception $e) {
    
            print "perhaps factory() failed to load the specified Adapter class";
    
        }
    
    
  }
   
}  