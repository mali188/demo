
//完成由lyh_951128；
//完成时间 2015.10.16
//个人博客：http://blog.csdn.net/lyh_951128 

function startenLargePicture(){
var ww=$(window).width();
var wh=$(window).height();
$('.enlarge-img').css('width',ww);
var img_number=$('.enlarge-img-box').children('.enlarge-div').length;
var imgh_s=$('.enlarge-img').height();
var imgw_s=ww;
$('.enlarge-number-tips-allnumber').html(img_number);
$('.enlarge-blackground').css('height',wh);
$('.enlarge-center-box').css('height',imgh_s);
$('.enlarge-img-box').css('width',imgw_s*img_number);
  var theimg_h=$('.enlarge-img-box').height();
 $('.enlarge-img-box').css('top',(wh-theimg_h)/2);

var set={                  //全局设置
  max_magnification: 2.0 , //最大放大倍数
  re_magnification: 1.5 ,  //回弹放大倍数
  changespeed: 0.4 ,      //换图灵敏度(0.1-0.9)
}

var touches={
  number : null ,
  one: { sy : 0 ,sx : 0 ,mx : 0 ,my : 0 ,ex : 0 ,ey : 0 ,},
  two : {sy : 0 ,sx : 0 ,mx : 0 ,my : 0 ,ex : 0 ,ey : 0 ,},}
var sl = null; //初始距离 
var _3d = null; var _2d = null; var scale = 1;
var global={scale : 1,offsetX : 0, offsetY : 0, is_scale : false, go_change:'static', moveX:0, allmoveX:img_number*imgw_s, indexpic:1, };
var img_offset={ left_s: 0 , top_s: 0 , left_m: 0 , top_m: 0 , left_e: 0 , top_e: 0 ,}


$('.enlarge-blackground').show();
 
$('.enlarge-div').on('touchstart touchmove touchend',function(e){
      var imgh=$(this).children('.enlarge-img').height();
      var imgw=$(this).children('.enlarge-img').width();
	switch(e.type){
		case 'touchstart':
      global.go_change='static';
      $(this).children('.enlarge-img').css({
          '-moz-transition': '-moz-transform 0s',
              '-webkit-transition': '-webkit-transform 0s',
              '-o-transition': '-o-transform 0s',
            '-ms-transition': '-ms-transform 0s',
            'transition': 'transform 0s',
      });
      img_offset.left_s=$(this).children('.enlarge-img').offset().left;
      img_offset.top_s=$(this).children('.enlarge-img').offset().top;

      touches.number=e.originalEvent.touches;
      if(touches.number.length==1){
      touches.one.sx=e.originalEvent.targetTouches[0].pageX;
      touches.one.sy=e.originalEvent.targetTouches[0].pageY;
        //return;
      }else{
      touches.one.sx=e.originalEvent.targetTouches[0].pageX;
      touches.one.sy=e.originalEvent.targetTouches[0].pageY;
      touches.two.sy=e.originalEvent.targetTouches[1].pageY;
      touches.two.sx=e.originalEvent.targetTouches[1].pageX;
      sl=Math.ceil(Math.sqrt(Math.pow((touches.one.sy-touches.two.sy),2)+Math.pow((touches.one.sx-touches.two.sx),2)));                                //初始距离
    }
		break;
		case 'touchmove':
      img_offset.left_m=$(this).children('.enlarge-img').offset().left;
      img_offset.top_m=$(this).children('.enlarge-img').offset().top;
      if((touches.number.length==1)&&(global.scale>=1)){      //单手指

      touches.one.ex=e.originalEvent.targetTouches[0].pageX;
      touches.one.ey=e.originalEvent.targetTouches[0].pageY;
      var stop_position_x=imgw/3;
      var stop_position_y=imgh/3;
      offsetX=(stop_position_x*(touches.one.ex-touches.one.sx))/(Math.abs((touches.one.ex-touches.one.sx))+stop_position_x)+global.offsetX;
      offsetY=(stop_position_y*(touches.one.ey-touches.one.sy))/(Math.abs((touches.one.ey-touches.one.sy))+stop_position_y)+global.offsetY;
      
      _3d='scale3d('+global.scale+','+global.scale+',1) '+' translate3d(' + offsetX    + 'px,' + offsetY    + 'px,0px)';
      _2d='scale('+global.scale+','+global.scale+') '+' translate(' + offsetX    + 'px,' + offsetY +'px)';
      if((scale>1)&&(img_offset.left_m<=0)&&(img_offset.left_m<=-(imgw_s-imgw/global.scale))){
        offsetY=touches.one.ey-touches.one.sy+global.offsetY;
        offsetX=touches.one.ex-touches.one.sx+global.offsetX;
      }
      }else {                 //双手指
      touches.one.ex=e.originalEvent.targetTouches[0].pageX;
      touches.one.ey=e.originalEvent.targetTouches[0].pageY;
      touches.two.ex=e.originalEvent.targetTouches[1].pageX;
      touches.two.ey=e.originalEvent.targetTouches[1].pageY;
      ml=Math.ceil(Math.sqrt(Math.pow((touches.one.ey-touches.two.ey),2)+Math.pow((touches.one.ex-touches.two.ex),2)));
      global.is_scale?scale=global.scale*2.5*ml/sl/(1.5+ml/sl) :scale=2.5*ml/sl/(1.5+ml/sl);    //放大阻尼效果
     
      offsetX=Math.ceil((touches.one.ex+touches.two.ex)/2)-Math.ceil((touches.one.sx+touches.two.sx)/2)+global.offsetX;
      offsetY=Math.ceil((touches.one.ey+touches.two.ey)/2)-Math.ceil((touches.one.sy+touches.two.sy)/2)+global.offsetY;
      _3d='scale3d('+scale+','+scale+',1) '+' translate3d(' + offsetX    + 'px,' + offsetY    + 'px,0px)';
      _2d='scale('+scale+','+scale+') '+' translate(' + offsetX    + 'px,' + offsetY +'px)';
      }  
       //console.log(offsetX,offsetY);
       $(this).children('.enlarge-img').css({
              
             '-webkit-transform':   _3d,
              '-o-transform':       _2d,
              '-ms-transform':      _2d,
              '-moz-transform':     _2d,
              'transform':         _3d,
           
        });
      
      
    break;
		case 'touchend':
    img_offset.left_e=$(this).children('.enlarge-img').offset().left;
    img_offset.top_e=$(this).children('.enlarge-img').offset().top;
       
    if((touches.one.ex-touches.one.sx<=-imgw*set.changespeed)&&(global.moveX+imgw<global.allmoveX)&&(touches.one.ex!=0)){global.go_change='down';}else if((touches.one.ex-touches.one.sx>=imgw*set.changespeed)&&(global.moveX-imgw>=0)&&(touches.one.ex!=0)){global.go_change='up'; }else{global.go_change='static';}  //判断图片是否切换
    
    if(scale<=1){
      scale=1;
    }else if(scale>set.max_magnification){
      scale=set.re_magnification;
    }
    if((img_offset.top_e!=0|| img_offset.left_e!=0)&&(scale<=1)){   //缩小或未放大时图片回弹效果
    	offsetX=0;
    	offsetY=0;
    }
    
    if((img_offset.left_e>0)&&(scale>1)){             //放大时X轴图片回弹效果
      offsetX=(imgw_s-imgw/scale)/2;
      }else if((img_offset.left_e<0)&&(img_offset.left_e<=-(imgw_s-imgw/scale))&&(scale>1)){
      offsetX=-(imgw_s-imgw/scale)/2;
      }
    if((img_offset.top_e>50)&&(scale>1)){             //放大时Y轴图片回弹效果
       offsetY=0;
     }else if((img_offset.top_e<-50)&&(scale>1)){
       offsetY=0;
     }
    if(global.go_change=='down'){     //多图片切换下一张
      scale=1;offsetX=0;offsetY=0;global.scale=1;
      global.moveX+=imgw;
       transFormMove(-global.moveX);
       tansForm(scale,offsetX,offsetY);
      global.indexpic+=1;
      $('.enlarge-number-tips-indexpic').html(global.indexpic);
    }else if(global.go_change=='up'){   //多图片切换上一张
      scale=1;offsetX=0;offsetY=0;global.scale=1;
      global.moveX-=imgw;
      transFormMove(-global.moveX);
      tansForm(scale,offsetX,offsetY);
      global.indexpic-=1;
      $('.enlarge-number-tips-indexpic').html(global.indexpic);
    }else{                              //多图片切换不变
       tansForm(scale,offsetX,offsetY);
     global.scale=scale;
     global.offsetY=offsetY;
     global.offsetX=offsetX;
     global.is_scale=true;
   }
    
    break;
  }
});
function tansForm(tran_scale,tran_offsetX,tran_offsetY){

    var tran_3d='scale3d('+tran_scale+','+tran_scale+',1) '+' translate3d(' + tran_offsetX    + 'px,' + tran_offsetY    + 'px,0px)';
    var tran_2d='scale('+tran_scale+','+tran_scale+') '+' translate(' + tran_offsetX    + 'px,' + tran_offsetY +'px)';
     $(event.target).css({
             '-webkit-transform':   tran_3d,
              '-o-transform':       tran_2d,
              '-ms-transform':      tran_2d,
              '-moz-transform':     tran_2d,
              'transform':          tran_3d,
              '-moz-transition': '-moz-transform .3s ease-out',
              '-webkit-transition': '-webkit-transform .3s ease-out',
              '-o-transition': '-o-transform .3s ease-out',
            '-ms-transition': '-ms-transform .3s ease-out',
            'transition': 'transform .3s ease-out',
        });
}

function transFormMove(tran_moveX){
   var tran_3d=' translate3d(' + tran_moveX    + 'px,' + '0'    + 'px,0px)';
   var tran_2d=' translate(' + tran_moveX    + 'px,' + '0' +'px)';
      $('.enlarge-img-box').css({
             '-webkit-transform':   tran_3d,
              '-o-transform':       tran_2d,
              '-ms-transform':      tran_2d,
              '-moz-transform':     tran_2d,
              'transform':          tran_3d,
              '-moz-transition': '-moz-transform .3s ease-out',
              '-webkit-transition': '-webkit-transform .3s ease-out',
              '-o-transition': '-o-transform .3s ease-out',
            '-ms-transition': '-ms-transform .3s ease-out',
            'transition': 'transform .3s ease-out',
        });
  }
}


function forbidTouchmove(){
  event.preventDefault();
}

$('.enlarge-blackground').bind('click',function(){
        $('.enlarge-blackground').animate({left:'100%'});
        $('body').unbind('touchmove',forbidTouchmove);//取消屏蔽
});
function enLargePicture(){      //触发函数
var wh=$(window).height();
var theimg_h=$('.enlarge-img-box').height();
 $('.enlarge-img-box').css('top',(wh-theimg_h)/2);

  $('.enlarge-blackground').animate({left:'0'});
  $('body').bind('touchmove',forbidTouchmove);//屏蔽自带touchmove
}
