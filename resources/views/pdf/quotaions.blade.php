<!DOCTYPE html>
<html>

<head>
    <title>Quotation</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .card {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-header,
        .card-body {
            margin-bottom: 20px;
        }

        .text-end {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-6">
                <div class="card-header">
                    <table width="100%" style="margin-bottom: 20px;">
                        <tr>
                            <td width="50%" style="vertical-align: top; text-align: left;">
                                {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('F jS Y') }}
                                <br><br>
                                {{ $quotation->customer->contact_firstname }}
                                {{ $quotation->customer->contact_lastname }}<br>
                                {{ $quotation->customer->company_name }}<br>
                                @isset($quotation->customer->billingaddress)
                                    <span style="text-transform: capitalize;">
                                        {{ $quotation->customer->billingaddress->address }},
                                        {{ $quotation->customer->billingaddress->county }}
                                    </span>
                                @endisset
                            </td>
                            <td width="50%" style="vertical-align: top; text-align: right;">
                                <img src="{{ $appbrand->business_logo??'' }}"
                                    style="max-width: 100px; max-height: 100px;" alt="{{ $appbrand->business_name??'Logo' }}">
                                <br>
                                Tel: {{ $appbrand->business_phone??'' }} <br>
                                Email: {{ $appbrand->business_email??'' }} <br>
                                Website: {{ $appbrand->business_website??'' }}
                            </td>
                            
                        </tr>
                    </table>


                    <table width="100%">
                        <tr>
                            <td width="50%" style="text-align: left;">
                                <strong>Estimated Total Cost:</strong> {{ formatPoundNumber($quotation->total_amount) }}
                            </td>
                            <td width="50%" style="text-align: right;">
                                <strong>Proposal valid until:</strong>
                                {{ \Carbon\Carbon::parse($quotation->valid_until)->format('F jS Y') }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-body pt-1">
                    @foreach ($quotation->sections as $sections)
                        <h5>{{ $sections->section_name }}: </h5>
                        @isset($sections->content->sample_type)
                            @if ($sections->content->sample_type == 'textbox')
                                <p class="mb-6">
                                    {!! $sections->content->sample_source !!}
                                </p>
                            @endif
                            @if ($sections->has_attachments)
                                <div class="row">
                                    @foreach ($sections->attachments as $saa)
                                        <div class="col-6 mb-4">
                                            <img src="{{ $saa->source == 's3' ? $saa->attachment_url : asset($saa->attachment_path) }}"
                                                alt="{{ $saa->attachment_name }}" width="100%" class="img-fluid">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endisset
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>

</html>
