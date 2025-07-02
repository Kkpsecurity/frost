<?php $orders = $content['orders']; ?>
<div class="card">
    <div class="card-head">
        <h3 class="card-title" style="padding-left: 20px;">
            <i class="fa fa-shopping-bag"></i> {{ __('Orders') }}
        </h3>
    </div>
    <div class="card-body">
        <div id="user-message-console"></div>
        <table class="table table-bordered table-striped data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">{{ __('ID') }}</th>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Purchase Date') }}</th>
                    <th>{{ __('Expires') }}</th>
                    <th style="width: 140px; text-align: right;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($user['ProductAuthIDs']))
                    <?php $orders = $content['orders']; ?>
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                            <td style="width: 40px;">{{ $order->id }}</td>
                            <td>{{ $order->product()->title }}</td>
                            <td>{{ \App\Support\Enum\ProductTypes::options($order->product()->type_id )}}</td>
                            <td>{{ date('M d, Y', $order->created_at) }}</td>
                            <td></td>
                            <td><a href="" class="btn btn-sm btn-accent-dark pull-right">Detail</a></td>
                        @endforeach
                    @else
                        <td colspan="5">No Order Found</td>
                    @endif
                @else
                    <td colspan="5">No Order Found</td>
                @endif
            </tbody>
        </table>

        {{ $orders->render() }}
    </div>
</div>
