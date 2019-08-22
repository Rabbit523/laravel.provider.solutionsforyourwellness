@foreach($data as $row)
@if($row['field_type'] == 1)
<div class="form-group">
	<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{isset($row['field_lable'])?$row['field_lable']:'Title'}}: @if($row['is_required'] == 'Yes'){{"*"}} @endif</label>

	<div class="col-sm-9">
		{{ Form::text("data[".$row['field_name']."]", null, ['placeholder' => $row['field_placeholder'], 'id' => 'master_txt', 'class' => 'col-xs-10 col-sm-5']) }}	
		<p class="text-danger"><?php echo $errors->first($row['field_name']); ?></p>
	</div>
</div>
@elseif($row['field_type'] == 2)
  <?php $options =	json_decode($row['field_options'], true);  ?>
<div class="form-group">
	<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{isset($row['field_lable'])?$row['field_lable']:'Type'}}: @if($row['is_required'] == 'Yes'){{"*"}} @endif </label>

	<div class="col-sm-9">
		<select id="master_select" name="data[{{$row['field_name']}}]" class="col-xs-10 col-sm-5">
			<option value="">{{ trans("Select") }}</option>
			@foreach($options as $key=>$val)
				  <option value="{{ $key }}" @if($val == $row['field_default']){{"selected"}}@endif>{{ $key }}</option>				
			@endforeach 
		</select>		
		<p class="text-danger"><?php echo $errors->first($row['field_name']); ?></p>
	</div>
</div>
@elseif($row['field_type'] == 3)
<div class="form-group">
	<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{isset($row['field_lable'])?$row['field_lable']:'Description'}}: @if($row['is_required'] == 'Yes'){{"*"}} @endif  </label>

	<div class="col-sm-9">
		{{ Form::textarea("data[".$row['field_name']."]", null, ['placeholder' => $row['field_placeholder'], 'class' => 'col-xs-10 col-sm-5']) }}		
		<p class="text-danger"><?php echo $errors->first($row['field_name']); ?></p>
	</div>
</div>
@elseif($row['field_type'] == 4)
<div class="form-group">
	<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{isset($row['field_lable'])?$row['field_lable']:'Image'}}: @if($row['is_required'] == 'Yes'){{"*"}} @endif  </label>

	<div class="col-sm-9">
		{{ Form::file("data[".$row['field_name']."]",['class' => 'col-xs-10 col-sm-5']) }}		
		{{ Form::hidden("file_fields[]",$row['field_name'],['class' => 'col-xs-10 col-sm-5']) }}		
		<p class="text-danger"><?php echo $errors->first($row['field_name']); ?></p>
	</div>
</div>
@endif
<div class="space-4"></div>
@endforeach