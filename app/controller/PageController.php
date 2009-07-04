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
  public $guild_ranks = array("Guild Master","Officer","Officer Alt","Raider","Member","Initiate","NOT USED","Alt");
  public $display_ranks = array(1,3,4,5,6,7,8);
  public $display_item_slots = array(0,2,4,5,6,7,8,9,14,15,16,17);
  public $counted_item_slots = array(0,1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17);
  public $this_page = false;
  
  public function controller_global(){
    $this->cms();
  }
  
  public function index(){
    $this->cms_section = new CmsSection();
    $this->cms_section = $this->cms_section->filter(array("title" => "News"))->first();
    $this->cms_content = new CmsContent("published");
    $this->cms_content = $this->cms_content->filter(array("cms_section_id" => $this->cms_section->id))->limit(2)->all();
  }
  
  public function login(){
    $user = new WildfireUser();
    $this->login_form = new WaxForm($user);
  }
  
  public function shoutbox(){
    
  }
  
  public function vent(){
    $this->vent_stat = new CVentriloStatus;
    $this->vent_stat->m_cmdprog	= APP_LIB_DIR."ventrilo_status";
    $this->vent_stat->m_cmdcode	= "2";
    $this->vent_stat->m_cmdhost	= "hydrogen.typefrag.com";
    $this->vent_stat->m_cmdport	= "28674";
    $this->vent_stat->m_cmdpass	= "";
    
    if($this->vent_stat->Request()) $this->vent_stat = false;
  }
  
  public function recruitment(){
    $this->rec_form = new WaxForm();
    $this->rec_form->add_element("age", "TextInput");
    $this->rec_form->add_element("english_level", "TextInput", array('label'=>'How well do you speak english'));
    $this->rec_form->add_element("name", "TextInput", array("label"=>"Character Name"));
    $this->rec_form->add_element("class", "TextInput", array('label'=>"Character Class"));
    $this->rec_form->add_element("level", "TextInput", array('label'=>"Character Level"));
    $this->rec_form->add_element("gear", "TextInput", array("label"=>"Average gear level (heroic/naxx10/25/uld10/25)"));
    $this->rec_form->add_element("attendance", "TextInput", array("label"=>"Can you attend 2 of our weekly planned raids every week"));
    $this->rec_form->add_element("talents", "TextareaInput", array("label"=>"Your chosen raid talents and why you chose them"));
    $this->rec_form->add_element("previous_guild", "TextareaInput", array("label"=>"Reason for leaving your previous guild"));
    $this->rec_form->add_element("raid_experience", "TextareaInput", array("label"=>"Previous raiding experience (which classes have you played in raids, and to what level of raid)"));
    $this->rec_form->add_element("internet", "TextareaInput", array("label"=>"What kind of internet connection do you have. (Please mention anything about regular lags here)"));
    $this->rec_form->add_element("other_members", "TextareaInput", array("label"=>"Do you know any other members in Deja Vu and what is your replationship to them"));
    $this->rec_form->add_element("about", "TextareaInput", array("label"=>"About Yourself"));
    $this->rec_form->submit_text = "Apply to Guild";
    if($this->rec_form->save()){
      $notifier = new Notifier();
      $notifier->send_recruitment($this->rec_form);
            
      $forum_data_row = array(
        "user_id"=>"1",
        "status"=>"0",
        "forum_id"=>"7",
        "thread"=>"0",
        "parent_id"=>"0",
        "thread_count"=>"0",
        "author"=>"Paracetamol",
        "ip"=>"127.0.0.1",
        "status"=>"2",
        "modifystamp"=>time(),
        "subject"=>"Application from ".$this->rec_form->handler->elements['name']->value,
        "body"=>"",
      );
      
      foreach($this->rec_form as $name => $element) $forum_data_row['body'] .= "[b][size=large]" . $element->label . " :[/size][/b]\n" . $element->value . "\n\n";
      
      $forum_model = new WaxModel;
      $forum_model->table = "phorum_messages";
      $forum_model->row = $forum_data_row;
      $forum_model->primary_key = "message_id";
      $forum_model->save();
      $forum_model->thread = $forum_model->message_id;
      $forum_model->save();

      $this->recruitment_message = 'Thanks for your application, we\'ll get back to your shortly. Please check our <a href="/forum/list.php?7">recruitment forum</a> for an assessment from our members.';
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

  public function members(){
    $guild_list_url = "http://eu.wowarmory.com/guild-info.xml?r=".urlencode($this->server_name)."&n=".urlencode($this->guild_name)."&p=1";
		$xml = $this->cached_feed($guild_list_url);
		$unsorted_members = $this->parse_xml($xml, "//character");
    //sort members by name
    usort($unsorted_members, array("PageController", "sort_members_custom_cmp"));
    //sort members by rank
    foreach($this->guild_ranks as $rank_id => $rank_name) $this->members[$rank_id] = array();
    foreach($unsorted_members as $id => $member){
			$item = &$unsorted_members[$id];
			if($item['rank'] === "0") $item['rank'] = "1";
			if($item['rank'] === "2") $item['rank'] = "7";
			$this->members[$item['rank']][] = &$item;
    }
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