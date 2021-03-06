<?php
session_start();
class DashboardController extends AppController {
   // {{{ Properties
   /**
    * Controller access name
    *
    * @access public
    * @var string
    */
   var $name = 'Dashboard';
   // {{{ Properties
   /**
    * Controller access uses
    *
    * @access public
    * @var string
    */
   var $uses = array('Dashboard','Calculation');
   
    public $components = array('Cookie');
   // {{{ method
   /**
    * Controller method Index()
    *
    */
   public function index($args){
     
     extract($args);
     
     $parameters = array();
     $returnValues = array();
     $currentYear = array();
     $lastYearValue = array();
     $lastYearTotalSum = "";
     $lastYearTotalDivider = "";
     
     //Current Year Date
     //$currentYear = date('Y-m-d');
     
     $currentYear['fromdate'] = $inputfromDate;
     $currentYear['todate'] = $inputtoDate;
     
     //Calling the getDatePreviousYearandLastPreviousYear() for GetLast two years from Current date...
     $lastYears = $this->getDatePreviousYearandLastPreviousYear($currentYear);
     
     $currentYear['todate'] = date("Y-m-d", strtotime($currentYear['todate']));
     //print_r($lastYears);
     
     //Get the array count of Years
     $countYears = count($lastYears);
     
     //Array Value for Parameters of tableNAme, fieldName, zipCode, fromDate and toDate
          
     $parameters['fieldName'] = $fieldName;
     $parameters['selectFieldName'] = $selectedFieldName;
     $parameters['tableName'] = $tableName;
     
     if($parameters['fieldName'] == "zip_code_area"){
          $parameters['fieldValue'] = $fieldValue;
     }
     else if($parameters['fieldName'] == "city"){
          $parameters['fieldValue'] = $fieldValue;
     }
     
    for($i=0; $i<$countYears; $i++){
      if($i == 0){
        $parameters['fromDate'] = $lastYears[0];
        $parameters['toDate'] = $currentYear['todate'];
      }
      else{
        $parameters['fromDate'] = $lastYears[1];
        $parameters['toDate'] = $lastYears[0];
      }
      $this->Calculation->setData($parameters);
      $lastYearValue = $this->Calculation->calculateMedian12months();

      if($i == 1){
        //Getting Divider here
        $lastYearTotalDivider = $lastYearValue['Value'];
        $returnValues['previousLastYear'] = $lastYearTotalDivider;
        $returnValues['MAXPreviousYear'] = $lastYearValue['MAXValue'];
        $returnValues['MINPreviousYear'] = $lastYearValue['MINValue'];
      }
      else{
        $returnValues['MAXLastYear'] = $lastYearValue['MAXValue'];
        $returnValues['MINLastYear'] = $lastYearValue['MINValue'];
        $returnValues['lastYear'] = $lastYearValue['Value'];
      }
      $lastYearTotalSum += $lastYearValue['Value'];
    }
    
    $returnValues['totalOfLastTwoYearsData'] = $lastYearTotalSum;
    $returnValues['diffFromLastYear'] = $returnValues['lastYear'] - $returnValues['previousLastYear'];
    $returnValues['avg_of_lastYear_and_previousLastYear'] = $returnValues['diffFromLastYear']/$lastYearTotalDivider;

     return $returnValues;
     $this->autoRender = false;
   }
   
   
   function getDatePreviousYearandLastPreviousYear($currentYear){
     
     //print_r($currentYear);
     extract($currentYear);
      
      
      
     $datetime1 = new DateTime($fromdate);
     $datetime2 = new DateTime($todate);
     //$interval = $datetime1->diff($datetime2);
     //$daysinterval = $interval->format('%a days');
     
     //$daysinterval = round(abs($datetime2->format('U') - $datetime1->format('U')) / (60*60*24));
     //$daysinterval = $daysinterval.' days';
     
     //echo $daysinterval;
     
     $datetime2 = date('Y-m-d', $datetime2);
     
     $lastyear = date('Y-m-d',strtotime($datetime2.'-1 year'));
     
     $lastyear = date('Y-m-d',strtotime($lastyear.'-1 month'));
     
     //echo $lastyear;
     //$lastyear = strtotime("-$daysinterval", strtotime($todate));
     
     // format and display the computed date
     //$lastYear = date("Y-m-d", $lastyear);
      
     //Get Second Prevoius Year from Last Year
      $previouslastyear = date('Y-m-d',strtotime($lastyear.'-1 year'));
      //$previouslastyear = strtotime("-$daysinterval", strtotime($lastYear));
     
     // format and display the computed date
     $previousLastYear = date("Y-m-d", $previouslastyear);
     
     $lastYears = array($lastyear, $previouslastyear);
     
     //print_r($lastYears);
     
     return $lastYears;
   }
   

