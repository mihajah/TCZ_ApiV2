@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Test add product</div>

				<div class="panel-body">
					<form method="POST" action="<?php echo url('products')?>">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="name" name="name" required/>
						</div>
						<div class="form-group">
							<select class="form-control" name="type" required>
								<option value="2">Type</option>
								@for($i=3; $i<16; $i++)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="subtype" required>
								<option value="2">Sub type</option>
								@for($i=3; $i<31; $i++)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="material" required>
								<option value="1">Material</option>
								@for($i=2; $i<17; $i++)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="brand" required>
								<option value="1">Brand</option>
									<option value="8">8</option>
									<option value="19">19</option>
									<option value="21">21</option>
									<option value="50">50</option>
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="collection" required>
								<option value="1">Collection</option>
								@for($i=2; $i<32; $i++)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Supplier name" name="suppliername" required />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Price" name="price" required />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Price reseller" name="price_reseller" required />
						</div>
						<div class="form-group">
							<select class="form-control" name="color" required>
								<option value="1">Color</option>
								@for($i=2; $i<29; $i++)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="pattern" required>
								<option value="1">Pattern</option>
								@for($i=2; $i<25; $i++)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<input type="text" class="form-control" value="475;476;67" placeholder="For device" name="fordevice" readonly="readonly" />
						</div>
						<div class="form-group">
							<select class="form-control" name="supplier" required>
								<option value="1">Supplier</option>
								@for($i=2; $i<12; $i++)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<input type="submit" class="btn btn-success" value="Enregistrer" />
						</div>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
