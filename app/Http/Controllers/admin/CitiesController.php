<?php

namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\admin\CitiesModel;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use ZipArchive,DateTime;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Toast,Image,Zipper;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CitiesController extends BaseController
{
	/**
    * Function for show all cities list.
    *
    * @param null.
    *
    * @return cities list page.
    */	
	public function index(){
			$cities = CitiesModel::orderBy('id','desc')->get();
			return  View::make('admin.cities.index',compact('cities'));
	}
	/**
    * Function for add city.
    *
    * @param null.
    *
    * @return response.
    */	
	public function add(){
		Input::replace($this->arrayStripTags(Input::all()));
		if(Input::isMethod('post')){
		  $rules = array(
			'city_name'    	    => 'required',
		  );
		$validator = Validator::make(Input::all(),$rules);
		  if ($validator->fails()){
			  $messages = $validator->messages();
			 return Redirect::back()->withErrors($validator)->withInput();
		  }else{
			  $savecity 	= 	CitiesModel::SaveCity();
			  Toast::success('City successfully added');
			  return redirect()->route('cities');
			}
	  }else{
		  return View::make('admin.cities.add');
		 }
	 }
 /**
  * Function to edit certificates
  *
  * @param user id (default null )
  *
  * @return certificates list page
  */
  public function edit($id=0){
   if(Input::isMethod('post')){
      $rules = array(
        'city_name'  		           => 'required',
      );
	 $validator = Validator::make(Input::all(),$rules);
     if ($validator->fails()) {
      $messages = $validator->messages();
		return Redirect::back()->withErrors($validator)->withInput();
      } else {
				$updatecity = CitiesModel::UpdateCity($id);    // calls function for update city.
				if($updatecity){
				   Toast::success('City successfully updated');
				  return redirect()->route('cities');
				}else{
				   Toast::error('Technical error');
				  return redirect()->route('cities');
				}
          }
     }else{
            $city = CitiesModel::where('id',$id)->first();
            return view('admin.cities.edit', compact('city'));
     }
  }
	/**
     * Function for delete cities
     *
     * @param city id
     *
     * @return response true on success otherwise false.
     */
     public function delete($id = ''){
       if($id){
         // Delete row
         CitiesModel::where('id',$id)->delete();
         Toast::success('City successfully deleted');
         return Redirect::back();
       }else{
			  $checkboxdata = Input::get('chk_ids');
			  CitiesModel::whereIn('id', $checkboxdata)->delete();
			  Toast::success('Cities successfully deleted');
			  return Redirect::back();
          }
     }
	  /**
     * Function for show cities with ajax pagination
     *
     * @param null
     *
     * @return response ajax load data page.
     */	
      public function ajaxcities(){
        $columns 				= 	Input::get('columns');
        $length					= 	Input::get('length');
        $start					= 	Input::get('start');
        $total_data 			= 	CitiesModel::count();
        $total_filtered_data	=	$total_data;
        $order					=	Input::get('order');
        $column_id				=	$order[0]['column'];
        $column_order			=	$order[0]['dir'];
        $search					= 	Input::get('search');
		$search					=	$search['value'];
        if($search != null){
			$cities				=	CitiesModel::GetCities($search,$start,$length,$column_id,$column_order);   // calls function for getting users data.
		}else{
			$cities				=	CitiesModel::GetCities($search="",$start,$length,$column_id,$column_order);
		}
		$table_data				=	array();
		return view('admin.cities.indexajax',compact('cities','total_data','total_filtered_data'));
      }
}
