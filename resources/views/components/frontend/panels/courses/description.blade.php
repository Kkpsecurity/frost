 <div class="course-content-section mb-5">
     <h3 class="section-title text-white">Course Overview</h3>
     <div class="course-description-full">
         <p class="lead text-white">
             {{ $course['description'] ?? 'Professional security training course designed to prepare you for success in the security industry.' }}
         </p>

         @if (isset($course['fullDescription']))
             <div class="text-white-50">{!! $course['fullDescription'] !!}</div>
         @else
             @if (str_contains(strtolower($course['badge'] ?? ''), 'd') || str_contains(strtolower($course['type'] ?? ''), 'armed'))
                 <p class="text-white-50">This comprehensive Class D training program prepares you for a successful
                     career as an armed security professional. You'll master firearms safety, legal protocols, crisis
                     management, and professional responsibilities required for armed security work.</p>
                 <p class="text-white-50">Our expert instructors combine decades of law enforcement and private security
                     experience with hands-on training scenarios. You'll learn critical decision-making skills, proper
                     use of force protocols, and emergency response procedures.</p>
                 <p class="text-white-50">Upon successful completion, you'll receive state-approved certification
                     qualifying you to work as an armed security officer in Florida and reciprocal states. Small class
                     sizes ensure personalized instruction and maximum skill development.</p>
             @else
                 <p class="text-white-50">This comprehensive Class G training program provides essential skills for
                     unarmed security professionals and private investigators. You'll master surveillance techniques,
                     professional communication, legal boundaries, and ethical conduct standards.</p>
                 <p class="text-white-50">Our experienced instructors emphasize practical application through real-world
                     scenarios and case studies. You'll develop professional report writing skills, learn de-escalation
                     techniques, and understand your legal authority and limitations.</p>
                 <p class="text-white-50">Upon completion, you'll receive state certification opening doors to careers
                     in corporate security, retail loss prevention, private investigation, and facility protection. Our
                     graduates are highly sought after by employers throughout Florida.</p>
             @endif
         @endif
     </div>
 </div>
