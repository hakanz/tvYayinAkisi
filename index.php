<?php
	date_default_timezone_set('Europe/Istanbul');
	
	$yayinlar = yayinAkisi($_GET["kanal"]);
	print_r(json_encode($yayinlar["yayinAkisi"]));
	
	function yayinAkisi($kanal){
		$linkBase = "https://www.hurriyet.com.tr/tv-rehberi/yayin-akisi/";
		$kanalList = array(
			"kanal-d"=>"94/0/kanal-d",
			"cnn-turk"=>"20/1/cnn-turk",
			"star-tv"=>"90/2/star-tv",
			"show-tv"=>"92/3/show-tv",
			"atv"=>"83/4/atv",
			"trt-1"=>"15/5/trt-1",
			"ntv"=>"21/29/ntv/",
			"fox"=>"87/7/fox",
			"tv-8"=>"24/8/tv8",
			"sky-360"=>"30/9/360",
			"bloomberg-ht"=>"295/10/bloomberg-ht",
			"a-haber"=>"365/11/a-haber",
			"kanal-7"=>"95/12/kanal-7",
			"24-tv"=>"105/13/24-tv",
			"haberturk"=>"22/14/haberturk",
			"beyaz-tv"=>"321/15/beyaz-tv",
			"animal-planet"=>"383/16/animal-planet",
			"discovery-channel"=>"98/17/discovery-channel",
			"nat-geo-people"=>"306/18/nat-geo-people",
			"nat-geo-wild"=>"309/19/national-geographic-wild-hd",
			"bbc-world"=>"171/20/bbc-world-news",
			"sinema-tv"=>"196/21/snema-tv",
			"euro-sport-1"=>"66/22/eurosport-1",
			"euro-sport-2"=>"165/23/eurosport-2-int",
			//"trt-spor"=>"24/trt-3-spor",
			"national-geographic"=>"126/25/national-geographic",
			"bein-sports-1"=>"49/26/bein-sports-1",
			"sports-tv"=>"329/27/sports-tv",
			"teve-2"=>"132/30/teve2",
			"bein-sports-3"=>"368/31/bein-sports-3",
			"dmax"=>"1900/32/dmax"
		);
		if (isset($kanalList[$kanal])){
			$d = getir($linkBase.$kanalList[$kanal]."/");
			if ($d){
				$xml = simplexml_load_string(xmleCevir($d));
				if ($xml){
					$yayinAkisi = array();
					$ind = 0;
					foreach($xml->ul->li as $i){
						$yayinSaati = $i->a->div[0]->span->__toString();
						$yayinAraligi = substr($i->a->div[1]->div[1]->div[0],23,48);
						$yayinAraligi = yerlestir($yayinAraligi,"  ","");
						$yayinBitis = substr($yayinAraligi,8,5);
						$prntz = strpos($yayinAraligi,"(")+1;
						$prntz2 = strpos($yayinAraligi,")")-$prntz;
						$yayinSuresi = substr($yayinAraligi,$prntz,$prntz2);
						$yayinAkisi[$ind]["yayinAdi"] = $i->a->div[1]->div[1]->h2->__toString();
						$yayinAkisi[$ind]["yayinResmi"] = "https:".$i->a->div[1]->div[0]->img["src"]->__toString();	
						$yayinAkisi[$ind]["yayinSaati"] = $yayinSaati;
						$yayinAkisi[$ind]["yayinBitisSaati"] = $yayinBitis;
						$yayinAkisi[$ind]["yayinSuresi"] = $yayinSuresi;
						$yayinAkisi[$ind]["yayinAraligi"] = $yayinAkisi[$ind]["yayinSaati"]." - ".$yayinBitis;
						$yayinAkisi[$ind]["yayinTuru"] = substr($i->a->div[1]->div[1]->div[1],22,strlen($i->a->div[1]->div[1]->div[1]));
						$yayinAkisi[$ind]["yayinOzeti"] = substr($i->a->div[1]->div[1]->div[2],22,strlen($i->a->div[1]->div[1]->div[2]));
						$yayinAkisi[$ind]["suanYayinda"] = false;
						$saat = date("H:i");
						if ($saat >= $yayinSaati and $saat <= $yayinBitis){
							$yayinAkisi[$ind]["suanYayinda"] = true;
						}
						$ind++;
					}
					return array("yayinAkisi"=>$yayinAkisi);
				}else{
					return false; //veri okunamadı.
				}
			}else{
				return false; //yayın akışı getirilemedi.
			}
		}else{
			return false; //kanal bulunamadı.
		}
	}
	
	function xmleCevir($data){
		$sec1 = '<div class="row default-list-wrapper">';
		$sec2 = '<div style="text-align: center; margin: 0 0 20px 0;" id="div-gpt-ad-1456233076352-13"';
		$data = sec($data,$sec1,$sec2);
		$data = substr($data,0,-12);
		return $data;
	}
	function getir($url=NULL){
		if($url == NULL) return false;  
		$ch = curl_init($url);  
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);  
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
		curl_close($ch); 
		if($httpcode>=200 && $httpcode<300){  
			return $data;  
		}else{
			echo $httpcode;
			return false;  
		}
	}
	
	function yerlestir($str,$degis,$yeni){
		return str_replace($degis,$yeni,$str);
	}
	
	function sec($i,$b,$s){
		$bir = strpos($i,$b);
		$iki = strpos($i,$s)-$bir;
		return substr($i,$bir,$iki);
	}
?>