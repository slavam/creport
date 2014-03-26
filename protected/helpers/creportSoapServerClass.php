<?php

class AnalyzeCreditHistoryResponse
{
	public $positive;
}

class creportSoapServerClass {

	function __construct()
	{
		loggerClass::write('[i] creportSoapServerClass object created',3);
	}

	public function AnalyzeCreditHistory($request)
	{

		loggerClass::write('==================> AnalyzeCrediHistory(): '.serialize($request),2);

		$p_taxpayer_number = $request->taxpayer_number;
		$p_pawn_credit = $request->pawn_credit;
//                $header_report = Report::model()->getLastReportByInn($p_taxpayer_number);
//                loggerClass::write('==================> AnalyzeCrediHistory(): '.serialize($header_report),2);
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
//                $ret = Yii::app()->createUrl('ReportMBKI/ShowAnalyzeResult',array('inn'=>$p_taxpayer_number));
		$response->positive = (+$p_taxpayer_number%2==0)? 'T':'F';// 'T';

//		loggerClass::write('<- AnalyzeCrediHistory(): $p_taxpayer_number='.($p_taxpayer_number%2==0? 'T':'F').' '.serialize($response),2);

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