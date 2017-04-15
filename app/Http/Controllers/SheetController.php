<?php

namespace App\Http\Controllers;

use App\Models\Sheet;
use Redirect;
use URL;

class SheetController extends Controller
{


    /**
     * Delete a certain sheet if you're its group owner
     *
     * @param Sheet $sheet
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteSheet(Sheet $sheet)
    {
        $sheet->delete();
        return Redirect::to(URL::previous() . "#sheets");;
    }

}
