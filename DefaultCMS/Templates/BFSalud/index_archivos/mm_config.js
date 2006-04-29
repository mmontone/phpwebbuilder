function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
<!--
function mmLoadMenus() {
  if (window.mm_menu_1) return;
  
  window.mm_menu_1a = new Menu("Salud",200,14,"Verdana, Arial, Helvetica, sans-serif",10,"#425863","#FFFFFF","#F6F6F6","#1284C2","left","middle",2,0,1200,-5,7,true,true,true,0,true,true);
  mm_menu_1a.addMenuItem("Programa de Soporte al Cáncer","location='programacancer.htm'");
   mm_menu_1a.fontWeight="bold";
   mm_menu_1a.hideOnMouseOut=true;
   mm_menu_1a.bgColor='#ADC1DA';
   mm_menu_1a.menuBorder=1;
   mm_menu_1a.menuLiteBgColor='';
   mm_menu_1a.menuBorderBgColor='#FFFFFF';
  
window.mm_menu_1 = new Menu("root",200,14,"Verdana, Arial, Helvetica, sans-serif",10,"#425863","#FFFFFF","#F6F6F6","#1284C2","left","middle",2,0,1200,-5,7,true,true,true,0,true,true);
  mm_menu_1.addMenuItem(mm_menu_1a,"location='salud.htm'");
  mm_menu_1.addMenuItem("Empresa","location='empresa.htm'");
  mm_menu_1.addMenuItem("Formación","location='formacion.htm'");
  mm_menu_1.addMenuItem("Crecimiento Personal","location='crecimientopersonal.htm'");
   mm_menu_1.fontWeight="bold";
   mm_menu_1.hideOnMouseOut=true;
   mm_menu_1.bgColor='#ADC1DA';
   mm_menu_1.menuBorder=1;
   mm_menu_1.menuLiteBgColor='';
   mm_menu_1.menuBorderBgColor='#FFFFFF';
   

   
window.mm_menu_2 = new Menu("root",133,14,"Verdana, Arial, Helvetica, sans-serif",10,"#425863","#FFFFFF","#F6F6F6","#1284C2","left","middle",2,0,1200,-5,7,true,true,true,0,true,true);
  mm_menu_2.addMenuItem("Equipo","location='equipo.htm'");
  mm_menu_2.addMenuItem("BioFeedback","location='biofeedback.htm'");
   mm_menu_2.fontWeight="bold";
   mm_menu_2.hideOnMouseOut=true;
   mm_menu_2.bgColor='#ADC1DA';
   mm_menu_2.menuBorder=1;
   mm_menu_2.menuLiteBgColor='';
   mm_menu_2.menuBorderBgColor='#FFFFFF';
 
mm_menu_2.writeMenus();
} // mmLoadMenus()