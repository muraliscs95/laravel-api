<?php

namespace App\Models;
//use App\BaseModel;
use DB;
use Illuminate\Routing\Controller as BaseController;

class ApiModel
{
	
	public function __construct()
    {
		
    }

	public static function strClear($string,$removeSapce)
	{
		$string = strip_tags($string);
		if($removeSapce === true){
           $string = preg_replace('/\s+/', '', $string);
		}
		$string = str_replace('>', '', $string);
		$string = str_replace('<', '', $string);
		return $string;
	}
	
	public static function ScrabSportsData()
	{

		$limit = 6;
		$mainUrl ="https://www.sportskeeda.com";
	    $url1 = $mainUrl.'/go/ipl/schedule';
		$content1 = file_get_contents($url1);
		$first_step1 = explode( '<div class="keeda_cricket_event_schedule">' , $content1 );
		$first_step2 = explode( '<div class="keeda_cricket_event_card"' , $first_step1[1]);

		$matches = [];
		for($i=1;$i<$limit;$i++)
		{
			$key = $i-1;
			$allData = explode( '"' , $first_step2[$i]);
			//print_r($allData);die;
			$matches[$key]['DataTime'] = $allData[11];
			$matches[$key]['TeamA'] = trim(strip_tags(preg_replace('/>+/', '', $allData[26])));
			$matches[$key]['TeamB'] = trim(strip_tags(preg_replace('/>+/', '', $allData[38])));
			$matches[$key]['ShortTeamA'] = $allData[29];
			$matches[$key]['ShortTeamB'] = $allData[41];
			$matches[$key]['TeamAImg'] = explode( "'" , $allData[23])[1];
			$matches[$key]['TeamBImg'] = explode( "'" , $allData[35])[1];
			$place = explode( "," , $allData[16]);
			$matches[$key]['MatchPlace'] = trim(strip_tags($place[1])).','.trim(strip_tags($place[2]));

						
			$limit2 = 26;
			$innerURL = $mainUrl.$allData[5]."/match-center";
			//print_r($innerURL);die;
			$Innercontent1 = file_get_contents($innerURL);
			$Innercontent2 = explode('<div class="squads-holder"' ,$Innercontent1);
			
			if(isset($Innercontent2[4]))
			{
				$teamAContent = explode('<div class="team-squad team-squad-left"' ,$Innercontent2[4]);
				$teamAContent2 = explode('<div class="player-role-info"' ,$teamAContent[1]);
				
				for($j=22;$j<$limit2;$j++)
				{
				 if(isset($teamAContent2[$j]))
				 {
					$teamAall = explode('"' ,$teamAContent2[$j]);
				//print_r($teamAall);die;
					if(isset($teamAall[20])){
						$plyrName = Self::strClear($teamAall[20],true);
						if(!empty($plyrName))
						{
						  $matches[$key]['TeamAPlayers'][$j-1]['name'] = $plyrName;
						  $matches[$key]['TeamAPlayers'][$j-1]['image'] = $teamAall[13];
						  $matches[$key]['TeamAPlayers'][$j-1]['type'] = Self::strClear($teamAall[22],true);
						}
						else
						{
							$matches[$key]['TeamAPlayers'][$j-1]['name'] = Self::strClear($teamAall[16],true);
							$matches[$key]['TeamAPlayers'][$j-1]['image'] = $teamAall[13];
							$matches[$key]['TeamAPlayers'][$j-1]['type'] = Self::strClear($teamAall[18],true);
						}
						
					}
					else{
						$matches[$key]['TeamAPlayers'][$j-1]['name'] = Self::strClear($teamAall[16],true);
						$matches[$key]['TeamAPlayers'][$j-1]['image'] = $teamAall[13];
						$matches[$key]['TeamAPlayers'][$j-1]['type'] = Self::strClear($teamAall[18],true);
					}
			     }
				}
			
				$teamBContent = explode('<div class="team-squad team-squad-right"' ,$Innercontent2[4]);
				$teamBContent = explode('<div class="player-role-info"' ,$teamBContent[1]);
				for($k=1;$k<$limit2;$k++)
				{
				 if(isset($teamBContent[$k]))
				 {
					$teamAall = explode('"' ,$teamBContent[$k]);
					if(isset($teamAall[21])){
						$matches[$key]['TeamBPlayers'][$k-1]['name'] = Self::strClear($teamAall[6],true);
						$matches[$key]['TeamBPlayers'][$k-1]['image'] = $teamAall[21];
						$matches[$key]['TeamBPlayers'][$k-1]['type'] = Self::strClear($teamAall[8],true);
					}
					else{
						$matches[$key]['TeamBPlayers'][$k-1]['name'] = Self::strClear($teamAall[2],true);
						$matches[$key]['TeamBPlayers'][$k-1]['image'] = $teamAall[17];
						$matches[$key]['TeamBPlayers'][$k-1]['type'] = Self::strClear($teamAall[4],true);
					}
				  }
				}
		   }else{
		      $matches[$key]['TeamAPlayers'] = "";
			  $matches[$key]['TeamBPlayers'] = "";
		   }

			echo "<pre>";print_r($matches);die;
		}


	   echo "<pre>";print_r($matches);die;

	}



	
	public static function ScrabSportsResult()
	{

		$limit = 6;
		$mainUrl ="https://www.sportskeeda.com";
	    $url1 = $mainUrl.'/go/ipl/results';
		$content1 = file_get_contents($url1);
		$first_step1 = explode( '<div class="result-schedule-items schedule-items"' , $content1 );
		$first_step2 = explode( '<div class="keeda_cricket_event_card"' , $first_step1[1]);
		
		$results = [];
		for($i=2;$i<$limit;$i++)
		{
		  if(isset($first_step2[$i]))
		  {
			$key = $i-1;
			$s1 = explode( '"' , $first_step2[$i] );
			
			$results[$key]['teamAshort'] = $s1[33];
			$results[$key]['teamBshort'] = $s1[45];

			$results[$key]['teamAScore'] = Self::strClear($s1[34],true);
			$results[$key]['teamBScore'] = Self::strClear($s1[46],true);

			$rTxt = explode( 'span' , $s1[48] );
			$results[$key]['matchResultTxt'] = Self::strClear($rTxt[1],false);
			$results[$key]['DateTime'] = $s1[3];
            
			$detailsPageUrl = $s1[7];
			$detailsPage = file_get_contents($mainUrl.$detailsPageUrl);
			$detailsPage2 = explode( '<div class="all-innings-new-parent-div "' , $detailsPage );
			$batsmanInfo = explode( '<div class="innings-table-body">' , $detailsPage2[1] );
			$batsmanInfo2 = explode( '<div class="innings-table-row-holder' , $batsmanInfo[1] );
			
			$limit2 = 11;
			// Team  A Batman info
			for($j=1;$j<$limit2;$j++)
			{
			 
			  if(isset($batsmanInfo2[$j]))
			  {
				$bt = explode( '"' , $batsmanInfo2[$j]);
				if(!isset($bt[59]) && isset($bt[17]))
				{
					$key1 = $j-1;
					
					if(!isset($bt[27]))
					{
						$results[$key]['teamA']['batsman'][$key1]['playername'] = Self::strClear($bt[9],true);
						$plInfo = array(
							"run" => Self::strClear($bt[11],true),
							"balls" => Self::strClear($bt[13],true),
							"fours" => Self::strClear($bt[15],true),
							"sixes" => Self::strClear($bt[17],true),
							"strike" => Self::strClear($bt[19],true),
							"status" => (Self::strClear($bt[23],true) == "Notout") ? "Notout" : "Out"
						);
					}
					elseif(Self::strClear($bt[15],true) == "(C)")
					{
						$results[$key]['teamA']['batsman'][$key1]['playername'] = Self::strClear($bt[13],true);
						$plInfo = array(
							"run" => Self::strClear($bt[17],true),
							"balls" => Self::strClear($bt[19],true),
							"fours" => Self::strClear($bt[21],true),
							"sixes" => Self::strClear($bt[23],true),
							"strike" => Self::strClear($bt[25],true),
							"status" => (Self::strClear($bt[27],true) == "Notout") ? "Notout" : "Out"
						);
					}
					elseif(Self::strClear($bt[15],true) == "")
					{
						$results[$key]['teamA']['batsman'][$key1]['playername'] = Self::strClear($bt[13],true);
						$plInfo = array(
							"run" => Self::strClear($bt[31],true),
							"balls" => Self::strClear($bt[33],true),
							"fours" => Self::strClear($bt[35],true),
							"sixes" => Self::strClear($bt[37],true),
							"strike" => Self::strClear($bt[39],true),
							"status" => (Self::strClear($bt[43],true) == "Notout") ? "Notout" : "Out"
						);
					}
					else{
						$results[$key]['teamA']['batsman'][$key1]['playername'] = Self::strClear($bt[13],true);
						$plInfo = array(
							"run" => Self::strClear($bt[15],true),
							"balls" => Self::strClear($bt[17],true),
							"fours" => Self::strClear($bt[19],true),
							"sixes" => Self::strClear($bt[21],true),
							"strike" => Self::strClear($bt[23],true),
							"status" => (Self::strClear($bt[27],true) == "Notout") ? "Notout" : "Out"
						);
					}
					$results[$key]['teamA']['batsman'][$key1]['playerInfo'] = $plInfo;
			    }
			  }
			}


            // Team  A Bowler info
			$BowlrInfo = explode( '<div class="innings-table-bowling' , $detailsPage2[1] );
			$BowlrInfo2 = explode( '<div class="innings-table-body' , $BowlrInfo[1] );
			$bl = explode( '<div class="innings-table-row-holder' , $BowlrInfo2[1] );
			for($k=1;$k<$limit2;$k++)
			{
			   if(isset($bl[$k]))
			   {
				$bl2 = explode('"', $bl[$k]);
				$key2 = $k-1;
				$results[$key]['teamA']['bowler'][$key2]['playername'] = Self::strClear($bl2[11],true);

				if(isset($bl2[39]))
				{
					$blInfo = array(
						"overs" => Self::strClear($bl2[29],true),
						"maidain" => Self::strClear($bl2[31],true),
						"runs" => Self::strClear($bl2[33],true),
						"wickets" => Self::strClear($bl2[35],true),
						"strike" => Self::strClear($bl2[37],true),
						"extras" => Self::strClear($bl2[39],true)
					);
				}
				else
				{
					$blInfo = array(
						"overs" => Self::strClear($bl2[13],true),
						"maidain" => Self::strClear($bl2[15],true),
						"runs" => Self::strClear($bl2[17],true),
						"wickets" => Self::strClear($bl2[19],true),
						"strike" => Self::strClear($bl2[21],true),
						"extras" => Self::strClear($bl2[23],true)
					);
			    }
				$results[$key]['teamA']['bowler'][$key2]['playerInfo'] = $blInfo;
			 }

			}


			// Team  B Batman info
			$teamBinfo = explode( '<div class="one-innings-div innings-content-1"' , $detailsPage2[1] );
			$teamBinfo2 = explode( '<div class="innings-table-body">' , $teamBinfo[1] );
			$teamBbt = explode( '<div class="innings-table-row-holder' , $teamBinfo2[1] );
			for($x=1;$x<$limit2;$x++)
			{
				if(isset($teamBbt[$x]))
				{
					$bt2 = explode('"', $teamBbt[$x]);
					if(!isset($bt2[59]) && isset($bt2[17]))
					{
						$key3 = $x-1;
						$results[$key]['teamB']['batsman'][$key3]['playername'] = Self::strClear($bt2[13],true);
						if($bt2[14] == "star")
						{	
							$pl2Info = array(
								"run" => Self::strClear($bt2[17],true),
								"balls" => Self::strClear($bt2[19],true),
								"fours" => Self::strClear($bt2[21],true),
								"sixes" => Self::strClear($bt2[23],true),
								"strike" => Self::strClear($bt2[25],true),
								"status" => (Self::strClear($bt2[29],true) == "Notout") ? "Notout" : "Out"
							);
						}
						elseif(Self::strClear($bt2[15],true) == "")
						{
							$pl2Info = array(
								"run" => Self::strClear($bt2[31],true),
								"balls" => Self::strClear($bt2[33],true),
								"fours" => Self::strClear($bt2[35],true),
								"sixes" => Self::strClear($bt2[37],true),
								"strike" => Self::strClear($bt2[39],true),
								"status" => (Self::strClear($bt2[43],true) == "Notout") ? "Notout" : "Out"
							);
						}
						else {
							$pl2Info = array(
								"run" => Self::strClear($bt2[15],true),
								"balls" => Self::strClear($bt2[17],true),
								"fours" => Self::strClear($bt2[19],true),
								"sixes" => Self::strClear($bt2[21],true),
								"strike" => Self::strClear($bt2[23],true),
								"status" => (Self::strClear($bt2[27],true) == "Notout") ? "Notout" : "Out"
							);
						}
						$results[$key]['teamB']['batsman'][$key3]['playerInfo'] = $pl2Info;
					}
			    }
			}


			// Team  B Bowler info
			$BowlBrInfo2 = explode( '<div class="innings-table-bowling' , $detailsPage2[1] );
			$BowlBrInfo3 = explode( '<div class="innings-table-body' , $BowlBrInfo2[2] );
			$BowlrBInfo2 = explode( '<div class="innings-table-row-holder' , $BowlBrInfo3[1] );
			for($y=6;$y<$limit2;$y++)
			{
               if(isset($BowlrBInfo2[$y]))
			   {
				$blb2 = explode('"', $BowlrBInfo2[$y]);
				print_r($blb2);die;
				$key4 = $y-1;
				
				if(!isset($blb2[25]))
				{
					$results[$key]['teamB']['bowler'][$key4]['playername'] = Self::strClear($blb2[11],true);
					$blbInfo = array(
						"overs" => Self::strClear($blb2[13],true),
						"maidain" => Self::strClear($blb2[15],true),
						"runs" => Self::strClear($blb2[17],true),
						"wickets" => Self::strClear($blb2[19],true),
						"strike" => Self::strClear($blb2[21],true),
						"extras" => Self::strClear($blb2[23],true)
					);
				}
				elseif(str_contains($blb2[12],".png"))
				{
					$results[$key]['teamB']['bowler'][$key4]['playername'] = Self::strClear($blb2[7],true);
					$blbInfo = array(
						"overs" => Self::strClear($blb2[25],true),
						"maidain" => Self::strClear($blb2[27],true),
						"runs" => Self::strClear($blb2[29],true),
						"wickets" => Self::strClear($blb2[31],true),
						"strike" => Self::strClear($blb2[33],true),
						"extras" => Self::strClear($blb2[35],true)
					);
				}
				elseif(str_contains($blb2[16],".png"))
				{
					$results[$key]['teamB']['bowler'][$key4]['playername'] = Self::strClear($blb2[11],true);
					$blbInfo = array(
						"overs" => Self::strClear($blb2[29],true),
						"maidain" => Self::strClear($blb2[31],true),
						"runs" => Self::strClear($blb2[33],true),
						"wickets" => Self::strClear($blb2[35],true),
						"strike" => Self::strClear($blb2[37],true),
						"extras" => Self::strClear($blb2[39],true)
					);
				}
				else
				{
					$results[$key]['teamB']['bowler'][$key4]['playername'] = Self::strClear($blb2[11],true);
					$blbInfo = array(
						"overs" => Self::strClear($blb2[13],true),
						"maidain" => Self::strClear($blb2[15],true),
						"runs" => Self::strClear($blb2[17],true),
						"wickets" => Self::strClear($blb2[19],true),
						"strike" => Self::strClear($blb2[21],true),
						"extras" => Self::strClear($blb2[23],true)
					);
			    }
				$results[$key]['teamB']['bowler'][$key4]['playerInfo'] = $blbInfo;
			 }

			}
			
		  }
		  echo "<pre>";print_r($results);die;

		}


	}


