<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Models\MstAsset;
use Illuminate\Support\Facades\DB;

class LoanFormController extends Controller
{
  public function index(){
    $item = Borrowing::select(
        'borrowings.id as borrowing_id',
        'mst_assets.id as asset_id',
        'mst_assets.name as asset_name',
        'mst_assets.description as asset_description',
        'mst_assets.asset_no',
        'mst_assets.qty',
        'mst_assets.status as asset_status',
        'borrowings.user_email',
        'borrowings.borrow_date',
        'borrowings.expected_return_date',
        'borrowings.actual_return_date',
        'borrowings.user_signature',
        'borrowings.it_staff_signature',
        'borrowings.status'
    )
    ->join('mst_assets', 'borrowings.asset_id', '=', 'mst_assets.id')
    ->get();
    $asset = MstAsset::where('status','Available')->get();
    return view('form.index',compact('item','asset'));
  }

  public function store(Request $request){
    // Validate the request data
    $request->validate([
        'asset' => 'required|numeric',
        'qty' => 'required|integer|min:1',
        'email' => 'required|email',
        'loan_date' => 'required|date',
        'return_date' => 'required|date',
        'user_signature' => 'required|string',
        'it_staff_signature' => 'required|string',
    ]);

    // Check if the requested asset is available and quantity is sufficient
    $asset = MstAsset::findOrFail($request->asset);

    if ($asset->status !== 'Available' || $asset->qty < $request->qty) {
        return redirect()->back()->with('error', 'Failed. The asset is not available or quantity is insufficient.');
    }

    // Calculate updated quantity and status for the master asset
    $updatedQty = $asset->qty - $request->qty;
    $updatedStatus = $updatedQty == 0 ? 'Borrowed' : 'Available';

    // Begin database transaction
    DB::beginTransaction();

    try {
        // Decode base64 encoded signatures and save them as JPG files
        $userSignature = base64_decode($request->user_signature);
        $itStaffSignature = base64_decode($request->it_staff_signature);

        // Generate unique filenames for the signatures
        $userSignatureFileName = uniqid('user_signature_') . '.jpg';
        $itStaffSignatureFileName = uniqid('it_staff_signature_') . '.jpg';

        // Save the signature images to the public directory
        $userSignaturePath = public_path('image/' . $userSignatureFileName);
        $itStaffSignaturePath = public_path('image/' . $itStaffSignatureFileName);

        // Save the signature images
        file_put_contents($userSignaturePath, $userSignature);
        file_put_contents($itStaffSignaturePath, $itStaffSignature);

        // Insert data into the database
        $borrowing = new Borrowing();
        $borrowing->asset_id = $request->asset;
        $borrowing->user_email = $request->email;
        $borrowing->borrow_date = $request->loan_date;
        $borrowing->expected_return_date = $request->return_date;
        $borrowing->user_signature = $userSignatureFileName;
        $borrowing->it_staff_signature = $itStaffSignatureFileName;
        $borrowing->status = 'On Loan'; // You can set the initial status here
        $borrowing->save();

        // Update master asset quantity and status
        $asset->qty = $updatedQty;
        $asset->status = $updatedStatus;
        $asset->save();

        // Commit the transaction
        DB::commit();

        // Redirect or return response
        return redirect()->back()->with('success', 'Borrowing record created successfully.');
    } catch (\Exception $e) {
      dd($e);
        // Rollback the transaction if an error occurs
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed. Something went wrong.');
    }
}


}
