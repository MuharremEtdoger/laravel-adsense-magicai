<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI;
use App\Models\OpenAIGenerator;
use App\Models\AdsenseTable;
class AdsenseController extends Controller{
	public static function googleAdsenseIsActive(){ //.env dosyasında tanımlama varmı
		$state=false;
		if(env("ADSENSE_CA_PUB")!=null){ 
			$state=true;	
		}
		return $state;
	}
	public static function assetsAdsenseHeader(){ // .env dosyasındaki pub- koduna göre adsbygoogle.js çağrılması
		$script_header='';
		if(AdsenseController::googleAdsenseIsActive()){ 
			$script_header='<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-'.env("ADSENSE_CA_PUB",null).'" crossorigin="anonymous"></script>';
		}	
		return $script_header; 
	}
	public static function assetsAdsenseFooter(){ // Sayfa içerisindeki tüm adsense kodlarını döngü ile çalıştırma
		$script_footer='';
		if(AdsenseController::googleAdsenseIsActive()){
			$script_footer.='<script>';
				$script_footer.='[].forEach.call(document.querySelectorAll(\'.adsbygoogle\'), function(){(adsbygoogle = window.adsbygoogle || []).push({});});';
			$script_footer.='</script>';		
		}	
		return $script_footer;
	}
	public static function getAdsenseTxt(){ // Manuel ads.txt dosyası oluşturma
		if(AdsenseController::googleAdsenseIsActive()){
			$ads_txt='google.com, '.env("ADSENSE_CA_PUB",null).', DIRECT, '.env("ADSENSE_CA_PUB_ADS_KEY",null);
			return response($ads_txt, 200)->header('Content-Type', 'text/plain');
		}
	}
	public static function setGoogleAdsenseTypes(){ //Veritabından çekilen adsense kodlarının ataması yapılıyor
		$square_html='';
		$horizontal_html='';	
		$vertical_html='';	
		$adsenses = AdsenseTable::all();
		if(AdsenseController::googleAdsenseIsActive()){
			$square_html.=$adsenses[0]->getAttributes()['ads_content'];
			$horizontal_html.=$adsenses[1]->getAttributes()['ads_content'];			
			$vertical_html.=$adsenses[2]->getAttributes()['ads_content'];
		}		
		$adsenses =  (object) 'adsenses';		
		$adsenses->square_ads = $square_html;		
		$adsenses->horizontal_ads = $horizontal_html;	
		$adsenses->vertical_ads = $vertical_html;		
		return $adsenses;
	}
}