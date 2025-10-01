  <div class="card-body">
      <div class="section-title">
          <i class="fas fa-chart-bar"></i>Order Statistics
      </div>

      <div class="row text-center">
          <div class="col-md-3 mb-3">
              <div class="stat-item">
                  <div class="stat-number">{{ $paymentsData['order_stats']['total_orders'] }}</div>
                  <div class="stat-label">Total Orders</div>
              </div>
          </div>
          <div class="col-md-3 mb-3">
              <div class="stat-item">
                  <div class="stat-number text-success">{{ $paymentsData['order_stats']['completed_orders'] }}</div>
                  <div class="stat-label">Completed</div>
              </div>
          </div>
          <div class="col-md-3 mb-3">
              <div class="stat-item">
                  <div class="stat-number text-warning">{{ $paymentsData['order_stats']['pending_orders'] }}</div>
                  <div class="stat-label">Pending</div>
              </div>
          </div>
          <div class="col-md-3 mb-3">
              <div class="stat-item">
                  <div class="stat-number text-primary">{{ $paymentsData['order_stats']['total_spent'] }}</div>
                  <div class="stat-label">Total Spent</div>
              </div>
          </div>
      </div>

      @if ($paymentsData['order_stats']['refunded_orders'] > 0)
          <div class="row justify-content-center">
              <div class="col-md-6 text-center">
                  <div class="stat-item">
                      <div class="stat-number text-danger">{{ $paymentsData['order_stats']['total_refunded'] }}</div>
                      <div class="stat-label">Total Refunded ({{ $paymentsData['order_stats']['refunded_orders'] }}
                          orders)</div>
                  </div>
              </div>
          </div>
      @endif
  </div>
