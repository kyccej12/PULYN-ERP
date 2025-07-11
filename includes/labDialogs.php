<div id="sampleDetails" style="display: none; padding: 20px;">
    <form name="frmSample" id="frmSample">
        <input type="hidden" name = "phleb_parentcode" id = "phleb_parentcode">
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="phleb_sono" id="phleb_sono">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="phleb_sodate" id="phleb_sodate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_pid" id="phleb_pid">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_pname" id="phleb_pname">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Address&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_paddr" id="phleb_paddr">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Contact Nos.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_contactno" id="phleb_contactno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Email Address&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_email" id="phleb_email">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_gender" id="phleb_gender">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_birthdate" id="phleb_birthdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_age" id="phleb_age">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_patientstat" id="phleb_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_physician" id="phleb_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Required Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_procedure" id="phleb_procedure">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_code" id="phleb_code">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="phleb_spectype" id="phleb_spectype">
                        <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_serialno" id="phleb_serialno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_date" id="phleb_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <?php
                        $o->timify("phleb",$w="");
                    ?>

                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="inputSearch2" style="width:100%;padding-left:22px;" name="phleb_by" id="phleb_by">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Kit Info (If Applicable)&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_testkit" id="phleb_testkit">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Lot No. (If Applicable)&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_testkit_lotno" id="phleb_testkit_lotno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Expiry (If Applicable)&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="phleb_testkit_expiry" id="phleb_testkit_expiry">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="phleb_location" id="phleb_location">
                        <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Memo or Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="phleb_remarks" id="phleb_remarks" style="width:100%;" rows=3></textarea>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="electrolytesResult" style="display: none;">
    <form name="frmElectroResult" id="frmElectroResult">  
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
             <tr>
                <td width=45% valign=top>
                        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="electro_sono" id="electro_sono">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="electro_sodate" id="electro_sodate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_pid" id="electro_pid">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_pname" id="electro_pname">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>

                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_gender" id="electro_gender">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_birthdate" id="electro_birthdate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_age" id="electro_age">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_patientstat" id="electro_patientstat" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_physician" id="electro_physician">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                    </table>
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_procedure" id="electro_procedure">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_code" id="electro_code">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="electro_spectype" id="electro_spectype">
                                <?php
                                        $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_serialno" id="electro_serialno">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_extractdate" id="electro_extractdate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                            <td align=left>
                    
                                <input type="text" class="gridInput" style="width:100%;" name="electro_extracttime" id="electro_extracttime" readonly>

                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>               
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="electro_extractby" id="electro_extractby" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="electro_location" id="electro_location">
                                <?php
                                        $iun = $o->dbquery("select id,location from lab_locations;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                    </table>
                </td>
                <td width=1%>&nbsp;</td>
                <td width=64% valign=top >                  
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=3 class="td_content">
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="electro_date" id="electro_date" value="<?php echo date('m/d/Y'); ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 15px; font-weight: bold;" valign=top>Sodium&nbsp;:</td>
                        <td align=left valign=top>
                            <input type="text" class="gridInput" style="width:100%;" name="electro_sodium" id="electro_sodium" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td align="center" class="bareBold" style="padding-left: 15px;">135-145&nbsp;mmol/L</td>	
                    </tr>
                    <tr><td height=3></td></tr> 
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 15px; font-weight: bold;" valign=top>Potassium&nbsp;:</td>
                        <td align=left valign=top>
                            <input type="text" class="gridInput" style="width:100%;" name="electro_potassium" id="electro_potassium" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td align="center" class="bareBold" style="padding-left: 15px;">3.5-5.5&nbsp;mmol/L</td>	
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 15px; font-weight: bold;" valign=top>Chloride&nbsp;:</td>
                        <td align=left valign=top>
                            <input type="text" class="gridInput" style="width:100%;" name="electro_chloride" id="electro_chloride" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td align="center" class="bareBold" style="padding-left: 15px;">98-106&nbsp;mmol/L</td>	
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 15px; font-weight: bold;" valign=top>Calcium:</td>
                        <td align=left valign=top>
                            <input type="text" class="gridInput" style="width:100%;" name="electro_total_calcium" id="electro_total_calcium" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td align="center" class="bareBold" style="padding-left: 15px;">1.09-1.35&nbsp;mmol/L</td>	
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                        <td align=left>
                            <textarea name="electro_remarks" id="electro_remarks" style="width:100%;" rows=3></textarea>
                        </td>
                    </tr>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="singleValueResult" style="display: none;">
    <form name="frmsingleValue" id="frmsingleValue">  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="sresult_sono" id="sresult_sono" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="sresult_sodate" id="sresult_sodate" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="sresult_date" id="sresult_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_pid" id="sresult_pid" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_pname" id="sresult_pname" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_gender" id="sresult_gender" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_birthdate" id="sresult_birthdate" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_age" id="sresult_age" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_patientstat" id="sresult_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_physician" id="sresult_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_procedure" id="sresult_procedure" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_code" id="sresult_code" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="sresult_spectype" id="sresult_spectype">
                        <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_serialno" id="sresult_serialno" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_extractdate" id="sresult_extractdate" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
                 <input type="text" class="gridInput" style="width:100%;" name="sresult_extracttime" id="sresult_extracttime" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="sresult_by" id="sresult_by" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="sresult_location" id="sresult_location">
                        <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result Attribute&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:50%;" name="sresult_attribute" id="sresult_attribute" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>               
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Unit of Measure (UoM)&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:50%;" name="sresult_unit" id="sresult_unit">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result Value&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:50%;" name="sresult_value" id="sresult_value">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="sresult_remarks" id="sresult_remarks" style="width:100%;" rows=3></textarea>
                </td>				
            </tr>
        </table>
    </form>
</div>
<div id="enumResult" style="display: none;">
    <form name="frmEnumResult" id="frmEnumResult">  
    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="enum_sono" id="enum_sono" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="enum_sodate" id="enum_sodate" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_pid" id="enum_pid" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_pname" id="enum_pname" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_gender" id="enum_gender" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_birthdate" id="enum_birthdate" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_age" id="enum_age" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_patientstat" id="enum_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_physician" id="enum_physician" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_procedure" id="enum_procedure" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_code" id="enum_code" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="enum_spectype" id="enum_spectype" >
                        <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_serialno" id="enum_serialno" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_extractdate" id="enum_extractdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <input type="text" class="gridInput" style="width:100%;" name="enum_extracttime" id="enum_extracttime" readonly>

                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Kit Type (If Applicable)&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_testkit" id="enum_testkit" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Lot No. (If Applicable)&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_testkit_lotno" id="enum_testkit_lotno" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Expiry (If Applicable&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_testkit_expiry" id="enum_testkit_expiry" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="enum_extractby" id="enum_extractby" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="enum_location" id="enum_location">
                        <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="enum_date" id="enum_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result&nbsp;:</td>
                <td align=left>
                <input type="text" class="gridInput" style="width:100%;" name="enum_result" id="enum_result">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Performed By&nbsp;:</td>
                <td align=left>
                    <select name="enum_result_by" id="enum_result_by" class="gridInput" style="width:100%">
                        <option value="">- Not Applicable -</option>
                        <?php
                            $pbyQuery = $o->dbquery("select emp_id, fullname from user_info where role like '%MEDICAL TECH%';");
                            while($pbyRow = $pbyQuery->fetch_array()) {
                                echo "<option value = '$pbyRow[0]'>$pbyRow[1]</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="enum_remarks" id="enum_remarks" style="width:100%;" rows=3></textarea>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="hivResult" style="display: none;">
    <form name="frmHivResult" id="frmHivResult">  
    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="hiv_sono" id="hiv_sono">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="hiv_sodate" id="hiv_sodate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_pid" id="hiv_pid">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_pname" id="hiv_pname">
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_gender" id="hiv_gender">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_birthdate" id="hiv_birthdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_age" id="hiv_age">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_patientstat" id="hiv_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_physician" id="hiv_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_procedure" id="hiv_procedure">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_code" id="hiv_code">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="hiv_spectype" id="hiv_spectype">
                    <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_serialno" id="hiv_serialno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_extractdate" id="hiv_extractdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_extracttime" id="hiv_extracttime" readonly>

                </td>				
            </tr>
            <tr><td height=3></td></tr>               
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hiv_extractby" id="hiv_extractby" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="hiv_location" id="hiv_location">
                    <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="hiv_date" id="hiv_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">HIV-1&nbsp;:</td>
                <td align=left>
                <select name="hiv_one" id="hiv_one" class="gridInput" style="width:100%;">
                        <option value="REACTIVE">REACTIVE</option>
                        <option value="NON-REACTIVE">NON-REACTIVE</option>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr> 
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">HIV-2&nbsp;:</td>
                <td align=left>
                <select name="hiv_two" id="hiv_two" class="gridInput" style="width:100%;">
                        <option value="REACTIVE">REACTIVE</option>
                        <option value="NON-REACTIVE">NON-REACTIVE</option>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px; display: none;">HIV-1/2&nbsp;:</td>
                <td align=left>                 
                    <select name="hiv_half" id="hiv_half" class="gridInput" style="width:100%; display: none;">
                        <option value="REACTIVE">REACTIVE</option>
                        <option value="NON-REACTIVE">NON-REACTIVE</option>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="hiv_remarks" id="hiv_remarks" style="width:100%;" rows=3></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>

<div id="occultResult" style="display: none;">
    <form name="frmOccultResult" id="frmOccultResult">  
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
             <tr>
                <td width=45% valign=top>
                        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="occultblood_sono" id="occultblood_sono">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="occultblood_sodate" id="occultblood_sodate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_pid" id="occultblood_pid">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_pname" id="occultblood_pname">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>

                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_gender" id="occultblood_gender">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_birthdate" id="occultblood_birthdate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_age" id="occultblood_age">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_patientstat" id="occultblood_patientstat" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_physician" id="occultblood_physician">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                    </table>
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_procedure" id="occultblood_procedure">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_code" id="occultblood_code">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="occultblood_spectype" id="occultblood_spectype">
                                <?php
                                        $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_serialno" id="occultblood_serialno">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_extractdate" id="occultblood_extractdate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                            <td align=left>
                    
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_extracttime" id="occultblood_extracttime" readonly>

                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>               
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="occultblood_extractby" id="occultblood_extractby" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="occultblood_location" id="occultblood_location">
                                <?php
                                        $iun = $o->dbquery("select id,location from lab_locations;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                    </table>
                </td>
                <td width=1%>&nbsp;</td>
                <td width=64% valign=top >                  
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="occultblood_date" id="occultblood_date" value="<?php echo date('m/d/Y'); ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Color&nbsp;:</td>
                        <td align=left>
                            <select name="occultblood_color" id="occultblood_color" class="gridInput" style="width:100%;">
                                <option value="Brown" <?php if($b['occultblood_color'] == 'Brown') { echo "selected"; } ?>>Brown</option>
                                <option value="Yellowish Brown" <?php if($b['occultblood_color'] == 'Yellowish Brown') { echo "selected"; } ?>>Yellowish Brown</option>
                                <option value="Yellow" <?php if($b['occultblood_color'] == 'Yellow') { echo "selected"; } ?>>Yellow</option>
                                <option value="Reddish Brown" <?php if($b['occultblood_color'] == 'Reddish Brown') { echo "selected"; } ?>>Reddish Brown</option>
                                <option value="Light Brown" <?php if($b['occultblood_color'] == 'Light Brown') { echo "selected"; } ?>>Light Brown</option>
                                <option value="Greenish Brown" <?php if($b['occultblood_color'] == 'Greenish Brown') { echo "selected"; } ?>>Greenish Brown</option>
                                <option value="Dark Brown" <?php if($b['occultblood_color'] == 'Dark Brown') { echo "selected"; } ?>>Dark Brown</option>
                                <option value="Brown Black" <?php if($b['occultblood_color'] == 'Brown Black') { echo "selected"; } ?>>Brown Black</option>
                                <option value="Green" <?php if($b['occultblood_color'] == 'Green') { echo "selected"; } ?>>Green</option>
                                <option value="Black" <?php if($b['occultblood_color'] == 'Black') { echo "selected"; } ?>>Black</option>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr> 
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Consistency&nbsp;:</td>
                        <td align=left>
                            <select name="occultblood_consistency" id="occultblood_consistency" class="gridInput" style="width:100%;">
                                <option value="Formed" <?php if($b['occultblood_consistency'] == 'Formed') { echo "selected"; } ?>>Formed</option>
                                <option value="Semi Formed" <?php if($b['appearance'] == 'Semi Formed') { echo "selected"; } ?>>Semi Formed</option>
                                <option value="Soft" <?php if($b['occultblood_consistency'] == 'Soft') { echo "selected"; } ?>>Soft</option>
                                <option value="Watery" <?php if($b['occultblood_consistency'] == 'Watery') { echo "selected"; } ?>>Watery</option>
                                <option value="Mucoid" <?php if($b['occultblood_consistency'] == 'Mucoid') { echo "selected"; } ?>>Mucoid</option>
                                <option value="Mushy" <?php if($b['occultblood_consistency'] == 'Mushy') { echo "selected"; } ?>>Mushy</option>
                                <option value="Loose" <?php if($b['occultblood_consistency'] == 'Loose') { echo "selected"; } ?>>Loose</option>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Occult Blood Result&nbsp;:</td>
                            <td align=left>                 
                                <select name="occultbloodres" id="occultbloodres" class="gridInput" style="width:100%;">
                                    <?php $occultblood_res = $o->getArray("select * from lab_occultblood where so_no = '$a[myso]' and serialno = '$a[serialno]' and branch = '$_SESSION[branchid]';"); ?>
                                        <option value="POSITIVE" <?php if($occultblood_res['result'] == 'POSITIVE') { echo "selected"; } ?>>POSITIVE</option>
                                        <option value="NEGATIVE" <?php if($occultblood_res['result'] == 'NEGATIVE') { echo "selected"; } ?>>NEGATIVE</option>
                                </select>
                            </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                        <td align=left>
                            <textarea name="occultblood_remarks" id="occultblood_remarks" style="width:100%;" rows=3></textarea>
                        </td>
                    </tr>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="pregnancyResult" style="display: none;">
    <form name="frmPregnancyResult" id="frmPregnancyResult">  
    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="pt_sono" id="pt_sono">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="pt_sodate" id="pt_sodate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_pid" id="pt_pid">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_pname" id="pt_pname">
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_gender" id="pt_gender">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_birthdate" id="pt_birthdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_age" id="pt_age">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_patientstat" id="pt_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_physician" id="pt_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_procedure" id="pt_procedure">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_code" id="pt_code">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="pt_spectype" id="pt_spectype">
                        <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_serialno" id="pt_serialno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_extractdate" id="pt_extractdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <input type="text" class="gridInput" style="width:100%;" name="pt_extracttime" id="pt_extracttime" readonly>

                </td>				
            </tr>
            <tr><td height=3></td></tr>               
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="pt_extractby" id="pt_extractby" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="pt_location" id="pt_location">
                        <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="pt_date" id="pt_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="pt_result" id="pt_result">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="pt_remarks" id="pt_remarks" style="width:100%;" rows=3></textarea>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="lipidResult" style="display: none;">
    <form name="frmLipidResult" id="frmLipidResult">  
    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="lipid_sono" id="lipid_sono">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="lipid_sodate" id="lipid_sodate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_pid" id="lipid_pid">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_pname" id="lipid_pname">
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_gender" id="lipid_gender">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_birthdate" id="lipid_birthdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_age" id="lipid_age">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_patientstat" id="lipid_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_physician" id="lipid_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_procedure" id="lipid_procedure">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_code" id="lipid_code">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="lipid_spectype" id="lipid_spectype">
                        <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_serialno" id="lipid_serialno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_extractdate" id="lipid_extractdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_extracttime" id="lipid_extracttime" readonly>

                </td>				
            </tr>
            <tr><td height=3></td></tr>               
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="lipid_extractby" id="lipid_extractby" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="lipid_location" id="lipid_location">
                        <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="lipid_date" id="lipid_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Total Cholesterol (mg/dL)&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="lipid_cholesterol" id="lipid_cholesterol">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Triglycerides (mg/dL)&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="lipid_triglycerides" id="lipid_triglycerides">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">HDL (mg/dL)&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="lipid_hdl" id="lipid_hdl">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">LDL (mg/dL)&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="lipid_ldl" id="lipid_ldl">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">VLDL (mg/dL)&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="lipid_vldl" id="lipid_vldl">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="lipid_remarks" id="lipid_remarks" style="width:100%;" rows=3></textarea>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="ogttResult" style="display: none;">
    <form name="frmOgttResult" id="frmOgttResult">  
    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="ogtt_sono" id="ogtt_sono">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="ogtt_sodate" id="ogtt_sodate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_pid" id="ogtt_pid">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_pname" id="ogtt_pname">
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_gender" id="ogtt_gender">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_birthdate" id="ogtt_birthdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_age" id="ogtt_age">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_patientstat" id="ogtt_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_physician" id="ogtt_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_procedure" id="ogtt_procedure">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_code" id="ogtt_code">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="ogtt_spectype" id="ogtt_spectype">
                    <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_serialno" id="ogtt_serialno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_extractdate" id="ogtt_extractdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_extracttime" id="ogtt_extracttime" readonly>

                </td>				
            </tr>
            <tr><td height=3></td></tr>               
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="ogtt_extractby" id="ogtt_extractby" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="ogtt_location" id="ogtt_location">
                    <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="ogtt_date" id="ogtt_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Fasting (mg/dL)&nbsp;:</td>
                <td align=left><input class="gridInput" style="width:100%;" type=text name="ogtt_fasting" id="ogtt_fasting"></td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Urine Glucose&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="ogtt_uglucose" id="ogtt_uglucose">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">1<sup>st</sup>&nbsp;Hour (mg/dL)&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="ogttFirstHr" id="ogttFirstHr">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">1<sup>st</sup>&nbsp;Hour (Urine Glucose)&nbsp;:</td>
                <td align=left>                 
                    <input class="gridInput" style="width:100%;" type=text name="first_hr_uglucose" id="first_hr_uglucose">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">2<sup>nd</sup>&nbsp;Hour (mg/dL)&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="second_hr" id="second_hr">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">2<sup>nd</sup>&nbsp;Hour (Urine Glucose)&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="second_hr_uglucose" id="second_hr_uglucose">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
    </form>
</div>

<div id="bloodtypeResult" style="display: none;">
    <form name="frmBloodType" id="frmBloodType">  
    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="btype_sono" id="btype_sono">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="btype_sodate" id="btype_sodate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_pid" id="btype_pid">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_pname" id="btype_pname">
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_gender" id="btype_gender">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_birthdate" id="btype_birthdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_age" id="btype_age">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_patientstat" id="btype_patientstat">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_physician" id="btype_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_procedure" id="btype_procedure">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_code" id="btype_code">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="btype_spectype" id="btype_spectype">
                        <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_serialno" id="btype_serialno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_extractdate" id="btype_extractdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <input type="text" class="gridInput" style="width:100%;" name="btype_extracttime" id="btype_extracttime" readonly>

                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="btype_extractby" id="btype_extractby" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Collection Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="btype_location" id="btype_location">
                        <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="btype_date" id="btype_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result&nbsp;:</td>
                <td align=left>
                    <select name="btype_result" id="btype_result" class="gridInput" style="width:100%">
                        <?php
                            $btQuery = $o->dbquery("select bloodType from options_bloodtypes;");
                            while($btRow = $btQuery->fetch_array()) {
                                echo "<option value='$btRow[0]'>$btRow[0]</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Rh&nbsp;:</td>
                <td align=left>
                    <select name="btype_rh" id="btype_rh" class="gridInput" style="width:100%">
                      <option value='Positive'>Positive</option>
                      <option value='Negative'>Negative</option>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Performed By&nbsp;:</td>
                <td align=left>
                    <select name="btype_result_by" id="btype_result_by" class="gridInput" style="width:100%">
                        <option value="">- Not Applicable -</option>
                        <?php
                            $pbyQuery = $o->dbquery("select emp_id, fullname from user_info where role like '%MEDICAL TECH%';");
                            while($pbyRow = $pbyQuery->fetch_array()) {
                                echo "<option value = '$pbyRow[0]'>$pbyRow[1]</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="btype_remarks" id="btype_remarks" style="width:100%;" rows=3></textarea>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="havResult" style="display: none;">
    <form name="frmHavResult" id="frmHavResult">  
    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="hav_sono" id="hav_sono">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="hav_sodate" id="hav_sodate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_pid" id="hav_pid">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_pname" id="hav_pname">
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_gender" id="hav_gender">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_birthdate" id="hav_birthdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_age" id="hav_age">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_patientstat" id="hav_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_physician" id="hav_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_procedure" id="hav_procedure">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_code" id="hav_code">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="hav_spectype" id="hav_spectype">
                        <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_serialno" id="hav_serialno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_extractdate" id="hav_extractdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <input type="text" class="gridInput" style="width:100%;" name="hav_extracttime" id="hav_extracttime" readonly>

                </td>				
            </tr>
            <tr><td height=3></td></tr>               
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="hav_extractby" id="hav_extractby" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="hav_location" id="hav_location">
                        <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="hav_date" id="hav_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="hav_result" id="hav_result">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="hav_remarks" id="hav_remarks" style="width:100%;" rows=3></textarea>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="dengueResult" style="display: none;">
    <form name="frmDengueResult" id="frmDengueResult">  
    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="dengue_sono" id="dengue_sono">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="dengue_sodate" id="dengue_sodate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_pid" id="dengue_pid">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_pname" id="dengue_pname">
                </td>				
            </tr>
            <tr><td height=3></td></tr>

            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_gender" id="dengue_gender">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_birthdate" id="dengue_birthdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_age" id="dengue_age">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_patientstat" id="dengue_patientstat" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_physician" id="dengue_physician">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
        </table>
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_procedure" id="dengue_procedure">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_code" id="dengue_code">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="dengue_spectype" id="dengue_spectype">
                    <?php
                            $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_serialno" id="dengue_serialno">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_extractdate" id="dengue_extractdate">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                <td align=left>
        
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_extracttime" id="dengue_extracttime" readonly>

                </td>				
            </tr>
            <tr><td height=3></td></tr>               
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                <td align=left>
                    <input type="text" class="gridInput" style="width:100%;" name="dengue_extractby" id="dengue_extractby" readonly>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                <td align=left>
                    <select class="gridInput" style="width:100%;" name="dengue_location" id="dengue_location">
                    <?php
                            $iun = $o->dbquery("select id,location from lab_locations;");
                            while(list($aa,$ab) = $iun->fetch_array()) {
                                echo "<option value='$aa'>$ab</option>";
                            }
                        ?>
                    </select>
                </td>				
            </tr>
        </table>                  
        <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
        <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
            <tr>
                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                <td align=left>
                    <input class="gridInput" style="width:100%;" type=text name="dengue_date" id="dengue_date" value="<?php echo date('m/d/Y'); ?>">
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Dengue&nbsp;NS1&nbsp;AG&nbsp;:</td>
                <td align=left>
                <select name="dengue_ag" id="dengue_ag" class="gridInput" style="width:100%;">
                    <?php $dengue_ag = $o->getArray("select * from lab_dengue where so_no = '$a[myso]' and serialno = '$a[serialno]' and branch = '$_SESSION[branchid]';"); ?>
                        <option value="POSITIVE" <?php if($dengue_ag['dengue_ag'] == 'POSITIVE') { echo "selected"; } ?>>POSITIVE</option>
                        <option value="NEGATIVE" <?php if($dengue_ag['dengue_ag'] == 'NEGATIVE') { echo "selected"; } ?>>NEGATIVE</option>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr> 
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Dengue&nbsp;IgG&nbsp;:</td>
                <td align=left>
                <select name="dengue_igg" id="dengue_igg" class="gridInput" style="width:100%;">
                    <?php $dengue_igg = $o->getArray("select * from lab_dengue where so_no = '$a[myso]' and serialno = '$a[serialno]' and branch = '$_SESSION[branchid]';"); ?>
                        <option value="POSITIVE" <?php if($dengue_igg['dengue_igg'] == 'POSITIVE') { echo "selected"; } ?>>POSITIVE</option>
                        <option value="NEGATIVE" <?php if($dengue_igg['dengue_igg'] == 'NEGATIVE') { echo "selected"; } ?>>NEGATIVE</option>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Dengue&nbsp;IgM&nbsp;:</td>
                <td align=left>                 
                    <select name="dengue_igm" id="dengue_igm" class="gridInput" style="width:100%;">
                    <?php $dengue_igm = $o->getArray("select * from lab_dengue where so_no = '$a[myso]' and serialno = '$a[serialno]' and branch = '$_SESSION[branchid]';"); ?>
                        <option value="POSITIVE" <?php if($dengue_igm['dengue_igm'] == 'POSITIVE') { echo "selected"; } ?>>POSITIVE</option>
                        <option value="NEGATIVE" <?php if($dengue_igm['dengue_igm'] == 'NEGATIVE') { echo "selected"; } ?>>NEGATIVE</option>
                    </select>
                </td>				
            </tr>
            <tr><td height=3></td></tr>
            <tr>
                <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                <td align=left>
                    <textarea name="dengue_remarks" id="dengue_remarks" style="width:100%;" rows=3></textarea>
                </td>				
            </tr>
        </table>
    </form>
</div>

<div id="vitals" style="display: none;">
	<form name="frmVitals" id="frmVitals">
		<table width=100% cellpadding=0 cellspacing=0 style="boder-collpase: collapse;">
            <tr><td colspan=8 align=center><img src="images/doc-header.jpg" width=85% height=85% align=absmiddle /></td></tr>
			<tr>
				<td colspan=4 align=center class=bebottom>
					<input type="radio" id="pe_type" name="pe_type" value="APE">&nbsp;<span class="spadix-l">Annual Physical Examination</span>
				</td>
				<td colspan=4 align=center class=bebottom>
					<input type="radio" id="type" name="type" value="PE">&nbsp;<span class="spadix-l">Pre-Employment Requirements</span>
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr>
				<td width=8% class="bebottom" >Last Name :</td>
				<td width=17% class="bebottom">	
					<input type="text" name="pe_lname" id="pe_lname" style="border: none; font-size: 11px; font-weight: bold;">
				</td>
				<td width=8% class="bebottom">First Name :</td>
				<td width=17% class="bebottom">	
					<input type="text" name="pe_fname" id="pe_fname" style="border: none; font-size: 11px; font-weight: bold;" >
				</td>
				<td width=8% class="bebottom">Middle Name :</td>
				<td width=17% class="bebottom">	
					<input type="text" name="pe_mname" id="pe_mname" style="border: none; font-size: 11px; font-weight: bold;">
				</td>
				<td width=8% class="bebottom">Date :</td>
				<td width=17% class="bebottom">	
					<input type="text" name="pe_date" id="pe_date" style="border: none; font-size: 11px; font-weight: bold;" value="<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
            <tr>
				<td class="bebottom" >Address :</td>
				<td class="bebottom">	
					<input type="text" name="pe_address" id="pe_address" style="border: none; font-size: 11px;width: 98%; font-weight: bold;">
				</td>
				<td class="bebottom">Age :</td>
				<td class="bebottom">	
					<input type="text" name="pe_age" id="pe_age" style="border: none; font-size: 11px;width: 98%; font-weight: bold;" >
				</td>
				<td class="bebottom">Civil Status :</td>
				<td class="bebottom">	
					<input type="text" name="pe_cstatus" id="pe_cstatus" style="border: none; font-size: 11px; width: 98%; font-weight: bold;">
				</td>
				<td class="bebottom">Gender :</td>
				<td class="bebottom">	
					<input type="text" name="pe_gender" id="pe_gender" style="border: none; font-size: 11px; width: 98%; font-weight: bold;">
				</td>
			</tr>
            <tr>
				<td class="bebottom" >Place of Birth :</td>
				<td class="bebottom">	
					<input type="text" name="pe_pob" id="pe_pob" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" >
				</td>
				<td class="bebottom">Date of Birth :</td>
				<td class="bebottom">	
					<input type="text" name="pe_dob" id="pe_dob" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" >
				</td>
				<td class="bebottom">Insurance :</td>
				<td class="bebottom" colspan=3>	
					<input type="text" name="pe_insurance" id="pe_insurance" style="border: none; font-size: 11px; width: 98%; font-weight: bold;">
				</td>
			</tr>
            <tr>
				<td class="bebottom" >Occupation :</td>
				<td class="bebottom">	
					<input type="text" name="pe_occ" id="pe_occ" style="border: none; font-size: 11px; width: 98%;" >
				</td>
				<td class="bebottom">Company :</td>
				<td class="bebottom">	
					<input type="text" name="pe_comp" id="pe_comp" style="border: none; font-size: 11px; width: 98%;" >
				</td>
				<td class="bebottom">Tel/Mobile # :</td>
				<td class="bebottom" colspan=3>	
					<input type="text" name="pe_contact" id="pe_contact" style="border: none; font-size: 11px; width: 98%;">
				</td>
			</tr>
		</table>
        <table width=100% cellpadding=5><tr><td align=center><span style="font-size: 10pt; font-weight: bold;">PHYSICAL EXAMINATION</span></td></tr></table>
        <table width=100% cellspacing=0 cellpadding=3>
            <tr>
                <td class="spandix-l" align=left colspan=3>
                    Temp: <input type="text" name="pe_temp" id="pe_temp" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" ><sup>0</sup>C&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    PR:  <input type="text" name="pe_pr" id="pe_pr" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >bpm&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    RR:  <input type="text" name="pe_rr" id="pe_rr" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >bpm&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    BP:  <input type="text" name="pe_bp" id="pe_bp" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >mm/HG&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Ht:  <input type="text" name="pe_ht" id="pe_ht" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >cm&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                    Wt:  <input type="text" name="pe_wt" id="pe_wt" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >kgs    
               </td>
            </tr>
            <tr>
                <td class="spandix-l" align=left>
                    Visual Acuity: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Right Eye:  <input type="text" name="pe_lefteye" id="pe_lefteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Left Eye:  <input type="text" name="pe_righteye" id="pe_righteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    BMI:  <input type="text" name="pe_bmi" id="pe_bmi" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               </td>
               <td class="spandix-l"d><input type=radio name="pe_bmitype" id="pe_bmitype" value="Underweight">&nbsp;Underweight</td>
               <td class="spandix-l"><input type=radio name="pe_bmitype" id="pe_bmitype" value="Overweight">&nbsp;Overweight</td>
            </tr> 
            <tr>
                <td class="spandix-l" align=center>
                    
                </td>
               <td class="spandix-l" ><input type=radio name="pe_bmitype" id="pe_bmitype" value="Normal">&nbsp;Normal Weight</td>
               <td class="spandix-l"><input type=radio name="pe_bmitype" id="pe_bmitype" value="Obese">&nbsp;Obese</td>
            </tr>                    
        </table>
        <table width=100% cellpadding=5><tr><td align=center><span style="font-size: 10pt; font-weight: bold;">MEDICAL HISTORY</span></td></tr></table>
        <table width=100% cellpadding=3>
            <tr>
                <td width=12% class="spandix-l" valign=top>Past Medical History :</td>
                <td align=right>
                    <table width=100% cellpadding=2 cellspacing=0>
                        <tr>
                            <td width=33% valign=top>
                                <?php
                                    $medh1 = $o->dbquery("select id, history from options_medicalhistory order by id limit 0,10");
                                    while($medh1_row = $medh1->fetch_array()) {
                                        echo '<input type="checkbox" name="medhistory[]" id="medhistory[]" value="'.$medh1_row[0].'">&nbsp;&nbsp;<span class="spandix-l">'.$medh1_row[1].'</span><br/>';
                                    }
                                ?>
                            </td>
                                      
                            <td width=33% valign=top>
                                 <?php
                                    $medh2 = $o->dbquery("select id, history from options_medicalhistory order by id limit 10,10");
                                    while($medh2_row = $medh2->fetch_array()) {
                                        echo '<input type="checkbox" name="medhistory[]" id="medhistory[]" value="'.$medh2_row[0].'">&nbsp;&nbsp;<span class="spandix-l">'.$medh2_row[1].'</span><br/>';
                                    }
                                ?>      
                            </td>

                            <td width=33% valign=top>
                                <?php
                                    $medh3 = $o->dbquery("select id, history from options_medicalhistory order by id limit 20,10");
                                    while($medh3_row = $medh3->fetch_array()) {
                                        echo '<input type="checkbox" name="medhistory[]" id="medhistory[]" value="'.$medh3_row[0].'">&nbsp;&nbsp;<span class="spandix-l">'.$medh3_row[1].'</span><br/>';
                                    }
                                ?>       
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
            <tr>
                <td class="spandix-l">Family History :</td>
                <td align=right><input type="text" name="pe_famhistory" id="pe_famhistory" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 98%;font-weight: bold;" ></td>
            </tr>
            <tr>
                <td class="spandix-l">Previous Hospitalization :</td>
                <td align=right><input type="text" name="pe_hospitalization" id="pe_hospitalization" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 98%;font-weight: bold;" ></td>
            </tr>
            <tr>
                <td colspan=2 class="spandix-l">
                    Menstrual History: <input type="text" name="pe_menshistory" id="pe_menshistory" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >y.o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Parity:  <input type="text" name="pe_parity" id="pe_parity" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    LMP:  <input type="text" name="pe_lmp" id="pe_lmp" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Contraceptive Use:  <input type="text" name="pe_contra" id="pe_contra" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 160px;font-weight: bold;" >      
                </td>
            </tr>
        </table>
        <table width=100% cellpadding=5 cellspacing=0 style="border-collapse: collapse; font-size: 11px;">
            <tr>
                <td width=15% align=center style="border: 1px solid black; font-weight: bold;">Review of Systems</td>
                <td width=8% align=center style="border: 1px solid black; font-weight: bold;">Normal</td>
                <td width=27% align=center style="border: 1px solid black; font-weight: bold;">Findings</td>
                <td width=15% align=center style="border: 1px solid black; font-weight: bold;">Review of Systems</td>
                <td width=8% align=center style="border: 1px solid black; font-weight: bold;">Normal</td>
                <td width=27% align=center style="border: 1px solid black; font-weight: bold;">Findings</td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Head & Scalp</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_hs_normal" id="pe_hs_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_hs_findings" id="pe_hs_findings"></td>
                <td style="border: 1px solid black;">Lungs</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_lungs_normal" id="pe_lungs_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_lungs_findings" id="pe_lungs_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Eyes & Ears</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_ee_normal" id="pe_ee_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_ee_findings" id="pe_ee_findings"></td>
                <td style="border: 1px solid black;">Heart</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_heart_normal" id="pe_heart_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_heart_findings" id="pe_heart_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Skin/Allergy</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_sa_normal" id="pe_sa_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_sa_findings" id="pe_sa_findings"></td>
                <td style="border: 1px solid black;">Abdomen</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_abdomen_normal" id="pe_abdomen_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_abdomen_findings" id="pe_abdomen_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Nose/Sinuses</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_nose_normal" id="pe_nose_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_nose_findings" id="pe_nose_findings"></td>
                <td style="border: 1px solid black;">Genitals</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_genitals_normal" id="pe_genitals_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_genitals_findings" id="pe_genitals_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Mouth/Teeth/Tongue</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_mouth_normal" id="pe_mouth_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_mouth_findings" id="pe_mouth_findings"></td>
                <td style="border: 1px solid black;">Extremities</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_extr_normal" id="pe_extr_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_extr_findings" id="pe_extr_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Neck/Nodes</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_neck_normal" id="pe_neck_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_neck_findings" id="pe_neck_findings"></td>
                <td style="border: 1px solid black;">Reflexes</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_ref_normal" id="pe_ref_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_ref_findings" id="pe_ref_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Check/Breast</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_check_normal" id="pe_check_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_check_findings" id="pe_check_findings"></td>
                <td style="border: 1px solid black;">BPE</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_bpe_normal" id="pe_bpe_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_bpe_findings" id="pe_bpe_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;">Rectal</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_rect_normal" id="pe_rect_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_rect_findings" id="pe_rect_findings"></td>
            </tr>
        </table>
        <table width=100% cellpadding=5 cellspacing=0 style="border-collapse: collapse; font-size: 11px; margin-top: 10px;">
            <tr>
                <td width=15% align=center style="border: 1px solid black; font-weight: bold;">Laboratory</td>
                <td width=8% align=center style="border: 1px solid black; font-weight: bold;">Normal</td>
                <td width=27% align=center style="border: 1px solid black; font-weight: bold;">Findings</td>
                <td width=15% align=center style="border: 1px solid black; font-weight: bold;">Review of Systems</td>
                <td width=8% align=center style="border: 1px solid black; font-weight: bold;">Normal</td>
                <td width=27% align=center style="border: 1px solid black; font-weight: bold;">Findings</td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Chest X-Ray</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_chest_normal" id="pe_chest_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_chest_findings" id="pe_chest_findings"></td>
                <td style="border: 1px solid black;">ECG</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_ecg_normal" id="pe_ecg_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_ecg_findings" id="pe_ecg_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">CBC</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_check_normal" id="pe_cbc_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_cbc_findings" id="pe_cbc_findings"></td>
                <td style="border: 1px solid black;">OTHER PROCEDURES:</td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Urinalysis</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_ua_normal" id="pe_ua_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_ua_findings" id="pe_ua_findings"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others1" id="pe_others1"></td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_others1_normal" id="pe_others1_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others1_findings" id="pe_others1_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Fecalysis</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_se_normal" id="pe_se_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_se_findings" id="pe_se_findings"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others2" id="pe_others2"></td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_others2_normal" id="pe_others2_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others2_findings" id="pe_others2_findings"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Drug Test</td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_dt_normal" id="pe_dt_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_dt_findings" id="pe_dt_findings"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others3" id="pe_others3"></td>
                <td style="border: 1px solid black;" align=center><input type="checkbox" name="pe_others3_normal" id="pe_others3_normal" value="Y"></td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others3_findings" id="pe_others3_findings"></td>
            </tr>
        </table>
        <table width=100% cellpadding=5 cellspacing=0>
            <tr><td colspan=2 class="spandix-l">I Hereby Certify that I have examined and found the employee to be <select class=gridInput name="pe_fit" id="pe_fit"><option value="FIT">FIT</option><option value="UNFIT">UNFIT</option></select> for employment.<br/><b>CLASSIFICATION:</b></td></tr>                
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l"><input type="radio" name="pe_class" id="pe_class" value="A">&nbsp;&nbsp;CLASS A</td>
                <td width=80% class="spandix-l">
                    Physically fit for all types of work
                   </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l" valign=top><input type="radio" name="pe_class" id="pe_class" value="B">&nbsp;&nbsp;CLASS B</td>
                <td width=80% class="spandix-l">Physically fit for all types of work
                <br/>
                    Has Minor ailment/defect. Easily curable or offers no handicap to applied.
                    <br/>
                    <input type="radio" name="pe_class_b" id="pe_class_b" value="1">&nbsp;&nbsp;Needs Treatment Correction : &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="pe_class_b_remarks1" id="pe_class_b_remarks1" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width:370px;" >
                    <br/>
                    <input type="radio" name="pe_class_b" id="pe_class_b" value="2">&nbsp;&nbsp;Treatment Optional For : &nbsp;&nbsp;<input type="text" name="pe_class_b_remarks_2" id="pe_class_b_remarks_2" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 400px;" >
                
                </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l" valign=top><input type="radio" name="pe_class" id="pe_class" value="C">&nbsp;&nbsp;CLASS C</td>
                <td width=80% class="spandix-l">Physically fit for less strenous type of work. Has minor ailments/defects.
                <br/>
                    Easily curable or offers no handicap to job applied.
                    <br/>
                    <input type="radio" name="pe_class_c" id="pe_class_c" value="1">&nbsp;&nbsp;Needs Treatment Correction : &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="pe_class_c_remarks1" id="pe_class_c_remarks1" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width:370px;" >
                    <br/>
                    <input type="radio" name="pe_class_c" id="pe_class_c" value="2">&nbsp;&nbsp;Treatment Optional For : &nbsp;&nbsp;<input type="text" name="pe_class_c_remarks_2" id="pe_class_c_remarks_2" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 400px;" >
                
                </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l"><input type="radio" name="pe_class" id="pe_class" value="D">&nbsp;&nbsp;CLASS D</td>
                <td width=80% class="spandix-l">
                    Employment at the risk and discretion of the management
                </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l"><input type="radio" name="pe_class" id="pe_class" value="E">&nbsp;&nbsp;CLASS E</td>
                <td width=80% class="spandix-l">
                    Unfit for Employment
                </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l"><input type="radio" name="pe_class" id="pe_class" value="PENDING">&nbsp;&nbsp;PENDING</td>
                <td width=80% class="spandix-l">
                    For further evaluation of: &nbsp;&nbsp;&nbsp;<input type="text" name="pe_eval_remarks" id="pe_eval_remarks" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 380px;" >
                </td>
            </tr>
            <tr><td colspan=2 class="spandix-l">Remarks: <input type="text" name="pe_remarks" id="pe_remarks" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 70%;" ></td></tr>
        </table>
	</form>
</div>

<div id="doctorDetails" name="newFile" style="display: none;">
    <form enctype="multipart/form-data" name="frmNewDoctorFile" id="frmNewDoctorFile" method="POST">
        <input type="hidden" name="mod" id="mod" value="newDoctorProfile">
        <table width="100%" cellpadding=2 cellspacing=0>
            <tr>
                <td width=30% class="spandix-l">Full Name: </td>
                <td><input type="text" name="doctorFullname" id="doctorFullname" style="width: 80%;"></td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Profession Prefix: </td>
                <td><input type="text" name="doctorPrefix" id="doctorPrefix" style="width: 80%;"></td>
            </tr>
            <tr>
                <td width=30% class="spandix-l" style="vertical-align: top;">Specialization: </td>
                <td><textarea name="doctorSpecialization" id="doctorSpecialization" style="width: 80%;" rows=2></textarea></td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">License / PTR No.: </td>
                <td><input type="text" name="doctorLicenseNo" id="doctorLicenseNo" style="width: 80%;"></td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Contact Nos.: </td>
                <td><input type="text" name="doctorContact" id="doctorContact" style="width: 80%;"></td>
            </tr>
            <tr>
                <td width=30% class="spandix-l" valign=top>Clinic Schedules: </td>
                <td>
                    <table width=100% cellpadding=2 cellspacing=0>
                        <tr>
                            <td width=30% class="spandix-l"><input type="checkbox" name="doctorMon" id="doctorMon" value="Y">&nbsp;Monday
                            <td width=70%><input type="text" class="gridInput" style="width:70%;" name="doctorMonSchedule" id="doctorMonSchedule" placeholder="hh:mm - hh:mm"></td>
                        </tr>
                        <tr>
                            <td class="spandix-l"><input type="checkbox" name="doctorTue" id="doctorTue" value="Y">&nbsp;Tuesday
                            <td><input type="text" class="gridInput" style="width:70%;" name="doctorTueSchedule" id="doctorTueSchedule" placeholder="hh:mm - hh:mm"></td>
                        </tr>
                        <tr>
                            <td class="spandix-l"><input type="checkbox" name="doctorWed" id="doctorWed" value="Y">&nbsp;Wednesday
                            <td><input type="text" class="gridInput" style="width:70%;" name="doctorWedSchedule" id="doctorWedSchedule" placeholder="hh:mm - hh:mm"></td>
                        </tr>
                        <tr>
                            <td class="spandix-l"><input type="checkbox" name="doctorThu" id="doctorThu" value="Y">&nbsp;Thursday
                            <td><input type="text" class="gridInput" style="width:70%;" name="doctorThuSchedule" id="doctorThuSchedule" placeholder="hh:mm - hh:mm"></td>
                        </tr>
                        <tr>
                            <td class="spandix-l"><input type="checkbox" name="doctorFri" id="doctorFri" value="Y">&nbsp;Friday
                            <td><input type="text" class="gridInput" style="width:70%;" name="doctorFriSchedule" id="doctorFriSchedule" placeholder="hh:mm - hh:mm"></td>
                        </tr>
                        <tr>
                            <td class="spandix-l"><input type="checkbox" name="doctorSat" id="doctorSat" value="Y">&nbsp;Saturday
                            <td><input type="text" class="gridInput" style="width:70%;" name="doctorSatSchedule" id="doctorSatSchedule" placeholder="hh:mm - hh:mm"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Signature File: </td>
                <td><input type=file name="doctorSignature" id="doctorSignature" style="width: 80%;"></td>
            </tr>
            <tr>
                <td width=30% class="spandix-l"></td>
                <td><span id="doctorSignatureImage" name="doctorSignatureImage" style="height: 200px;">&nbsp;</span></td>
            </tr>
        </table>
    </form>
</div>

<div id="appointment" style="display: none;">
    <form name="patientAppointment" id="patientAppointment">
        <input type="hidden" name="appRecordId" id="appRecordId">
        <table width=100% cellspacing=4 class="td_content">
            <tr>
                <td width=30% class="spandix-l">Patient ID :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="inputSearch2" style="width: 56%; padding-left: 22px;" id="appPatientId" name="appPatientId">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Patient name :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 100%;" id="appPatientName" name="appPatientName">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Address :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 100%;" id="appPatientAddress" name="appPatientAddress">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Gender :</td>
                <td width=70% class="spandix-l">
                    <select class="gridInput" style="width: 56%; font-size: 11px;" name="appGender" id="appGender">
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Birthdate :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 100%;" id="appBirthdate" name="appBirthdate">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Contact No. :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 100%;" id="appContactNo" name="appContactNo">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Guardian (if Minor) :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 100%;" id="appGuardian" name="appGuardian">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">SO Ref # :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 56%;" id="appSOno" name="appSOno">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">SO Ref Date :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 56%;" id="appSOdate" name="appSOdate">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Appointment Date :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 56%;" id="appDate" name="appDate">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Time :</td>
                <td width=70% class="spandix-l">
                    <select class="gridInput" style="width: 100%; font-size: 11px;" name="appSchedule" id="appSchedule">
                        <?php
                            $slotQuery = $o->dbquery("SELECT id, slot FROM options_timeslot ORDER BY id");
                            while($slotRow = $slotQuery->fetch_array()) {
                                echo "<option value='$slotRow[0]'>$slotRow[1]</option>";
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Consultation Type :</td>
                <td width=70% class="spandix-l">
                    <select class="gridInput" style="width: 100%; font-size: 11px;" name="appConsultType" id="appConsultType" onchange="javascript: queryRequestCategory(this.value,'appCategory','1');">
                        <option value="">- Select Type -</option>
                        <option value="1">Medical</option>
                        <option value="2">Dental</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Category :</td>
                <td width=70% class="spandix-l">
                    <select class="gridInput" style="width: 100%; font-size: 11px;" name="appCategory" id="appCategory" onchange="queryRequestCategory(this.value,'appConsultType','2');">
                        <option value="">- Select Category -</option>
                        <?php
                            $subCatQuery = $o->dbquery("select record_id, request_type from options_requesttype order by request_category, request_type;");
                            while($subCatRow = $subCatQuery->fetch_array()) {

                                echo "<option value='$subCatRow[0]'>$subCatRow[1]</option>";
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l">Preferred Doctor :</td>
                <td width=70% class="spandix-l">
                    <input type="text" class="gridInput" style="width: 100%;" id="appDoctor" name="appDoctor">
                </td>
            </tr>
            <tr>
                <td width=30% class="spandix-l" valign=top>Patient's Remarks :</td>
                <td width=70% class="spandix-l">
                    <textarea style="width: 100%;" id="appRemarks" name="appRemarks" rows=3></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>

<div id="xrayLogBook" name="xrayLogBook" style="display: none;">
	<table width=100% cellpadding=2 cellspacing=0>
		<tr>
			<td width=35% class="spandix-l">Result Date Coverage :</td>
			<td><input type="text" id="xraylog_dtf" name="xraylog_dtf" class="gridInput"  style="width: 80%;" value="<?php echo date('m/d/Y'); ?>"></td>
		</tr>
		<tr>
			<td width=35% class="spandix-l"></td>
			<td><input type="text" id="xraylog_dt2" name="xraylog_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>"></td>
		</tr>
		<tr>
			<td width=35% class="spandix-l">Consultant :</td>
			<td>
				<select name="xraylog_consultant" id="xraylog_consultant" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value=''>All Consultants</option>
					<?php
						$query = $o->dbquery("select id, fullname from options_doctors order by fullname;");
						while($d = $query->fetch_array()) {
							echo "<option value='$d[0]'>$d[1]</option>";
						}
					?>			
				</select>
			</td>
		</tr>
        <tr>
			<td width=35% class="spandix-l">Result Type :</td>
			<td>
				<select name="xraylog_type" id="xraylog_type" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value=''>All</option>
					<option value='1'>Normal</option>
					<option value='2'>With Findings</option>
				</select>
				</select>
			</td>
		</tr>
		<tr>
			<td width=35% class="spandix-l">Encoder :</td>
			<td>
				<select name="xraylog_encoder" id="xraylog_encoder" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value=''>All Encoders</option>
					<?php
						$equery = $o->dbquery("SELECT emp_id, fullname FROM user_info WHERE role LIKE '%encod%' ORDER BY fullname;");
						while($e = $equery->fetch_array()) {
							echo "<option value='$e[0]'>$e[1]</option>";
						}
					?>			
				</select>
			</td>
		</tr>
		<tr>
			<td width=35% class="spandix-l">Sort By :</td>
			<td>
				<select name="xraylog_sort" id="xraylog_sort" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value='1'>Patient Name</option>
					<option value='2'>By X-Ray No. (Ascending)</option>
					<option value='3'>By Company Name</option>
				</select>
				</select>
			</td>
		</tr>
	</table>
</div>

<div id="antigenResult" style="display: none;">
    <form name="frmAntigenResult" id="frmAntigenResult">
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
            <tr>
                <td width=44% valign=top>  
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="antigen_sono" id="antigen_sono" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="antigen_sodate" id="antigen_sodate" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_pid" id="antigen_pid" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_pname" id="antigen_pname" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>

                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_gender" id="antigen_gender" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_birthdate" id="antigen_birthdate" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_age" id="antigen_age" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_patientstat" id="antigen_patientstat" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_physician" id="antigen_physician" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                    </table>
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_procedure" id="antigen_procedure" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_code" id="antigen_code" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="antigen_spectype" id="antigen_spectype" >
                                    <?php
                                        $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_serialno" id="antigen_serialno" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_extractdate" id="antigen_extractdate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                            <td align=left>
                    
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_extracttime" id="antigen_extracttime" readonly>

                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Kit Type (If Applicable)&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_testkit" id="antigen_testkit" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Lot No. (If Applicable)&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_testkit_lotno" id="antigen_testkit_lotno" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Expiry (If Applicable&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_testkit_expiry" id="antigen_testkit_expiry" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antigen_extractby" id="antigen_extractby" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="antigen_location" id="antigen_location">
                                    <?php
                                        $iun = $o->dbquery("select id,location from lab_locations;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                    </table>
                </td>
                <td width=1%>&nbsp;</td>
                <td width=64% valign=top >                  
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 class="td_content">
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="antigen_date" id="antigen_date" value="<?php echo date('m/d/Y'); ?>">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result&nbsp;:</td>
                            <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="antigen_result" id="antigen_result">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sensitivity&nbsp;:</td>
                            <td align=left>
                            <input type="number" class="gridInput" style="width:100%;" name="antigen_sensitivity" id="antigen_sensitivity">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specificity&nbsp;:</td>
                            <td align=left>
                            <input type="number" class="gridInput" style="width:100%;" name="antigen_specificity" id="antigen_specificity">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Performed By&nbsp;:</td>
                            <td align=left>
                                <select name="antigen_result_by" id="antigen_result_by" class="gridInput" style="width:100%">
                                    <option value="">- Not Applicable -</option>
                                    <?php
                                        $pbyQuery = $o->dbquery("select emp_id, fullname from user_info where role like '%MEDICAL TECH%';");
                                        while($pbyRow = $pbyQuery->fetch_array()) {
                                            echo "<option value = '$pbyRow[0]'>$pbyRow[1]</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                            <td align=left>
                                <textarea name="antigen_remarks" id="antigen_remarks" style="width:100%;" rows=3></textarea>
                            </td>				
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>

<div id="antibodyResult" style="display: none;">
    <form name="frmAntibodyResult" id="frmAntibodyResult">
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
            <tr>
                <td width=44% valign=top>  
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="antibody_sono" id="antibody_sono" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="antibody_sodate" id="antibody_sodate" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_pid" id="antibody_pid" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_pname" id="antibody_pname" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>

                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_gender" id="antibody_gender" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_birthdate" id="antibody_birthdate" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_age" id="antibody_age" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_patientstat" id="antibody_patientstat" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_physician" id="antibody_physician" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                    </table>
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_procedure" id="antibody_procedure" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_code" id="antibody_code" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="antibody_spectype" id="antibody_spectype" >
                                    <?php
                                        $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_serialno" id="antibody_serialno" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_extractdate" id="antibody_extractdate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                            <td align=left>
                    
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_extracttime" id="antibody_extracttime" readonly>

                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Kit Type (If Applicable)&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_testkit" id="antibody_testkit" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Lot No. (If Applicable)&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_testkit_lotno" id="antibody_testkit_lotno" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Expiry (If Applicable&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_testkit_expiry" id="antibody_testkit_expiry" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="antibody_extractby" id="antibody_extractby" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="antibody_location" id="antibody_location">
                                    <?php
                                        $iun = $o->dbquery("select id,location from lab_locations;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                    </table>
                </td>
                <td width=1%>&nbsp;</td>
                <td width=64% valign=top >                  
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 class="td_content">
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="antibody_date" id="antibody_date" value="<?php echo date('m/d/Y'); ?>">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result IGM&nbsp;:</td>
                            <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="antibody_result_igm" id="antibody_result_igm">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result IGG&nbsp;:</td>
                            <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="antibody_result_igg" id="antibody_result_igg">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sensitivity&nbsp;:</td>
                            <td align=left>
                            <input type="number" class="gridInput" style="width:100%;" name="antibody_sensitivity" id="antibody_sensitivity">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specificity&nbsp;:</td>
                            <td align=left>
                            <input type="number" class="gridInput" style="width:100%;" name="antibody_specificity" id="antibody_specificity">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Performed By&nbsp;:</td>
                            <td align=left>
                                <select name="antibody_result_by" id="antibody_result_by" class="gridInput" style="width:100%">
                                    <option value="">- Not Applicable -</option>
                                    <?php
                                        $pbyQuery = $o->dbquery("select emp_id, fullname from user_info where role like '%MEDICAL TECH%';");
                                        while($pbyRow = $pbyQuery->fetch_array()) {
                                            echo "<option value = '$pbyRow[0]'>$pbyRow[1]</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                            <td align=left>
                                <textarea name="antibody_remarks" id="antibody_remarks" style="width:100%;" rows=3></textarea>
                            </td>				
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>

<div id="ctbtResult" style="display: none;">
    <form name="frmCtbtResult" id="frmCtbtResult">
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
            <tr>
                <td width=44% valign=top>  
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order No.&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="ctbt_sono" id="ctbt_sono" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Service Order Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="ctbt_sodate" id="ctbt_sodate" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_pid" id="ctbt_pid" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_pname" id="ctbt_pname" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>

                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_gender" id="ctbt_gender" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_birthdate" id="ctbt_birthdate" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Age&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_age" id="ctbt_age" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_patientstat" id="ctbt_patientstat" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_physician" id="ctbt_physician" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                    </table>
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test or Procedure&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_procedure" id="ctbt_procedure" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_code" id="ctbt_code" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="ctbt_spectype" id="ctbt_spectype" >
                                    <?php
                                        $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_serialno" id="ctbt_serialno" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Extracted&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_extractdate" id="ctbt_extractdate">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                            <td align=left>
                    
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_extracttime" id="ctbt_extracttime" readonly>

                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Kit Type (If Applicable)&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_testkit" id="ctbt_testkit" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Lot No. (If Applicable)&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_testkit_lotno" id="ctbt_testkit_lotno" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Expiry (If Applicable&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_testkit_expiry" id="ctbt_testkit_expiry" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="ctbt_extractby" id="ctbt_extractby" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Phleb/Imaging Site&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="ctbt_location" id="ctbt_location">
                                    <?php
                                        $iun = $o->dbquery("select id,location from lab_locations;");
                                        while(list($aa,$ab) = $iun->fetch_array()) {
                                            echo "<option value='$aa'>$ab</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                    </table>
                </td>
                <td width=1%>&nbsp;</td>
                <td width=64% valign=top >                  
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 class="td_content">
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="ctbt_date" id="ctbt_date" value="<?php echo date('m/d/Y'); ?>">
                            </td>				
                        </tr>
                        <tr><td colspan=5><hr style="color: white;"></td></tr>            
                        <tr><td height=4></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Clotting Time&nbsp;:</td>
                            <td align=left></td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="center" width="35%" class="bareBold" style="padding-right: 15px;">Minute/s</td>
                            <td align=left>
                            <input type="number" class="gridInput" style="width:100%;" name="ctbt_ct_min" id="ctbt_ct_min">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="center" width="35%" class="bareBold" style="padding-right: 15px;">Seconds</td>
                            <td align=left>
                            <input type="number" class="gridInput" style="width:100%;" name="ctbt_ct_sec" id="ctbt_ct_sec">
                            </td>				
                        </tr>
                        <tr><td height=4></td></tr>
                        <tr><td colspan=5><hr style="color: white;"></td></tr>            
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Bleeding Time&nbsp;:</td>
                            <td align=left></td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="center" width="35%" class="bareBold" style="padding-right: 15px;">Minute/s</td>
                            <td align=left>
                            <input type="number" class="gridInput" style="width:100%;" name="ctbt_bt_min" id="ctbt_bt_min">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="center" width="35%" class="bareBold" style="padding-right: 15px;">Seconds</td>
                            <td align=left>
                            <input type="number" class="gridInput" style="width:100%;" name="ctbt_bt_sec" id="ctbt_bt_sec">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr><td colspan=5><hr style="color: white;"></td></tr>            
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Test Performed By&nbsp;:</td>
                            <td align=left>
                                <select name="ctbt_result_by" id="ctbt_result_by" class="gridInput" style="width:100%">
                                    <option value="">- Not Applicable -</option>
                                    <?php
                                        $pbyQuery = $o->dbquery("select emp_id, fullname from user_info where role like '%MEDICAL TECH%';");
                                        while($pbyRow = $pbyQuery->fetch_array()) {
                                            echo "<option value = '$pbyRow[0]'>$pbyRow[1]</option>";
                                        }
                                    ?>
                                </select>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Other Notable Remarks&nbsp;:</td>
                            <td align=left>
                                <textarea name="ctbt_remarks" id="ctbt_remarks" style="width:100%;" rows=3></textarea>
                            </td>				
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>

<div id="pemeReport" style="display: none;">
	<form name="frmPEMEReport" id="frmPEMEReport">
		<table width=100% callpaddin=0 cellspacing=3>
		<tr>
			<td width=35%><span class="spandix-l">Company Name: </span></td>
			<td>
				<select name="peme_cid" id="peme_cid" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value=''>- All Customers -</option>
					<?php
						$custQuery = $o->dbquery("SELECT DISTINCT customer_code, customer_name FROM so_header WHERE customer_code != 0 AND `status` = 'Finalized' ORDER BY customer_name;");
						while($custRow = $custQuery->fetch_array()) {
							echo "<option value='$custRow[0]'>$custRow[1]</option>";

						}
					?>
				</select>
			</td>
		</tr>
			<tr>
				<td width=35% class="spandix-l">Date From:</td>
				<td>
					<input type="text" class="gridInput" style="width: 80%;" id="peme_dtf" name="peme_dtf" value = "<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
			<tr>
				<td width=35% class="spandix-l">Date To:</td>
				<td>
					<input type="text" class="gridInput" style="width: 80%;" id="peme_dt2" name="peme_dt2" value = "<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
            <tr><td height=4></td></tr>
            <tr><td colspan=2><hr></hr></td></tr>
            <tr>
                <td align=center colspan=2>
                    <button onClick="generatePEMEDR();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
                    <button onClick="generatePEMEDRX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export Report to Excel</button>
                </td>
            </tr>
		</table>
	</form>
</div>
<div id="pemeTally" style="display: none;">
	<form name="frmPEMETally" id="frmPEMETally">
		<table width=100% callpaddin=0 cellspacing=3>
		<tr>
			<td width=35%><span class="spandix-l">Doctors' Name: </span></td>
			<td>
				<select name="pemetally_cid" id="pemetally_cid" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value=''>- Select Doctors -</option>
					<?php
						$custQuery = $o->dbquery("SELECT id, CONCAT(fullname,', ',prefix) AS prefix FROM options_doctors WHERE id NOT IN ('1','2') AND file_status = 'Active' ORDER BY id;");
						while($custRow = $custQuery->fetch_array()) {
							echo "<option value='$custRow[0]'>$custRow[1]</option>";

						}
					?>
				</select>
			</td>
		</tr>
			<tr>
				<td width=35% class="spandix-l">Date From:</td>
				<td>
					<input type="text" class="gridInput" style="width: 80%;" id="pemetally_dtf" name="pemetally_dtf" value = "<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
			<tr>
				<td width=35% class="spandix-l">Date To:</td>
				<td>
					<input type="text" class="gridInput" style="width: 80%;" id="pemetally_dt2" name="pemetally_dt2" value = "<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
            <tr><td height=4></td></tr>
            <tr><td colspan=2><hr></hr></td></tr>
            <tr>
                <td align=center colspan=2>
                    <button onClick="generatePEMETally();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
                    <button onClick="generatePEMETallyX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export Report to Excel</button>
                </td>
            </tr>
		</table>
	</form>
</div>
<div id="pemeBatch" style="display: none;">
	<form name="frmPEMEBatch" id="frmPEMEBatch">
		<table width=100% callpaddin=0 cellspacing=3>
        <tr>
			<td width=35%><span class="spandix-l">Company Name: </span></td>
			<td>
				<select name="pemebatch_cid" id="pemebatch_cid" class="gridInput" style="width: 90%; font-size: 11px;">
					<option value=''>- All Customers -</option>
					<?php
						$custQuery = $o->dbquery("SELECT DISTINCT customer_code, customer_name FROM so_header WHERE customer_code != 0 AND `status` = 'Finalized' ORDER BY customer_name;");
						while($custRow = $custQuery->fetch_array()) {
							echo "<option value='$custRow[0]'>[$custRow[0]] $custRow[1]</option>";

						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td width=35%><span class="spandix-l">Doctors' Name: </span></td>
			<td>
				<select name="pemebatch_cname" id="pemebatch_cname" class="gridInput" style="width: 90%; font-size: 11px;">
					<option value=''>- Select Doctors -</option>
					<?php
						$custQuery = $o->dbquery("SELECT id, CONCAT(fullname,', ',prefix) AS prefix FROM options_doctors WHERE id NOT IN ('0','2') AND file_status = 'Active' ORDER BY id;");
						while($custRow = $custQuery->fetch_array()) {
							echo "<option value='$custRow[0]'>$custRow[1]</option>";

						}
					?>
				</select>
			</td>
		</tr>
			<tr>
				<td width=35% class="spandix-l">Date From:</td>
				<td>
					<input type="text" class="gridInput" style="width: 90%;" id="pemebatch_dtf" name="pemebatch_dtf" value = "<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
			<tr>
				<td width=35% class="spandix-l">Date To:</td>
				<td>
					<input type="text" class="gridInput" style="width: 90%;" id="pemebatch_dt2" name="pemebatch_dt2" value = "<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
            <tr><td height=4></td></tr>
            <tr><td colspan=2><hr></hr></td></tr>
            <tr>
                <td align=center colspan=2>
                    <button onClick="generatePEMEBatch();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
                </td>
            </tr>
		</table>
	</form>
</div>
<div id="descResult" name="descResult" style="display: none;"></div>
<div id="cbcResult" name="cbcResult" style="display: none;"></div>
<div id="ecgResult" name="ecgResult" style="display: none;"></div>
<div id="bloodChemResult" name="bloodChemResult" style="display: none;"></div>
<div id="uaResult" name="uaResult" style="display: none;"></div>
<div id="stoolResult" name="stoolResult" style="display: none;"></div>
<div id="semAnalReport" name="semAnalReport" style="display: none;"></div>
<div id="barcode" name="barcode" style="display: none;"></div>
<div id="ft4Result" name="ft4Result" style="display: none;"></div>
<div id="tshResult" name="tshResult" style="display: none;"></div>
<div id="eGFR" name="eGFR" style="display: none;"></div>
<div id="audiometryResult" name="audiometryResult" style="display: none;"></div>
<div id="singleResults" name="singleResults" style="display: none;"></div>
<div id="hepaResult" name="hepaResult" style="display: none;"></div>