	/*public static function ScrabSportsResult()
    {
		$limit = 5;
	    $url1 = 'https://sports.ndtv.com/cricket/schedules-fixtures';
		$content1 = file_get_contents($url1);
		
		$first_step1 = explode( '<div class="scr-pag_cnt">' , $content1 );
		$step2 = explode( '<div>' , $first_step1[1]);
		$step3 =  explode( '<div class="sp-scr_wrp vevent"' , $step2[1]);
		
		for($i=1;$i<$limit;$i++)
		{
		  if(isset($step3[$i])){
		 
		  
		  $s1 = explode( '"' , $step3[$i] );
		  //if(strpos($step3[$i], "ind-hig_crd-flg") !== false){ $cntry = "IND"; }else { $cntry = "OTHERS"; }
		  if($s1[117] == "lazy"){ $cntry = "OTHERS"; }else { $cntry = "IND"; }
		
		  $teamA = ($cntry=="IND") ? $s1[117] : $s1[113];
		  $teamAFlag = ($cntry=="IND") ? $s1[111] : $s1[109];
		  
		  $teamB = ($cntry=="IND") ? $s1[147] : $s1[145];
		  $teamBFlag = ($cntry=="IND") ? $s1[143] : $s1[147];
		  
		  $matchName = ($cntry=="IND") ? str_replace('>','',trim(strip_tags($s1[52]))) : str_replace('>','',trim(strip_tags($s1[48])));
		  
		  $link1 = ($cntry=="IND") ? explode( "'" , $s1[87]) :  explode( "'" , $s1[83]);
		  $link2 = explode( "vs" , $link1[3]);
		  $teamAShort = trim($link2[0]);
		  $teamBShort = trim($link2[1]);
		  
		  
		  
		  $date1 = ($cntry=="IND") ? str_replace('>','',trim(strip_tags($s1[28]))) : str_replace('>','',trim(strip_tags($s1[24])));
		  $date2 = explode('&#x2B;05:30',$date1);
		  $date1 = explode('T',$date2[0]);
		  $MatchDateTime = $date1[0].' '.$date1[1].':00';
		  $MatchDate     = ($cntry=="IND") ? date('Y-m-d',strtotime($s1[15])) :  date('Y-m-d',strtotime($s1[11]));
		 
		  
		  $detailsA = json_encode(array('shortname'=>$teamAShort ,'flag'=>$teamAFlag));
		  $detailsB = json_encode(array('shortname'=>$teamBShort ,'flag'=>$teamBFlag));
		  
        
		  
		  $link3 = '/'.strtolower($teamAShort).'-'.strtolower($teamBShort).'-squads';
		  if($cntry=="IND"){
		    $detailsLink = 'https://sports.ndtv.com/'.str_replace('/fantasy-tips',$link3,$s1[39]);
		  }
		  else{
			$detailsLink = 'https://sports.ndtv.com/'.str_replace('/fantasy-tips',$link3,$s1[35]);  
		  }
		  
		 
		  $content2 = file_get_contents($detailsLink);
		  $Inner1 = explode( '<div class="swiper-container scr_crd_tbB">' , $content2 );
		  $Inner2 = explode( '<ul class="grd_crd-ul">' , $Inner1[1] );
		  
		  // Team A players
		  $playersA = [];
		  $loop1 = explode( '<li class="grd_crd-li">' , $Inner2[1] );
		  for($j=1;$j<20;$j++)
		  {
			if(isset($loop1[$j])) { 
			$playA = explode( '"' , $loop1[$j]); 
			if(isset($playA[9]))
			{  
			   if(isset($playA[14]))
			   {
			     if(isset($playA[1]) && $playA[14] !='><div class='){ 
				   $playersA[$j]['plyrImg']  = $playA[9];
			       $playersA[$j]['plyrProf'] = $playA[1];
			       $playersA[$j]['plyrName']  = str_replace('>','',trim(strip_tags($playA[12])));
			       $playersA[$j]['plyrCate'] = str_replace('>','',trim(strip_tags($playA[14])));
				 }
				 else{ 
				   $playersA[$j]['plyrImg']  = $playA[7];
			       $playersA[$j]['plyrProf'] = '';
			       $playersA[$j]['plyrName']  = str_replace('>','',trim(strip_tags($playA[10])));
			       $playersA[$j]['plyrCate'] = str_replace('>','',trim(strip_tags($playA[12]))); 
					 
				 }
			   }
			   else
			   {
				 $playersA[$j]['plyrImg']  = $playA[7];
			     $playersA[$j]['plyrProf'] = "";
			     $playersA[$j]['plyrName']  = str_replace('>','',trim(strip_tags($playA[10])));
			     $playersA[$j]['plyrCate'] = str_replace('>','',trim(strip_tags($playA[12])));  
			   }
			 
			   
			}
			
			}
			
		  }
		  
		  
		  // Team B players
		  $playersB = [];
		  $loop2 = explode( '<li class="grd_crd-li">' , $Inner2[2] );
		  for($k=1;$k<20;$k++)
		  {
			
			if(isset($loop2[$k])) { 
			$playB = explode( '"' , $loop2[$k] ); 
			// print_r($playB);die;
			if(isset($playB))
			{ 
		      if(isset($playB[14]))
			  {
				if(isset($playB[9]) && $playB[14] !='><div class=')
				{
                  $playersB[$k]['plyrImg'] = $playB[9];
			      $playersB[$k]['plyrProf'] = $playB[1];
			      $playersB[$k]['plyrName'] = str_replace('>','',trim(strip_tags($playB[12])));

                  if(stripos($playB[14], "SI_Array")!== false) { $cate1 = explode( '</div>' , $playB[14]);$cateB= $cate1[0];  } else {$cateB = str_replace('>','',trim(strip_tags($playB[14]))); }
			      $playersB[$k]['plyrCate'] = str_replace('>','',trim(strip_tags($cateB)));
				}
				else
				{
			      $playersB[$k]['plyrImg'] = $playB[7];
			      $playersB[$k]['plyrProf'] = "";
			      $playersB[$k]['plyrName'] = str_replace('>','',trim(strip_tags($playB[10])));
			      if(stripos($playB[12], "SI_Array")!== false) { $cate1 = explode( '</div>' , $playB[12]);$cateB= $cate1[0];  } else {$cateB = str_replace('>','',trim(strip_tags($playB[12]))); }
			      $playersB[$k]['plyrCate'] = str_replace('>','',trim(strip_tags($cateB)));
				}
			  }
			  else
			  { 
				$playersB[$k]['plyrImg'] = $playB[7];
			    $playersB[$k]['plyrProf'] = "";
			    $playersB[$k]['plyrName'] = str_replace('>','',trim(strip_tags($playB[10])));

			    if(stripos($playB[12], "SI_Array")!== false) { $cate1 = explode( '</div>' , $playB[12]);$cateB= $cate1[0];  } else {$cateB = str_replace('>','',trim(strip_tags($playB[12]))); }
			    $playersB[$k]['plyrCate'] = str_replace('>','',trim(strip_tags($cateB)));
			  }
			 
			 }
			}
			
		  }

		  
          }


         }
		}*/

}
