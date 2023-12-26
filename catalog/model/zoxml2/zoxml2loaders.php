<?php
class ModelZoXml2ZoXml2Loaders extends Model {

public function doUserLoad($data) {

  if ($data['settings']['user_loader']=='exit') return NULL;
  
  return NULL;
  }

}
?>
