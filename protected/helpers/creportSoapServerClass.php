<?php

class creportSoapServerClass {

	function __construct()
	{
		loggerClass::write('[i] creportSoapServerClass object created',3);
	}

	public function AnalyzeCreditHistory($request)
	{

		loggerClass::write('-> AnalyzeCrediHistory(): '.serialize($request),2);
		

		if(!isset($request->taxpayer_number))
			return $this->raiseServiceError(0x80000003,sprintf('Element: %s not found in AnalyzeCreditHistoryRequest!','taxpayer_number'),'AnalyzeCrediHistory');
		else $p_taxpayer_number = $request->taxpayer_number;

		if(!isset($request->pawn_credit))
			return $this->raiseServiceError(0x80000003,sprintf('Element: %s not found in AnalyzeCreditHistoryRequest!','pawn_credit'),'AnalyzeCrediHistory');
		else $p_pawn_credit = $request->pawn_credit;

		if(!isset($request->request_issuer))
			return $this->raiseServiceError(0x80000003,sprintf('Element: %s not found in AnalyzeCreditHistoryRequest!','request_issuer'),'AnalyzeCrediHistory');
		else $p_request_issuer = $request->request_issuer;

                if(preg_match_all("/([^0-9])/",$request->taxpayer_number,$matches))
			return $this->raiseServiceError(0x80000004,sprintf('Element: %s has wrong format!','taxpayer_number'),'AnalyzeCrediHistory');
                
                if(preg_match_all("/([^a-z.])/",$request->request_issuer,$matches))
			return $this->raiseServiceError(0x80000004,sprintf('Element: %s has wrong format!','request_issuer'),'AnalyzeCrediHistory');
		
		

//debug - show input parameters in soapUI		
//		return $this->raiseServiceError('code','debug::input parameters'.' ='.$p_taxpayer_number.' ='.$p_pawn_credit,'AnalyzeCreditHistory');


//sample how to raise service errors, 3rd parameter - function name
/*		
		if ($this->conn)
		{
			// prepeare data query
		}
		else 
			return $this->raiseServiceError('code','msg','AnalyzeCreditHistory');
*/
		
//build correct response
		$response = new AnalyzeCreditHistoryResponse();
//		$response->positive = 'T';
		$response->positive = (+$p_taxpayer_number%2==0)? 'T':'F';// 'T';


		loggerClass::write('<- AnalyzeCrediHistory(): '.serialize($response),2);

		return $response;
	}
	
	private function raiseServiceError($error_code,$error_message,$fn_name)
	{
		$array_details = array("errorCode"=>$error_code, "errorMessage" => $error_message);
		loggerClass::write("[!] Server fault: "."Ошибка в работе функции ".$fn_name.": ".serialize($array_details),1);
		return new SoapFault("Server", "Ошибка в работе функции ".$fn_name, null ,$array_details, "ServiceFault");
	}

}
?>