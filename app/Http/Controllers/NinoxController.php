<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NinoxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    /*
      Get Information about Database
      Analog to https://api.ninoxdb.de/v1/teams/teamid/databases/databseid/
    */
    public function handleRoot(Request $request){
      return response()->json($this->ninoxApiCall());
    }
    /*
      Get All Tables
      Analog to https://api.ninoxdb.de/v1/teams/:teamid/databases/:databseid/tables
    */
    public function handleTables(Request $request){
      return response()->json($this->ninoxApiCall('/tables'));
    }

    /*
      Get a Tables
      Analog to https://api.ninoxdb.de/v1/teams/:teamid/databases/:databseid/tables/:tableid
    */
    public function handleTable(Request $request,$id = ""){
      $whiteListTables = explode(',',env('PUBLIC_TABLES'));
      if(!sizeof($whiteListTables) || in_array($id,$whiteListTables)){
        return response()->json($this->ninoxApiCall('/tables/'.$id));
      } else {
        return response()->json([]);
      }
    }
    /*
      Get Records by Table
      Analog to https://api.ninoxdb.de/v1/teams/:teamid/databases/:databseid/tables/:tableid/records
    */
    public function handleTablesRecords(Request $request,$id = ""){
      $whiteListTables = explode(',',env('PUBLIC_TABLES'));
      if(!sizeof($whiteListTables) || in_array($id,$whiteListTables)){
        return response()->json($this->ninoxApiCall('/tables/'.$id.'/records'));
      } else {
        return response()->json([]);
      }
    }
    /*
      Get a Single Record by Table
      Analog to https://api.ninoxdb.de/v1/teams/:teamid/databases/:databseid/tables/:tableid/records/:recordid
    */
    public function handleTablesRecordSingle(Request $request,$id = "",$recordId = ""){
      $whiteListTables = explode(',',env('PUBLIC_TABLES'));
      if(!sizeof($whiteListTables) || in_array($id,$whiteListTables)){
        return response()->json($this->ninoxApiCall('/tables/'.$id.'/records/'.$recordId));
      } else {
        return response()->json([]);
      }
    }
    /*
      Call Sub Url of original ninox api
    */
    protected function ninoxApiCall($uri = ""){
      ob_start();
      $ch = curl_init();
      $timeout = 20;
      $url = env('NINOX_API_URL').'teams/'.env('NINOX_TEAM_ID').'/databases/'.env('NINOX_DATABASE_ID').$uri;

      $header = array(
        "content-type: application/json",
        "Authorization: Bearer ".str_replace('Bearer ', '',env('NINOX_API_KEY')),
        "Cache-Control: no-cache"
      );

      curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
      curl_setopt( $ch, CURLOPT_POST, 0);

      curl_setopt( $ch, CURLOPT_HEADER, 0);
      curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );

      curl_setopt( $ch, CURLOPT_URL, $url);
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

      //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
      curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout);
      //curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
      curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
      $data = curl_exec($ch);
      curl_close($ch);
      ob_end_clean();

      $data = json_decode($data,true);

      // filter trough results, remove tables, we don't want to expose
      if($uri == "/tables"){
        $this->clean_recursive($data,null,'id-on-first-level');
      } else {
        $this->clean_recursive($data,null,'sub-value');
      }

      return $data;
    }

    /*
      filter ninox result with whitelist tables
    */
    protected function clean_recursive(array &$array, $path = null, $cleanstrategy = "sub-value") {

      $whiteListTables = explode(',',env('PUBLIC_TABLES'));

      // only show allowed tables publicly
      if(sizeof($whiteListTables) > 0){

        // strategy: look if the Key of a Subarray is NOT in the white list
        if(substr($path, -strlen('/fields')) !== '/fields' && $cleanstrategy == "sub-value" ){
          foreach($whiteListTables as $tableId){
            $foundSomeWhiteListed = false;
            foreach ($array as $k => &$v) {
              if(in_array($k,$whiteListTables)){
                $foundSomeWhiteListed = true;
                break;
              }
            }
            if($foundSomeWhiteListed){
              foreach ($array as $k => &$v) {
                if(!in_array($k,$whiteListTables)) unset($array[$k]);
              }
            }
          }
        }

        // if the id-on-first-level doesnt match white list, remove element
        if(substr_count($path,'/') == 0 && $cleanstrategy == "id-on-first-level" ){
          foreach ($array as $k => &$v) {
            foreach($whiteListTables as $tableId){
              if(!in_array($v['id'],$whiteListTables)) unset($array[$k]);
            }
          }
        }

      }

      // iterate
      foreach ($array as $k => &$v) {
          if (!is_array($v)) {
              // leaf node (file) -- print link
              $fullpath = $path.$v;
          }
          else {
            $this->clean_recursive($v, $path.'/'.$k, $cleanstrategy);
          }
    }
}

}
