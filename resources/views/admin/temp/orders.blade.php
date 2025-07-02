@extends('layouts.admin')


@section('content')
    @include('admin.temp.partials.messages')


    <div class="tablewrapper">
      <div class="row">
        <div class="col-auto">
          <form method="post" action="{{ route('admin.temp.orders.search_id') }}">
          @csrf
          <div class="input-group">
            <input type="text" name="search_id" id="search_id" class="form-control"
                   placeholder="Invoice ID or Transaction ID" value="{{ old( 'search_id')  }}" />
            <input type="submit" value="Seach" class="btn btn-primary" />
          </div>
          </form>
        </div>
        <div class="col-auto">
          <form method="post" action="{{ route('admin.temp.orders.search_name') }}">
          @csrf
          <div class="input-group">
            <input type="text" name="search_name" id="search_name" class="form-control"
                   placeholder="Student Name" value="{{ old('search_name') }}" />
            <input type="submit" value="Seach" class="btn btn-primary" />
          </div>
          </form>
        </div>

        <div class="col-auto">
          <a href="{{ route( 'admin.temp.orders.csv.query' ) }}" class="btn btn-primary">
            <i class="fa fa-file-csv"></i>
          </a>
        </div>
      </div>

    </div>


    <div class="tablewrapper">

        <table border="1" cellspacing="0" cellpadding="0">
            <tr class="header">
                <td align="center">Invoice ID</td>
                <td align="center">Completed</td>
                <td nowrap align="center">CC Transaction ID</td>
                <td nowrap align="center">TransTime (PST)</td>
                <td nowrap>CC Amt</td>
                <td nowrap>CC Fee</td>
                <td>ATT</td>
                <td>Course</td>
                <td nowrap>Student Name</td>
                <td>Email</td>
            </tr>

            @foreach ($Orders as $Order)
                @include('admin.temp.order-partials.orders-item')
            @endforeach

        </table>
        <span class="d-flex float-end">{{ $Orders->links('pagination::bootstrap-4') }}</span>


        <div style="margin: 10px">
            <a class="btn btn-danger" href="{{ route('admin.temp.orders.getmissingorderdetails') }}">Load Missing PayPal
                Details</a>
        </div>

    </div>


    @include('admin.temp.partials.asset-loader')

    <style>
        table>* {
            font-size: 14px;
        }

        tr,
        td {
            padding: 3px 6px;
        }
    </style>
@endsection
