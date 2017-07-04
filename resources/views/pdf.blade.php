<!DOCTYPE html>
<html>
	<head>
		<title>Facture n°{{ $nbFacture }} - {{ $userData[0]['name'] }}</title>
		<link href="{{asset('public/assets/css/bootstrap.min.css')}}" rel="stylesheet">
		<link href="{{asset('public/assets/css/style.css')}}" rel="stylesheet">
		<style>
			tbody:before, tbody:after { display: none; }
		</style>
	</head>
	<body style="background-color: white; font-family:LatoRegular;">
		<div class="col-md-6"  style="position:absolute; left:0pt; width:192pt; line-height: 1.0em;">
			<img alt="Brand" src="{{asset('public/assets/img/logo.png')}}">
			<br>
			<h4 style="font-family:LatoRegular;">TECH TABLET</h4>
			<p>26 Rue Antoine Chaperon<br>
			42300 Roanne<br>
			RCS Roanne 791 21 1 055<br>
			TVA intra : FR 1 2 791 21 1 055
			<br>
			<br>
			Facturé à :<br>
			{{ $userData[0]['name'] }}<br>
			{{ $userData[0]['address_billing']['street'] }}<br>
			{{ $userData[0]['address_billing']['postcode'] }} {{ $userData[0]['address_billing']['city'] }}<br>
			{{ $userData[0]['address_billing']['country'] }}<br>
			{{ $userData[0]['tva_number'] }}
			<br>
		</div>
		<?php
				if($delivery24==1){
					$d = $shipping_fee;
				}else{
					$d = 0;
				}
				$T = $a + $d;
				if($userData[0]['discount'] > 0){
					$remise = $T/100*$userData[0]['discount'];
					$T = $T - $remise;
				}else{
					$remise = 0;
				}
				$tva = $T/100*20;
				$ttc = $T+$tva;
				
			?>
		<div class="col-md-6" align="right" style="margin-left:200pt;">
			<h2 style="font-family:LatoRegular;">Facture n° {{ $nbFacture }}</h2>
			<h3 style="font-family:LatoRegular;">Commande n° {{ $nbCmd }}</h3>
			<?php 
				$typeCmd = json_decode(file_get_contents($BASE_URL.'ws/orders/'.$nbCmd),true);
			?>
			@if( $typeCmd['payment_method']==1)
				<?php
					$dateBilling30 = date_create($dateBilling);
					date_add($dateBilling30, date_interval_create_from_date_string('30 days'));
				?>
			Date : {{ $dateBilling }}<br>
			Montant due : {{ number_format($ttc,2) }} &euro;<br>
			Règlement par lettre de change non signé à 30 jours<br>
			Echeance le : {{ date_format($dateBilling30,'d-m-Y') }}<br>
			@else
				Règlement par CB le : {{ $dateBilling }}<br>
				Montant réglé : {{ number_format($ttc,2) }} &euro;
			@endif
		</div>
		<div class="col-xs-12" style="margin-top:100pt;">	
			<table class="table table-hover">
				<tr style="border-bottom: 1px solid black;">
					<td>Code barre</td> 
					<td>Désignation</td>
					<td><center>Quantité</center></td>
					<td><center>PU HT</center></td>
					<td><center>Montant HT</center></td>
				</tr>
				@foreach($Products as $index => $product)
				<?php $p = $carts[$index]*$product['price_reseller']; 
				 $P = number_format($p,2); ?>
					<tr><td><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($product['ean'], 'EAN13')}}" alt="barcode" style="width:100px;" /><span style = "margin-left: 1px; font-size: 9px; letter-spacing: 2.5px;" >{{ $product['ean'] }}1</span></td><td><div style="display:inline-block; width:30px;"><img src="{{ $product['pictures'][0] }}" style="width:30px;"></div><div style = "display:inline-block;">{{ $product['name'] }}</div></td><td><center>{{ $carts[$index] }}</center></td><td><center>{{ number_format($product['price_reseller'],2) }} &euro;</center></td><td><center>{{ $P }} &euro;</center></td>
				@endforeach
				
			</table>
		</div>
		<div class="col-md-6" align="right" style="margin-left:200pt;">
			Total HT sans discount : {{ number_format($a,2) }} &euro;<br>
			Discount : {{ $discount+$remise }} &euro;<br> 
			Total HT avec discount : {{ number_format($a-$discount-$remise,2) }}  &euro;<br>
			Frais de port :<?php if($d==0){ echo '0 &euro;';}else{ echo number_format($d,2).' &euro;';}?> <br> 
			<br>
			Total HT : {{ number_format($T,2) }}  &euro;<br>
			
			TVA(20%) : {{ number_format($tva,2) }}  &euro;<br>
			Total TTC : {{ number_format($ttc,2) }}  &euro;
		</div>
	</body>

</html>