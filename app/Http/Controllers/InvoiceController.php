<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config; 
use App;
use Response;

use Illuminate\Http\Request;

class InvoiceController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
	
	public function createPdf(Request $req){
		$URLStaging = Config::get('constants.PROD_BASE_URL');
		$OLD_INVOICE_PATH 	= Config::get('invoice.OLD_INVOICE_PATH');
		$user = $req->input('user');
		$cmd = $req->input('cmd');
		$userData[0] = json_decode(file_get_contents($URLStaging.'ws/customers/'.$user),true);
		$URL2 = $URLStaging.'ws/orders/customer/'.$user;
		$commandes = json_decode(file_get_contents($URL2),true);
		foreach($commandes as $commande){
			if($commande['id'] == $cmd){
				$nbFacture 		= 	$commande['billing_number'];
				$Carts 			= 	$commande['cart'];
				$DateBilling 	= 	date("d-m-Y", strtotime($commande['billing_date']));
				$temp 				= substr($commande['billing_date'],0,10);
				$Date 				= str_replace('-','',$temp);
				$Date 				= substr($Date,2,7);
				$discount		= 	$commande['discount'];
				$delivery24 	= 	$commande['delivery24'];
				$shipping_fee 	= 	$commande['shipping_fee'];
			}
		}
		$nbP = 0;
		$A = 0;
		foreach($Carts as $index => $value){
			$URL3 				=	$URLStaging.'ws/products/'.$index;
			$products			= 	json_decode(file_get_contents($URL3),true);
			$Products[] 		= 	$products;
			$nbP 				= 	$nbP + $value;
			$A					= 	$A+$products['price_reseller']*$value;
			$Qty[] 				= 	$value;
		}
		$a = number_format($A,2);
		$pdf = App::make('dompdf.wrapper');
		$pdf->loadView('pdf',['userData'=>$userData,'nbFacture'=>$nbFacture,'nbCmd'=>$cmd,'a'=>$a,'Products'=>$Products,'carts'=>$Qty,'dateBilling'=>$DateBilling,'discount'=>$discount,'delivery24'=>$delivery24,'shipping_fee'=>$shipping_fee, 'BASE_URL'=>$URLStaging])->save($OLD_INVOICE_PATH.'Facture'.$nbFacture.'cde'.$cmd.'_'.$Date.'_('.$userData[0]['address_billing']['city'].').pdf');
		return json_encode(['id_customer' => $user, 'id_order' => $cmd], JSON_UNESCAPED_SLASHES); 

	}
	
	public function viewPdf($id, $cmd){
		$URLStaging 		= Config::get('constants.PROD_BASE_URL');
		$INVOICE 			= Config::get('invoice.INVOICE');
		$OLD_INVOICE 		= Config::get('invoice.OLD_INVOICE');
		$OLD_INVOICE_PATH 	= Config::get('invoice.OLD_INVOICE_PATH');
		$oldInvoice 		= glob($OLD_INVOICE_PATH.'*.pdf');
		$userData[0] 		= json_decode(file_get_contents($URLStaging.'ws/customers/'.$id),true);
		$InvoiceOld			= Array();
		foreach($oldInvoice as $old){
			$InvoiceOld[]	= str_replace('/var/www/old_factures/','',$old);
		}
		
		if(!empty($InvoiceOld)){
			foreach($InvoiceOld as $old){
				$cmdProv = '/'.$cmd.'/';
				$result = preg_match($cmdProv ,$old,$matches);
				if($result > 0){
					$old_fact = $old;
					break;
				}
			}
		}
		
		if(file_exists($INVOICE.$cmd.'.pdf')){
			return Response::make(file_get_contents($INVOICE.$cmd.'.pdf'), 200, [
				'Content-Type' 			=> 'application/pdf',
				'Content-Disposition' 	=> 'inline; filename="'.$cmd.'"'
			]);
		}elseif($result > 0){
			return Response::make(file_get_contents($OLD_INVOICE_PATH.$old_fact), 200, [
				'Content-Type' 			=> 'application/pdf',
				'Content-Disposition' 	=> 'inline; filename="'.$cmd.'"'
			]);
		}else{
			$URL = $URLStaging.'ws/orders/customer/'.$id;
			$commandes = json_decode(file_get_contents($URL),true);
			foreach($commandes as $commande){
				if($commande['id'] == $cmd){
					$nbFacture 		= $commande['billing_number'];
					$Carts 			= $commande['cart'];
					$temp 			= substr($commande['billing_date'],0,10);
					$Date 			= str_replace('-','',$temp);
					$Date 			= substr($Date,2,7);
					$DateBilling 	= date("d-m-Y", strtotime($commande['billing_date']));
					$discount 		= $commande['discount'];
					$delivery24 	= $commande['delivery24'];
					$shipping_fee 	= $commande['shipping_fee'];
				}
			}
			$nbP = 0;
			$A = 0;
			foreach($Carts as $index => $value){
				$URL3 = $URLStaging.'ws/products/'.$index;
				$products = json_decode(file_get_contents($URL3),true);
				$Products[] = $products;
				$nbP = $nbP + $value;
				$A = $A+$products['price_reseller']*$value;
				$Qty[] = $value; 
			}
			$a = number_format($A,2);
			$pdf = App::make('dompdf.wrapper');
			$pdf->loadView('pdf',['userData'=>$userData,'nbFacture'=>$nbFacture,'nbCmd'=>$cmd,'a'=>$a,'Products'=>$Products,'carts'=>$Qty,'dateBilling'=>$DateBilling,'discount'=>$discount,'delivery24'=>$delivery24,'shipping_fee'=>$shipping_fee, 'BASE_URL'=>$URLStaging])->save($OLD_INVOICE_PATH.'Facture'.$nbFacture.'cde'.$cmd.'_'.$Date.'_('.$userData[0]['address_billing']['city'].').pdf');
			return Response::make(file_get_contents($INVOICE.$cmd.'.pdf'), 200, [
				'Content-Type' 			=> 'application/pdf',
				'Content-Disposition' 	=> 'inline; filename="'.$cmd.'"'
			]);
		}
	}

}
