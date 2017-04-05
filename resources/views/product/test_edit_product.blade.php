@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Test edit product : 6169</div>

				<div class="panel-body">
					<form onsubmit="return false;" name="frm_edit">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="name" name="name" value="coque sans fil" required/>
						</div>
						<div class="form-group">
							<select class="form-control" name="type" required>
								<option value="2">Type</option>
								@for($i=3; $i<16; $i++)
									<option value="{{$i}}" @if($i == 5) selected="selected" @endif>{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="subtype" required>
								<option value="2">Sub type</option>
								@for($i=3; $i<31; $i++)
									<option value="{{$i}}" @if($i == 8) selected="selected" @endif>{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="material" required>
								<option value="1">Material</option>
								@for($i=2; $i<17; $i++)
									<option value="{{$i}}" @if($i == 5) selected="selected" @endif>{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="brand" required>
								<option value="1">Brand</option>
									<option value="8" selected="selected">8</option>
									<option value="19">19</option>
									<option value="21">21</option>
									<option value="50">50</option>
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="collection" required>
								<option value="1">Collection</option>
								@for($i=2; $i<32; $i++)
									<option value="{{$i}}" @if($i == 4) selected="selected" @endif>{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Supplier name" name="suppliername" value="supp name" required />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Price" name="price" value="1.666660" required />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Price reseller" name="price_reseller" value="6" required />
						</div>
						<div class="form-group">
							<select class="form-control" name="color" required>
								<option value="1">Color</option>
								@for($i=2; $i<29; $i++)
									<option value="{{$i}}" @if($i == 4) selected="selected" @endif>{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="pattern" required>
								<option value="1">Pattern</option>
								@for($i=2; $i<25; $i++)
									<option value="{{$i}}" @if($i == 4) selected="selected" @endif>{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<input type="text" class="form-control" value="675" placeholder="For device" name="fordevice" readonly="readonly" />
						</div>
						<div class="form-group">
							<select class="form-control" name="supplier" required>
								<option value="1">Supplier</option>
								@for($i=2; $i<12; $i++)
									<option value="{{$i}}" @if($i == 4) selected="selected" @endif>{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<input type="submit" class="btn btn-success" id="btn_edit" value="Enregistrer" />
						</div>
						<input class="form-control" type="hidden" name="_token" value="{{ csrf_token() }}">
						<input class="form-control" type="hidden" name="id_product" value="6169">

					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
