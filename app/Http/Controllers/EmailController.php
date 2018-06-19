<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendInvoice;
use Mail;
use File;

class EmailController extends Controller
{
    public function create()
    {
    	$form = [
    		'email_to' => 'jane@example.com',
    		'subject' => 'Invoice {number} from {business_name}',
    		'message' => File::get(base_path('resources/views/email-message.txt'))
    	];

    	$options = [
    		'variables' => [
    			'subject' => [
    				'{number}' => 'Number',
    				'{business_name}' => 'Business Name'
    			],
    			'message' => [
    				'{customer}' => 'Customer',
    				'{amount}' => 'Amount',
    				'{due_date}' => 'Due Date',
    				'{payment_link}' => 'Payment Link',
    				'{business_name}' => 'Business Name'
    			]
    		],
    		'replaceable' => $this->getReplaceable()
    	];

    	return response()
    		->json([
    			'form' => $form,
    			'options' => $options
    		]);
    }

    protected function getReplaceable()
    {
    	return [
    		'{number}' => 'INV/2018/1002',
    		'{business_name}' => 'John Doe Inc',
    		'{customer}' => 'Jane Doe',
    		'{amount}' => '$850.00',
    		'{due_date}' => 'May 25th 2018',
    		'{payment_link}' => 'https://example.com/payment-link'
    	];
    }

    public function send(Request $request)
    {
    	$request->validate([
    		'email_to' => 'required|email',
    		'subject' => 'required',
    		'message' => 'required'
    	]);

    	$data = [
    		'subject' => $this->replaceVariables($request->subject),
    		'message' => $this->replaceVariables($request->message)
    	];

    	return Mail::to($request->email_to)
    		->send(new SendInvoice($data));

    	// debug
    	// return new SendInvoice($data);
    }

    protected function replaceVariables($message)
    {
    	$items = $this->getReplaceable();

    	foreach($items as $key => $value) {
    		$message = str_replace($key, $value, $message);
    	}

    	return $message;
    }
}
