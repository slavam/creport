<?php
class MbkiReport {
    public $addresses = array();
    public $identifications = array();
    public $relations = array();
    public $summaryInformations = array();
    public $inquiryList = array();
    public $inquiers = array();
    public $contracts = array();
    public $roles = array();
    public $contacts = array();
//    public $summaryInformations = array();
//    public $contractTerminatedSummaryInformations = array();
//    public $contractExistingSummaryInformations = array();
//    public $months = array();
    public function __construct ($xml){
        $this->usageIdentity = (string)$xml->Report['UsageIdentity'];
        $this->issueDate = (string)$xml->Report['issued'];
        $this->updated = (string)$xml->Report['updated'];
        $this->mbkiId = (string)$xml->Report->Subject->CreditinfoId;
        // Subject
        $this->dateOfBirth = (string)$xml->Report->Subject->DateOfBirth;
        $this->taxpayerNumber = (string)$xml->Report->Subject->TaxpayerNumber;
        $this->passport = (string)$xml->Report->Subject->Passport;
	$this->gender = (string)$xml->Report->Subject->Gender['ImportCode'];
        $this->genderName = (string)$xml->Report->Subject->Gender;
        $this->surname = (string)$xml->Report->Subject->Surname;
        $this->name = (string)$xml->Report->Subject->Name;
        $this->fathersName = (string)$xml->Report->Subject->FathersName;
        $this->birthName = (string)$xml->Report->Subject->BirthName;
//        $this->negativeStatus = (string)$xml->Report->Subject->NegativeStatus['ImportCode'];
        $this->residency = (string)$xml->Report->Subject->Residency['ImportCode'];
        $this->residencyName = (string)$xml->Report->Subject->Residency;
        $this->nationality = (string)$xml->Report->Subject->Nationality;
	$this->education = (string)$xml->Report->Subject->Education['ImportCode'];
        $this->educationName = (string)$xml->Report->Subject->Education;
	$this->classification = (string)$xml->Report->Subject->Classification['ImportCode'];
        $this->classificationName = (string)$xml->Report->Subject->Classification;
//        var_dump($xml->Report->Addresses);
        if (isset($xml->Report->Addresses))
        foreach ($xml->Report->Addresses->Address as $a) 
            $this->addresses[] = array(
                'type'=>(string)$a->AddressType,
                'id_type'=>(string)$a->AddressType['ImportCode'],
                'street'=>(string)$a->Street, 
                'city'=>(string)$a->City, 
                'zip'=>(string)$a->Zipcode,
                'region'=>(string)$a->Region, 
                'area'=>(string)$a->District,
                'country'=>(string)$a->Country,
                );
        if (isset($xml->Report->Contacts))
            foreach ($xml->Report->Contacts->Contact as $c) 
                $this->contacts[] = array(
                    'code'=>(string)$c->ImportCode, 
                    'name'=>(string)$c->Name, 
                    'value'=>(string)$c->Value
                    );
        if (isset($xml->Report->Identifications))
        foreach ($xml->Report->Identifications->Identification as $i) {
            $this->identifications[] = array(
                'idType'=>(string)$i->IdType['ImportCode'],
                'idDocName'=>(string)$i->IdType,
                'docNumber'=>(string)$i->DocumentNumber,
                'issuedBy'=>(string)$i->IssuedBy,
                'issuedDate'=>(string)$i->IssueDate);
        }
        if(isset($xml->Report->Relations))
            foreach ($xml->Report->Relations->Relation as $r) {
                $this->relations[] = array(
                    'state'=>               (string)$r->State,
                    'jobTitle'=>            (string)$r->JobTitle,
                    'companyName'=>         (string)$r->CompanyName,
                    'subjectsPosition'=>    (string)$r->SubjectsPosition,
                    'registrationNumber'=>  (string)$r->RegistrationNumber,
                    'startDate'=>           (string)$r->StartDate,
                    'address'=>             (string)$r->Address,
                    'providerCode'=>        (string)$r->ProviderCode);
            }
        $this->negativeInfoType = (string)$xml->Report->SummaryInformation->NegativeInfoType;
        $this->numberOfUsersReportingNegativeStatus = (string)$xml->Report->SummaryInformation->NumberOfUsersReportingNegativeStatus;
        
        foreach ($xml->xpath('//SummaryInformation') as $si) 
            if ($si->SummaryType == 'SummaryInformationDebtor') {
                if (!isset($si->ContractType)) { // total
                    $this->numberOfExistingContracts = (string)$si->NumberOfExistingContracts;
                    $this->totalOutstandingDebtCurrency = (string)$si->TotalOutstandingDebt->Amount->Currency;
                    $this->totalOutstandingDebtValue = (string)$si->TotalOutstandingDebt->Amount->Value;
                    $this->numberOfTerminatedContracts = (string)$si->NumberOfTerminatedContracts;
                    $this->currency = (string)$si->TotalDebtOverdue->Amount->Currency;
                    $this->value = (string)$si->TotalDebtOverdue->Amount->Value;
                    $this->numberOfUnsolvedApplications = (string)$si->NumberOfUnsolvedApplications;
                    $this->numberOfUnpaidInstalments = (string)$si->NumberOfUnpaidInstalments;
                    $this->numberOfRejectedApplications = (string)$si->NumberOfRejectedApplications;
                    $this->numberOfRevokedApplications = (string)$si->NumberOfRevokedApplications;
                    $this->numberOfUsersReportingNegativeStatus = (string)$si->NumberOfUsersReportingNegativeStatus;
                } else {
                    $this->summaryInformations[] = array(
                        'contractType'=>    (string)$si->ContractType,
                        'numberOfExistingContracts' => (string)$si->NumberOfExistingContracts,
                        'totalCurrency' => (string)$si->TotalOutstandingDebt->Amount->Currency,
                        'totalValue' => (string)$si->TotalOutstandingDebt->Amount->Value,
                        'numberOfTerminatedContracts' => (string)$si->NumberOfTerminatedContracts,
                        'currency' => (string)$si->TotalDebtOverdue->Amount->Currency,
                        'value' => (string)$si->TotalDebtOverdue->Amount->Value,
                        'numberOfUnsolvedApplications' => (string)$si->NumberOfUnsolvedApplications,
                        'numberOfUnpaidInstalments' => (string)$si->NumberOfUnpaidInstalments,
                        'numberOfRejectedApplications' => (string)$si->NumberOfRejectedApplications,
                        'numberOfRevokedApplications' => (string)$si->NumberOfRevokedApplications,
                        'numberOfUsersReportingNegativeStatus' => (string)$si->NumberOfUsersReportingNegativeStatus
                    );
                }
//            }else {
//                if($si->NumberOfExistingContracts>'0')
//                    $this->contractExistingSummaryInformations[] = array(
//                            'contractType' => (string)$si->ContractType,
//                            'numberOfExistingContracts' => (string)$si->NumberOfExistingContracts,
//                            'totalCurrency' => (string)$si->TotalOutstandingDebt->Amount->Currency,
//                            'totalValue' => (string)$si->TotalOutstandingDebt->Amount->Value,
//                            'numberOfTerminatedContracts' => (string)$si->NumberOfTerminatedContracts,
//                            'currency' => (string)$si->TotalDebtOverdue->Amount->Currency,
//                            'value' => (string)$si->TotalDebtOverdue->Amount->Value,
//                            'numberOfUnsolvedApplications' => (string)$si->NumberOfUnsolvedApplications,
//                            'numberOfUnpaidInstalments' => (string)$si->NumberOfUnpaidInstalments,
//                            'numberOfRejectedApplications' => (string)$si->NumberOfRejectedApplications,
//                            'numberOfRevokedApplications' => (string)$si->NumberOfRevokedApplications,
//                            'numberOfUsersReportingNegativeStatus' => (string)$si->NumberOfUsersReportingNegativeStatus
//                        );
//                if($si->NumberOfTerminatedContracts>'0')
//                    $this->contractTerminatedSummaryInformations[] = array(
//                            'contractType' => (string)$si->ContractType,
//                            'numberOfExistingContracts' => (string)$si->NumberOfExistingContracts,
//                            'totalCurrency' => (string)$si->TotalOutstandingDebt->Amount->Currency,
//                            'totalValue' => (string)$si->TotalOutstandingDebt->Amount->Value,
//                            'numberOfTerminatedContracts' => (string)$si->NumberOfTerminatedContracts,
//                            'currency' => (string)$si->TotalDebtOverdue->Amount->Currency,
//                            'value' => (string)$si->TotalDebtOverdue->Amount->Value,
//                            'numberOfUnsolvedApplications' => (string)$si->NumberOfUnsolvedApplications,
//                            'numberOfUnpaidInstalments' => (string)$si->NumberOfUnpaidInstalments,
//                            'numberOfRejectedApplications' => (string)$si->NumberOfRejectedApplications,
//                            'numberOfRevokedApplications' => (string)$si->NumberOfRevokedApplications,
//                            'numberOfUsersReportingNegativeStatus' => (string)$si->NumberOfUsersReportingNegativeStatus
//                        );
            }
        if(isset($xml->Report->Contracts))
        foreach ($xml->Report->Contracts->Contract as $c) {
            $this->contracts[] = array(
		'exportCode'=>(string)$c->ExportCode,
		'importCode'=>(string)$c->ImportCode,
		'contractType'=>(string)$c->ContractType,
		'contractPosition'=>(string)$c->ContractPosition,
		'contractRole'=>(string)$c->ContractRole,
		'contractPhase'=>(string)$c->ContractPhase,
                'contractPhaseCode'=>(string)$c->ContractPhase['ImportCode'],
		'codeOfContract'=>(string)$c->CodeOfContract,
		'purposeOfCredit'=>(string)$c->PurposeOfCredit,
                'purposeOfCreditCode'=>(string)$c->PurposeOfCredit['ImportCode'],
		'currencyCode'=>(string)$c->CurrencyCode['Code'],
                'currencyName'=>(string)$c->CurrencyCode,
//			<NegativeStatus>
//				<ExportCode>Contract.ContractLookup.ContractStatus.[None]</ExportCode>
//				<ImportCode>0</ImportCode>
//				<Value>Негативний статус відсутній</Value>
//			</NegativeStatus>
		'subjectRole'=>(string)$c->SubjectRole,
                'subjectRoleCode'=>(string)$c->SubjectRole['ImportCode'],
//			<ContractStatus>
//				<ExportCode>Contract.ContractLookup.ContractStatus.[None]</ExportCode>
//				<ImportCode>0</ImportCode>
		'contractStatusValue'=>(string)$c->ContractStatus->Value,
//			</ContractStatus>
		'dateOfApplication'=>(string)$c->DateOfApplication,
		'creditStartDate'=>(string)$c->CreditStartDate,
		'contractEndDate'=>(string)$c->ContractEndDate,
		'totalAmountType'=>(string)$c->TotalAmount->AmountType,
		'totalAmountCurrency'=>(string)$c->TotalAmount->Currency,
		'totalAmountValue'=>(string)$c->TotalAmount->Value,
		'numberOfOutstandingInstalments'=>(string)$c->NumberOfOutstandingInstalments,
		'pereodicityOfPayments'=>(string)$c->PereodicityOfPayments,
                'pereodicityOfPaymentsCode'=>(string)$c->PereodicityOfPayments['ImportCode'],
		'outstandingAmountCurrency'=>(string)$c->OutstandingAmount->Currency,
                'outstandingAmountValue'=>(string)$c->OutstandingAmount->Value,
		'methodOfPayments'=>(string)$c->MethodOfPayments,
                'methodOfPaymentsCode'=>(string)$c->MethodOfPayments['ImportCode'],
		'numberOfInstalments'=>(string)$c->NumberOfInstalments,
		'numberOfOverdueInstalments'=>(string)$c->NumberOfOverdueInstalments,
		'numberOfInstalmentsNotPaidAccordingToInterestRate'=>(string)$c->NumberOfInstalmentsNotPaidAccordingToInterestRate,
		'monthlyInstalmentAmountCurrency'=>(string)$c->MonthlyInstalmentAmount->Currency,
                'monthlyInstalmentAmountValue'=>(string)$c->MonthlyInstalmentAmount->Value,
		'overdueAmountCurrency'=>(string)$c->OverdueAmount->Currency,
                'overdueAmountValue'=>(string)$c->OverdueAmount->Value,
                'overdueAmountPaymentCount'=>(string)$c->OverdueAmount->PaymentCount,
		'dueInterestAmountCurrency'=>(string)$c->DueInterestAmount->Currency,
                'dueInterestAmountValue'=>(string)$c->DueInterestAmount->Value,
//			<Roles>
//				<Role>
//					<ExportCode>Contract.Relational.Role.Borrower</ExportCode>
//					<ImportCode>1</ImportCode>
		'subjectRole'=>(string)$c->Roles->Role->SubjectRole,
//					<LastUpdateSubject>2013-12-09T22:43:15</LastUpdateSubject>
//					<Identification>
//						<IdentificationType>Report.CoDebtorTaxId</IdentificationType>
		'identificationValue'=>(string)$c->Roles->Role->Identification->IdentificationValue,
//					</Identification>
//				</Role>
//			</Roles>
		'creditor'=>(string)$c->Creditor,
		'interesRate'=>(string)$c->InteresRate,
		'lastUpdateContract'=>(string)$c->LastUpdateContract,
		'accountingDate'=>(string)$c->AccountingDate,
		'dateOfSignature'=>(string)$c->DateOfSignature,
                'historicalCalendar'=>(string)$c->HistoricalCalendar,
                'months'=>  array($this->getMonths($c->HistoricalCalendar->Months)),
                'hCTotalNumberOfOverdueInstalments'=>$this->getNumberOfOverdueInstalments($c->HistoricalCalendar->HCTotalNumberOfOverdueInstalments),
                'hCTotalOverdueAmount'=>$this->getNumberOfOverdueInstalments($this->query_attribute($c->HistoricalCalendar, 'months', '-12')->HCTotalOverdueAmount),
                'hCResidualAmount'=>$this->getNumberOfOverdueInstalments($this->query_attribute($c->HistoricalCalendar, 'months', '-12')->HCResidualAmount),
                'hCCreditCardUsedInMonth'=>$this->getNumberOfOverdueInstalments($this->query_attribute($c->HistoricalCalendar, 'months', '-12')->HCCreditCardUsedInMonth),
                
                'months24'=>array($this->getMonths($this->query_attribute($c->HistoricalCalendar,'months', '-24')->Months)),
                'hCTotalNumberOfOverdueInstalments24'=>$this->getNumberOfOverdueInstalments($this->query_attribute($c->HistoricalCalendar, 'months', '-24')->HCTotalNumberOfOverdueInstalments),
                'hCTotalOverdueAmount24'=>$this->getNumberOfOverdueInstalments($this->query_attribute($c->HistoricalCalendar, 'months', '-24')->HCTotalOverdueAmount),
//                'hCTotalOverdueAmount'=>$this->getOverdueAmounts($c->HistoricalCalendar->HCTotalOverdueAmount)
                    );
        }
        if(isset($xml->Report->SearchInquiries->InquiryList->SearchInquiry))
        foreach ($xml->Report->SearchInquiries->InquiryList->SearchInquiry as $si) 
            $this->inquiryList[] = array(
                'date'=> (string)$si->Date,
                'subscriber'=>(string)$si->Subscriber,
                'subscriberType'=>(string)$si->SubscriberType,
                'subscriberCode'=>(string)$si->SubscriberType['code']
            );
        $this->summarySubscriberType = (string)$xml->Report->SearchInquiries->Summary->SubscriberType['type'];
        $this->summarySubscriberCode = (string)$xml->Report->SearchInquiries->Summary->SubscriberType['code'];
        $this->summarySubscriberCount = (string)$xml->Report->SearchInquiries->Summary->SubscriberType['count'];
        $this->numberOfInquiers = (string)$xml->Report->Inquiers->NumberOfInquiers;
        if (isset($xml->Report->Inquiers))
        foreach ($xml->Report->Inquiers->Inquiery as $i) 
            $this->inquiers[] = array(
                'year'=> (string)$i->Year,
                'quarter'=>(string)$i->Quarter,
                'value'=>(string)$i->Value
            );
//                foreach ($character->attributes() as $key => $value) {
//        var_dump($this->identifications);
    }
    public function getMonths($xmlMonths){
        if(!isset($xmlMonths)) return null;
        $months = array();
        $months['description'] = 'месяц/год:'; //(string)$xmlMonths->Description;
        $months['month1'] = (string)$xmlMonths->Month1->Month;
        $months['month2'] = (string)$xmlMonths->Month2->Month;
        $months['month3'] = (string)$xmlMonths->Month3->Month;
        $months['month4'] = (string)$xmlMonths->Month4->Month;
        $months['month5'] = (string)$xmlMonths->Month5->Month;
        $months['month6'] = (string)$xmlMonths->Month6->Month;
        $months['month7'] = (string)$xmlMonths->Month7->Month;
        $months['month8'] = (string)$xmlMonths->Month8->Month;
        $months['month9'] = (string)$xmlMonths->Month9->Month;
        $months['month10'] = (string)$xmlMonths->Month10->Month;
        $months['month11'] = (string)$xmlMonths->Month11->Month;
        $months['month12'] = (string)$xmlMonths->Month12->Month;
        return $months;
    }
    public function getNumberOfOverdueInstalments($xmlData){
        if(!isset($xmlData->Description)) return null;
        $numberOfOverdueInstalments = array();
	$numberOfOverdueInstalments['description']=(string)$xmlData->Description;
        $numberOfOverdueInstalments['month1'] = array('month'=>(string)$xmlData->Month1->Month, 'value'=>(string)$xmlData->Month1->Value);
        $numberOfOverdueInstalments['month2'] = array('month'=>(string)$xmlData->Month2->Month, 'value'=>(string)$xmlData->Month2->Value);
        $numberOfOverdueInstalments['month3'] = array('month'=>(string)$xmlData->Month3->Month, 'value'=>(string)$xmlData->Month3->Value);
        $numberOfOverdueInstalments['month4'] = array('month'=>(string)$xmlData->Month4->Month, 'value'=>(string)$xmlData->Month4->Value);
        $numberOfOverdueInstalments['month5'] = array('month'=>(string)$xmlData->Month5->Month, 'value'=>(string)$xmlData->Month5->Value);
        $numberOfOverdueInstalments['month6'] = array('month'=>(string)$xmlData->Month6->Month, 'value'=>(string)$xmlData->Month6->Value);
        $numberOfOverdueInstalments['month7'] = array('month'=>(string)$xmlData->Month7->Month, 'value'=>(string)$xmlData->Month7->Value);
        $numberOfOverdueInstalments['month8'] = array('month'=>(string)$xmlData->Month8->Month, 'value'=>(string)$xmlData->Month8->Value);
        $numberOfOverdueInstalments['month9'] = array('month'=>(string)$xmlData->Month9->Month, 'value'=>(string)$xmlData->Month9->Value);
        $numberOfOverdueInstalments['month10'] = array('month'=>(string)$xmlData->Month10->Month, 'value'=>(string)$xmlData->Month10->Value);
        $numberOfOverdueInstalments['month11'] = array('month'=>(string)$xmlData->Month11->Month, 'value'=>(string)$xmlData->Month11->Value);
        $numberOfOverdueInstalments['month12'] = array('month'=>(string)$xmlData->Month12->Month, 'value'=>(string)$xmlData->Month12->Value);
        return $numberOfOverdueInstalments;
    }
    private function query_attribute($xmlNode, $attr_name, $attr_value) {
        foreach($xmlNode as $node) { 
          switch($node[$attr_name]) {
            case $attr_value:
              return $node;
          }
        }
    }
    
//    private function getOverdueAmounts($xmlData){
//        $overdueAmounts = array();
//	$overdueAmounts['description']=(string)$xmlData->Description;
//        $overdueAmounts['month1'][] = array('month'=>(string)$xmlData->Month1->Month, 'value'=>(string)$xmlData->Month1->Value);
//			
//				<HCTotalOverdueAmount>
//					<Description>Несплачена прострочена сума платежів</Description>
//					<Month1><Month>1/13</Month><Value>16 997,17</Value></Month1>
//					<Month2><Month>2/13</Month><Value>16 997,17</Value></Month2>
//					<Month3><Month>3/13</Month><Value>16 997,17</Value></Month3>
//					<Month4><Month>4/13</Month><Value>16 997,17</Value></Month4>
//					<Month5><Month>5/13</Month><Value>16 997,17</Value></Month5>
//					<Month6><Month>6/13</Month><Value>16 997,17</Value></Month6>
//					<Month7><Month>7/13</Month><Value>16 997,17</Value></Month7>
//					<Month8><Month>8/13</Month><Value>16 997,17</Value></Month8>
//					<Month9><Month>9/13</Month><Value>16 997,17</Value></Month9>
//					<Month10><Month>10/13</Month><Value>16 997,17</Value></Month10>
//					<Month11><Month>11/13</Month><Value>16 997,17</Value></Month11>
//					<Month12><Month>12/13</Month><Value>16 997,17</Value></Month12>
//				</HCTotalOverdueAmount>
//        
//    }
}
?>
