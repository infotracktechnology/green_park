<head>
    <title>Fee Receipt</title>

    <style>
        body {
            font-family: "Arial", sans-serif;
            background: #f5f7fa;
        }

        .receipt-container {
            position: relative;
            width: 750px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .receipt-header img {
            width: 80%;
            height: 80px;
            margin-bottom: 5px;
        }

        .receipt-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .receipt-header p {
            margin: 0;
            font-size: 14px;
            color: #444;
        }

        .details-section p {
            margin: 4px 0;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            font-size: 15px;
            text-align: left;
        }

        th {
            background: #f1f1f1;
        }

        .total-box {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        .receipt-footer {
            border-top: 2px solid #333;
            margin-top: 25px;
            padding-top: 10px;
            text-align: center;
            font-size: 13px;
            color: #555;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }

            .receipt-container {
                position: relative;
                box-shadow: none;
                border: none;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .receipt-header, .receipt-footer {
                page-break-inside: avoid;
            }

            @page {
                margin: 10mm;
            }
        }
    </style>
</head>


<div class="receipt-container" id="printArea">
    
    {{-- HEADER --}}
    <div class="receipt-header">
        <img src="{{ asset('img/favicon.png') }}" alt="Institute Logo"> <!-- Change logo path -->
        {{-- <h2>ABC INSTITUTE OF TECHNOLOGY</h2>
        <p>123, Main Road, Chennai – 600001</p>
        <p>Phone: +91 98765 43210 | Email: info@abcinst.com</p> --}}
    </div>

    {{-- DETAILS --}}
    <div class="details-section">
        <p><strong>Receipt No:</strong> {{ $fee_collection->receipt_no }}
        @if(isset($copyType) && $copyType === 'DUPLICATE')
        <span style="color:red; font-weight:bold; margin-left:10px;">(DUPLICATE)</span>
    @endif</p>
        <p><strong>Date:</strong> {{ $fee_collection->payment_date }}</p>
        <p><strong>Student Name:</strong> {{ $fee_collection->student->student_name }}</p>
        <p><strong>Student ID:</strong> {{ $fee_collection->student->student_id }}</p>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>Installment</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fee_collection->item as $item)
                <tr>
                    <td>{{ $item->feeplanitem->instalment ?? '-' }}</td>
                    <td>{{ $item->payamount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        Total Paid: ₹{{ number_format($fee_collection->total, 2) }}
    </div>

    <div class="details-section">
        <p><strong>Payment Mode:</strong> {{ strtoupper($fee_collection->payment_mode === 'neft' ? 'ONLINE' : $fee_collection->payment_mode) }}</p>

        @if($fee_collection->payment_mode === 'neft')
            <p style="text-transform: uppercase"><strong>Transfer Mode:</strong> {{ $fee_collection->bank_transfer_mode }}</p>
            <p style="text-transform: uppercase"><strong>Bank:</strong> {{ $fee_collection->bank_name }}</p>
            <p><strong>Transfer Date:</strong> {{ $fee_collection->bank_transfer_date }}</p>
            <p><strong>Transaction ID:</strong> {{ $fee_collection->transaction_id }}</p>
            <p><strong>Subject to Realization:</strong> This receipt is subject to realization of the payment amount. Please verify the payment details with the Accounts department.</p>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="receipt-footer">
        This is a computer-generated receipt. No signature required.<br>
        © {{ date('Y') }} Spectra Academy. All Rights Reserved.

        <p>Printed on: {{ now()->format('d-m-Y h:i A') }}</p>
    </div>

    @if($fee_collection->is_cancelled)
    <div style="
        position: absolute;
        top: 40%;
        left: 20%;
        font-size: 80px;
        opacity: 0.15;
        transform: rotate(-30deg);
        color: red;
        z-index: 0;
    ">
        CANCELLED
    </div>
@endif


</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    window.print();
});
</script>
