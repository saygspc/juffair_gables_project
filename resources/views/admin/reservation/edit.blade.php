@extends('layouts.admin.app')
{{-- Page title --}}

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('public/admin/assets/') }}/bundles/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="{{asset('public/admin/assets/bundles/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{  asset('public/assets/css/intlTelInput.css') }}">
<style>
.iti--allow-dropdown
{
   width:100%;
}
</style>
@stop
@section('content')

   <div class="section-body">
      <form method="POST" action="{{route('reservation.update',$reservation->id) }}" enctype="multipart/form-data" autocomplete="off">
         @csrf
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-header">
                     <h4>Reservation Details</h4>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="form-group col-md-4">
                           <label for="">Select Facility <sup class="text-danger">*</sup></label>
                           <select class="form-control" name="room_id" >
                             <option value="0" selected disabled>---Select---</option>
                             @foreach ($facilities_list as $facility)
                                 <option value="{{ $facility->id }}" @if($reservation->room_id == $facility->id) selected @endif>{{ $facility->name }}</option>
                             @endforeach
                           </select>
                       </div>
                        <div class="form-group col-md-4">
                            <label>Reservation Date <sup class="text-danger">*</sup></label>
                            <input type="text" value="{{ isset($reservation->reservation_date)? \Carbon\Carbon::parse($reservation->reservation_date)->format('Y-m-d') : '' }}" name="reservation_date" class="form-control datepicker">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Start Time <sup class="text-danger">*</sup></label>
                             <input type="text" name="start_time" value="@if(isset($reservation)) {{ $reservation->start_time }} @endif"  class="form-control timepicker">
                        </div>
                         <div class="form-group col-md-4">
                            <label>End Time <sup class="text-danger">*</sup></label>
                            <input type="text" name="end_time" value="@if(isset($reservation)) {{ $reservation->end_time }} @endif"  class="form-control timepicker">
                         </div>
                         <div class="form-group col-md-4">
                            <label>Amount</label>
                            <input type="text" name="amount" value="@if(isset($reservation)) {{ $reservation->amount }} @endif" class="form-control">
                         </div>
                         <div class="form-group col-md-4">
                            <label>Tenant Name <sup class="text-danger">*</sup></label>
                            <input type="text" name="tenant_name" value="@if(isset($reservation)) {{ $reservation->tenant_name }} @endif" class="form-control">
                         </div>
                        <div class="form-group col-md-4">
                            <label style="display:block;">Contact Number <sup class="text-danger">*</sup></label>
                            <input type="tel" maxLength="11" value="{{ $reservation->contact_number }}"  name="tenant_mobile_phone" id="contactNo" class="form-control">
                            <input type="hidden" name="country_code" value="" >
                        </div>
                     </div>
                     <button  class="btn btn-primary mr-1" type="submit">update</button>
                     <a href="{{ url()->previous() }}"  class="btn btn-primary ml-2">Cancel</a>
                     
                  </div>
               </div>
            </div>
         </div>
   </div>
   </div>
   </form>
   </div>
   </div>
</section>
@stop
@section('footer_scripts')
<script src="{{ asset('public/admin/assets/') }}/bundles/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script src="{{asset('public/admin/assets/bundles/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{  asset('public/assets/js/intlTelInput.js') }}"></script>
<script src="{{ asset('public/assets/js/intlTelInput.js')}}"></script>
<script>
         var input = document.querySelector("#contactNo");
         window.intlTelInput(input, {
         //   allowDropdown: false,
         //   autoHideDialCode: true,
         autoPlaceholder: "On",
         //   dropdownContainer: document.body,
         //   excludeCountries: ["us"],
         //   formatOnDisplay: true,
         geoIpLookup: function(callback) {
           $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
             var countryCode = (resp && resp.country) ? resp.country : "";
             callback(countryCode);
           });
         },
         //   hiddenInput: "full_number",
         //   initialCountry: "auto",
         //   localizedCountries: { 'de': 'Deutschland' },
         //   nationalMode: false,
         //   onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
         placeholderNumberType: "MOBILE",
         //   preferredCountries: ['cn', 'jp'],
         separateDialCode: true,
         
         utilsScript: "build/js/utils.js",
         });
         
      </script>
      <script>
         $(document).ready(function(){
            let country_code = $(".iti__selected-dial-code").html()

            $("input[name=country_code]").val(country_code)
         })

         $(".iti__selected-dial-code").on('DOMSubtreeModified',function(){
            let country_code = $(".iti__selected-dial-code").html()

            $("input[name=country_code]").val(country_code)
         })
      </script>
@stop

