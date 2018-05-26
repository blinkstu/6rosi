var check = new TimelineMax({paused:true});
var uncheck = new TimelineMax({paused:true});

check
  .set('.ring', {opacity:1})
  .set('.drops', {opacity:0})
  .set('.ring0', {opacity:0})
  .set('.drop', {opacity:0, y:-32, scale:.4, x:0, transformOrigin:"50%, 0%"})
  .set('.ring', {transformOrigin:"50%, 50%"})
  .set('.dropTop', {opacity:1,scale:.2, transformOrigin:"50%, 0%"})
  .add('sync')
  .to('.ring', .17, { scaleY:.95}, 'sync')
   .to('.dropTop', .1, { scale:1, y:.5, ease:Power0.easeNone}, 'sync')
  .to('.dropTop', .1, { scale:.3, ease:Power0.easeNone}, 'sync +=.08')
  .to('.dropTop', .08, {transformOrigin:"50%, 40%", scale:0, ease:Power0.easeNone}, 'sync +=.181')
  .set('.drop',  { opacity:1, ease:Power0.easeNone}, 'sync')
  .to('.drop', .17, { y:0, ease:Power1.easeIn}, 'sync')
  .to('.drop', .08, {scale:.9, ease:Power0.easeNone}, 'sync +=.02')
  .to('.ring', 2, {transformOrigin:"50%, 50%", scaleY:1, ease:Elastic.easeOut.config(.8, .1)}, 'sync +=.14')
  .to('.drop', 1.8, {transformOrigin:"50%, 10%", scale:1, ease:Elastic.easeOut.config(.8, .14)}, 'sync +=.14')


uncheck
  .set('.ring0', {opacity:1})
  .set('.drop', {opacity:0}) 
  .set('.ring', {opacity:0})
  .set('.drops', {opacity:1}) 
  .set('.drop0', {rotation:'40deg',transformOrigin:"50%, 50%"})
  .set('.drop1', {rotation:'112deg',transformOrigin:"50%, 50%"})
  .set('.drop2', {rotation:'175deg',transformOrigin:"50%, 50%"})
  .set('.drop3', {rotation:'-110deg',transformOrigin:"50%, 50%"})
  .set('.drop4', {rotation:'-35deg',transformOrigin:"50%, 50%"}) 
  .add('uncheck')
  .to('.drops', .2, {transformOrigin:"50%, 50%", scaleX:.5, scaleY:.3,})
  .staggerTo('.drops', .2, { cycle:{
    x:[45, 59, 14, -62, -35],
    y:[-46, 29, 62, 15, -55],    
  }}, '0.0184', 'uncheck+=.1')
  .to('.ring0', .2, {transformOrigin:"50%, 50%", scale:1.05}, 'uncheck+=.1')
  .add('last')
  .to('.ring0', 2, {transformOrigin:"50%, 50%", scale:1, ease:Elastic.easeOut.config(.8, .1)}, 'last')
  .to('.drops', .2, {scaleY:.1, scaleX:.3},'last+=0');


check.timeScale(1.27);
uncheck.timeScale(1.14);

$('.toggler').click(function(){
  if (!$('#boom').is(':checked')){
    check.play(0);
    $('#boom').prop('checked', true);
    
  } else{
    uncheck.play(0);
    $('#boom').prop('checked', false);
  }
});