if (document.images)
{
   var images = new Array("e:/wamp/www/epp/images/botonera.jpg",
"e:/wamp/www/epp/images/cargando.gif",
"e:/wamp/www/epp/images/cargando2.gif",
"e:/wamp/www/epp/images/cargando_new.gif",
"e:/wamp/www/epp/images/cargando_new_static.gif",
"e:/wamp/www/epp/images/cargando_static_white.gif",
"e:/wamp/www/epp/images/centro_r10_c1.jpg",
"e:/wamp/www/epp/images/centro_r10_c2.jpg",
"e:/wamp/www/epp/images/centro_r10_c7.jpg",
"e:/wamp/www/epp/images/centro_r11_c1.jpg",
"e:/wamp/www/epp/images/centro_r11_c3.jpg",
"e:/wamp/www/epp/images/centro_r11_c5.jpg",
"e:/wamp/www/epp/images/centro_r12_c1.jpg",
"e:/wamp/www/epp/images/centro_r12_c2.jpg",
"e:/wamp/www/epp/images/centro_r12_c7.jpg",
"e:/wamp/www/epp/images/centro_r13_c1.jpg",
"e:/wamp/www/epp/images/centro_r13_c6.jpg",
"e:/wamp/www/epp/images/centro_r14_c2.jpg",
"e:/wamp/www/epp/images/centro_r14_c3.jpg",
"e:/wamp/www/epp/images/centro_r14_c4.jpg",
"e:/wamp/www/epp/images/centro_r14_c4_f2.jpg",
"e:/wamp/www/epp/images/centro_r14_c4_f3.jpg",
"e:/wamp/www/epp/images/centro_r14_c7.jpg",
"e:/wamp/www/epp/images/centro_r15_c1.jpg",
"e:/wamp/www/epp/images/centro_r15_c3.jpg",
"e:/wamp/www/epp/images/centro_r15_c4.jpg",
"e:/wamp/www/epp/images/centro_r16_c2.jpg",
"e:/wamp/www/epp/images/centro_r16_c7.jpg",
"e:/wamp/www/epp/images/centro_r17_c2.jpg",
"e:/wamp/www/epp/images/centro_r17_c3.jpg",
"e:/wamp/www/epp/images/centro_r17_c7.jpg",
"e:/wamp/www/epp/images/centro_r18_c2.jpg",
"e:/wamp/www/epp/images/centro_r18_c3.jpg",
"e:/wamp/www/epp/images/centro_r18_c7.jpg",
"e:/wamp/www/epp/images/centro_r19_c2.jpg",
"e:/wamp/www/epp/images/centro_r1_c1.jpg",
"e:/wamp/www/epp/images/centro_r1_c3.jpg",
"e:/wamp/www/epp/images/centro_r1_c4.jpg",
"e:/wamp/www/epp/images/centro_r20_c7.jpg",
"e:/wamp/www/epp/images/centro_r21_c3.jpg",
"e:/wamp/www/epp/images/centro_r21_c6.jpg",
"e:/wamp/www/epp/images/centro_r21_c6_f2.jpg",
"e:/wamp/www/epp/images/centro_r21_c6_f3.jpg",
"e:/wamp/www/epp/images/centro_r22_c3.jpg",
"e:/wamp/www/epp/images/centro_r22_c5.jpg",
"e:/wamp/www/epp/images/centro_r23_c2.jpg",
"e:/wamp/www/epp/images/centro_r23_c7.jpg",
"e:/wamp/www/epp/images/centro_r24_c6.jpg",
"e:/wamp/www/epp/images/centro_r2_c1.jpg",
"e:/wamp/www/epp/images/centro_r3_c1.jpg",
"e:/wamp/www/epp/images/centro_r3_c2.jpg",
"e:/wamp/www/epp/images/centro_r3_c3.jpg",
"e:/wamp/www/epp/images/centro_r3_c7.jpg",
"e:/wamp/www/epp/images/centro_r3_c8.jpg",
"e:/wamp/www/epp/images/centro_r4_c1.jpg",
"e:/wamp/www/epp/images/centro_r4_c2.jpg",
"e:/wamp/www/epp/images/centro_r4_c3.jpg",
"e:/wamp/www/epp/images/centro_r4_c7.jpg",
"e:/wamp/www/epp/images/centro_r5_c1.jpg",
"e:/wamp/www/epp/images/centro_r5_c3.jpg",
"e:/wamp/www/epp/images/centro_r6_c1.jpg",
"e:/wamp/www/epp/images/centro_r6_c2.jpg",
"e:/wamp/www/epp/images/centro_r7_c1.jpg",
"e:/wamp/www/epp/images/centro_r7_c2.jpg",
"e:/wamp/www/epp/images/centro_r7_c3.jpg",
"e:/wamp/www/epp/images/centro_r7_c4.jpg",
"e:/wamp/www/epp/images/centro_r7_c4_f2.jpg",
"e:/wamp/www/epp/images/centro_r7_c4_f3.jpg",
"e:/wamp/www/epp/images/centro_r7_c5.jpg",
"e:/wamp/www/epp/images/centro_r7_c6.jpg",
"e:/wamp/www/epp/images/centro_r7_c7.jpg",
"e:/wamp/www/epp/images/centro_r8_c1.jpg",
"e:/wamp/www/epp/images/centro_r8_c2.jpg",
"e:/wamp/www/epp/images/centro_r8_c3.jpg",
"e:/wamp/www/epp/images/centro_r8_c4.jpg",
"e:/wamp/www/epp/images/centro_r8_c7.jpg",
"e:/wamp/www/epp/images/centro_r9_c1.jpg",
"e:/wamp/www/epp/images/centro_r9_c3.jpg",
"e:/wamp/www/epp/images/centro_r9_c6.jpg",
"e:/wamp/www/epp/images/centro_r9_c6_f2.jpg",
"e:/wamp/www/epp/images/centro_r9_c6_f3.jpg",
"e:/wamp/www/epp/images/competicion.jpg",
"e:/wamp/www/epp/images/contacto.jpg",
"e:/wamp/www/epp/images/coordinadores.jpg",
"e:/wamp/www/epp/images/foto_pt_pq_r28_c2.gif",
"e:/wamp/www/epp/images/foto_pt_pq_r28_c3.gif",
"e:/wamp/www/epp/images/indumentaria.jpg",
"e:/wamp/www/epp/images/inicio.jpg",
"e:/wamp/www/epp/images/marca.jpg",
"e:/wamp/www/epp/images/noticias.jpg",
"e:/wamp/www/epp/images/pie_r1_c1.jpg",
"e:/wamp/www/epp/images/pie_r2_c1.jpg",
"e:/wamp/www/epp/images/pie_r3_c1.jpg",
"e:/wamp/www/epp/images/pie_r4_c1.jpg",
"e:/wamp/www/epp/images/ranking.jpg",
"e:/wamp/www/epp/images/spacer.gif");
    var img;
   for (var i = 0; i < images.length; i++) {
    img = new Image();
    //alert("Loading image: " + images[i]);
    img.src = images[i];
    }
}
