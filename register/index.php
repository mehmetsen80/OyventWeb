<?php 
include($_SERVER['DOCUMENT_ROOT']."/Settings.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	
	@session_start();

	if(isset($_SESSION['userObject']))
	{
		header("Location: ".$sitepath);
	}
	
	
	
	//http://www.funtle.com/register/?su=fmw2e3is252  send this link to subscribers

	//invitation starts here
	//$fkInviteFriendID = $_GET['cd'];
	//$from = $_GET['fr'];
	//$su = $_GET['su'];
	
	/*if($su == 'fmw2e3is252') //use this code for the subscriber invitations
	{
		$fkInviteFriendID = "allow subscriber to register";
	}
	else
	{
		$query = "SELECT * FROM TBLINVITEFRIEND WHERE PKINVITEFRIENDID='".$fkInviteFriendID."' AND FROMID='".$from."' ";
		$result = executeQuery($query);
	
		if(mysql_num_rows($result)>0)
		{
			$query = "SELECT * FROM TBLUSER WHERE FKINVITEFRIENDID='".$fkInviteFriendID."'";
			$result = executeQuery($query);
	
			$iscodeused='no';	
			if(mysql_num_rows($result)>0)
			{
				$iscodeused = 'yes';
			}
		}
		else
		{
			$fkInviteFriendID = NULL;
		}
	}*/
	


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Register SMS Film</title>
<script type="text/javascript" src="/js/lib/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="/js/Register.js"></script>
<link href="/css/style.css" rel="stylesheet" type="text/css" />


<script type="text/javascript">
$(document).ready(function(){	

	focusFullnameTextBox();
	
});//ready

function focusFullnameTextBox()
{
	$('#txtFullName').focus();
}

</script>

</head>

<body>


<?php //include("../Header.php")?>
        
  <div id="cont">
        <div class="box large left">

	<h2><b>Üye Ol</b></h2>
	
	</div>
<br>
	<div class="slogan large left">
    	Oyvent
    </div>

	 

</div>

<input type="hidden" id="txtInviteFriendCode" value="<?= $fkInviteFriendID ?>" >

<div class="detailsbox">
	
	<div id="divFullName" style="font-size:11px; float:left; text-align:left; color:#ff0000;"></div><div style='font-size:12px;'>İsim-Soyisim</div>
	<input name="txtFullName"  class="text" onFocus="checkFullName()"  onKeyUp="checkFullName()"  type="text" id="txtFullName"  maxlength="150"  />
	
	<br/><br/><br/>
	
	<div id="divEmail" style="font-size:11px; text-align:left; float:left; color:#ff0000;"></div><div style='font-size:12px;'>E-Posta</div>
	<input name="txtEmail"  class="text" onFocus="validateEmailSignUpUser()"  onKeyUp="validateEmailSignUpUser()" type="text" id="txtEmail"  maxlength="60" />
	
	<br/><br/><br/>
	
	<div id="divUsername"  style="font-size:11px; float:left; text-align:left; color:#ff0000;"></div><div style='font-size:12px;'>Kullanıcı Adı</div>
	<input name="txtUsername" class="text" onFocus="checkUsername()" onKeyUp="checkUsername()"   type="text" id="txtUsername"  maxlength="40" />
	
	<br/><br/><br/>
	
	<div id="divPassword" style="font-size:11px; text-align:left; float:left; color:#ff0000;"></div><div style='font-size:12px;'>Şifre</div>
	<input name="txtPassword" class="text"  type="password" id="txtPassword" onFocus="checkPassword()" onKeyUp="checkPassword()"   maxlength="40"  />
		
	<br/><br/><br/>
	
	<div id="divRePassword" style="font-size:11px; text-align:left; color:#ff0000;"></div><div style='font-size:12px;'>Şifre-Tekrar</div>
	<input  name="txtRePassword" class="text"  type="password" onFocus="checkRePassword()" onKeyUp="checkRePassword()"  id="txtRePassword"  maxlength="40" />
		
	<br/><br/><br/>
	
	<div style='width:70%; float:left;'>
	
	
	<select style="width:80px;"  name="cmbMonth" id="cmbMonth">
         <option value="01">Ocak</option>
         <option value="02">Şubat</option>
         <option value="03">Mart</option>
         <option value="04">Nisan</option>
         <option value="05">Mayıs</option>
         <option value="06">Haziran</option>
         <option value="07">Temmuz</option>
         <option value="08">Ağustos</option>
         <option value="09">Eylül</option>
         <option value="10">Ekim</option>
         <option value="11">Kasım</option>
         <option value="12">Aralık</option>
       </select>  <select style="width:46px;"  name="cmbDay" id="cmbDay">
         <option value="01">1</option>
         <option value="02">2</option>
         <option value="03">3</option>
         <option value="04">4</option>
         <option value="05">5</option>
         <option value="06">6</option>
         <option value="07">7</option>
         <option value="08">8</option>
         <option value="09">9</option>
         <option value="10">10</option>
         <option value="11">11</option>
         <option value="12">12</option>
         <option value="13">13</option>
         <option value="14">14</option>
         <option value="15">15</option>
         <option value="16">16</option>
         <option value="17">17</option>
         <option value="18">18</option>
         <option value="19">19</option>
         <option value="20">20</option>
         <option value="21">21</option>
         <option value="22">22</option>
         <option value="23">23</option>
         <option value="24">24</option>
         <option value="25">25</option>
         <option value="26">26</option>
         <option value="27">27</option>
         <option value="28">28</option>
         <option value="29">29</option>
         <option value="30">30</option>
         <option value="31">31</option>
       </select> 
       <select name="cmbYear" style="width:70px;"  id="cmbYear">
       	 <option value="1940" >1940</option>
         <option value="1941" >1941</option>
         <option value="1942" >1942</option>
         <option value="1943" >1943</option>
         <option value="1944" >1944</option>
         <option value="1945" >1945</option>
         <option value="1946" >1946</option>
         <option value="1947" >1947</option>
         <option value="1948" >1948</option>
         <option value="1949" >1949</option>
         <option value="1950" >1950</option>
         <option value="1951" >1951</option>
         <option value="1952" >1952</option>
         <option value="1953" >1953</option>
         <option value="1954" >1954</option>
         <option value="1955" >1955</option>
         <option value="1956" >1956</option>
         <option value="1957" >1957</option>
         <option value="1958" >1958</option>
         <option value="1959" >1959</option>
         <option value="1960" >1960</option>
         <option value="1961" >1961</option>
         <option value="1962" >1962</option>
         <option value="1963" >1963</option>
         <option value="1964" >1964</option>
         <option value="1965" >1965</option>
         <option value="1966" >1966</option>
         <option value="1967" >1967</option>
         <option value="1968" >1968</option>
         <option value="1969" >1969</option>
         <option value="1970" >1970</option>
         <option value="1971" >1971</option>
         <option value="1972" >1972</option>
         <option value="1973" >1973</option>
         <option value="1974" >1974</option>

         <option value="1975" >1975</option>
         <option value="1976" >1976</option>
         <option value="1977" >1977</option>
         <option value="1978" >1978</option>
         <option value="1979" >1979</option>
         <option value="1980" >1980</option>
         <option value="1981" >1981</option>
         <option value="1982" >1982</option>
         <option value="1983" >1983</option>
         <option value="1984" >1984</option>
         <option value="1985" >1985</option>
         <option value="1986" >1986</option>
         <option value="1987" >1987</option>
         <option value="1988" >1988</option>
         <option value="1989" >1989</option>
         <option value="1990" >1990</option>
         <option value="1991" >1991</option>
         <option value="1992" >1992</option>
         <option value="1993" >1993</option>
         <option value="1994" >1994</option>
         <option value="1995" >1995</option>
         <option value="1996" >1996</option>
         <option value="1997" >1997</option>
         <option value="1998" >1998</option>
         <option value="1999" >1999</option>
         <option value="2000" >2000</option>
         <option value="2001" >2001</option>
         <option value="2002" >2002</option>
         <option value="2003" >2003</option>
         <option value="2004" >2004</option>
       </select>
	</div>
	
	<div>
	
		<select name="cmbGender"   id="cmbGender">
         	<option  value="Male" >Bay</option>
         	<option value="Female" >Bayan</option>
    	</select>
    </div>
    
    <br/>
	<input type="checkbox" style="width:15px;" name="cbxTerms" id="cbxTerms"   >&nbsp;<a href="/Terms.php">Sözleşme Şartlarını Kabul Ediyorum</a>
	<br/><br/>
	<input name="btnCreate"  onClick="signUpUser()" style="width:300px; height:50px;" type="submit" class="button" id="btnCreate" value="Üye Ol" />	
	
</div> 

<?php //include("../Footer.php"); ?>








</body>
</html>

