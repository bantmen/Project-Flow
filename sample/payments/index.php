<?php

function trans ($amount) {
	require __DIR__ . '/../bootstrap.php';
	use PayPal\Api\Amount;
	use PayPal\Api\Details;
	use PayPal\Api\Item;
	use PayPal\Api\ItemList;
	use PayPal\Api\CreditCard;
	use PayPal\Api\Payer;
	use PayPal\Api\Payment;
	use PayPal\Api\FundingInstrument;
	use PayPal\Api\Transaction;

	// ### CreditCard
	// A resource representing a credit card that can be
	// used to fund a payment.
	$card = new CreditCard();
	$card->setType("visa")
		->setNumber("4769440539574013")
		->setExpireMonth("05")
		->setExpireYear("2019")
		->setCvv2("555")
		->setFirstName("Rahul")
		->setLastName("Chaudhary");

	// ### FundingInstrument
	// A resource representing a Payer's funding instrument.
	// For direct credit card payments, set the CreditCard
	// field on this object.
	$fi = new FundingInstrument();
	$fi->setCreditCard($card);

	// ### Payer
	// A resource representing a Payer that funds a payment
	// For direct credit card payments, set payment method
	// to 'credit_card' and add an array of funding instruments.
	$payer = new Payer();
	$payer->setPaymentMethod("credit_card")
		->setFundingInstruments(array($fi));

	// ### Itemized information
	// (Optional) Lets you specify item wise
	// information
	$item1 = new Item();
	$item1->setName('Ground Coffee 40 oz')
		->setCurrency('USD')
		->setQuantity(1)
		->setPrice('7.50');
	$item2 = new Item();
	$item2->setName('Granola bars')
		->setCurrency('USD')
		->setQuantity(5)
		->setPrice('0.00');

	$itemList = new ItemList();
	$itemList->setItems(array($item1, $item2));

	// ### Additional payment details
	// Use this optional field to set additional
	// payment information such as tax, shipping
	// charges etc.
	$details = new Details();
	$details->setShipping('0.00')
		->setTax('0.00')
		->setSubtotal('00.00');

	// ### Amount
	// Lets you specify a payment amount.
	// You can also specify additional details
	// such as shipping, tax.
	$amount = new Amount();
	$amount->setCurrency("USD")
		->setTotal("$amount")
		->setDetails($details);

	// ### Transaction
	// A transaction defines the contract of a
	// payment - what is the payment for and who
	// is fulfilling it. 
	$transaction = new Transaction();
	$transaction->setAmount($amount)
		->setItemList($itemList)
		->setDescription("Payment description");

	// ### Payment
	// A Payment Resource; create one using
	// the above types and intent set to sale 'sale'
	$payment = new Payment();
	$payment->setIntent("sale")
		->setPayer($payer)
		->setTransactions(array($transaction));

	// ### Create Payment
	// Create a payment by calling the payment->create() method
	// with a valid ApiContext (See bootstrap.php for more on `ApiContext`)
	// The return object contains the state.
	try {
		$payment->create($apiContext);
	} catch (PayPal\Exception\PPConnectionException $ex) {
		echo "Exception: " . $ex->getMessage() . PHP_EOL;
		var_dump($ex->getData());
		exit(1);
	}
}

?>

