<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Part;


class MstPartSAPController extends Controller
{
    public function sapPart(){
        $item = Part::get();
        return view('master.sap',compact('item'));
    }


}
