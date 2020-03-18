<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportRevenueExport implements FromView, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        dd($this->data);
        $type = $this->data->request->type;
        return view('exports.report_revenues', $this->data);
    }
}
