<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Invoice</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #ffffff; /* Soft background */
        }
        .container {
            width: 80%;
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            background-color: #ffffff; /* White background */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Soft shadow for professional look */
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo img {
            width: 150px;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
            letter-spacing: 1px;
        }
        .invoice-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        .table th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: 600;
        }
        .table td {
            color: #555;
        }
        .total {
            text-align: right;
            font-weight: bold;
            background-color: #f4f4f4;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #888;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ public_path('storage/images/IMG-20231213-WA0022.jpg') }}" alt="Company Logo">
            </div>
            <div class="invoice-info">
                <h2>Invoice</h2>
                <p>Order #: {{ str_pad($transactions->first()->id, 8, '0', STR_PAD_LEFT) }}</p>
                <p>{{ \Carbon\Carbon::now()->format('F jS, Y') }}</p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    @foreach($transaction->products as $product)
                        <tr>
                            <td>{{ $product->nama_barang }}</td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>${{ number_format($product->pivot->price, 2) }}</td>
                            <td>${{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total">Subtotal</td>
                    <td>${{ number_format($totalRevenue, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="total">Tax & Fees (7%)</td>
                    <td>${{ number_format($totalRevenue * 0.07, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="total">Grand Total (Incl. Tax)</td>
                    <td>${{ number_format($totalRevenue * 1.07, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>If you have any questions, feel free to contact us at support@company.com.</p>
        </div>
    </div>
</body>
</html>
