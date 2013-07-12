<?php

class SiteController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->_forward('contact');
    }

    public function contactAction()
    {
        // action body
    }

    public function aboutAction()
    {
        // action body
    }

    public function termAction()
    {
        // action body
    }

    public function friendLinkAction()
    {
        $mFriendLink = new Application_Model_FriendLink();
        $$this->view->links = $mFriendLink->getAll();
    }


}







