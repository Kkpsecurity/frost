 <div class="col-lg-4 col-md-12">
     <div class="course-sidebar">
         {{-- Pricing Card --}}
         <div class="card mb-4 shadow-sm"
             style="background: linear-gradient(145deg, rgba(37, 99, 235, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%); border: 1px solid rgba(255, 255, 255, 0.1);">
             <div class="card-body text-center">
                 <div class="price-display mb-3">
                     <span class="price-large text-white"
                         style="font-size: 2rem; font-weight: bold;">${{ number_format($course['price'] ?? 299, 2) }}</span>
                     <span class="price-currency text-white-50">USD</span>
                 </div>
                 <p class="text-white-50 mb-4">Complete training package with certification</p>

                 <div class="d-grid gap-2">
                     <a href="{{ route('courses.enroll', $course['id'] ?? 1) }}" class="btn btn-primary btn-lg">
                         <i class="fas fa-credit-card me-2"></i>Enroll Now
                     </a>
                     <a href="#" class="btn btn-outline-light" data-bs-toggle="modal"
                         data-bs-target="#contactModal">
                         <i class="fas fa-phone me-2"></i>Contact Us
                     </a>
                 </div>

                 <div class="enrollment-features mt-4">
                     <div class="feature-item d-flex align-items-center mb-2">
                         <i class="fas fa-shield-check text-success me-2"></i>
                         <small class="text-white-50">Money-back guarantee</small>
                     </div>
                     <div class="feature-item d-flex align-items-center mb-2">
                         <i class="fas fa-certificate text-success me-2"></i>
                         <small class="text-white-50">State-approved certification</small>
                     </div>
                     <div class="feature-item d-flex align-items-center">
                         <i class="fas fa-headset text-success me-2"></i>
                         <small class="text-white-50">24/7 student support</small>
                     </div>
                 </div>
             </div>
         </div>

         {{-- Quick Info Card --}}
         <div class="card mb-4"
             style="background: linear-gradient(145deg, rgba(37, 99, 235, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%); border: 1px solid rgba(255, 255, 255, 0.1);">
             <div class="card-header"
                 style="background: rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                 <h5 class="mb-0 text-white">Course Information</h5>
             </div>
             <div class="card-body">
                 <div class="info-item d-flex justify-content-between mb-2">
                     <span class="info-label text-white-50">Duration:</span>
                     <span class="info-value text-white">{{ $course['duration'] ?? '3-5 Days' }}</span>
                 </div>
                 <div class="info-item d-flex justify-content-between mb-2">
                     <span class="info-label text-white-50">Format:</span>
                     <span class="info-value text-white">{{ $course['format'] ?? 'Hybrid' }}</span>
                 </div>
                 <div class="info-item d-flex justify-content-between mb-2">
                     <span class="info-label text-white-50">Level:</span>
                     <span class="info-value text-white">{{ $course['level'] ?? 'Beginner' }}</span>
                 </div>
                 <div class="info-item d-flex justify-content-between mb-2">
                     <span class="info-label text-white-50">Language:</span>
                     <span class="info-value text-white">{{ $course['language'] ?? 'English' }}</span>
                 </div>
                 <div class="info-item d-flex justify-content-between">
                     <span class="info-label text-white-50">Students:</span>
                     <span class="info-value text-white">{{ $course['studentsEnrolled'] ?? '200+' }}
                         enrolled</span>
                 </div>
             </div>
         </div>


     </div>
 </div>
