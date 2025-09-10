<?php namespace App\Http\Controllers\Web\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class ContactController extends Controller
{

    public function index()
    {
        return view('web.contact.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'message' => 'required'
        ]);

    }

}