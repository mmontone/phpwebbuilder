function playSound(surl) {
  document.getElementById("sound_player").innerHTML=
    "<embed src='"+surl+"' hidden=true autostart=true loop=false>";
}