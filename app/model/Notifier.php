<?php
class Notifier extends WXEmail{
  public $from = "website@dejavu.oneblackbear.com";
  public $from_name = "Deja Vu Website";
    
  public function recruitment($post_data){
    $this->subject = "Deja Vu Application";
    $this->add_to_address("marko.kommer@mail.ee", "Marko");
    $this->add_to_address("bartlomiej.michna@gmail.com", "Bartek");
    $this->add_to_address("arir47@gmail.com", "Ari");
    $this->post_data = $post_data;
  }
}
?>