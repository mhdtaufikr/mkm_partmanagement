<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Part;
use App\Models\RepairPart;
use App\Models\MachineSparePartsInventory;
use App\Exports\PartsSAPTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PartsSAPImport;
use Illuminate\Support\Facades\DB;


class MstPartSAPController extends Controller
{
    public function sapPart(){
        $item = Part::get();
        return view('master.sap',compact('item'));
    }

    public function sapTemplate()
    {
        return Excel::download(new PartsSAPTemplateExport, 'parts_sap_template.xlsx');
    }

    public function sapUpload(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Excel::import(new PartsSAPImport, $request->file('excel-file'));
            });
            return redirect()->back()->with('status', 'File imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Failed to import file: ' . $e->getMessage());
        }
    }

    public function sapPartDetail($id) {
        $id = decrypt($id);

        // Fetch part details
        $part = Part::find($id);

        // Fetch repair part details
        $repairParts = RepairPart::where('part_id', $id)->get();

        // Fetch machine details using the part
        $machineParts = MachineSparePartsInventory::where('part_id', $id)->with('machine')->get();

        return view('master.sapdtl', compact('part', 'repairParts', 'machineParts'));
    }


}
