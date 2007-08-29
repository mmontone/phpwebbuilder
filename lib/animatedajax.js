
var Hide=function(elem){$(elem).hide();}
var Show=function(elem){$(elem).show();}

var allEffects=[
	{insertEffect:Show,removeEffect:Hide}
	//,{insertEffect:Effect.Appear,removeEffect:Effect.Fade}
	//var insertEffect = Effect.SlideDown; var removeEffect = Effect.SlideUp;
	//,{insertEffect:Effect.BlindDown,removeEffect:Effect.BlindUp}
	//,{insertEffect:Effect.Grow,removeEffect:Effect.Shrink}
	];

function do_repn(target,str,eff){
  if (!eff){
	eff=getEffects();
  }
  var tpn = $(target.parentNode);
  tpn.setStyle({height:tpn.getHeight()+'px',width:tpn.getHeight()+'px'});
  do_rem(target,eff);
  do_insert(target, str,eff);
  tpn.setStyle({height:'auto',width:'auto'});
}

function getEffects(){
	return allEffects[Math.floor(Math.random()*allEffects.length)];
}

function do_append(target,str,eff){
  if(inIE()){
     var child = $(document.createElement(""));
     child.hide();
     target.appendChild(child);
     child.outerHTML = str;
  } else {
     child.hide();
     var html = str2html(str);
     target.appendChild(html);
  }
  if (!eff){
	eff=getEffects();
  }
  new eff.insertEffect(child);

}


function do_rem(target,eff) {
  if (target) {
     target= $(target);
     var dur = 1;
     var w = target.getWidth();
     var h = target.getHeight();
     Position.absolutize(target);
     target.setStyle({height:h+'px',width:w+'px'});
	  if (!eff){
		eff=getEffects();
	  }
     new eff.removeEffect(target,{duration:dur});
	 setTimeout(function(){target.parentNode.removeChild(target);}, dur*1000);
  }
}


function do_insert(target,str,eff){
  if(inIE()){
    var child = $(document.createElement());
     child.hide();
    target.parentNode.insertBefore(child, target);
    child.outerHTML = str;
  } else {
    var child = str2html(str);
     child.hide();
    target.parentNode.insertBefore(child, target);
  }
  if (!eff){
	eff=getEffects();
  }
  new eff.insertEffect(child);
}


function do_setatt(target,attribute,value){
    if (attribute=="class"){
	    target.className=value;
    } else {
	    target.setAttribute(attribute, value);
    }
}

function do_rematt(target,attribute){
    target.removeAttribute(attribute);
}
