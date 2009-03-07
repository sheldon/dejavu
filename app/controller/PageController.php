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
  
  public $cache_time = 3600; //default cache time of 1 hour
  public $guild_name = "Deja Vu";
  public $server_name = "Azuremyst";
  
  public function controller_global(){
    $this->cms();
  }
  
  public function index(){
    $this->cms_section = new CmsSection();
    $this->cms_section = $this->cms_section->filter(array("title" => "News"))->first();
    $this->cms_content = new CmsContent("published");
    $this->cms_content = $this->cms_content->filter(array("cms_section_id" => $this->cms_section->id))->order("published DESC")->all();
  }
  
  public function login(){
    $user = new WildfireUser();
    $this->login_form = new WaxForm($user);
  }
  
  public function shoutbox(){
    
  }
  
  public function recruitment(){
    $this->rec_form = new WaxForm();
    $this->rec_form->add_element("Character Name", "TextInput");
    $this->rec_form->add_element("Class", "TextInput");
    $this->rec_form->add_element("Level", "TextInput");
    $this->rec_form->add_element("Talent Spec", "TextInput");
    $this->rec_form->add_element("About yourself", "TextareaInput");
    $this->rec_form->submit_text = "Apply to Guild";
    if($this->rec_form->save()){
      $notifier = new Notifier();
      $notifier->send_recruitment($this->rec_form->post_data);
      $this->recruitment_message = "Thanks for your application, we'll get back to your shortly.";
    }
  }
  
  public function member(){
    $allowed_item_slots = array(0,2,4,5,6,7,8,9,14,15,16,17);
    
    $character_data_url = "http://eu.wowarmory.com/character-sheet.xml?r=".urlencode($this->server_name)."&n=".urlencode(WaxUrl::get("id"));
		$character_xml = $this->cached_feed($character_data_url);
		$this->member = $this->parse_xml($character_xml, "//character");
		$this->member = $this->member[0];
		$this->member["items"] = $this->parse_xml($character_xml, "//item");
		foreach($this->member["items"] as $item_id => $item){
  		if(in_array($item['slot'], $allowed_item_slots)){
		    $item_reference = &$this->member["items"][$item_id];
        $wowhead_item_data = "http://www.wowhead.com/?item=".$item['id']."&xml";
    		$item_xml = $this->cached_feed($wowhead_item_data, 2592000); //cache wowhead item data for 30 days
    		$display_id = $this->parse_xml($item_xml, "//icon");
    		$display_id = $display_id[0]['displayId'];
    		$item_reference["wowhead_display_id"] = $display_id;
    		$slot_id = $this->parse_xml($item_xml, "//inventorySlot");
    		$slot_id = $slot_id[0]['id'];
    		$item_reference["wowhead_slot_id"] = $slot_id;
    		//print_r($item_reference); exit;
		  }
		}
		//print_r($this->member); exit;
  }
  
  public function members(){
    $guild_list_url = "http://eu.wowarmory.com/guild-info.xml?r=".urlencode($this->server_name)."&n=".urlencode($this->guild_name)."&p=1";
		$xml = $this->cached_feed($guild_list_url);
		$this->members = $this->parse_xml($xml, "//character");
		//print_r($this->members); exit;
  }
  
	protected function parse_xml($xml, $xpath, $items=false){		
		$xml = simplexml_load_string($xml);
		if(!count($xml)) return array();
		$counter = 0;
		$data=array();
		if($xml instanceOf SimpleXMLElement){
			foreach($xml->xpath($xpath) as $key => $child){
				if($items && $counter >= $items) break;
				else{
				  $tmparray = (array) $child;
				  $data[] = $tmparray['@attributes'];
			  }
				$counter++;
			}
		}
		return $data;
	}
	
	protected function cached_feed($url, $cache_time=false, $header=false){
	  if(!$cache_time) $cache_time = $this->cache_time;
		$fname = CACHE_DIR . md5($url).".xml";
		if(!is_readable($fname)) $mod_time = 0;
		else $mod_time = filemtime($fname);
		$now = time();
		if(($now - $mod_time) > $cache_time){
			if($copy = $this->fetch_file($url, $header)){
				@file_put_contents($fname, $copy);
				return $copy;
			}else return false;			
		}else return file_get_contents($fname);		
	}
	
	protected function fetch_file($url, $header=false){
		if(!$header) $header="application/rss+xml";
		$headers[] = "Content-Type: $header; charset=UTF-8";
    $headers[] = "Accept: $header; charset=UTF-8";
		$session = curl_init($url);
    curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($session, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
    $exec = curl_exec($session);		
		$info = curl_getInfo($session);
		if($info['http_code'] == 200) return $exec;
		else return false;
	}
}