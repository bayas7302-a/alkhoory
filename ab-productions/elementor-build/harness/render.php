<?php
// Minimal WordPress stubs to render the theme's shortcodes outside WordPress.
define('ABSPATH', '/');
$T = realpath(__DIR__ . '/../../abproductions-child');
define('ABP_DIR', $T);
define('ABP_URI', 'file://' . $T);
$SC = []; $GLOBALS['abp_lightbox'] = false;
function add_action(){} function add_filter(){} function register_post_type(){} function register_taxonomy(){} function register_post_meta(){} function add_meta_box(){} function wp_nonce_field(){}
function add_shortcode($n,$f){ global $SC; $SC[$n]=$f; }
function shortcode_atts($d,$a){ $a=(array)$a; $o=[]; foreach($d as $k=>$v){ $o[$k]=array_key_exists($k,$a)?$a[$k]:$v; } return $o; }
function esc_html($s){return htmlspecialchars((string)$s,ENT_QUOTES);} function esc_attr($s){return htmlspecialchars((string)$s,ENT_QUOTES);} function esc_url($s){return htmlspecialchars((string)$s,ENT_QUOTES);}
function esc_html__($s){return esc_html($s);} function esc_attr__($s){return esc_attr($s);} function __($s){return $s;} function esc_html_e($s){echo esc_html($s);} function esc_attr_e($s){echo esc_attr($s);}
function sanitize_html_class($s){return preg_replace('/[^A-Za-z0-9_-]/','',$s);} function sanitize_title($s){return strtolower(preg_replace('/[^A-Za-z0-9]+/','-',$s));}
$uid=0; function wp_unique_id($p=''){ global $uid; return $p.(++$uid); }
function wp_parse_url($u,$c){return parse_url($u,$c);}
function home_url($p='/'){ return 'https://soharon.co.uk/ab-production'.$p; }
function get_theme_mod($k,$d){ return $d; }
function wp_list_pluck($l,$f){ return array_map(function($o)use($f){return $o->$f;},$l); }
class WP_Term { function __construct($id,$slug,$name){$this->term_id=$id;$this->slug=$slug;$this->name=$name;} }
class WP_Post { function __construct($a){ foreach($a as $k=>$v) $this->$k=$v; } }
$CATS=['events'=>new WP_Term(1,'events','Events'),'exhibitions'=>new WP_Term(2,'exhibitions','Exhibitions'),'brand-activations'=>new WP_Term(3,'brand-activations','Brand Activations'),'hospitality'=>new WP_Term(4,'hospitality','Hospitality')];
$PROJ=[];
$data=[['Yas Island Fan Zone','events','World Cup Fan Zone  |  Abu Dhabi','Events · LED & AV','project-yas-island-fan-zone.jpg',1],['CÉ LA VI New Year’s','hospitality','Hospitality Event  |  Dubai','Hospitality · Production','project-ce-la-vi-new-years.jpg',1],['Mirbad Jewellery Exhibition','exhibitions','Exhibition Stand','Exhibition Stand','project-mirbad-jewellery.jpg',1],['Moët & Chandon','brand-activations','#ToastWithMoet Activation','Brand Activation','project-moet-chandon.jpg',1],['UAE National Day','events','Branding & Structures  |  Emaar','Branding & Structures','project-uae-national-day.jpg',1],['Montblanc Anniversary','brand-activations','Backdrop & Floral Set Design','Backdrop & Set Design','project-montblanc-anniversary.jpg',1],['Abu Dhabi Mall Christmas','events','Seasonal Décor  |  Abu Dhabi','','project-abu-dhabi-mall-christmas.jpg',0],['Sharjah Bridal Fair','exhibitions','Event Branding  |  Sharjah','','project-sharjah-bridal-fair.jpg',0],['Wide Test 3:1','events','Center third should show','','WIDE',0]];
foreach($data as $i=>$d){ $PROJ[]=new WP_Post(['ID'=>100+$i,'title'=>$d[0],'cat'=>$d[1],'sub'=>$d[2],'tag'=>$d[3],'img'=>$d[4],'feat'=>$d[5]]); }
function get_posts($q){ global $PROJ; $o=$PROJ; if(!empty($q['meta_query'])) $o=array_values(array_filter($o,function($p){return $p->feat;})); if($q['posts_per_page']>0) $o=array_slice($o,0,$q['posts_per_page']); return $o; }
function get_the_terms($p,$t){ global $CATS; return [$CATS[$p->cat]]; }
function get_the_title($p){ return $p->title; }
function get_post_meta($id,$k,$s){ global $PROJ; foreach($PROJ as $p) if($p->ID==$id) return ['abp_subtitle'=>$p->sub,'abp_tag'=>$p->tag,'abp_featured'=>$p->feat][$k]; }
function img_url($p){ return $p->img==='WIDE' ? 'https://soharon.co.uk/ab-production/wp-content/uploads/2026/10/ab-why-camera-bokeh.jpg' : 'IMGBASE/'.$p->img; }
function get_post_thumbnail_id($p){ return $p->ID; }
function wp_get_attachment_image_src($id,$s){ global $PROJ; foreach($PROJ as $p) if($p->ID==$id) return [img_url($p)]; }
function wp_get_attachment_image($id,$s,$i,$a){ $src=wp_get_attachment_image_src($id,$s)[0]; return '<img class="'.$a['class'].'" src="'.$src.'" alt="">'; }
function abp_opt_dummy(){}
function language_attributes(){echo 'lang="en"';} function bloginfo(){echo 'UTF-8';} function wp_head(){} function wp_body_open(){} function wp_footer(){} function body_class(){echo 'class="abp-site"';}
function has_custom_logo(){return false;} function has_nav_menu(){return false;} function get_bloginfo(){return 'AB Productions';}
function add_query_arg(){return '/ab-production/';} function trailingslashit($s){return rtrim($s,'/').'/';}
require $T.'/inc/helpers.php';
require $T.'/inc/projects.php';
require $T.'/inc/shortcodes.php';
require $T.'/inc/setup.php';
require $T.'/inc/cf7.php';
// Approximate Contact Form 7's HTML for the form-tags used in the theme's form.
function cf7_mock(){
  $h=abp_cf7_form_markup();
  $h=preg_replace_callback('/\[(text|email|tel)\*? ([a-z-]+)[^\]]*?placeholder "([^"]*)"\]/',function($m){return '<span class="wpcf7-form-control-wrap"><input type="'.($m[1]==='text'?'text':$m[1]).'" class="wpcf7-form-control" placeholder="'.$m[3].'"></span>';},$h);
  $h=preg_replace_callback('/\[radio ([a-z-]+) class:([a-z-]+)[^\]]*?"Company" "Individual"\]/',function($m){return '<span class="wpcf7-form-control-wrap"><span class="wpcf7-form-control wpcf7-radio '.$m[2].'"><span class="wpcf7-list-item first"><label><input type="radio" name="e" checked><span class="wpcf7-list-item-label">Company</span></label></span><span class="wpcf7-list-item last"><label><input type="radio" name="e"><span class="wpcf7-list-item-label">Individual</span></label></span></span></span>';},$h);
  $h=preg_replace_callback('/\[select\* ([a-z-]+) class:([a-z-]+) first_as_label "([^"]*)"[^\]]*\]/',function($m){return '<span class="wpcf7-form-control-wrap"><select class="wpcf7-form-control '.$m[2].'"><option>'.$m[3].'</option></select></span>';},$h);
  $h=preg_replace_callback('/\[textarea ([a-z-]+) x4 placeholder "([^"]*)"\]/',function($m){return '<span class="wpcf7-form-control-wrap"><textarea class="wpcf7-form-control" placeholder="'.$m[2].'"></textarea></span>';},$h);
  $h=preg_replace('/\[submit class:([a-z-]+) "([^"]*)"\]/','<input type="submit" class="wpcf7-form-control wpcf7-submit $1" value="$2">',$h);
  return '<div class="wpcf7"><form class="wpcf7-form">'.$h.'</form></div>';
}
$out=[];
foreach(json_decode($argv[1],true) as $code){
  preg_match('/^\[([a-z_\-0-9]+)\s*(.*)\]$/',$code,$m); $atts=[];
  preg_match_all('/(\w+)="([^"]*)"/',$m[2],$mm,PREG_SET_ORDER); foreach($mm as $x) $atts[$x[1]]=$x[2];
  if ($m[1]==='contact-form-7') { $out[$code]=cf7_mock(); continue; }
  $out[$code]= isset($SC[$m[1]]) ? $SC[$m[1]]($atts) : '<p>SC '.$m[1].'</p>';
}
ob_start(); do_action_footer(); $lb=ob_get_clean();
function do_action_footer(){ if(!empty($GLOBALS['abp_lightbox'])) { ?>
<div class="abp-lightbox" id="abp-lightbox" role="dialog" hidden><button type="button" class="abp-lightbox__close" data-abp-lb="close"><?php echo abp_icon('close'); ?></button><button type="button" class="abp-lightbox__nav abp-lightbox__nav--prev" data-abp-lb="prev"><?php echo abp_icon('prev'); ?></button><figure class="abp-lightbox__figure"><img class="abp-lightbox__img" alt=""></figure><button type="button" class="abp-lightbox__nav abp-lightbox__nav--next" data-abp-lb="next"><?php echo abp_icon('next'); ?></button></div>
<?php } }
$out['__lightbox']=$lb;
ob_start(); include $T.'/header.php'; $hd=ob_get_clean(); preg_match('#<header.*</header>#s',$hd,$hm); $out['__header']=$hm[0];
ob_start(); include $T.'/footer.php'; $ft=ob_get_clean(); preg_match('#<footer.*</footer>#s',$ft,$fm); $out['__footer']=$fm[0];
echo json_encode($out);
