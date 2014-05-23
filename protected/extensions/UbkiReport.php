<?php
class UbkiReport {
    public $auth_hist = array();
    public $doc_hist = array();
    public $contact_hist = array();
    public $contacts = array();
    public $credits = array();
//    public $payments = array();
    public function __construct ($xml){
        $this->ruLName=(string)$xml->r[1]->LST['ruLName'];
        $this->ruFName=(string)$xml->r[1]->LST['ruFName'];
        $this->ruMName=(string)$xml->r[1]->LST['ruMName'];
        $this->uaLName=(string)$xml->r[1]->LST['uaLName'];
        $this->uaFName=(string)$xml->r[1]->LST['uaFName'];
        $this->uaMName=(string)$xml->r[1]->LST['uaMName'];
        $this->okpo=(string)$xml->r[1]->LST['OKPO'];
        $this->familySt=(string)$xml->r[1]->LST['FamilySt'];
        $this->db=(string)$xml->r[1]->LST['DB'];
        $this->dser=(string)$xml->r[1]->LST['DSer'];
        $this->dnum=(string)$xml->r[1]->LST['DNum'];
        $this->sex=(string)$xml->r[1]->LST['Sex'];
        $this->dds=(string)$xml->r[1]->LST['DDS'];
        $this->clDate=(string)$xml->r[1]->LST['CLDATE'];
        $this->wokpo=(string)$xml->r[1]->LST['WOKPO'];
        $this->clWName=(string)$xml->r[1]->LST['clWName'];
        $this->clWDate=(string)$xml->r[1]->LST['clWDate'];
        $this->address1=(string)$xml->r[1]->LST['Address1'];
        $this->address2=(string)$xml->r[1]->LST['Address2'];
        $this->address3=(string)$xml->r[1]->LST['Address3'];
        if (isset($xml->r[2]))
        foreach ($xml->r[2] as $ah) 
            $this->auth_hist[] = array(
                'clDate'=>(string)$ah['CLDATE'],
                'db'=>(string)$ah['DB'],
                'okpo'=>(string)$ah['OKPO'],
                'familySt'=>(string)$ah['FamilySt'],
                'sex'=>(string)$ah['Sex'],
                'uaLName'=>(string)$ah['uaLName'],
                'uaFName'=>(string)$ah['uaFName'],
                'uaMName'=>(string)$ah['uaMName'],
                'fioEn'=>(string)$ah['FIOEn'],
                'ruLName'=>(string)$ah['ruLName'],
                'ruFName'=>(string)$ah['ruFName'],
                'ruMName'=>(string)$ah['ruMName'],
                'wokpo'=>(string)$ah['WOKPO'],
                'clWName'=>(string)$ah['clWName'],
                'clWDate'=>(string)$ah['clWDate']);
        if (isset($xml->r[3]))
        foreach ($xml->r[3] as $dh) 
            $this->doc_hist[] = array(
                'dtm'=>(string)$dh['DTM'], // date
                'dds'=>(string)$dh['DDS'], // issue date
                'dser'=>(string)$dh['DSer'], // doc serie
                'dnum'=>(string)$dh['DNum'], // doc number
                'dwho'=>(string)$dh['DWho'], // who doc issue
                );
        if (isset($xml->r[4]))
        foreach ($xml->r[4] as $ch) 
            $this->contact_hist[] = array(
                'type'=>(string)$ch['Type'], 
                'date'=>(string)$ch['DTM'],
                'address'=>(string)$ch['Address'],
                );
        if (isset($xml->r[5]))
        foreach ($xml->r[5] as $c) 
            $this->contacts[] = array(
                'type'=>(string)$c['Type'], 
                'versionDate'=>(string)$c['VersionDate'],
                'number'=>(string)$c['Number'],
                );
        if (isset($xml->r[6]))
        foreach ($xml->r[6] as $cr) 
            $this->credits[] = array(
                'reference'=>(string)$cr['Reference'], 
                'startDate'=>(string)$cr['DS'],
                'stopDate'=>(string)$cr['DE'],
                'creditType'=>(string)$cr['CR_Type'],
                'creditTypeName'=>$this->creditTypeCode2Name((string)$cr['CR_Type']),
                'currencyCode'=>((string)$cr['Curr']=='980'? 'UAH':((string)$cr['Curr']=='840'? 'USD':(string)$cr['Curr'])),
                'crSetAmount'=>(string)$cr['crSetAmount'],
                'amount'=>(string)$cr['Amount'],
                'flClose'=>(string)$cr['FlClose'],
                'amtCurr'=>(string)$cr['AmtCurr'],
                'amtExp'=>(string)$cr['AmtExp'],
                'daysExp'=>(string)$cr['DaysExp'],
                'dateCalc'=>(string)$cr['DateCalc'],
                'nBreak1'=>(string)$cr['NBreak1'],
                'nBreak2'=>(string)$cr['NBreak2'],
                'nBreak3'=>(string)$cr['NBreak3'],
                'nBreak4'=>(string)$cr['NBreak4'],
                'nBreak5'=>(string)$cr['NBreak5'],
                'donor'=>(string)$cr['Donor'],
                'payments'=>$this->getPayments($xml->r[7],(string)$cr['Reference'],(string)$cr['crSetAmount'])
                );
            $this->rating = array(
                'scoreinn'=>(string)$xml->r[8]->URATING['scoreinn'],
                'scorerlname'=>(string)$xml->r[8]->URATING['scorerlname'],
                'scorerfname'=>(string)$xml->r[8]->URATING['scorerfname'],
                'scorermname'=>(string)$xml->r[8]->URATING['scorermname'],
                'scoreulname'=>(string)$xml->r[8]->URATING['scoreulname'],
                'scoreufname'=>(string)$xml->r[8]->URATING['scoreufname'],
                'scoreumname'=>(string)$xml->r[8]->URATING['scoreumname'],
                'scorebdate'=>(string)$xml->r[8]->URATING['scorebdate'],
                'score'=>(string)$xml->r[8]->URATING['score'],
                'scorelast'=>(string)$xml->r[8]->URATING['scorelast'],
                'scoredate'=>(string)$xml->r[8]->URATING['scoredate'],
                'scoredatelast'=>(string)$xml->r[8]->URATING['scoredatelast'],
                'scorelevel'=>(string)$xml->r[8]->URATING['scorelevel'],
                'scoredelta'=>(string)$xml->r[8]->URATING['scoredelta'],
                'scoretendency'=>(string)$xml->r[8]->URATING['scoretendency'],
            );
            $this->query_register = array(
                'hr'=>(string)$xml->r[9]->ZINT['hr'],
                'da'=>(string)$xml->r[9]->ZINT['da'],
                'wk'=>(string)$xml->r[9]->ZINT['wk'],
                'mn'=>(string)$xml->r[9]->ZINT['mn'],
                'qw'=>(string)$xml->r[9]->ZINT['qw'],
                'ye'=>(string)$xml->r[9]->ZINT['ye'],
                'yu'=>(string)$xml->r[9]->ZINT['yu'],
            );
        if (isset($xml->r[10])){
            if($xml->r[10]->ZINTHIS['errcode']=='nozinthis')
                $this->query_hist = array();
            else
                foreach ($xml->r[10] as $qh) 
                    $this->query_hist[] = array(
                        'reqID'=>(string)$qh['ReqID'], 
                        'reqDateTime'=>(string)$qh['ReqDateTime'],
                        'reqType'=>(string)$qh['ReqType'],
                        'partnerType'=>(string)$qh['PartnerType'],
                        );
        }
        $this->photo = (string)$xml->r[13]->CL_FOTO['fotobase64'];
    }  
    private function getPayments($xml_in, $creditName, $crSetAmount){
        $payments = array();
        $ps =$xml_in->xpath('//CL_DEAL[@Reference="'.$creditName.'"]');
        foreach ($ps as $v) 
            $payments[] = array(
                'year'=>(string)$v['Year'],
                'month'=>((string)$v['Month']<='9'?'0'.(string)$v['Month']:(string)$v['Month']),
                'reference'=>(string)$v['Reference'],
                'flPay'=>(string)$v['FlPay'],
                'flBreak'=>(string)$v['FlBreak'],
                'flClose'=>(string)$v['FlClose'],
                'flUse'=>(string)$v['FlUse'],
                'amtCurr'=>(string)$v['AmtCurr'],
                'amtExp'=>(string)$v['AmtExp'],
                'daysExp'=>(string)$v['DaysExp'],
                'crSetAmount'=>$crSetAmount,
            );      
        return $payments;
    }

    private function creditTypeCode2Name($code){
        $creditTypes = array('Кредитный договор на другие потребительские цели',
        'Обеспеченная ссуда',
        'Необеспеченная ссуда',
        'Ипотечный кредит',
        'Автокредит',
        'Кредитная карта',
        'Товары в кредит');
        return $creditTypes[(int)$code];
    }
//    private function query_attribute($xmlNode, $attr_name, $attr_value) {
//        foreach($xmlNode as $node) { 
//          switch($node[$attr_name]) {
//            case $attr_value:
//              return $node;
//          }
//        }
//    }

}
?>
