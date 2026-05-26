<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sticker Labels</title>
    <style>


    body {
        font-family: 'SolaimanLipi', sans-serif;
    }
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        table.sheet {
            border-collapse: collapse;
            width: 190mm;
            /* A4 width */
            /* height: 297mm; */
            /* A4 height */
            table-layout: fixed;
            padding: 10px;
        }
        tr {
            height: fit-content;
        }
        td {
            padding: 5px;
        }

        td.label-container {
            width: 40mm;
            /* 3 per row (210mm / 3) */
            height: 35mm;
            /* 8 per column (297mm / 8) */


            padding: 5px;
            vertical-align: top;
            text-align: left;
            box-sizing: border-box;

        }
        td.label-container .label {
             /* border: 1px solid #000; */
            border-radius: 8px;
            padding: 5px;
            vertical-align: top;
            text-align: left;
            box-sizing: border-box;

        }

        .logo {
            text-align: center;
        }

        .logo img {
            width: 100%;
            height: auto;
            margin-bottom: 2px;
        }

        .info p {
            margin: 1px 0;
            font-size: 10px;
            line-height: 1.2;
        }

        .barcode {
            text-align: center;
            margin: 2px auto;
            /* background: rgb(126, 74, 74); */
        }

        .barcode div {
            display: block;
            margin: 0 auto;
        }

        /* Force page break after 24 labels */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
  @php
        $count = 0;
        $total = count($stickers);
    @endphp

    <table class="sheet">
        <tr>
        @foreach($stickers as $item)
            <td class="label-container">
                <div class="label">
                    <div class="logo">
                        <img src="{{ public_path('admin-resource/images/logo/new_log.png') }}" alt="Logo">
                    </div>

                    <div class="info">
                        <p><strong>ID:</strong> {{ $item['id'] }}</p>
                        <p><strong>Name:</strong> {{ $item['name'] }}</p>
                        <p><strong>Phone:</strong> {{ $item['phone'] }}</p>
                        <p><strong>Price:</strong> {{ $item['price'] }}</p>
                        <p><strong>Items:</strong> {{ $item['items'] }}</p>
                    </div>

                    <div class="barcode">
                        {!! DNS1D::getBarcodeHTML((string) $item['phone'], 'C128', 1, 30) !!}
                    </div>
                </div>
            </td>

            @php $count++; @endphp

            {{-- New row after every 3 stickers --}}
            @if($count % 3 == 0)
                </tr><tr>
            @endif

            {{-- Page break after every 9 stickers --}}
            @if($count % 9 == 0)
                </tr></table><div class="page-break"></div><table class="sheet"><tr>
            @endif
        @endforeach

        {{-- Fill remaining cells in last row to keep layout even --}}
        @php
            $remaining = 3 - ($count % 3);
            if ($remaining < 3 && $remaining > 0) {
                for ($i = 0; $i < $remaining; $i++) {
                    echo '<td class="label-container"></td>';
                }
            }
        @endphp
        </tr>
    </table>
</body>
</html>
