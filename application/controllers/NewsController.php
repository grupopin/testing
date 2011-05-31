<?php

class NewsController extends Zend_Controller_Action
{
    public function indexAction()
    {
    print_r($this->_request);
        $id = (int)$this->_request->getParam('id');
        if ($id == 0) {
            $this->_redirect('/');
            return;
        }

        $newsFinder = new News();
        $news = $newsFinder->fetchRow('news_id='.$id);
        if ($news->news_id != $id) {
            $this->_redirect('/');
            return;
        }
        $this->view->news = $news;
        $this->view->title = $news->news_title;

    }
    
    public function browseAction()
    {
        $newsFinder = new News();
        $this->view->news = $newsFinder->fetchAll(null, 'news_title');
        $this->view->title = 'Browse News !!!';
    }

    public function norouteAction()
    {
        // redirect to home page
        $this->_redirect('/');
    }
}