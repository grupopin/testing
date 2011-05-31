<?php
class AuthController extends Hw_Controller_Action
{


	public function indexAction(){
		print "index";
	}



	public function loginAction(){

		//read all users from config
		$users=$this->_config->user->toArray();

		//print_r($this->_session->user);
		if ($this->_session->user['name']){
			print $this->view->l("You are logged in already");
			return true;
		}

		// print "be admin:".$this->_config->be_admin_ps."<br>";
		if ($this->_getParam("ps")){
			//$found=false;
			foreach($users as $uid=>$uprops){
				if ($uprops['ps']==$this->_getParam('ps')){
					$this->_session->user=$uprops;
					$this->_session->user['id']=$uid;
					$from=urldecode($this->_getParam("from"));
					return $this->_redirect($from);
				}
			}

			$this->view->notcorrect=true;

		}else{
			$this->view->from=$this->_getParam("from");


		}



	}




}


?>