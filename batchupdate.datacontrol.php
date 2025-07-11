<?php	
	ini_set("memory_limit","5024M");
	set_time_limit(0);

	session_start();
	include("includes/dbUSE.php");
	$date = date('Y-m-d');
	
	switch($_POST['mod']) {
		case "getData":
			if(isset($_POST['igroup']) && $_POST['igroup'] !='' ){
				$g1 = " AND a.group = '$_POST[igroup]' ";
			}

			if(isset($_POST['isgroup']) && $_POST['isgroup'] !='' ){
				$g2 = " AND a.sgroup = '$_POST[isgroup]' ";
			}

			if(isset($_POST['text']) && $_POST['text']!='' ){
				$text = $_POST['text'];
				$g3 = " AND locate('$text',description)>0 ";
			}

			if(isset($_POST['isgroup']) && $_POST['isgroup'] !='' ){
				$g4 = " AND a.type = '$_POST[itype]' ";
			}

			$data = dbquery("SELECT record_id,item_code,a.description,b.group_description,c.subgroup_description,d.description AS itype,unit_cost,srp,indcode,walkin_price,price_a,price_b,price_c,price_aaa,price_bbb,price_ccc,price_proj,price_ox,unit_price1,unit_price2,unit_price3,unit_price4,unit_price5,unit_price6,unit_price7,unit_price8,unit_price9,unit_price10,unit_price11,price_walk
							 FROM cebuglass.products_master a 
							 INNER JOIN cebuglass.options_igroup b ON a.group = b.group 
							 INNER JOIN cebuglass.options_isgroup c ON a.sgroup = c.subgroup_id 
							 LEFT JOIN cebuglass.options_itype d ON a.type = d.type 
							 WHERE 1=1 $g1 $g2 $g3 $g4;");
					
			$ctr=1;
			while($idata = mysql_fetch_array($data)){
				if($ctr%2==0){ $bgcolor = "#ADD8E6";	}else{ $bgcolor = "white";	}
					if(in_array($idata[record_id],$_SESSION['ques'])) { $chk="checked";  }else {   $chk="";}
					$html.= "<tr bgcolor=$bgcolor > 
							<td align=center width='9%' style='font-size:7pt;'>$idata[item_code]</td>
							<td align=left width='4%' style='font-size:7pt;'>$idata[indcode]</td>
							<td align=left width='15%' style='font-size:7pt;'>$idata[description]</td>
							<td align=center width='8%' style='font-size:7pt;'>$idata[group_description]</td>
							<td align=center width='7%' style='font-size:7pt;'>$idata[subgroup_description]</td>
							<td align=center width='7%' style='font-size:7pt;'>$idata[itype]</td>
							<td align=center width='4%' style='font-size:7pt;'>$idata[unit_cost]</td>
							<td align=center width='4%' style='font-size:7pt;'>$idata[srp]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_a]%' >$idata[unit_price4]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_b]%'>$idata[unit_price5]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_c]%'>$idata[unit_price6]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_aaa]%'>$idata[unit_price1]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_bbb]%'>$idata[unit_price2]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_ccc]%'>$idata[unit_price3]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_ox]%'>$idata[unit_price8]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_proj]%'>$idata[unit_price7]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_walk]%' >$idata[walkin_price]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_kim]%' >$idata[unit_price9]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_candice]%' >$idata[unit_price10]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_clent]%' >$idata[unit_price11]</td>
							<td align=center  width='2%' style='font-size:7pt;'><input type = 'checkbox' id='$idata[record_id]'  value='$idata[record_id]' onclick='tagItem(this.value)' $chk ></td>
						</tr> ";
						$ctr++;
			}
			echo $html;
		break;

		case "getSubGroup" :
			$sgroup = dbquery("SELECT parent_gid,subgroup_id,subgroup_description FROM cebuglass.options_isgroup WHERE parent_gid = '$_POST[grp]';");
				$select = "<option value=''> SELECT </option>";
			while($isgroup = mysql_fetch_array($sgroup)){
				$select.= "<option value=".$isgroup[subgroup_id].">".$isgroup[subgroup_description]."</option> ";
			}
			echo $select;
		break;

		case "tagItem":
			$val = array();
			$push = $_REQUEST['push'];
			array_push($val,$_REQUEST['val']);
			if(!isset($_SESSION['ques'])) { $_SESSION['ques'] = array(); }
			if($push == 'Y') { if(array_search($val[0],$_SESSION['ques'])==0) { array_push($_SESSION['ques'],$val[0]); }
			} else { $_SESSION['ques'] = array_diff($_SESSION['ques'],$val); }

		break;

		case "updateLevel":
			if(isset($_POST['a']) && $_POST['a']!=''){ $sub_str .= " price_a='$_POST[percent]' "; } 
			if(isset($_POST['b']) && $_POST['b']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_b='$_POST[percent]' "; }
			if(isset($_POST['c']) && $_POST['c']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_c='$_POST[percent]' "; }
			if(isset($_POST['aaa']) && $_POST['aaa']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_aaa='$_POST[percent]' "; }
			if(isset($_POST['bbb']) && $_POST['bbb']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_bbb='$_POST[percent]' "; }
			if(isset($_POST['ccc']) && $_POST['ccc']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_ccc='$_POST[percent]' "; }
			if(isset($_POST['proj']) && $_POST['proj']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_proj='$_POST[percent]' "; }
			if(isset($_POST['ox']) && $_POST['ox']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_ox='$_POST[percent]' "; }
			if(isset($_POST['walk_in']) && $_POST['walk_in']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_walk='$_POST[percent]' "; }
			
			if(isset($_POST['kim']) && $_POST['kim']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_kim='$_POST[percent]' "; }
			if(isset($_POST['can']) && $_POST['can']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_candice='$_POST[percent]' "; }
			if(isset($_POST['clen']) && $_POST['clen']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_clent='$_POST[percent]' "; }

			foreach($_SESSION['ques'] as $index) {
				//dbquery("UPDATE cebuglass.products_master a SET a.$_POST[price_level] = '$_REQUEST[percent]' WHERE a.record_id = '$index';");
				//$str .= "UPDATE cebuglass.products_master a SET a.$_POST[price_level] = '$_REQUEST[percent]' WHERE a.record_id = '$index';";
				//echo var_dump($index);
				$qry = "UPDATE cebuglass.products_master a SET ".$sub_str." WHERE a.record_id = '$index';";
				dbquery($qry);
				/*
					dbquery("UPDATE cebuglass.products_master a SET 
						a.unit_price1=unit_cost + ROUND(ROUND(price_aaa/100,2)*unit_cost,2),
						a.unit_price2=unit_cost + ROUND(ROUND(price_bbb/100,2)*unit_cost,2),
						a.unit_price3=unit_cost + ROUND(ROUND(price_ccc/100,2)*unit_cost,2),
						a.unit_price4=unit_cost + ROUND(ROUND(price_a/100,2)*unit_cost,2),
						a.unit_price5=unit_cost + ROUND(ROUND(price_b/100,2)*unit_cost,2),
						a.unit_price6=unit_cost + ROUND(ROUND(price_c/100,2)*unit_cost,2),
						a.unit_price7=unit_cost + ROUND(ROUND(price_proj/100,2)*unit_cost,2),
						a.unit_price8=unit_cost + ROUND(ROUND(30/100,2)*unit_cost,2),
						a.walkin_price=unit_cost + ROUND(ROUND(price_walk/100,2)*unit_cost,2)
						 WHERE a.record_id = '$index' AND a.group != 'AL';");
				*/

				dbquery("UPDATE cebuglass.products_master a SET 
						a.unit_price1=if(price_aaa>0,unit_cost + ROUND(ROUND(price_aaa/100,2)*unit_cost,2),0),
						a.unit_price2=if(price_bbb>0,unit_cost + ROUND(ROUND(price_bbb/100,2)*unit_cost,2),0),
						a.unit_price3= if(price_ccc>0,unit_cost + ROUND(ROUND(price_ccc/100,2)*unit_cost,2),0), 
						a.unit_price4= if(price_a>0,unit_cost + ROUND(ROUND(price_a/100,2)*unit_cost,2),0),
						a.unit_price5= if(price_b>0,unit_cost + ROUND(ROUND(price_b/100,2)*unit_cost,2),0),
						a.unit_price6= if(price_c>0,unit_cost + ROUND(ROUND(price_c/100,2)*unit_cost,2),0), 
						a.unit_price7= if(price_proj>0,unit_cost + ROUND(ROUND(price_proj/100,2)*unit_cost,2),0),
						a.unit_price8= unit_cost + ROUND(ROUND(30/100,2)*unit_cost,2),
						a.unit_price9= if(price_kim>0,unit_cost + ROUND(ROUND(price_kim/100,2)*unit_cost,2),0),
						a.unit_price10= if(price_candice>0,unit_cost + ROUND(ROUND(price_candice/100,2)*unit_cost,2),0),
						a.unit_price11= if(price_clent>0,unit_cost + ROUND(ROUND(price_clent/100,2)*unit_cost,2),0),
						a.walkin_price= if(price_walk>0,unit_cost + ROUND(ROUND(price_walk/100,2)*unit_cost,2),0)
						 WHERE `group` != 'UV' OR (`group` = 'AL' AND sgroup = '7' AND `type` = '22') and a.record_id = '$index';");

				dbquery("UPDATE cebuglass.products_master a SET 
						a.unit_price1=if(price_aaa>0, srp - ROUND(ROUND(price_aaa/100,2)*srp,2) , 0),
						a.unit_price2=if(price_bbb>0, srp - ROUND(ROUND(price_bbb/100,2)*srp,2) , 0),
						a.unit_price3=if(price_ccc>0, srp - ROUND(ROUND(price_ccc/100,2)*srp,2) , 0),
						a.unit_price4=if(price_a>0, srp - ROUND(ROUND(price_a/100,2)*srp,2) , 0),
						a.unit_price5=if(price_b>0, srp - ROUND(ROUND(price_b/100,2)*srp,2) , 0),
						a.unit_price6=if(price_c>0, srp - ROUND(ROUND(price_c/100,2)*srp,2) , 0),
						a.unit_price7=if(price_proj>0, srp - ROUND(ROUND(price_proj/100,2)*srp,2) ,0 ),
						a.unit_price8=unit_cost + ROUND(ROUND(30/100,2)*unit_cost,2),
						a.walkin_price=srp - ROUND(ROUND(price_walk/100,2)*srp,2),
						a.unit_price9=if(price_kim>0, srp - ROUND(ROUND(price_kim/100,2)*srp,2) ,0 ),
						a.unit_price10=if(price_candice>0, srp - ROUND(ROUND(price_candice/100,2)*srp,2) ,0 ),
						a.unit_price11=if(price_clent>0, srp - ROUND(ROUND(price_clent/100,2)*srp,2) ,0 )
						 WHERE `group` = 'UV' OR (`group` = 'AL' AND sgroup = '7' AND `type` != '22') and a.record_id = '$index';");

				/*
				dbquery("UPDATE cebuglass.products_master a SET 
						a.unit_price1=srp - ROUND(ROUND(price_aaa/100,2)*srp,2),
						a.unit_price2=srp - ROUND(ROUND(price_bbb/100,2)*srp,2),
						a.unit_price3=srp - ROUND(ROUND(price_ccc/100,2)*srp,2),
						a.unit_price4=srp - ROUND(ROUND(price_a/100,2)*srp,2),
						a.unit_price5=srp - ROUND(ROUND(price_b/100,2)*srp,2),
						a.unit_price6=srp - ROUND(ROUND(price_c/100,2)*srp,2),
						a.unit_price7=srp - ROUND(ROUND(price_proj/100,2)*srp,2),
						a.unit_price8=unit_cost + ROUND(ROUND(30/100,2)*unit_cost,2),
						a.walkin_price=srp - ROUND(ROUND(price_walk/100,2)*srp,2)
						 WHERE a.record_id = '$index' AND a.group = 'AL';");
				*/
				
				/*dbquery("UPDATE cebuglass.products_master a SET 
						a.unit_price1=unit_cost + ROUND(ROUND(price_aaa/100,2)*unit_cost,2),
						a.unit_price2=unit_cost + ROUND(ROUND(price_bbb/100,2)*unit_cost,2),
						a.unit_price3=unit_cost + ROUND(ROUND(price_ccc/100,2)*unit_cost,2),
						a.unit_price4=unit_cost + ROUND(ROUND(price_a/100,2)*unit_cost,2),
						a.unit_price5=unit_cost + ROUND(ROUND(price_b/100,2)*unit_cost,2),
						a.unit_price6=unit_cost + ROUND(ROUND(price_c/100,2)*unit_cost,2),
						a.unit_price8=unit_cost + ROUND(ROUND(30/100,2)*unit_cost,2),
						a.walkin_price=unit_cost + ROUND(ROUND(price_walk/100,2)*unit_cost,2)
						 WHERE a.record_id = '$index' AND a.group = 'AL' and a.sgroup='7';");*/
			}
			//unset($_SESSION['ques']);
			//echo $str;
		break;

		case 'clear':
			unset($_SESSION['ques']);
		break;

		case 'reset' : 
			if(isset($_POST['a']) && $_POST['a']!=''){ $sub_str .= " price_a='0' , unit_price4 = '0' "; } 
			if(isset($_POST['b']) && $_POST['b']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_b='0' , unit_price5 = '0'  "; }
			if(isset($_POST['c']) && $_POST['c']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_c='0' , unit_price6 = '0' "; }
			if(isset($_POST['aaa']) && $_POST['aaa']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_aaa='0' , unit_price1 = '0' "; }
			if(isset($_POST['bbb']) && $_POST['bbb']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_bbb='0' , unit_price2 = '0' "; }
			if(isset($_POST['ccc']) && $_POST['ccc']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_ccc='0' , unit_price3 = '0' "; }
			if(isset($_POST['proj']) && $_POST['proj']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_proj='0' , unit_price7 = '0' "; }
			if(isset($_POST['ox']) && $_POST['ox']!=''){ if($sub_str!=''){ $sub_str .= ",";	} $sub_str .= " price_ox='0' , unit_price8 = '0' "; }
			$qry = "UPDATE cebuglass.products_master a SET ".$sub_str." ;";
			dbquery($qry);
			//dbquery("UPDATE cebuglass.products_master a SET a.unit_price4 = IF(IFNULL(price_a,0)=0,0,unit_price4);");
		break;
		
		case "getDataPromo":
			if(isset($_POST['igroup']) && $_POST['igroup'] !='' ){
				$g1 = " AND a.group = '$_POST[igroup]' ";
			}

			if(isset($_POST['isgroup']) && $_POST['isgroup'] !='' ){
				$g2 = " AND a.sgroup = '$_POST[isgroup]' ";
			}

			if(isset($_POST['text']) && $_POST['text']!='' ){
				$text = $_POST['text'];
				$g3 = " AND locate('$text',description)>0 ";
			}

			$data = dbquery("SELECT record_id,item_code,description,b.group_description,c.subgroup_description,unit_cost,srp,indcode,walkin_price,promo_price,promo_rate
							 FROM cebuglass.products_master a INNER JOIN cebuglass.options_igroup b ON a.group = b.group INNER JOIN cebuglass.options_isgroup c ON a.sgroup = c.subgroup_id WHERE 1=1 $g1 $g2 $g3 ;");
			
			$ctr=1;
			while($idata = mysql_fetch_array($data)){
				if($ctr%2==0){ $bgcolor = "#ADD8E6";	}else{ $bgcolor = "white";	}
					if(in_array($idata[record_id],$_SESSION['ques'])) { $chk="checked";  }else {   $chk="";}
					$html.= "<tr bgcolor=$bgcolor > 
							<td align=center width='9%' style='font-size:7pt;'>$idata[item_code]</td>
							<td align=left width='4%' style='font-size:7pt;'>$idata[indcode]</td>
							<td align=left width='15%' style='font-size:7pt;'>$idata[description]</td>
							<td align=center width='8%' style='font-size:7pt;'>$idata[group_description]</td>
							<td align=center width='7%' style='font-size:7pt;'>$idata[subgroup_description]</td>
							<td align=center width='4%' style='font-size:7pt;'>$idata[unit_cost]</td>
							<td align=center width='4%' style='font-size:7pt;'>$idata[walkin_price]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[promo_rate]%' >$idata[promo_price]</td>
							<td align=center  width='2%' style='font-size:7pt;'><input type = 'checkbox' id='$idata[record_id]'  value='$idata[record_id]' onclick='tagItem(this.value)' $chk ></td>
						</tr> ";
						$ctr++;
			}
			echo $html;
		break;

		case 'updateLevelPromo':

			if(isset($_POST['promo']) && $_POST['promo']!=''){ $sub_str .= " promo_rate='$_POST[percent]' "; } 

			foreach($_SESSION['ques'] as $index) {
				$qry = "UPDATE cebuglass.products_master a SET ".$sub_str." WHERE a.record_id = '$index';";
				dbquery($qry);

				dbquery("UPDATE cebuglass.products_master a SET 
						a.promo_price=if(promo_rate>0, walkin_price - ROUND(ROUND(promo_rate/100,2)*walkin_price,2) , 0)
						 WHERE a.record_id = '$index';");
			}
		break;

		case 'search' :

			if(isset($_POST['text']) && $_POST['text']!='' ){
				$text = $_POST['text'];
				$g3 = " AND locate('$text',description)>0 ";
			}

			if(isset($_POST['igroup']) && $_POST['igroup'] !='' ){
				$g1 = " AND a.group = '$_POST[igroup]' ";
			}

			if(isset($_POST['isgroup']) && $_POST['isgroup'] !='' ){
				$g2 = " AND a.sgroup = '$_POST[isgroup]' ";
			}

			$data = dbquery("SELECT record_id,item_code,description,b.group_description,c.subgroup_description,unit_cost,srp,indcode,walkin_price,promo_price,promo_rate
							 FROM cebuglass.products_master a INNER JOIN cebuglass.options_igroup b ON a.group = b.group INNER JOIN cebuglass.options_isgroup c ON a.sgroup = c.subgroup_id WHERE 1=1 $g1 $g2 $g3 ;");
			
			$ctr=1;
			while($idata = mysql_fetch_array($data)){
				if($ctr%2==0){ $bgcolor = "#ADD8E6";	}else{ $bgcolor = "white";	}
					if(in_array($idata[record_id],$_SESSION['ques'])) { $chk="checked";  }else {   $chk="";}
					$html.= "<tr bgcolor=$bgcolor > 
							<td align=center width='9%' style='font-size:7pt;'>$idata[item_code]</td>
							<td align=left width='4%' style='font-size:7pt;'>$idata[indcode]</td>
							<td align=left width='15%' style='font-size:7pt;'>$idata[description]</td>
							<td align=center width='8%' style='font-size:7pt;'>$idata[group_description]</td>
							<td align=center width='7%' style='font-size:7pt;'>$idata[subgroup_description]</td>
							<td align=center width='4%' style='font-size:7pt;'>$idata[unit_cost]</td>
							<td align=center width='4%' style='font-size:7pt;'>$idata[walkin_price]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[promo_rate]%' >$idata[promo_price]</td>
							<td align=center  width='2%' style='font-size:7pt;'><input type = 'checkbox' id='$idata[record_id]'  value='$idata[record_id]' onclick='tagItem(this.value)' $chk ></td>
						</tr> ";
						$ctr++;
			}
			echo $html;
		break;

		case 'search2' :

			if(isset($_POST['text']) && $_POST['text']!='' ){
				$text = $_POST['text'];
				$g3 = " AND locate('$text',description)>0 ";
			}

			if(isset($_POST['igroup']) && $_POST['igroup'] !='' ){
				$g1 = " AND a.group = '$_POST[igroup]' ";
			}

			if(isset($_POST['isgroup']) && $_POST['isgroup'] !='' ){
				$g2 = " AND a.sgroup = '$_POST[isgroup]' ";
			}

			//$data = dbquery("SELECT record_id,item_code,description,b.group_description,c.subgroup_description,unit_cost,srp,indcode,walkin_price,promo_price,promo_rate
			//			 FROM cebuglass.products_master a INNER JOIN cebuglass.options_igroup b ON a.group = b.group INNER JOIN cebuglass.options_isgroup c ON a.sgroup = c.subgroup_id WHERE 1=1 $g1 $g2 $g3 ;");
			
			$data = dbquery("SELECT record_id,item_code,description,b.group_description,c.subgroup_description,unit_cost,srp,indcode,walkin_price,price_a,price_b,price_c,price_aaa,price_bbb,price_ccc,price_proj,price_ox,unit_price1,unit_price2,unit_price3,unit_price4,unit_price5,unit_price6,unit_price7,unit_price8,unit_price9,unit_price10,unit_price11,price_walk
							 FROM cebuglass.products_master a INNER JOIN cebuglass.options_igroup b ON a.group = b.group INNER JOIN cebuglass.options_isgroup c ON a.sgroup = c.subgroup_id WHERE 1=1 $g1 $g2 $g3;");
			
			$ctr=1;
			while($idata = mysql_fetch_array($data)){
				if($ctr%2==0){ $bgcolor = "#ADD8E6";	}else{ $bgcolor = "white";	}
					if(in_array($idata[record_id],$_SESSION['ques'])) { $chk="checked";  }else {   $chk="";}
					$html.= "<tr bgcolor=$bgcolor > 
							<td align=center width='9%' style='font-size:7pt;'>$idata[item_code]</td>
							<td align=left width='4%' style='font-size:7pt;'>$idata[indcode]</td>
							<td align=left width='15%' style='font-size:7pt;'>$idata[description]</td>
							<td align=center width='8%' style='font-size:7pt;'>$idata[group_description]</td>
							<td align=center width='7%' style='font-size:7pt;'>$idata[subgroup_description]</td>
							<td align=center width='4%' style='font-size:7pt;'>$idata[unit_cost]</td>
							<td align=center width='4%' style='font-size:7pt;'>$idata[srp]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_a]%' >$idata[unit_price4]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_b]%'>$idata[unit_price5]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_c]%'>$idata[unit_price6]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_aaa]%'>$idata[unit_price1]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_bbb]%'>$idata[unit_price2]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_ccc]%'>$idata[unit_price3]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_ox]%'>$idata[unit_price8]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_proj]%'>$idata[unit_price7]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_walk]%' >$idata[walkin_price]</td>
							
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_kim]%' >$idata[unit_price9]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_candice]%' >$idata[unit_price10]</td>
							<td align=center width='4%' style='font-size:7pt;' title='$idata[price_clent]%' >$idata[unit_price11]</td>
							<td align=center  width='2%' style='font-size:7pt;'><input type = 'checkbox' id='$idata[record_id]'  value='$idata[record_id]' onclick='tagItem(this.value)' $chk ></td>
						</tr> ";
						$ctr++;
			}
			echo $html;
		break;

	}
	
	mysql_close($con);
?>