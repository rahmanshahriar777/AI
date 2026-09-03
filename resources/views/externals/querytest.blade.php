@extends('layouts.blank')

@section('title', 'Contact page test')
@section('scripts')
    @parent
    
@endsection

@section('styles')
    @parent
    <style>
        .container-fluid {
            padding: 1% 15% 7%15%;
        }
    </style>
@endsection

@section('content')
    <!-- Content -->
    <div class="row">
        <div class="col-xl">
            
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <h4 class="display-4 text-center">Contact US</h4>
                            <hr class="my-4">
                        </div>

                        <div class="col-xs-12 col-md-6 " id="contact">

                            <iframe src="https://jems.live/office/system/StdEnquiry.asp?es=http://www.practicalroofing.co.uk&amp;wid=Industrial-Roofing" width="100%" height="1500px" frameborder="0" scrolling="no"></iframe>

                        </div>

                        <div class="col-xs-12 col-md-6 " id="contact">

                            <iframe src="{{ URL::to('/') }}/query" width="100%" height="1050px" frameborder="0" scrolling="yes"></iframe>

                        </div>

                        <div class="col-xs-12 col-md-6" id="contact">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d115806.34359550575!2d91.79094155131145!3d24.899747201107232!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x375054d3d270329f%3A0xf58ef93431f67382!2sSylhet!5e0!3m2!1sen!2sbd!4v1516883061409"
                                width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen=""></iframe>

                        </div>


                    </div>

                </div>

        </div>
    </div>
    <!-- / Content -->



@endsection
