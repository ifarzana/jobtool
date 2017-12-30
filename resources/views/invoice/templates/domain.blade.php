<?php
$client = $invoice->client;
?>

<style>

    html,
    body {
        font-size: 13px;
    }

    #wrap {
        min-height: 100%;
    }

    #main {
        margin-top: -30px;
    }

    #wrapper {
        padding: 0;
        margin-left: 15px;
        margin-right: 15px;
    }

    .container {
        width:700px;
        margin: auto;
        padding: 0;
    }

    #sidebar-wrapper,
    #footer {
        display: none;
    }

    .table {
        display: table;
        width: 100%;
    }
    .tr {
        display: table-row;
    }

    .tr th {
        text-align: left;
    }

    .tr.center,
    .tr.center th,
    .tr.center td {
        text-align: center !important;
    }

    .pdf-footer {
        position: fixed;
        left: 0;
        right: 0;
        width: 100%;
        text-align: center;
        font-size: 12px;
        height: 130px;
        bottom: 0;
    }

    #watermark {
        position: fixed;
        top: 35%;
        left: 105px;
        transform: rotate(45deg);
        transform-origin: 50% 50%;
        opacity: .3;
        font-size: 120px;
        color: #000000;
        width: 480px;
        text-align: center;
    }

    .details-div {
        background-color: #EEEEEE;
        margin-bottom: 15px;
    }

    .details-div h4 {
        margin-top: 0;
        margin-bottom: 0;
        margin-left: 5px;
    }

    .received {
        color: #3c763d;
    }

    .refunded {
        color: #8a6d3b;
    }

    .due,
    .danger {
        color: #a94442;
    }

    @page {
        margin-top: 10px;
        margin-bottom: 10px;
    }

</style>

@if($invoice->status_id == 1)

    <div id="watermark">PAID</div>

@endif

<div class="container" style="font-family:'Arial';">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>

            <td width="65%" valign="top">
                <br/>
                <br/>

                Invoiced to:
                <br/>
                <br/>

                @if(!empty($invoice->client->title)) {{ $invoice->client->title }} @endif {{ $invoice->client->name }}
                <br/>

                <br/>

            </td>

            <td width="35%" valign="top">

                <br/>
                <br/>
                <span style="font-size: 22px;">Invoice</span>
                <br/>
                <br/>
                <br/>

                Invoice date: {{ date_format(date_create($invoice->created_at), 'd-M-Y') }}
                <br/>

                Invoice #: {{ $invoice->reference }}
                <br/>

                Invoice type: {{ $invoice->type->name }}
                <br/>
                <br/>

                Domain: {{ $invoice->domain->name }}
                <br/>

                Client number: {{ $invoice->client_id }}
                <br/>

            </td>
        </tr>

    </table>

    <br/>
    <br/>



</div>

<div style="page-break-after:always;"></div>

<div class="container" style="font-family:'Arial';">

    <div class="details-div">
        <h4>Details for Invoice #{{ $invoice->reference }}</h4>
    </div>

</div>