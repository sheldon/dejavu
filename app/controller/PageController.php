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
  public $display_item_slots = array(0,2,4,5,6,7,8,9,14,15,16,17);
  public $counted_item_slots = array(0,1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17);
  
  public function controller_global(){
    $this->cms();
    if($this->action != "member"){
      $this->members();
      $chosen_one = $this->members[array_rand($this->members)]['name'];
      $this->member = $this->fetch_member_data($chosen_one);
      $this->model_viewer_width = 175;
      $this->model_viewer_height = 400;
    }
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
    $this->rec_form->add_element("Character Name", "TextInput", array("id"=>"name"));
    $this->rec_form->add_element("Class", "TextInput");
    $this->rec_form->add_element("Level", "TextInput");
    $this->rec_form->add_element("Talent Spec", "TextInput", array("id"=>"talent"));
    $this->rec_form->add_element("Age", "TextInput");
    $this->rec_form->add_element("Gear level (heroic/naxx10/etc.)", "TextInput", array("id"=>"gear"));
    $this->rec_form->add_element("Can you attend 2 of our weekly planned raids", "CheckboxInput", array("id"=>"attend"));
    $this->rec_form->add_element("Reason for leaving your previous guild", "TextareaInput", array("id"=>"reason_for_leaving"));
    $this->rec_form->add_element("About yourself", "TextareaInput", array("id"=>"about_yourself"));
    $this->rec_form->submit_text = "Apply to Guild";
    if($this->rec_form->save()){
      $notifier = new Notifier();
      $notifier->send_recruitment($this->rec_form->post_data);
      $this->recruitment_message = "Thanks for your application, we'll get back to your shortly.";
    }
  }
  
  public function member(){
    $this->member = $this->fetch_member_data(WaxUrl::get("id"));
    $this->model_viewer_width = 703;
    $this->model_viewer_height = 500;
    foreach($this->member['items'] as $item) if(in_array($item['slot'],$this->counted_item_slots) && $item['ilevel']){
      $total_ilevel += $item['ilevel'];
      $total_counted_items += 1;
    }
    if($total_counted_items > 0) $this->member['avg_ilevel'] = $total_ilevel / $total_counted_items;
    //print_r($this->member); exit;
  }
  
  static function sort_members_custom_cmp($a, $b)
  {
    return strcmp($a["name"], $b["name"]);
  }

  static function remove_alts($a)
  {
    return (($a["rank"] != 4) && ($a["rank"] != 6));
  }

  public function members(){
    $guild_list_url = "http://eu.wowarmory.com/guild-info.xml?r=".urlencode($this->server_name)."&n=".urlencode($this->guild_name)."&p=1";
		$xml = $this->cached_feed($guild_list_url);
		$this->members = $this->parse_xml($xml, "//character");
    //sort members by name
    usort($this->members, array("PageController", "sort_members_custom_cmp"));
    //remove alts from the member list
    $this->members = array_filter($this->members, array("PageController", "remove_alts"));
    
		//print_r($this->members); exit;
  }
  
  protected function fetch_member_data($character_name){    
    $character_data_url = "http://eu.wowarmory.com/character-sheet.xml?r=".urlencode($this->server_name)."&n=".urlencode($character_name);
		$character_xml = $this->cached_feed($character_data_url);
		$member = $this->parse_xml($character_xml, "//character");
		$member = $member[0];
		$member["items"] = $this->parse_xml($character_xml, "//item");
		foreach($member["items"] as $item_id => $item){
	    $item_reference = &$member["items"][$item_id];
      $wowhead_item_data = "http://www.wowhead.com/?item=".$item['id']."&xml";
  		$item_xml = $this->cached_feed($wowhead_item_data, 2592000); //cache wowhead item data for 30 days
  		$xml = @simplexml_load_string($item_xml, "SimpleXMLElement", LIBXML_NOERROR);
  		$ilevel = (integer) $xml->item->level;
  		$item_reference["ilevel"] = $ilevel;
  		$display_id = $this->parse_xml($item_xml, "//icon");
  		$display_id = $display_id[0]['displayId'];
  		$item_reference["wowhead_display_id"] = $display_id;
  		$slot_id = $this->parse_xml($item_xml, "//inventorySlot");
  		$slot_id = $slot_id[0]['id'];
  		$item_reference["wowhead_slot_id"] = $slot_id;
  		//print_r($item_reference); exit;
		}
		//print_r($member); exit;
		return $member;
  }
  
	protected function parse_xml($xml, $xpath, $items=false){		
		$xml = @simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOERROR);
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