<?php

namespace App\Http\Controllers\Vouchers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Customer;

class ReleaseVoucherController extends Controller
{
    public function getRelease($step) {
        if($step == 'step-1a') {
            return view('vouchers.release.step1a');
        }
        if($step == 'step-1b') {
            return view('vouchers.release.step1b');
        }
        else if($step == 'step-2') {

        }
        else {
            return redirect()->back();
        }
    }

    public function postRelease(Request $request, $step) {
        if($step == 'step-1a') {
            $filterdList = $request->filterdList;
        }
        if($step == 'step-1b') {
            
        }
        else if($step == 'step-2') {
            $this->handlePostReleaseStep1b($request);
        }
        else {
            return redirect()->back();
        }
    }
    
    private function handlePostReleaseStep1a(Request $request) {
        $filterdList = $request->filterdList;
    }

    private function handlePostReleaseStep1b(Request $request) {
        $name = $request->name;
        $conditionType = $request->conditionType;
        $operators = $request->operator;
        $values = $request->value;
        $config = [];

        foreach ($operators as $column => $operator) {
            if(!empty($values[$column])) {
                $config[$column] = [
                    'operator' => $operator, 
                    'value' => ($operator == 'like') ? '%' . $values[$column] . '%' : $values[$column]
                ];
            }
        }

        $customers = $this->getListFromJsonConfig($config, $conditionType);

        // return view()
    }

    
        
    /**
     * getListFromJsonConfig
     *
     * @param  array $config
     * @param  string $conditionType
     * @return array
     */
    
    private function getListFromJsonConfig($config, $conditionType) {

        $customers = DB::table('customers');

        foreach($config as $column => $condition) {
            if($conditionType == 'or') {
                $customers->orWhere($column, $condition['operator'], $condition['value']);
            }
            else if ($conditionType == 'and') {
                $customers->andWhere($column, $condition['operator'], $condition['value']);
            }
        }

        return $customers->get();
    }
}
