<?php

namespace App\Http\Controllers\Cloud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Models\Cloud\Cloud;


class ElasticController extends Controller
{

    public function index(){

    	$cloud=new Cloud;

    	$returnArray=$cloud->getIndex();

    	return view('cloud.elastic.index')->with('data', $returnArray);

    }

    public function getClusterHealth(Request $request){

    	$post=$request->all();

    	$cloud=new Cloud;

    	return response()->json($cloud->getClusterHealthThroughClusterId($post));
    }

    public function getClusterNode(Request $request){

    	$post=$request->all();

    	$cloud=new Cloud;

    	return response()->json($cloud->getClusterNodeDetailsThroughClusterId($post));
    }

    public function getAssignOrNotIndices(Request $request){

    	$post=$request->all();

    	$cloud=new Cloud;

    	return response()->json($cloud->getIndicesAssignOrNotThroughClusterAndNodeId($post));
    }

}
 
?>