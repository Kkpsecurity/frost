@extends('layouts.app')


@section('content')
    <div class="dashboard-area bg-color area-padding">
        <div class="container">
            <div class="row">
               <div class="col-md-12 col-sm-12 col-xs-12">
                   <div class="dashboard-head">
                       <div class="row">
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <div class="single-dash-head">
                                    <div class="dashboard-profile">
                                        <div class="profile-content">
                                            <img src="img/about/profile.png" alt="">
                                            <span class="pro-name">Mickel jhon</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <div class="single-dash-head">
                                    <div class="dashboard-amount">
                                        <div class="amount-content">
                                            <i class="flaticon-028-money"></i>
                                            <span class="pro-name">Balance: $440</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <div class="single-dash-head">
                                    <div class="dashboard-amount">
                                        <div class="amount-content">
                                            <a class="edit-icon" href="a-add-bank.html"><i class="ti-pencil-alt"></i></a>
                                            <i class="flaticon-043-bank-2"></i>
                                            <span class="pro-name">Bank Account</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <div class="single-dash-head">
                                    <div class="dashboard-amount">
                                        <div class="amount-content">
                                            <a class="edit-icon" href="a-card-number.html"><i class="ti-pencil-alt"></i></a>
                                            <i class="flaticon-050-credit-card-2"></i>
                                            <span class="pro-name">Card Number</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                       </div>
                   </div>
               </div>
           </div>
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <aside class="sidebar">
                        <div class="dashboard-side">
                            <ul>
                                <li><a href="a-dashboard.html"><i class="ti-dashboard"></i>Dashboard</a></li>
                                <li><a href="a-send-money.html"><i class="ti-new-window"></i>Send Money</a></li>
                                <li><a href="a-request-money.html"><i class="ti-receipt"></i>Request Money</a></li>
                                <li><a href="a-withdraw-money.html"><i class="ti-import"></i>Withdraw Money</a></li>
                                <li><a href="a-deposite-money.html"><i class="ti-wallet"></i>Deposite Money</a></li>
                                <li><a href="a-currency-change.html"><i class="ti-stats-up"></i>Currency Exchange</a></li>
                                <li><a href="a-add-bank.html"><i class="ti-pencil-alt"></i>Bank Account</a></li>
                                <li class="active"><a href="a-card-number.html"><i class="ti-credit-card"></i>Card Number</a></li>
                                <li><a href="a-transection-log.html"><i class="ti-layout-list-thumb"></i>Transection Log</a></li>
                                <li><a href="a-setting-money.html"><i class="ti-settings"></i>Settings</a></li>
                            </ul>
                        </div>
                        <div class="dashboard-support">
                            <div class="support-inner">
                                <div class="help-support">
                                    <i class="flaticon-107-speech-bubbles"></i>
                                    <a href="contact.html"><span class="help-text">Need Help?</span></a>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <div class="dashboard-content">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="send-money-form add-bank-form">
                                    <div class="form-text">
                                        <h4 class="form-top">Add Card Number</h4>
                                        <div class="form-inner">
                                            <form action="#">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                       <label for="m-text">Card Number</label>
                                                       <input type="number" value="0" id="m-text">
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                       <label for="currencyfrr">Curreny</label>
                                                         <select name="currency-select" id="currencyfrr">
                                                            <option value="position">USD</option>
                                                            <option value="position">EUR</option>
                                                            <option value="position">KSR</option>
                                                            <option value="position">INR</option>
                                                            <option value="position">BDT</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="email" placeholder="Card Name/ Email">
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <button type="submit">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection