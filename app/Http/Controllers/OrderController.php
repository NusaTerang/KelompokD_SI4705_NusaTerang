use App\Services\Midtrans\CreateSnapTokenService; // => put it at the top of the class

public function show(Order $order)
     {
         $snapToken = $order->snap_token;
         if (is_null($snapToken)) {
             // If snap token is still NULL, generate snap token and save it to database

             $midtrans = new CreateSnapTokenService($order);
             $snapToken = $midtrans->getSnapToken();

             $order->snap_token = $snapToken;
             $order->save();
         }

         return view('orders.show', compact('order', 'snapToken'));
     }


<!-- /* need to create payment execurtion function. When the button is clicked, it will display a Midtrans payment pop up. Here's an example of the button from the article that i found: */
/* @if ($order->payment_status == 1)
     <button class="btn btn-primary" id="pay-button">Pay Now</button>
@else
     Payment successful
@endif */ -->

<!-- /* The article provide an id attribute with a pay-button value. This attribute will later be used for binding by Javascript. Next, still in the view add the following Javascript.
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        const payButton = document.querySelector('#pay-button');
        payButton.addEventListener('click', function(e) {
            e.preventDefault();

            snap.pay('{{ $snapToken }}', {
                // Optional
                onSuccess: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                }
            });
        });
    </script>

*/ -->