   function getSameMonthDateOfLastYear($args = null,$zip){
     
     //print_r($ar)
     
     extract($args);
     $finalResult = array();
     
     //Current Year Date
     //$currentYear = date('Y-m-d');
     
     $currentYear['fromdate'] = $inputfromDate;
     $currentYear['todate'] = $inputtoDate;
     
     //Calling the getDatePreviousYearandLastPreviousYear() for GetLast two years from Current date...
     $lastTwoYearsDates = $this->getDatePreviousYearandLastPreviousYear($currentYear);
     
     //print_r($lastTwoYearsDates);
     
     $parameters['fieldName']           = $fieldName;
     $parameters['selectFieldName']     = $selectedFieldName;
     $parameters['tableName']           = $tableName;
     
     //$getMonthOfLastYear = date_parse_from_format('Y-m-d',$lastTwoYearsDates[0]);
     $dateGetMonLY = explode('-',$lastTwoYearsDates[0]);
     $getMonthOfLastYear = array('year' => $dateGetMonLY[0],'month' => $dateGetMonLY[1],'day' => $dateGetMonLY[2]);

     /*$parameters['fieldName']           = 'month_year';
     $parameters['selectFieldName']     = 'sold';
     $parameters['tableName']           = 'tab_median_price_2years';*/
     $parameters['fieldValue']          = $getMonthOfLastYear;
     $parameters['extrafieldValue']     = $zip;
      $parameters['fromDate'] = $inputfromDate;
      $parameters['toDate'] = $inputtoDate;
     
     $this->Calculation->setData($parameters);
     $lastYearValue = $this->Calculation->getSameDateOfLastYear();
     
     //print_r($lastYearValue);
     
     $finalResult['currentYear'] = $lastYearValue[0][0]['CURRENTYEAR'];
     $finalResult['lastYear']    = $lastYearValue[0][0]['LASTYEAR'];
     $finalResult['difference']  = $lastYearValue[0][0]['DIFFERENCE'];
     $finalResult['changes']     = $lastYearValue[0][0]['DIFFERENCE']/$lastYearValue[0][0]['CURRENTYEAR'];
     
     return $finalResult;
     
     $this->autoRender = false;
     
   }
   
   
   function getJsonFormat(){
     
     //Hide the notice from the result page.
     //error_reporting(E_ALL ^ E_NOTICE);
     //error_reporting(0);
     
     //print_r($this->params['url']);
     
     $fromDate = $_POST['fromdate'];
     $toDate   = $_POST['todate'];
     
     $months = (int)abs((strtotime($fromDate) - strtotime($toDate))/(60*60*24*30));
     
     //$city   = $_POST['city'];
     //$state   = $_POST['state'];
     $zip   = $_POST['zip'];
     
     $zip = trim($zip);
     
     $_SESSION['zip'] = $zip;

     $args = array();
     $finalInputToJson = array();
     
     $resultDb = $this->Dashboard->getDashboardData($zip);
     
     //print_r($resultDb);
     //exit;
     $finalInputToJson['saleMedianZip'] = array("lastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['median_price_avg_last_six_months'],
                                                "previousLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['median_price_avg_previous_six_months'],
                                                "MAXLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['median_max'],
                                                "avg_of_lastYear_and_previousLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['median_change_percent'],
                                                "MINLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['median_min']);

     $finalInputToJson['saleMedianCity'] =  array("lastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['san_antonio_median_last_six_months'],
                                                "previousLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['san_antonio_median_previous_six_months'],
                                                "MAXLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['san_antonio_max_median'],
                                                "avg_of_lastYear_and_previousLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['SA_median_change_persent'],
                                                "MINLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['san_antonio_min_median']);

     $finalInputToJson['soldSqft'] = array("lastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['avg_ft_last_six_month'],
                                                "previousLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['avg_ft_previous_six_month'],
                                                "MAXLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['san_antonio_max_median'],
                                                "avg_of_lastYear_and_previousLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['SA_median_change_persent'],
                                                "MINLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['san_antonio_min_median']);
     
     $finalInputToJson['soldDifferenceWithLastYearAndCurrentYear'] = array("currentYear" => $resultDb[0]['tab_tmp_dashboard_fields']['sold_homes'],
                                                                           "lastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['sold_homes_same_period_last_yr'],
                                                                           );
     
     $finalInputToJson['avgDifferenceWithLastYearAndCurrentYear'] = array("currentYear" => $resultDb[0]['tab_tmp_dashboard_fields']['avg_days_on_market'],
                                                                           "lastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['avg_days_on_market_same_period_last_yr'],
                                                                           );
     
     $finalInputToJson['soldAvgSqft'] = array("lastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['avg_home_size_last_six_months'],
                                                "previousLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['avg_ft_previous_six_month'],
                                                "MAXLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['san_antonio_max_median'],
                                                "avg_of_lastYear_and_previousLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['SA_median_change_persent'],
                                                "MINLastYear" => $resultDb[0]['tab_tmp_dashboard_fields']['san_antonio_min_median']);
     
     $args['selectFieldName'] = 'solds';
     $args['tableName']         = 'tab_tmp_chart_dashboard';
     $args['fieldName']         = 'price_per_sqft_over_time';
     
     $this->Calculation->setData($args);
     
     $finalInputToJson['groupByMonthAndYearForMedian'] = $this->Calculation->groupBymonthWiseWithDifferentYears($zip);

     echo json_encode($finalInputToJson);
     $this->autoRender = false;
   }
   
   
     // {{{ method
     /**
     * Controller method dashboardImport()
     *
     */
     
     public function dashboard(){
      
      $zipcode = $_REQUEST['zip'];
      
      $currentDate = date('Y-m-d');
      $lastyear = strtotime("-1 year", strtotime($currentDate));
      $lastYear = date("m/d/Y", $lastyear);
      $this->set('lastYear', $lastYear);
      
      $this->set('youtube_data',$this->Dashboard->getFieldDatas('youtube_data',$zipcode));
      $this->set('dashboardData',$this->Dashboard->getFieldDatas('text_message',$zipcode));
      $this->set('rssFieldData_left',$this->Dashboard->getFieldDatas('rss_field_left',$zipcode));
      $this->set('rssFieldData_right',$this->Dashboard->getFieldDatas('rss_feed_right',$zipcode));
     
      
      $this->set('zipCode',$this->Dashboard->getDataFromDB('zip_code_area','tab_median_price_2years','normalType'));
      $this->set('dateRangeTo',$this->Dashboard->getDataFromDB('month_year','tab_median_price_2years','dateType'));
      $this->set('dateRangeFrom',$this->Dashboard->getDataFromDB('month_year','tab_median_price_2years','dateType'));
      $this->render('dashboard');
     }
   
   
     public function parseRSS($url, $xml)
     {
         echo '<a style="color:red" href="'.$url.'">'.$xml->channel->title.'</a>';
         $cnt = count($xml->channel->item);
         for($i=0; $i<$cnt; $i++)
         {
             $url 	= $xml->channel->item[$i]->link;
             $title 	= $xml->channel->item[$i]->title;
             $desc = $xml->channel->item[$i]->description;
      
            //echo '<a href="'.$url.'">'.$title.'</a>'.$desc.'</br>';
         }
     }
     
     public function parseAtom($url, $xml)
     {
         echo '<a style="color:red" href="'.$url.'">'.$xml->author->name.'</a>';
         $cnt = count($xml->entry);
         for($i=0; $i<$cnt; $i++)
         {
             $urlAtt = $xml->entry->link[$i]->attributes();
             $url	= $urlAtt['href'];
             $title 	= $xml->entry->title;
             $desc	= strip_tags($xml->entry->content);
      
             //echo '<a href="'.$url.'">'.$title.'</a>'.$desc.'</br>';
         }
     }
   
     public function readingRss($url = null){
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          
          $data = curl_exec($ch);
          curl_close($ch);
          
          $doc = new SimpleXmlElement($data, LIBXML_NOCDATA);
          
          if(isset($doc->channel))
          {
               return $this->parseRSS($url,$doc);
          }
          if(isset($doc->entry))
          {
               return $this->parseAtom($url,$doc);
          }
     }
     
     public function getZipArea(){
      $zipValue = $_REQUEST['zipValue'];
      echo $this->Dashboard->getZipCodeAreaName($zipValue);exit;
     }
     
     public function clearDashboard($bools = null){
      if($bools == true){
        $this->Dashboard->truncateDashboard();  
      }
      else{
        
      }
     }
     
     public function getMinAndMaxDate(){
      $typeDate = $_REQUEST['typeDate'];
      $ts = date('Y-m-d');
      $lastday = date('t',strtotime($ts));
      $thismonthLastDay = date('Y')."-".date('m')."-".$lastday."<br/>";
      $sixMonthFirstDay = date('Y-m-d',strtotime("$ts -6 month"))."<br/>";
      $oneyearFirstDay = date('Y-m-d',strtotime("$ts -12 month"))."<br/>";
      
      //date("m/d/Y", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
      
      
      $str = $this->Dashboard->getDateMM($typeDate, $thismonthLastDay, $sixMonthFirstDay, $oneyearFirstDay);
      
      $str = explode("--",$str);
      $strTo = explode("/",$str[0]);
      $strToMonth = $strTo[0];
      $strToYear = $strTo[2];
      
      $strFrom = explode("/",$str[1]);
      $strFromMonth = $strFrom[0];
      $strFromYear = $strFrom[2];
      
      echo $strToMonth."-".$strToYear."--".$strFromMonth."-".$strFromYear."--".$str[0]."--".$str[1];
      
     $this->autoRender = false;
     }
}
?>

