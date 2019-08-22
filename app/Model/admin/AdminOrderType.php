<?php 
namespace App\Model; 
use Eloquent;
/**
 * EmailAction Model
 */
class AdminOrderType extends Eloquent {

	public function __construct($params=array()) {
		if (isset($params['table'])) {
			$this->table = $params['table'];
			unset($params['table']);
		}
		if (isset($params['fillable'])) {
			$this->fillable = $params['fillable'];
			unset($params['fillable']);
		}
		if(isset($params['data'])){
			parent::__construct($params['data']);
		}else{
			parent::__construct();
		}
	}
/**
 * The database table used by the model.
 *
 * @var string
 */
	//protected $table = 'order_type';
	
}// end EmailAction class
