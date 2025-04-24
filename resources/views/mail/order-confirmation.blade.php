<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        @include('mail.mail-css')
    </head>
    <body>
        <div class="wrapper">
            @include('mail.header')
            <div class="middle">
              <b>Hello {{ $data->userDetail->first_name }}!</b>
              <br/>
              <br/>
              <p>We’re happy to let you know that we’ve received your order.</p>
              <br/>
              <p>Order No.: <b>{{ $data->order_no }}</b></p>
              <p>Date: <b>{{ date_format($data->created_at,"M d, Y") }} </b></p>
              <br/>
              <table class="order_table">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Variation</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($data->items as $item)
                    <tr class="data-column">
                      <td data-column="Item">
                        {{ $item->accessory->title }}
                      </td>
                      <td data-column="Variation">{{ $item->variation ? $item->variation->name : 'N/A' }}</td>
                      <td data-column="Quantity">{{ $item->quantity }}</td>
                      <td data-column="Price">₱{{ number_format($item->variation ? $item->variation->price : $item->accessory->price, '2', '.', '') }}</td>
                      <td data-column="Subtotal">₱{{ number_format($item->original_amount, '2', '.', '') }}</td>
                    </tr>
                  @endforeach
                  <tr class="no_data_column">
                    <td>Subtotal</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>₱{{ number_format($data->total_original_amount, '2', '.', '') }}</td>
                  </tr>
                  <tr class="no_data_column">
                    <td>Total Discount</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>₱{{ number_format($data->total_discounted_amount, '2', '.', '') }}</td>
                  </tr>
                  <tr class="total_row">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>₱{{ number_format($data->total_amount, '2', '.', '') }}</td>
                  </tr>
                </tbody>
              </table>
              <br/>
              <!-- <p class="black disclaimer">For PWD/Senior Citizen disclaimer here</p> -->
            </div>
            @include('mail.footer')
        </div>
    </body>
</html>