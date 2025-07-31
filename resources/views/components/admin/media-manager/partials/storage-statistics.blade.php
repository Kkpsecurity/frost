  <div class="card card-outline card-success">
      <div class="card-header">
          <h3 class="card-title">
              <i class="fas fa-chart-bar"></i> Storage Statistics
          </h3>
      </div>
      <div class="card-body">
          <div class="row" id="storage-stats">
              @if (isset($stats) && is_array($stats))
                  @foreach ($stats as $category => $stat)
                      <div class="col-md-4 col-sm-6 mb-3">
                          <div class="small-box bg-info">
                              <div class="inner">
                                  <h3>{{ $stat['file_count'] ?? 0 }}</h3>
                                  <p>{{ $stat['name'] ?? 'Unknown' }}</p>
                                  <small>{{ \App\Services\MediaManagerService::formatBytes($stat['total_size'] ?? 0) }}</small>
                              </div>
                              <div class="icon">
                                  <i class="fas fa-folder"></i>
                              </div>
                              <button class="small-box-footer browse-category" data-category="{{ $category }}">
                                  Browse Files <i class="fas fa-arrow-circle-right"></i>
                              </button>
                          </div>
                      </div>
                  @endforeach
              @else
                  <div class="col-12">
                      <div class="alert alert-info">
                          <i class="fas fa-info-circle"></i>
                          Loading storage statistics...
                      </div>
                  </div>
              @endif
          </div>
      </div>
  </div>
