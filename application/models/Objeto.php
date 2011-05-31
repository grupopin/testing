<?php

class Application_Model_Objeto
{

    protected $_id;
  
    protected $_tipo;
    
    protected $_subtipo;
    
    protected $_codigo;
    
    protected $_user;

    protected $_created;

    
    public function __construct(array $options = null)

    {

        if (is_array($options)) {

            $this->setOptions($options);

        }

    }

 

    public function __set($name, $value)

    {

        $method = 'set' . $name;

        if (('mapper' == $name) || !method_exists($this, $method)) {

            throw new Exception('Invalid objeto property');

        }

        $this->$method($value);

    }

 

    public function __get($name)

    {

        $method = 'get' . $name;

        if (('mapper' == $name) || !method_exists($this, $method)) {

            throw new Exception('Invalid objeto property');

        }

        return $this->$method();

    }

 

    public function setOptions(array $options)

    {

        $methods = get_class_methods($this);

        foreach ($options as $key => $value) {

            $method = 'set' . ucfirst($key);

            if (in_array($method, $methods)) {

                $this->$method($value);

            }

        }

        return $this;

    }

 

    public function setTipo($text)

    {

        $this->_tipo = (string) $text;

        return $this;

    }

 

    public function getTipo()

    {

        return $this->_tipo;

    }


    public function setSubtipo($text)

    {

        $this->_subtipo = (string) $text;

        return $this;

    }

 
    public function getSubtipo()

    {

        return $this->_subtipo;

    }
    
    
    public function setCodigo($text)

    {

        $this->_codigo = (string) $text;

        return $this;

    }

 
    public function getCodigo()

    {

        return $this->_codigo;

    }
    
    public function setUser($text)

    {

        $this->_user = (string) $text;

        return $this;

    }

 
    public function getUser()

    {

        return $this->_user;

    }
   
    public function setCreated($ts)

    {

        $this->_created = $ts;

        return $this;

    }

 

    public function getCreated()

    {

        return $this->_created;

    }

 

    public function setId($id)

    {

        $this->_id = (int) $id;

        return $this;

    }

 

    public function getId()

    {

        return $this->_id;

    }

}

