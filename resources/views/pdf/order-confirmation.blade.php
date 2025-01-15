<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Potwierdzenie zamówienia</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .customer-info { margin-bottom: 30px; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; }
        .total { margin-top: 20px; text-align: right; }
        .images-section { margin-top: 20px; }
        .item-images { margin-bottom: 20px; }
        .image { max-width: 200px; margin: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Potwierdzenie zamówienia #{{ $orderId }}</h1>
        <p>Data: {{ $orderDate }}</p>
    </div>

    <div class="customer-info">
        <h2>Dane klienta:</h2>
        <p>{{ $customer['name'] }} {{ $customer['surname'] }}</p>
        <p>{{ $customer['email'] }}</p>
        <p>{{ $customer['address']['street'] }} {{ $customer['address']['house'] }}</p>
        <p>{{ $customer['address']['postal'] }} {{ $customer['address']['city'] }}</p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Produkt</th>
                <th>Ilość</th>
                <th>Cena za sztukę</th>
                <th>Suma</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['item']['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['item']['unit_price'] }} €$</td>
                    <td>{{ $item['quantity'] * $item['item']['unit_price'] }} €$</td>
                </tr>
            @endforeach        </tbody>
    </table>

    <div class="total">
        <h3>Suma całkowita: {{ $total }} €$</h3>
    </div>

    <div class="images-section">
        <h2>Zdjęcia produktów:</h2>
        @foreach($items as $item)
            <div class="item-images">
                <h3>{{ $item['item']['name'] }}</h3>
                
                <!-- Screenshot -->
                @if($item['item']['screenshot_path'])
                    <img class="image" src="{{ public_path($item['item']['screenshot_path']) }}" alt="Screenshot">
                @endif

                <!-- Logos -->
                @if(is_array($item['item']['logos']))
                    @foreach($item['item']['logos'] as $logo)
                        <img class="image" src="{{ public_path($logo) }}" alt="Logo">
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>