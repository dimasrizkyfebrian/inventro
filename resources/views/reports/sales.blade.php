<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header-table { width: 100%; border: none; margin-bottom: 20px; }
        .header-table td { vertical-align: top; }
        .company-details { text-align: left; }
        .company-details h1 { margin: 0; font-size: 24px; color: #000; }
        .report-details { text-align: right; }
        .report-details h2 { margin: 0; font-size: 20px; }
        .report-details p { margin: 5px 0 0 0; }
        hr { border: 0; border-top: 1px solid #ccc; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .total { font-weight: bold; text-align: right; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="company-details">
                <h1>Inventro Inc.</h1>
                <p>
                    123 Innovation Drive<br>
                    Business City, 12345<br>
                    contact@inventro.com
                </p>
            </td>
            <td class="report-details">
                <h2>Sales Report</h2>
                <p>
                    <strong>Report Period:</strong><br>
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </p>
                <p>
                    <strong>Generated On:</strong><br>
                    {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
                </p>
            </td>
        </tr>
    </table>

    <hr>

    <table>
        <thead>
            <tr>
                <th>SO Number</th>
                <th>Date</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($salesOrders as $order)
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $loop->first ? $order->so_number : '' }}</td>
                        <td>{{ $loop->first ? \Carbon\Carbon::parse($order->order_date)->format('d M Y') : '' }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No sales data found for this period.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="total">Total Sales:</td>
                <td class="total">${{ number_format($totalSales, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>