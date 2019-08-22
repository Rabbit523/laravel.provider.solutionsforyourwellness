<?php
	namespace App\Http\Controllers\admin;
	//use Illuminate\Http\Request;
	use App\Http\Controllers\BaseController;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\AdminMaster;	
	use App\Model\AdminOrderType;	
	use App\Model\AdminFieldTypes;	
	use Storage;
	use League\Flysystem\Filesystem;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	//use Illuminate\Routing\Controller as BaseController;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class AdminMasterController extends BaseController {		
		
		
		/**
		* Function for display admin view all master fields
		*
		* @param null
		*
		* @return view page. 
		*/			
			public function viewMaster(){
				$fieldsdata = AdminMaster::get();
				return  View::make('admin.fields.view_master_fields',['fieldsdata'=>$fieldsdata,'key'=>'master']);
			}

		 /**
		 * Function for save images and description  for Blog
		 *
		 * @param null
		 *
		 * @return redirect page. 
		 */
		 
			public function addMaster(){
				Input::replace($this->arrayStripTags(Input::all()));
				if(Input::isMethod('post')){
					if(Input::get('is_required') == 'Yes'){
						$rules = array(
						'key_type'        => 'required',
						'field_type'      => 'required',			       
						'field_name'      => 'required',
						'required_rules'  => 'required',
						);
					}else{
						$rules = array(
						'key_type'        => 'required',
						'field_lable'     => 'required',			       
						'field_type'      => 'required',			       
						'field_name'      => 'required',
						);
					}
					$validator = Validator::make(Input::all(),$rules);
					 if ($validator->fails()) {
						$messages = $validator->messages();
						return Redirect::route('addmaster')
			            ->withErrors($validator)->withInput();	
					  } else {
							$master						=	new AdminMaster;
							$master->key_type			=	Input::get('key_type');
							$master->field_lable		=	Input::get('field_lable');
							$master->field_type			=	Input::get('field_type');
							$master->field_name			=	Input::get('field_name');
							$master->field_placeholder	=	Input::get('field_placeholder');
							$master->field_options		=	Input::get('field_options');
							$master->is_required		=	Input::get('is_required');
							$master->required_rules		=	Input::get('required_rules');
							$master->field_default		=	Input::get('field_default');
							$master->order_by			=	Input::get('order_by');
							$master->save();
							Session::flash('flash_success',  trans("Fields inserted successfully.")); 
							return Redirect::route('viewmaster');
					}
				}else{
					$data = AdminFieldTypes::where('status','Active')->orderBy('id','ASC')->get();
					return  View::make('admin.fields.add_master_fields',['fieldsType' => $data,'key'=>'master']);
				} 
			}			
			
		/**
		* Function for display admin view all genrate fields
		*
		* @param null
		*
		* @return view page. 
		*/			
			public function genrateFields($data){
				if($data['type_id']	==	1){
					return  View::make('admin.fields.textfield',['textdata'=>$data]);
				} else if($data['type_id']	==	2){
					return  View::make('admin.fields.selectbox',$data);
				} else if($data['type_id']	==	3){
					return  View::make('admin.fields.textarea',$data);
				}
			}	
			
		/**
		* Function for display admin Add field data
		*
		* @param null
		*
		* @return view page. 
		*/			
			public function add($key = ''){
				Input::replace($this->arrayStripTags(Input::all()));
				if(Input::isMethod('post')){						
					$requiredFields = AdminMaster::select('field_name','required_rules')->where('is_required','Yes')->where('key_type',$key)->where('status','Active')->orderBy('order_by','ASC')->get()->toArray();
					$rules = array_column($requiredFields, 'required_rules', 'field_name');	
					$validator = Validator::make(Input::get('data'),$rules);
					if ($validator->fails()) {				
						$messages = $validator->messages();
						return Redirect::back()
						->withErrors($validator)->withInput(Input::except('file_fields'));	
					  } else {
							$data 					=	Input::get('data');							
							$file_fields 			=	Input::get('file_fields');
							$fillable				=	array_keys($data);
							
							if(!empty($file_fields)){
								$this->processImage($file_fields, $data);
							}
							$order	=	new AdminOrderType(['table'=>$key,'fillable'=>$fillable,'data'=>$data]);
							$order->save();
							Session::flash('flash_success', trans("Fields inserted successfully.")); 
							return Redirect::route('viewfields',$key);
					}
				}else{
					$data = AdminMaster::where('key_type',$key)->where('status','Active')->orderBy('order_by','ASC')->get()->toArray();
					if(!empty($data)){
						return  View::make('admin.fields.add_form_field',['data'=>$data,'key'=>$key]);
					}else{
						Session::flash('flash_error',  trans("Invalid url! Please try again...")); 
						return Redirect::route('viewfields',$key);
					}
				}
			}
			
			
			/**
			* Function for display admin Edit field data
			*
			* @param null
			*
			* @return view page. 
			*/			
			public function edit($key = ''){
				$id	= Input::get('id');
				Input::replace($this->arrayStripTags(Input::all()));
				if(Input::isMethod('post')){	
					$field_id	= Input::get('field_id');
					$requiredFields = AdminMaster::select('field_name','required_rules')->where('is_required','Yes')->where('key_type',$key)->where('status','Active')->orderBy('order_by','ASC')->get()->toArray();
					$rules = array_column($requiredFields, 'required_rules', 'field_name');					
					$validator = Validator::make(Input::get('data'),$rules);
					/* if ($validator->fails()) {
						$messages = $validator->messages();
						return Redirect::back()
						->withErrors($validator)->withInput();	
					  } else {  */
							$data 					=	Input::get('data');
							$file_fields 			=	Input::get('file_fields');
							$fillable				=	array_keys($data);
							if(!empty($file_fields)){
								$this->processImage($file_fields, $data);
							}							
							$order		=	new AdminOrderType(['table'=>$key,'fillable'=>$fillable,'data'=>$data]);
							$order->where('id', $field_id)->update($data);
							Session::flash('flash_success',  trans("Records updated successfully.")); 
							return Redirect::route('viewfields',$key);
					//}
				}else{
					$data 	= 	AdminMaster::where('key_type',$key)->where('status','Active')->orderBy('order_by','ASC')->get()->toArray();
					$obj		= 	new AdminOrderType(['table'=>$key]);
					$fieldsData	= 	$obj->get()->where('id',$id)->first()->toArray();
					$fields		=	$this->merge_column_values($data, $fieldsData);
					
					if(!empty($fields)){
						return  View::make('admin.fields.edit_form_field',['data'=>$fields,'key'=>$key,'field_id'=>$id]);
					}else{
						Session::flash('flash_error',  trans("Invalid url! Please try again...")); 
						return Redirect::route('viewfields',$key);
					}
				}
			}
			
			/**
			* Function for merging columns and values to display on edit page
			*
			* @param null
			*
			* @return view page. 
			*/	
			function merge_column_values($fields=array(), $data=array()){
				if(!empty($data)){
					foreach($fields as &$field){
						if(isset($field['field_name']) && isset($data[$field['field_name']])){
							$field['field_value']	=	$data[$field['field_name']];
						}else{
							$field['field_value']	=	'';
						}
					}
				}
				return $fields;
			}
			
			/**
			* Function for display admin delete field data
			*
			* @param null
			*
			* @return view page. 
			*/			
			public function delete($key = ''){	
				if($key == ''){
					$key = Input::get('key_val');
				}
				$id	= Input::get('id');
				if($id && $key){
					$obj	= new AdminOrderType(['table'=>$key]);
					$obj->where('id',$id)->delete();		
					Session::flash('flash_success', trans("Row successfully deleted."));
					return Redirect::route('viewfields',$key);		
				}else if($key){
					$checkboxdata = Input::get('chk_ids');
					 if(!empty($checkboxdata)){
							$obj	= new AdminOrderType(['table'=>$key]);
							$obj->whereIn('id', $checkboxdata)->delete();
							Session::flash('flash_success', trans("Records successfully deleted."));
							return Redirect::route('viewfields',$key);
					 }else{
						 Session::flash('flash_error',  trans("Invalid url! Please try again...")); 
						return Redirect::route('viewfields',$key);
					 }
				} else{
					Session::flash('flash_error',  trans("Invalid url! Please try again...")); 
					return Redirect::route('viewfields',$key);
				}
			}
			
			/**
			* Function for display admin all field data
			*
			* @param null
			*
			* @return view page. 
			*/			
			public function view($key = ''){				
				$fieldsColumn = AdminMaster::select('field_lable')->where('key_type',$key)->where('status','Active')->orderBy('order_by','ASC')->get();
				$obj	= new AdminOrderType(['table'=>$key]);
				$fieldsData	= $obj->orderBy('id','DESC')->get()->toArray();
				return  View::make('admin.fields.view_form_fields',['fieldsColumn'=>$fieldsColumn,'fieldsData'=>$fieldsData,'key'=>$key]);				
			}
			
			/**
			* Function for Array Index change for ajax list
			*
			* @param array
			*
			* @return view page. 
			*/	
			
			function array_flatten($array) { 
				  if (!is_array($array)) { 
					return FALSE; 
				  } 
				  $result = array(); 
				  foreach ($array as $key => $value) { 
					if (is_array($value)) { 
					  $result = array_merge($result, array_flatten($value)); 
					} 
					else { 
					  $result[$key] = $value; 
					} 
				  } 
				  return $result; 
			} 
			
			/**
			* Function for display admin all field data with ajax
			*
			* @param null
			*
			* @return view page. 
			*/		
			public function ajax_view($key = ''){		
				$start 	= Input::get('start');
				$length = Input::get('length');
				$search = Input::get('search.value');
				$totalData	= $this->count_data($key,$search);
				$fieldsColumn = AdminMaster::select('field_lable')->where('key_type',$key)->where('status','Active')->orderBy('order_by','ASC')->get()->toArray();
				$columnsName = AdminMaster::select('field_name')->where('key_type',$key)->where('status','Active')->orderBy('order_by','ASC')->get()->toArray();
				$allColumns = $this->array_flatten($columnsName);	
				$obj	= new AdminOrderType(['table'=>$key]);
				if($search != null){	
					$fieldsData 		= 	$obj->orWhere(function($obj) use ($allColumns, $search){
												foreach($allColumns as $column){
													$obj->orWhere($column, 'LIKE', '%'.$search.'%');
												}
												$obj->orWhere('status', 'LIKE', '%'.$search.'%');
												$obj->orWhere('created_at', 'LIKE', '%'.$search.'%');
												$obj->orWhere('updated_at', 'LIKE', '%'.$search.'%');
											})->orderBy('id','DESC')->limit($length)->offset($start)->get()->toArray();
				}else{
					$fieldsData	= $obj->orderBy('id','DESC')->limit($length)->offset($start)->get()->toArray();
				}      				
				return  View::make('admin.fields.ajax_load_data', ['fieldsColumn'=>$fieldsColumn,'fieldsData'=>$fieldsData,'key'=>$key,'total'=>$totalData]);				
			}
			
			/**
			* Function for count number of records
			*
			* @param null
			*
			* @return view page. 
			*/			
			public function count_data($key = '', $search = ''){	
				$columnsName = AdminMaster::select('field_name')->where('key_type',$key)->where('status','Active')->orderBy('field_type','ASC')->get()->toArray();
				$allColumns = $this->array_flatten($columnsName);				
				$obj	= new AdminOrderType(['table'=>$key]);
				if($search != null){		
					$totalData 		= 	$obj->orWhere(function($obj) use ($allColumns, $search){
												foreach($allColumns as $column){
													$obj->orWhere($column, 'LIKE', '%'.$search.'%');
												}
												$obj->orWhere('status', 'LIKE', '%'.$search.'%');
												$obj->orWhere('created_at', 'LIKE', '%'.$search.'%');
												$obj->orWhere('updated_at', 'LIKE', '%'.$search.'%');
											})->get()->count();
				}else{
					$totalData	=  $obj->get()->count();
				}
				return $totalData;				
			}
			
			/**
			* Function for display admin Active & Inactive field data
			*
			* @param null
			*
			* @return view page. 
			*/	
			public function activeInactive($key = ''){
				$id	= Input::get('id');
				$obj	= new AdminOrderType(['table'=>$key]);
				$data	= $obj->where('id',$id)->first();
				if($data->status == 'Active'){
					$obj->where('id',$id)->update(['status' => 'Inactive']);
					Session::flash('flash_success', trans("Record successfully deactivated."));
					return Redirect::route('viewfields',$key);
				}else{
					$obj->where('id',$id)->update(['status' => 'Active']);
					Session::flash('flash_success', trans("Record successfully activated."));
					return Redirect::route('viewfields',$key);
				}
			}
			
			/**
			 * Return View file
			 *
			 * @var array
			 */
			public function importExport()
			{
				return  View::make('admin.fields.importExport');
			}
			
			/**
			 * File Export Code
			 *
			 * @var array
			 */
			public function downloadExcel(Request $request, $key='')
			{
				$type	= Input::get('type');
				$obj	= new AdminOrderType(['table'=>$key]);
				$data	= $obj->orderBy('id','DESC')->get()->toArray();
				return Excel::create($key.'_data', function($excel) use ($data) {
					$excel->sheet('mySheet', function($sheet) use ($data)
					{
						$sheet->fromArray($data);
					});
				})->download($type);
			}
			
			/**
			 * Import file into database Code
			 *
			 * @var array
			 */
			public function importExcel(Request $request)
			{
				$key_type = Input::get('key');			
				$columnsName = AdminMaster::select('field_name')->where('key_type',$key_type)->where('status','Active')->orderBy('field_type','ASC')->get()->toArray();
				$allColumns = $this->array_flatten($columnsName);
				if($request->hasFile('import_file')){
					$path = $request->file('import_file')->getRealPath();
					$data = Excel::load($path, function($reader) {})->get();					
					if(!empty($data) && $data->count()){
						foreach ($data->toArray() as $key => $value) {	
							$order	=	new AdminOrderType(['table'=>$key_type,'fillable'=>$allColumns,'data'=>$value]);
							$order->save();
						}
				 	}
					return back()->with('flash_success',trans('Records inserted successfully.'));
				}
				return back()->with('flash_error',trans('Please Check your file, Something is wrong there.'));
			}

}