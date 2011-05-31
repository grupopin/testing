<?php

class ContactController extends Hw_Controller_Action
{
    public function indexAction()
    {
//        $id = (int)$this->_request->getParam('id');
//        if ($id == 0) {
//            $this->_redirect('/');
//            return;
//        }

    	if ($this->_getParam('submit')){
    		
    		
    		$filters=array('*'=>'StringTrim');    		
    		$validators=array();
    		$data=array_intersect_key($this->_getAllParams(),array('name'=>'','surname'=>'','email'=>'','message'=>'','country'=>''));
    		//print_r($data);
    		$input=new Zend_Filter_Input($filters,$validators,$data);
    		//print_r($input);
    		if ($input->isValid()){
    			$mail=new Zend_Mail("UTF-8");
    			$mail->setSubject($this->view->l("Contact information from HW Archive site"));
    			//$mail->addTo("archiv@harel.at","Andrea Furst");
    			$mail->addTo("archiv@harel.at","Andrea");
    			//$mail->addTo("levan.cheishvili@gmail.com","L.C.");
    			$mail->addBcc("casado.gustavo@gmail.com","Gustavo");
    			$mail->addBcc("hemy.ar@gmail.com","Hemy");
    			$txt=<<<EOD
  <table> 
   <tr>
   <td>Name: </td><td>{$input->getUnescaped('name')}</td>
   </tr>
   <tr>
   <td>Surname: </td><td>{$input->getUnescaped('surname')}</td>
   </tr>
   <tr>
   <td>Email:</td><td>{$input->getUnescaped('email')}</td>
   </tr>
   <tr>
   <td>Country:</td><td>{$input->getUnescaped('country')}</td>
   </tr>
   <tr>
   <td colspan='2'>	
    {$input->getUnescaped('message')}
  </td>
  </tr>
 </table> 
EOD;
    			$mail->setBodyHtml($txt,"UTF-8");
    			$mail->send();
    			$this->_redirect("/contact/thanks");
    			exit();
    		}
    		
    	}
    	
    	
          $this->view->title = 'Contact Form';
          $this->_helper->layout->setLayout('skins');
          

        
    }
    
    public function thanksAction(){
    	$this->view->title = 'Contact Form';
    	$this->_helper->layout->setLayout('skins');
    	 
    }
    
    public function browseAction()
    {
        $apaFinder = new Apa();
        $this->view->apas = $apaFinder->fetchAll(null, 'apa_title_en');
        $this->view->title = 'Contact Form';
        
	

		$newsFinder=new News();
		$this->view->news = $newsFinder->fetchLatest(10);
		$this->view->title_news="Latest News";
		
		$usersFinder=new Users();
		$this->view->users = $usersFinder->fetchLatest(2);
		$this->view->title_users="Latest Users";
		
		$form = new Zend_Form;
		$form->setAction('/login/do')     
			 ->setMethod('post');
		$form->setAttrib('id', 'login');
		
		$form->addElement('text', 'username'); 
		$form->addElement('text', 'telephone');
		$form->addElement('text', 'email');
		$form->addElement('textarea', 'comments');
		$form->addElement('submit', 'Send');
		$this->view->form=$form;

    }

    public function norouteAction()
    {
        // redirect to home page
        $this->_redirect('/');
    }
}