<?php

$isofmt = 'ddd MM/DD HH:mm:ss';
$Payment = $Order->GetPayment();

$canUpdateSaleDetails = $Payment->cc_last_result === 0 && $Payment->pp_ppref && !$Payment->pp_is_sandbox;
$canRefund = Auth::user()->IsAdministrator() && $Order->CanRefund();

?>
@extends('layouts.admin')


@section('content')
    @include('admin.temp.partials.messages')


    <div class="tablewrapper">

        <table border="0" cellspacing="0" cellpadding="0">
            <tr class="subheader">
                <td colspan="2">Order Details</td>
            </tr>
            <tr>
                <td>OrderID</td>
                <td>{{ $Order->id }}</td>
            </tr>
            <tr>
                <td>Completed</td>
                <td>{{ $Order->CompletedAt($isofmt) }}</td>
            </tr>
            @if ($Order->refunded_at)
                <tr class="refunded">
                    <td>Refunded</td>
                    <td>{{ $Order->RefundedAt($isofmt) }}</td>
                </tr>
            @endif

            <tr>
                <td>Payment</td>
                <td>{{ $Payment->total_price }}</td>
            </tr>

            @if ($DiscountCode = $Order->GetDiscountCode())
                <tr>
                    <td>DiscountCode</td>
                    <td class="mono">{{ $DiscountCode->code }}</td>
                </tr>
            @endif

            <tr>
                <td>Student</td>
                <td class="copy_to_clipboard">{{ $Order->GetUser() }}</td>
            </tr>
            <tr>
                <td>Course</td>
                <td class="copy_to_clipboard">{{ $Order->GetCourse()->title_long }}</td>
            </tr>

            <tr class="subheader">
                <td colspan="2">PayPal Details</td>
            </tr>
            <tr>
                <td>InvoiceID</td>
                <td class="copy_to_clipboard">{{ $Payment->InvoiceID() }}</td>
            </tr>
            <tr>
                <td>TransTime</td>
                <td>
                    @if ($Payment->cc_transtime)
                        {{ $Payment->TransTime($isofmt) }} (PST)
                    @elseif ($canUpdateSaleDetails)
                        <a href="{{ route('admin.temp.orders.getsaledetails', $Order) }}"
                            class="btn btn-sm btn-info">Update Sale Details</a>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Transaction ID</td>
                <td class="copy_to_clipboard">{{ $Payment->pp_ppref }}</td>
            </tr>
            <tr>
                <td>CC Amount</td>
                <td class="copy_to_clipboard">{{ $Payment->cc_amount }}</td>
            </tr>
            <tr>
                <td>CC Fee</td>
                <td class="copy_to_clipboard">{{ $Payment->cc_fee }}</td>
            </tr>

            @if ($canRefund)
                <tr>
                    <td colspan="2">
                        <form method="post" action="{{ route('admin.temp.orders.refund', $Order) }}">
                            @csrf
                            <input type="button" class="btn btn-danger confirmThis" value="Issue Refund" />
                        </form>
                    </td>
                </tr>
            @endif


            @if ($Order->refunded_at)
                @if (!$Order->CourseAuth)
                    <tr>
                        <td colspan="2">No CourseAuth</td>
                    </tr>
                @elseif ($Order->CourseAuth->disabled_at)
                    <tr>
                        <td colspan="2">
                            <form method="post" action="{{ route('admin.temp.orders.reactivatecourseauth', $Order) }}">
                                @csrf
                                <input type="button" class="btn btn-warning confirmThis" value="Reactivate CourseAuth" />
                            </form>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="2"><b>CourseAuth {{ $Order->CourseAuth->id }} is Active</b></td>
                    </tr>
                @endif
            @endif

        </table>
        
    </div>


    @if (Auth::user()->IsSysAdmin())
        <div class="tablewrapper">
            <pre style="border: 1px solid #666; background-color: white;">
            {{ print_r(json_decode($Payment->cc_last_data), true) }}</pre>
        </div>
    @endif


    @include('admin.temp.partials.asset-loader')
@endsection
