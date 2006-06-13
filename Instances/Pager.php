<?

/*
  show()
v img()

*/

class Pager {
      var $designNum;
      function fileCaption($id) {
        $obj = new Archivo;
        $obj->setID($id);
        $obj->load();
        return $obj->showCaption();
      }
      function file($id, $text) {
        $obj = new Archivo;
        $obj->setID($id);
        $obj->load();
        return $obj->show($text);
      }
      function fileCap($id) {
        $obj = new Archivo;
        $obj->setID($id);
        $obj->load();
        return $obj->show($obj->showCaption());
      }
      function img($id) {
        $obj = new Imagen;
        $obj->setID($id);
        return $obj->showImg();
      }
      function imgSize($id, $w, $h) {
        $obj = new Imagen;
        $obj->setID($id);
        return $obj->showImgSize($w, $h);
      }
      function imgCaption($id) {
        $obj = new Imagen;
        $obj->setID($id);
        $obj->load();
        return $obj->showCaption();
      }
      function init(){
         $data = new Data;
         $d = $data->name("designNum");
         $this->designNum = $d->getValue();
      }
      function show($obj, $action){
         $views = new Views;
         /*$view = $views->getView(class($obj), $action, $this->designNum);*/
         return $view->show();
      }
}


?>
