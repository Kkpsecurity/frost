<?php

$isofmt      = 'ddd MM/DD/YYYY HH:mm:ss';

$row_class   = $Order->refunded_at ? 'refunded' : '' ;
$Payment     = $Order->GetPayment();
$InvoiceID   = $Payment->InvoiceID();
$transid     = $Payment->pp_ppref;
$transtime   = $Payment->TransTime($isofmt);
$payment_amt = $Order->total_price == '0.00' ? '[Free]' : $Order->total_price;
$payment_fee = $Payment->cc_fee;

$User        = $Order->GetUser();

$attended    = \App\Models\StudentLesson::whereIn( 'student_unit_id',
                    \App\Models\StudentUnit::where( 'course_auth_id', $Order->course_auth_id  )->get()->pluck( 'id' )
               )->get()->count() ? 'X' : '';


?>
<tr class="{{ $row_class }}">
  <td nowrap align="right">
    <a href="{{ route( 'admin.temp.orders.show', $Order ) }}">{{ $InvoiceID }}</a>
  </td>
  <td align="right">{{ $Order->CompletedAt($isofmt) }}</td>
  <td nowrap class="copy_to_clipboard">{{ $transid }}</td>
  <td align="right">{{ $transtime }}</td>
  <td nowrap align="right" class="copy_to_clipboard">{{ $payment_amt }}</td>
  <td nowrap align="right" class="copy_to_clipboard">{{ $payment_fee }}</td>
  <td align="center">{{ $attended }}</td>
  <td nowrap class="copy_to_clipboard">{{ $Order->GetCourse() }}</td>
  <td class="copy_to_clipboard">{{ $User }}</td>
  <td class="copy_to_clipboard">{{ $User->email }}</td>
</tr>
