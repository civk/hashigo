<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HotpepperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hpg_key = config('apikey.hpg-key');
        $shopid="";
        $name="";
        $address="";
        $lat="";
        $lng="";
        $url = "http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=".$hpg_key."&large_area=Z011&id=".$shopid."&name=".$name."&address=".$address."&lat=".$lat."&lng=".$lng."&format=json";
        $json = file_get_contents($url);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        // $json2 = response()->json(['results' => $json]);
        $arr = json_decode($json,true);
        $json = json_encode($arr);
        // return view('hotpepper',compact('arr'));
        return view('example',compact('json'));
        // return view('example',compact('url'));
    }

    public function list(Request $request)
    {
        $json = array();
        $hpg_key = config('apikey.hpg-key');
        $shopid="";
        $name="";
        $address="";
        $lat= $request->lat;
        $lng= $request->lng;
        $url = "http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=".$hpg_key."&lat=".$lat."&lng=".$lng."&range=5&format=json";
        $json = file_get_contents($url);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        // $json2 = response()->json(['results' => $json]);
        $arr = json_decode($json,true);
        // $jsone = json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

        // $jsone = json_encode($arr);
        // return view('hotpepper',compact('arr'));
        // return view('example',compact('json'));
        // return view('example',compact('url'));



        return $arr['results']['shop'];
    }

    public function search(Request $request){
        $word = $request->keyword;
        return $word;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
