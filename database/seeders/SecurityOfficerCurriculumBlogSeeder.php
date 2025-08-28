<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class SecurityOfficerCurriculumBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlogPost::create([
            'title' => '5N-1.140 Security Officer, Recovery Agent, and Private Investigative Intern School Curriculum',
            'slug' => 'security-officer-curriculum-training-requirements-florida',
            'excerpt' => 'Comprehensive guide to Florida\'s security officer training curriculum requirements, including online training standards, record-keeping protocols, and firearms qualification procedures as outlined in regulations 5N-1.140 and 5N-1.132.',
            'content' => '
                <div class="blog-content">
                    <p class="lead-paragraph">This comprehensive guide outlines the essential requirements for security officer training in Florida, covering both basic security officer certification and firearms training protocols. Understanding these regulations is crucial for training schools, instructors, and aspiring security professionals seeking compliance with state standards.</p>
                    
                    <h2 class="section-heading">Security Officer Schools and Training Facilities Requirements</h2>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Examination and Certification Standards</h3>
                        <ul class="feature-list">
                            <li class="feature-item">Passing score requires answering at least <strong class="highlight-text">128 questions correctly</strong>, with no more than 50% being true or false questions</li>
                            <li class="feature-item">Issuance of <strong class="highlight-text">Certificate of Security Officer Training within 3 business days</strong> using Form FDACS-16103</li>
                            <li class="feature-item">Training programs approved by the Florida Criminal Justice Standards and Training Commission are deemed approved by the department</li>
                        </ul>
                    </div>
                    
                    <h2 class="section-heading">Online Training Requirements</h2>
                    <p class="content-text">The following standards must be met for all online security officer training programs:</p>
                    
                    <ul class="feature-list">
                        <li class="feature-item"><strong class="highlight-text">Live Transmission Format:</strong> Class "DI" instructors and students must participate simultaneously</li>
                        <li class="feature-item"><strong class="highlight-text">Florida-Based Instruction:</strong> Online instruction conducted from a school or training facility in Florida</li>
                        <li class="feature-item"><strong class="highlight-text">Department Access:</strong> Live access provided to department investigators for auditing, monitoring, or inspection</li>
                        <li class="feature-item"><strong class="highlight-text">Secure Platform:</strong> Online instruction and testing through secure website using SSL or TLS technology</li>
                        <li class="feature-item"><strong class="highlight-text">Identity Verification:</strong> Student identity verified using U.S. state or federal-issued photo identification</li>
                        <li class="feature-item"><strong class="highlight-text">Attendance Tracking:</strong> Verification and documentation of daily attendance in digital log</li>
                        <li class="feature-item"><strong class="highlight-text">Single Device Access:</strong> Student log-in restricted to single device</li>
                        <li class="feature-item"><strong class="highlight-text">Active Participation:</strong> Security questions included to ensure active student participation</li>
                        <li class="feature-item"><strong class="highlight-text">Reading Time Requirements:</strong> Minimum time requirement for reading screens with text</li>
                        <li class="feature-item"><strong class="highlight-text">Instructor Interaction:</strong> Opportunity for students to submit questions to Class "DI" instructors</li>
                        <li class="feature-item"><strong class="highlight-text">Make-up Instruction:</strong> Delivery of missed class hours through recorded instruction for absent students</li>
                        <li class="feature-item"><strong class="highlight-text">Randomized Testing:</strong> Administration of online tests with randomized questions</li>
                        <li class="feature-item"><strong class="highlight-text">Completion Verification:</strong> Verification of online training completion before reporting to division</li>
                    </ul>
                    
                    <h2 class="section-heading">Record Retention Requirements</h2>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Standard Record-Keeping for Schools and Facilities</h3>
                        <p class="content-text">All schools or facilities administering examinations must maintain the following records:</p>
                        
                        <ul class="feature-list">
                            <li class="feature-item">Schedule including date, time, location, and instructor of each class session</li>
                            <li class="feature-item">Separate files for each course containing materials, reference sources, and graded final exams</li>
                            <li class="feature-item">Log for each class session with student signatures</li>
                            <li class="feature-item">Copy of Certificate of Security Officer Training presented to each student</li>
                            <li class="feature-item">Separate file for each approved instructor with qualifications and license copy</li>
                            <li class="feature-item"><strong class="highlight-text">Records maintained for minimum of two years</strong> and produced upon request</li>
                        </ul>
                    </div>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Additional Requirements for Online Courses</h3>
                        <p class="content-text">Class "DS" security schools or training facilities offering online courses must additionally maintain:</p>
                        
                        <ul class="feature-list">
                            <li class="feature-item">Digital record of student attendance log</li>
                            <li class="feature-item">Records of training sessions and proof of compliance with security protocols</li>
                            <li class="feature-item">Accessibility of records to department investigators upon request</li>
                            <li class="feature-item">Explanation for unavailability of electronic records, with provision within 3 business days</li>
                        </ul>
                    </div>
                    
                    <h2 class="section-heading">5N-1.132 Firearms Training Requirements</h2>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Initial Firearms Qualification</h3>
                        <ul class="feature-list">
                            <li class="feature-item">Completion of <strong class="highlight-text">28 hours of range and classroom training</strong> by Class "K" firearms instructor(s)</li>
                            <li class="feature-item">Classroom training options: in-person or live online instruction with identity and attendance verification</li>
                            <li class="feature-item"><strong class="highlight-text">8 hours of in-person range training</strong> covering safe handling and storage of firearms</li>
                            <li class="feature-item">Qualification allows using specific revolver or semiautomatic handgun calibers for regulated duties</li>
                            <li class="feature-item">Licensees can only carry firearms they have completed training with</li>
                            <li class="feature-item">Submission of original white page from Certificate of Firearms Proficiency for Statewide Firearms License with application</li>
                        </ul>
                    </div>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Annual Requalification</h3>
                        <p class="content-text">Annual submission of the original white page from Certificate of Firearms Proficiency for Statewide Firearms License required before expiration date.</p>
                    </div>
                    
                    <h2 class="section-heading">Firearms Instruction Standards</h2>
                    
                    <ul class="feature-list">
                        <li class="feature-item">Use of <strong class="highlight-text">Firearms Training Manual Student Handbook and Study Guide</strong> (FDACS-P-02079)</li>
                        <li class="feature-item">Use of <strong class="highlight-text">Firearms Training Manual Instructor\'s Guide</strong> (FDACS-P-02078) by Class "K" licensed instructors</li>
                        <li class="feature-item">Audio/video materials may be used as instructional aids but not relied upon solely</li>
                        <li class="feature-item">Classroom training conducted in-person or through live online instruction with verification protocols</li>
                        <li class="feature-item">Certificate generation by Class "K" instructors for Firearms Proficiency</li>
                        <li class="feature-item">Provision of electronic or paper certificate copies to Class "G" students within 3 business days</li>
                        <li class="feature-item">Retention of completed certificates by instructors</li>
                    </ul>
                    
                    <h2 class="section-heading">Firearms Instructor Record-Keeping</h2>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Required Documentation</h3>
                        <ul class="feature-list">
                            <li class="feature-item">Daily schedule maintenance and class logs (in-person and online)</li>
                            <li class="feature-item">Digital logs for online students\' attendance and identity verification methods</li>
                            <li class="feature-item">Copies of issued certificates and student test records</li>
                            <li class="feature-item">Original paper records or scanned electronic records acceptable</li>
                            <li class="feature-item">Records accessible to department investigators upon request</li>
                            <li class="feature-item">Records retained for minimum two years at instructor\'s business location</li>
                            <li class="feature-item">Student records produced for inspection upon request</li>
                        </ul>
                    </div>
                    
                    <h2 class="section-heading">Firearms Online Training Specifications</h2>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Classroom Component</h3>
                        <ul class="feature-list">
                            <li class="feature-item">Up to <strong class="highlight-text">20 hours of classroom training</strong> through live online instruction</li>
                            <li class="feature-item">Class "K" instructors must maintain physical location in Florida</li>
                            <li class="feature-item">Live access for department investigators for auditing and monitoring</li>
                            <li class="feature-item">Secure website with SSL or TLS technology for instruction and testing</li>
                            <li class="feature-item">Student identity verification using U.S. state or federal-issued photo ID</li>
                            <li class="feature-item">Digital attendance documentation and single device login restriction</li>
                        </ul>
                    </div>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Active Participation Requirements</h3>
                        <ul class="feature-list">
                            <li class="feature-item">Security questions (challenges) to ensure active participation</li>
                            <li class="feature-item">At least one security question every two hours of instruction</li>
                            <li class="feature-item">5-minute window for re-attempting unsuccessful responses</li>
                            <li class="feature-item">Failure to respond successfully marked as student absence</li>
                            <li class="feature-item">Instructor discretion for student explanations and make-up opportunities</li>
                        </ul>
                    </div>
                    
                    <div class="requirement-section">
                        <h3 class="subsection-heading">Reading and Discussion Requirements</h3>
                        <ul class="feature-list">
                            <li class="feature-item"><strong class="highlight-text">Minimum reading time:</strong> One minute per every 50 words of text</li>
                            <li class="feature-item">Proration for screens with fewer than 50 words</li>
                            <li class="feature-item">Discussion of content on each screen by instructor required</li>
                            <li class="feature-item">Student question submission opportunity to Class "K" instructors</li>
                            <li class="feature-item">Up to 4 hours of missed class delivery through recorded instruction</li>
                            <li class="feature-item">Question submission allowed for recorded instruction content</li>
                            <li class="feature-item">Online tests administered with randomized questions</li>
                        </ul>
                    </div>
                    
                    <h2 class="section-heading conclusion">Compliance and Best Practices</h2>
                    <p class="content-text">These comprehensive regulations ensure that security officer and firearms training programs maintain the highest standards of education, security, and accountability. Training facilities and instructors must implement robust systems for identity verification, attendance tracking, and record-keeping to remain compliant with Florida Department of Agriculture and Consumer Services requirements.</p>
                    
                    <p class="content-text">By adhering to these detailed specifications, training providers can offer quality education that prepares security professionals for their critical roles while meeting all regulatory obligations. Regular review and implementation of these requirements ensures continued compliance and optimal training outcomes.</p>
                </div>
            ',
            'featured_image' => 'images/blog/security-officer-curriculum-requirements.jpg',
            'tags' => ['security officer training', 'firearms training', 'Florida regulations', 'curriculum requirements', 'compliance', 'online training'],
            'category' => 'Regulations',
            'author' => 'Security Training Group',
            'published_at' => now(),
            'is_published' => true,
            'is_featured' => false,
            'meta_keywords' => 'Florida security officer training, curriculum requirements, 5N-1.140, 5N-1.132, regulations, compliance',
            'meta_description' => 'Complete guide to Florida security officer training curriculum requirements including online training standards, record-keeping protocols, and firearms qualification procedures.',
        ]);
    }
}
