<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MstAsset;
use App\Models\Dropdown;

class MstAssetController extends Controller
{
   public function index(){
    $item = MstAsset::get();
    $dropdown = Dropdown::where('category','Status')->get();
    return view('master.index',compact('item','dropdown'));
   }

   public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'asset_no' => 'required|string|max:50',
            'asset_name' => 'required|string|max:100',
            'asset_description' => 'required|string',
            'qty' => 'required|integer',
            'status' => 'required|string|max:50'
        ]);

        // Periksa apakah asset_no sudah ada
        $existingAsset = MstAsset::where('asset_no', $request->input('asset_no'))->first();

        if ($existingAsset) {
            // Redirect dengan pesan gagal jika asset_no sudah ada
            return redirect('/mst/asset')->with('failed', 'Failed Add Asset already exist');
        }

        // Simpan data ke database jika asset_no belum ada
        MstAsset::create([
            'asset_no' => $request->input('asset_no'),
            'name' => $request->input('asset_name'),
            'description' => $request->input('asset_description'),
            'qty' => $request->input('qty'),
            'status' => $request->input('status')
        ]);

        // Redirect dengan pesan sukses
        return redirect('/mst/asset')->with('status', 'Success Add Asset');
    }

    public function update(Request $request, $id)
    {
        // Dekripsi ID
        $id = decrypt($id);

        // Cari data berdasarkan ID
        $asset = MstAsset::findOrFail($id);

        // Validasi input
        $request->validate([
            'asset_no' => 'required|string|max:50|unique:mst_assets,asset_no,' . $id,
            'asset_name' => 'required|string|max:100',
            'asset_description' => 'required|string',
            'qty' => 'required|integer',
            'status' => 'required|string|max:50'
        ]);

        // Simpan data input ke model
        $asset->asset_no = $request->input('asset_no');
        $asset->name = $request->input('asset_name');
        $asset->description = $request->input('asset_description');
        $asset->qty = $request->input('qty');
        $asset->status = $request->input('status');

        // Periksa apakah ada perubahan
        if (!$asset->isDirty()) {
            return redirect('/mst/asset')->with('failed', 'Failed, there is no change');
        }

        // Simpan perubahan
        $asset->save();

        // Redirect dengan pesan sukses
        return redirect('/mst/asset')->with('status', 'Success Update Asset');
    }

    public function delete($id)
    {
        // Dekripsi ID
        $id = decrypt($id);

        // Cari data berdasarkan ID
        $asset = MstAsset::findOrFail($id);

        // Hapus data dari database
        $asset->delete();

        // Redirect dengan pesan sukses
        return redirect('/mst/asset')->with('status', 'Success Delete Asset');
    }
}
