<?php

namespace App\Http\Controllers\admin;

use Session;
use App\Models\Rent;
use App\Models\Unit;
use App\Models\Floor;
use App\Models\Tenant;
use App\Models\Building;
use App\Models\RentType;
use App\Models\FloorType;
use App\Models\UnitStatus;
use Illuminate\Http\Request;
use App\Models\RentPaidStatus;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
        $rent_paid_status = RentPaidStatus::where('rent_paid_status_code','<>', 3)->get();
        $rent_details = Rent::orderBy('id','desc')->get();
        $tenant_list = Tenant::where('is_passed', null)->get();

    
        return view('admin.rent.index', compact('rent_details','rent_paid_status','tenant_list'));
    }

    public function generate_receipt($id)
    {
        $rent_details = Rent::find($id);
        return view('admin.rent.receipt',compact('rent_details'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        $tenant_list = Tenant::where('is_passed', null)->get();
        
        return view('admin.rent.create', compact('tenant_list'));
    }
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'tenant_id' => 'required',
            'rent_amount' => 'required',
            // 'rent_status' => 'required'
        ],[
            'tenant_id.required' => 'Please select the tenant first.'
        ]);
        
        if($request->input('monthly_checkbox') == "on")
        {
            $request->validate([
                'rent_month' => 'required',
            ]);
        }
        else
        {
            $request->validate([
                'rent_start_month' => 'required',
                'rent_end_month' => 'required',
            ],[
                'rent_start_month.required' => 'Rent form field is required.',
                'rent_end_month.required' => 'Rent to field is required.'
            ]);
        }

        $rent = new Rent();

        // if($request['rent_status'] == 'paid')
        // {
        //     $request->validate([
        //         'received_amount' => 'required',
        //     ]);

        // }
        // else
        // {
        //     $rent_paid_status_code = 2; // unpaid
            
        // }
        
        $rent_paid_status_code = 1; // paid
        $rent->received_date = Carbon::now();
        $rent->tenant_id = $request['tenant_id'];
        $rent->rent_amount = $request['rent_amount'];

        if($request['received_amount'])
        {
            
            $rent->received_amount = $request['received_amount'];
            $rent->received_date = Carbon::now();
            $rent_paid_status_code = 1; //paid
        }
        

        $rent->rent_paid_status_code = $rent_paid_status_code;
        
        if($request->input('monthly_checkbox') == "on")
        {
            $rent->rent_month = $request->input('rent_month');
        }
        else
        {
            $rent->rent_start_month = $request->input('rent_start_month');
            $rent->rent_end_month = $request->input('rent_end_month');
        }
        
        if($rent->save()){

            if($rent->rent_paid_status_code == 1)
            {
               
                $receipt_number = $rent->id . rand(1000, 9999);
                $rent->receipt_no = $receipt_number;
                $rent->save();
            }
            Toastr::success('Rent detail added successfully.');
            return redirect()->route('rent.list');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rent = Rent::where('id', $id)->first();
        $html_response = view('admin.rent.partials.rent_detail_view_modal', compact('rent'))->render();

        return response()->json([
            'success' => true,
            'html_response' => $html_response
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {    
        $rent = Rent::find($id);
        
        $tenant_list = Tenant::where('is_passed', null)->get();
        
        return view('admin.rent.edit', compact('rent','tenant_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tenant_id' => 'required',
            'rent_amount' => 'required',
            // 'rent_status' => 'required'
        ],[
            'tenant_id.required' => 'Please select the tenant first.'
        ]);
        
        if($request->input('monthly_checkbox') == "on")
        {
            $request->validate([
                'rent_month' => 'required',
            ]);
        }
        else
        {
            $request->validate([
                'rent_start_month' => 'required',
                'rent_end_month' => 'required',
            ],[
                'rent_start_month.required' => 'Rent form field is required.',
                'rent_end_month.required' => 'Rent to field is required.'
            ]);
        }

        $rent = Rent::find($id);

        // if($request['rent_status'] == 'paid')
        // {
        //     $request->validate([
        //         'received_amount' => 'required',
        //     ]);
        //     $rent_paid_status_code = 1; // paid
        //     $rent->received_date = Carbon::now();
        // }
        // else
        // {
        //     $rent_paid_status_code = 2; // unpaid

        // }

        $rent->tenant_id = $request['tenant_id'];
        $rent->rent_amount = $request['rent_amount'];

        if($request['received_amount'])
        {
            
            $rent->received_amount = $request['received_amount'];
            $rent->received_date = Carbon::now();
            $rent_paid_status_code = 1; //paid
        }
        

        $rent->rent_paid_status_code = $rent_paid_status_code;
        
        if($request->input('monthly_checkbox') == "on")
        {
            $rent->rent_month = $request->input('rent_month');
        }
        else
        {
            $rent->rent_start_month = $request->input('rent_start_month');
            $rent->rent_end_month = $request->input('rent_end_month');
        }
        
        if($rent->save()){
            Toastr::success('Rent detail updated successfully.');
            return redirect()->route('rent.list');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rent = Rent::find($id);
        $rent->delete();
        Toastr::success('This Rent details deleted successfully!');
        return back();
    }
   
    public function search_rent(Request $request)
    {
        $query = Rent::query();

        if($request['tenant_id'])
        {
            $query->where('tenant_id', $request['tenant_id']);
        }

        if($request['rent_month'])
        {
            $query->where('rent_month', $request['rent_month']);
        }

        if($request['rent_paid_status_code'])
        {
            $query->where('rent_paid_status_code', $request['rent_paid_status_code']);
        }

        $rent_details = $query->get();

        $tenant_id = $request['tenant_id'];
        $rent_month = $request['rent_month'];
        $rent_paid_status_code = $request['rent_paid_status_code'];

        $rent_paid_status = RentPaidStatus::where('rent_paid_status_code','<>', 3)->get();
        $tenant_list = Tenant::where('is_passed', null)->get();

        return view('admin.rent.index', compact('rent_details','rent_paid_status','tenant_list','tenant_id','rent_month','rent_paid_status_code'));
    }

    public function invoice(Request $request)
    {
        return view('admin.rent.invoice');
    }
    public function reciept(Request $request)
    {
        return view('admin.rent.reciept');
    }
}
