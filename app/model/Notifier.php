<?php
class Notifier extends WXEmail{
  public $from = "website@dejavu.oneblackbear.com";
  public $from_name = "Deja Vu Website";
    
  public function recruitment($post_data){
    $this->subject = "Deja Vu Application";
    $this->add_to_address("sheldon.els@gmail.com", "Sheldon");
    $this->post_data = $post_data;
  }
}
?>