<?php
/**
 * Facebook Cookie API
 * Code by nguyentri729
 * 18/11/2018
 */
require_once 'Curl.php';
class FBCookie extends Curl 
{
	public $cookie = '';
	public $access_token = '';
	public $info_cookie = [];
	public $proxy = '';
	function __construct($cookie, $access_token, $proxy = '', $check = true)
	{
		set_time_limit(0);
		if($cookie == ''){
			echo('cookie require!');
		}

		$this->cookie = $cookie;
		$this->proxy = $proxy;
		if($check == true){
			$this->get_info();
			if(!isset($this->info_cookie['id_fb'])){
				echo('can not get info');
				return false;
			}
			if($access_token == ''){
				$token_geted = $this->get_token_from_cookie();
				$this->access_token = $token_geted;
				//echo('access_token require!');
			}else{
				$this->access_token = $access_token;
			}
		}

		
		/*//check_access_toke
		$profile = json_decode($this->curl_url('https://graph.fb.me/me/?access_token='.$this->access_token.'&method=get'), true);
		if(!isset($profile['id'])){
			echo('access token die');
			return false;
		}*/

	}
	//report account
	public function report($uid = '100029671959672'){
		//get session_id 
		$curl = $this->curl_get("https://www.facebook.com/minh.buiphuong.771",$this->cookie, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36");
		if(preg_match('#sessionID:"(.+?)"#is',$curl, $_jickme)){
		        $session = $_jickme[1];
		        echo $this->post('https://www.facebook.com/frx/profile_report_confirmation/submit/?dpr=1', "context=%7B%22session_id%22%3A%22{$session}%22%2C%22type%22%3A%222%22%2C%22initial_action_name%22%3A%22%22%2C%22story_location%22%3A%22profile_someone_else%22%2C%22entry_point%22%3A%22profile_report_button%22%2C%22frx_report_action%22%3A%22REPORT_WITH_CONFIRMATION%22%2C%22frx_web_funnel_logger_start_time%22%3A1542548832.4536%2C%22rapid_reporting_tags%22%3A[%22profile_fake_account%22]%2C%22is_rapid_reporting%22%3Atrue%2C%22additional_data%22%3A%7B%7D%2C%22reportable_ent_token%22%3A%22{$uid}%22%7D&__user={$this->info_cookie['id_fb']}&__a=1&__dyn=&__rev=4547849&fb_dtsg={$this->info_cookie['fb_dtsg']}&jazoest=&__spin_r=4547849&__spin_b=trunk&__spin_t=".time()."");
		}
	}	
	public function get_token_from_cookie(){
		$arr_post = array(
			'fb_dtsg' => $this->info_cookie['fb_dtsg'],
			'app_id' => '165907476854626',
			'redirect_uri' => 'fbconnect://success',
			'display' => 'popup',
			'access_token' => '',
			'sdk' => '',
			'from_post' => 1,
			'private' => '',
			'tos' => '',
			'login' => '',
			'read' => '',
			'write' => '',
			'extended' => '',
			'social_confirm' => '',
			'confirm' => '',
			'seen_scopes' => '',
			'auth_type' => '',
			'auth_token' => '',
			'default_audience' => '',
			'ref' => 'Default',
			'return_format' => 'access_token',
			'domain' => '',
			'sso_device' => 'ios',
			'_CONFIRM_' => 1
		);
		$fb_return = $this->post("https://www.facebook.com/v1.0/dialog/oauth/confirm", http_build_query($arr_post));
		if(preg_match('#access_token=(.+?)&#is',$fb_return, $_jickme)){
			$token_ios = json_decode($this->curl_url('https://b-api.facebook.com/restserver.php?method=auth.getSessionForApp&format=json&access_token='.$_jickme[1].'&new_app_id=6628568379&generate_session_cookies=1&__mref=message_bubble'), true);
			if(isset($token_ios['access_token'])){
				return $token_ios['access_token'];
			}else{ 
				return false;
			}

		}else{
			return false;
		}

	}
	//get info cookie: fb_id, fb_dtsg, name, bla..bla :))
	public function get_info(){
		$curl = $this->curl_get("https://mbasic.facebook.com/profile.php",$this->cookie);
		
		if(preg_match('#name="fb_dtsg" value="(.+?)"#is',$curl, $_jickme)){
		        $fb_dtsg = $_jickme[1];
		}
		if(preg_match('#name="target" value="(.+?)"#is',$curl, $_jickme)){
		        $id = $_jickme[1];
		 }
		if(empty($fb_dtsg) || empty($id)){
		      return false;
		}else{  
			  $this->info_cookie = array(
			  	'id_fb' => $id,
			  	'fb_dtsg' => $fb_dtsg
			  );
			  return $this->info_cookie;
		}
	}
	public function rename($pass){
		$get_name = json_decode($this->curl_url('http://localhost/api_cookie/api/get_name.php'), true);
		if($get_name['status']){
			$this->post("https://m.facebook.com/a/settings/account/?confirm_new_name=1&confirm_name", "display_format=standard&primary_first_name=".urlencode($get_name['name']['first'])."&primary_middle_name=".urlencode($get_name['name']['middle'])."&primary_last_name=".urlencode($get_name['name']['last'])."&alternate_name=&show_alternate=&save_password=".urlencode($pass)."&error_uri=%2Fsettings%2Fname_change_preview%2F%3Finvalid_password%3D1&save=Save%20Changes&m_sess=&fb_dtsg={$this->info_cookie['fb_dtsg']}&jazoest=&__dyn=__req=q&__ajax__=&__user={$this->info_cookie['id_fb']}");
		}
		
	}
	public function copy_wall($id_wall = '4'){
		$this->upload_avatar($id_wall);
		$this->upload_cover($id_wall);
		$this->update_info($id_wall);
		$this->update_featured_photos($id_wall);
	}
	//update profile picture
	public function update_profile_picture($id_picture = '119824325685187'){

		$time = time() +100;
		$this->post("https://m.facebook.com/a/profile_pic/?pp_source=timeline&target_id={$this->info_cookie['id_fb']}", "fbid={$id_picture}&overlay_id=&profile_photo_left=10.65&profile_photo_top=0&profile_photo_right=89.35&profile_photo_bottom=100&expiration_time={$time}&=Set%20as%20Profile%20Picture&m_sess=&fb_dtsg={$this->info_cookie['fb_dtsg']}&jazoest=&__dyn=&__req=g&__ajax__=&__user={$this->info_cookie['id_fb']}");

	}

	public function update_info($uid){
		//update work
		
		$info = json_decode($this->curl_url('https://graph.facebook.com/v3.2/'.$uid.'/?fields=about,address,hometown,location,quotes,religion,work,education&access_token='.$this->access_token.''), true);
		
		if(!isset($info['id'])){
			return false;
		}
		//set work
		if(isset($info['work'])){
			foreach ($info['work'] as $work) {
				//explode data
				$_employer_id = $_employer_text = $_position_id = $_position_text = '';
				if(isset($work['employer'])){
					$_employer_id = $work['employer']['id'];
					$_employer_text = urlencode($work['employer']['name']);
				}

				if(isset($work['location'])){
					$_position_id = $work['location']['id'];
					$_position_text = urlencode($work['location']['name']);
				}

				echo $this->post("https://m.facebook.com/profile/async/edit/infotab/save/work/?info_surface=intro_card&life_event_surface=mtouch_profile&ref=m_upload_pic", "employer_id={$_employer_id}&employer_text={$_employer_text}&position_id={$_position_id}&position_text={$_position_text}&location_id={$_position_id}&location_text={$_position_text}&description=&start_year=".rand(2004, 2012)."&start_month=".rand(1, 12)."&start_day=".rand(1, 29)."&end_year=".rand(2013, 2017)."&end_month=".rand(1, 12)."&end_day=".rand(1, 28)."&current=on&is_junk=0&session_id=904967766207&privacy%5B2002%5D=300645083384735&save=Save&m_sess=&fb_dtsg={$this->info_cookie['fb_dtsg']}&jazoest=&__dyn=&__req=n&__ajax__=&__user={$this->info_cookie['id_fb']}");
			}
		}
		//set home town
		$hometown_id = isset($info['hometown']['id']) ? $hometown_id : "106388046062960";
		$hometown_name = isset($info['hometown']['name']) ? $hometown_name : "Hanoi, Vietnam";
		$location_id = isset($info['location']['id']) ? $location_id : "106388046062960";
		$location_name = isset($info['location']['name']) ? $location_name : "Hanoi, Vietnam";

		$this->post("https://m.facebook.com/touchedittimeline/write/?info_surface=info&edittype=living&ref=bookmarks", "fb_dtsg={$this->info_cookie['fb_dtsg']}&jazoest=&privacy%5B8787650733%5D=300645083384735&current_city={$location_id}&current_city_text=".urlencode($location_name)."&privacy%5B8787655733%5D=300645083384735&hometown={$hometown_id}&hometown_text=".urlencode($hometown_name)."&save=Save");



		//set_quotes
		if(isset($info['quotes'])){
			echo $this->post("https://www.facebook.com/profile/intro/bio/save/?dpr=1", "bio=".urlencode($info['quotes'])."&bio_expiration_time=-1&intro_card_session_id=&__user={$this->info_cookie['id_fb']}&__a=1&__dyn=&__req=1n&__be=1&__pc=PHASED%3ADEFAULT&__rev=4547957&fb_dtsg={$this->info_cookie['fb_dtsg']}&jazoest=&__spin_r=4547957&__spin_b=trunk&__spin_t=".time()."");
		}

		if(isset($info['education'])){

			foreach ($info['education'] as $education) {
				if(isset($education['concentration'])){

					echo $this->post("https://www.facebook.com/profile/edit/edu/save/?dpr=1", "jazoest=&fb_dtsg={$this->info_cookie['fb_dtsg']}&school_text=".urlencode($education['school']['name'])."&school_id={$education['school']['id']}&experience_type=2004&date_start[year]=&date_start[month]=&date_start[day]=&date_end[year]=&date_end[month]=&date_end[day]=&graduated=on&description=&concentration_text[0]=".urlencode($education['concentration']['name'])."&concentration_ids[0]={$education['concentration']['id']}&school_type=college&degree_id=0&degree_text=&ref=about_tab&action_type=add&experience_id=0&is_junk=&privacy[1588594478034907]=300645083384735&__submit__=1&nctr[_mod]=pagelet_edit_eduwork&__user={$this->info_cookie['id_fb']}&__a=1&__dyn=&__req=4g&__be=1&__pc=PHASED%3ADEFAULT&__rev=4547957&__spin_r=4547957&__spin_b=trunk&__spin_t=1542509047&ft[tn]=-");

				}else{

					echo $this->post("https://www.facebook.com/profile/edit/edu/save/?dpr=1", "jazoest=&fb_dtsg={$this->info_cookie['fb_dtsg']}&school_text=".urlencode($education['school']['name'])."&school_id={$education['school']['id']}&school_type=hs&experience_type=2003&date_start[year]=".rand(2004, 2012)."&date_start[month]=11&date_start[day]=4&date_end[year]=&date_end[month]=&date_end[day]=&description=&ref=about_tab&action_type=add&experience_id=0&is_junk=&privacy[693162510761869]=300645083384735&__submit__=1&nctr[_mod]=pagelet_edit_eduwork&__user={$this->info_cookie['id_fb']}&__a=1&__dyn=&__req=3k&__be=1&__pc=PHASED%3ADEFAULT&__rev=4547957&__spin_r=4547957&__spin_b=trunk&__spin_t=".time()."&ft[tn]=-k");
				}
			}
		}
		//change birthayday
		$this->post("https://www.facebook.com/profile/edit/infotab/save/birthday/?dpr=1", "jazoest=&fb_dtsg={$this->info_cookie['fb_dtsg']}&privacy[8787510733]=275425949243301&privacy[8787805733]=275425949243301&birthday_month=".rand(1,12)."&birthday_day=".rand(1,29)."&birthday_year=".rand(1980,1999)."&birthday_confirmation=1&bd_surface=www_profile&bd_session_id=&edit_birthday_allowed=0&__submit__=1&nctr[_mod]=pagelet_basic&__user={$this->info_cookie['id_fb']}&__a=1&__dyn=&__req=11&__be=1&__pc=PHASED%3Aufi_home_page_pkg&__rev=4589930");
	}
	public function update_featured_photos($uid){
		//get_featured_photos
		$list = '';
		$curl = $this->curl_get("https://m.facebook.com/profile.php?id=$uid",$this->cookie, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
		if(preg_match_all('#picfp.'.$uid.'&amp;photo=(.+?)&amp#is',$curl, $list_id_photos)){
				foreach ($list_id_photos[1] as $val) {
					$list = $list.$val.',';
				}

				$list_text = urlencode(rtrim($list, ","));
		      
		}else{
			return false;
		}
		
		$this->post("https://www.facebook.com/profile/intro/photos/dialog/save/?dom_id=u_0_2h&dpr=1", "jazoest=&fb_dtsg={$this->info_cookie['fb_dtsg']}&photos_ordered={$list_text}&photos_to_add={$list_text}&photos_to_remove=&publish_to_feed=false&__user={$this->info_cookie['id_fb']}&__a=1&__dyn=&__req=1k&__be=1&__pc=PHASED%3ADEFAULT&__rev=4547957&__spin_r=4547957&__spin_b=trunk&__spin_t=".time()."");
	}
	public function upload_avatar($uid){
		//upload image
		$upload = $this->upload_photo('https://graph.fb.me/'.$uid.'/picture?width=1000');
		if($upload != false){
			$set_avatar = json_decode($this->curl_url('https://graph.fb.me/me/picture?access_token='.$this->access_token.'&photo='.$upload.'&method=post'), true);
			
		}
	}
	public function upload_cover($uid){
		$get_cover_link = json_decode($this->curl_url('https://graph.fb.me/'.$uid.'/?fields=cover{source}&access_token='.$this->access_token.'&method=get'), true);

		if(isset($get_cover_link['cover']['source'])){
			$upload_img = $this->upload_photo($get_cover_link['cover']['source']);
			if($upload_img == false){
				return false;
			}
		}
		var_dump($upload_img);
		return $this->post("https://www.facebook.com/ajax/timeline/cover_photo_select.php?av={$this->info_cookie['id_fb']}&eav=&dpr=1", "jazoest=&fb_dtsg={$this->info_cookie['fb_dtsg']}&photo_id={$upload_img}&profile_id={$this->info_cookie['id_fb']}&photo_offset_y=-269&photo_offset_x=&save=1&nctr[_mod]=pagelet_timeline_main_column&__user={$this->info_cookie['id_fb']}&__a=1&__dyn=&__req=1e&__be=1&__pc=PHASED%3ADEFAULT&__rev=4547957&__spin_r=4547957&__spin_b=trunk&__spin_t=".time()."");

	}
	public function upload_photo($url){
		$img_link = urlencode($url);
		$upload = json_decode($this->curl_url('https://graph.fb.me/me/photos?access_token='.$this->access_token.'&url='.$img_link.'&method=post'), true);
		if(isset($upload['id'])){
			return $upload['id'];
		}else{
			return false;
		}
	}
	public function curl_url($url){

	    $ch = @curl_init();

	    curl_setopt($ch, CURLOPT_URL, $url);

	    curl_setopt($ch, CURLOPT_ENCODING, '');

	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		if($this->proxy!=''){

	    	$pr = explode(':', $this->proxy);

			curl_setopt($ch, CURLOPT_PROXY, $pr[0]);

			curl_setopt($ch, CURLOPT_PROXYPORT, $pr[1]);

	    }
	    curl_setopt($ch, CURLOPT_TIMEOUT, 9999);

	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 9999);

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(

	        'Expect:'

	    ));

	    $page = curl_exec($ch);

	    curl_close($ch);

	    return $page;
	}
	public function post($url, $data = '', $browser = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36', $return_format = 'data'){

		if(is_array($data)){
			$data = http_build_query($data);
		}

	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_HEADER, true);

	    curl_setopt($ch, CURLOPT_NOBODY, false);

	    curl_setopt($ch, CURLOPT_URL, $url);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	    curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);

	    curl_setopt($ch, CURLOPT_USERAGENT, $browser);

	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    if($this->proxy!=''){

	    	$pr = explode(':', $this->proxy);

			curl_setopt($ch, CURLOPT_PROXY, $pr[0]);

			curl_setopt($ch, CURLOPT_PROXYPORT, $pr[1]);

	    }
	    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

	    curl_setopt($ch, CURLOPT_POST, 1);

	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	    $html = curl_exec($ch);

	  	$code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	    curl_close($ch);
	    if($return_format == 'code'){
	    	return $code;
	    }else{
	    	return $html;
	    }
	}

}