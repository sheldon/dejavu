<?php

/**
 * Page Controller
 *
 * This is a default controller installed by PHP-WAX
 * Feel free to use this one, or just create your own using 'script/new_controller my_name'
 *
 * All you need to do in this controller is make one public method for each url.
 * Then make html tempalates in the 'view/page' directory.
 * The default 'index' is setup already.
 **/

class PageController extends ApplicationController {
  
  public function controller_global(){
    $this->cms();
  }
  
  public function index() {
    $this->cms_section = new CmsSection();
    $this->cms_section = $this->cms_section->filter(array("title" => "Home"))->first();
    $this->cms_content = $this->cms_section->content;
    print_r($this->cms_content); exit;
  }
  
  public function login() {
    $user = new WildfireUser();
    $this->login_form = new WaxForm($user);
  }
  
  public function shoutbox() {
    
  }
}