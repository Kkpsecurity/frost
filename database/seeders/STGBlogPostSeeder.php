<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlogPost;

class STGBlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlogPost::updateOrCreate(
            ['slug' => 'stg-online-security-training-advantages'],
            [
            'title' => 'Advantages of Choosing Security Training Group (STG) for Online Security Guard Training',
            'slug' => 'stg-online-security-training-advantages',
            'excerpt' => 'Discover why Security Training Group (STG) is the premier choice for online security guard training in Florida. Learn about state-compliant courses, advanced security measures, and comprehensive firearms training.',
            'featured_image' => '/images/Security-Page-1.jpg',
            'content' => '<p>Security Training Group (STG) emerges as the leading online security guard training provider, offering a comprehensive curriculum that meets the state\'s stringent requirements. With a focus on delivering high-quality instruction and practical experience, STG sets itself apart by combining online learning with in-person range training, ensuring students receive a well-rounded and effective education.</p>

<h2>Adapting to State Regulations</h2>
<p>In April 2022, Governor Ron DeSantis signed a law allowing online security officer training. STG fully aligns with these regulations, offering in-person training courses and live virtual training through a state-approved Learning Management System. This compliance ensures that students receive training that meets the standards set by the state law.</p>

<h2>Enhanced Security Measures</h2>
<p>Unlike the emergency order during the Covid-19 pandemic, which allowed training through various platforms without proper accountability, STG employs a robust learning management system incorporating advanced security measures. This ensures the integrity, confidentiality, and accountability of the training process.</p>

<h2>Online Security Training for Unarmed Class D License</h2>
<ul>
<li><strong>Live Transmission Format:</strong> Students participate in live virtual classes facilitated by experienced Class "DI" instructors, ensuring real-time interaction and engagement.</li>
<li><strong>Secure Online Environment:</strong> All instruction and testing occur on a secure website that uses SSL or TLS technology to safeguard students\' information.</li>
<li><strong>Identity Verification:</strong> Students\' identities are verified using U.S. state or federal-issued photo identification, ensuring the integrity of the training process.</li>
<li><strong>Attendance Tracking:</strong> Daily attendance is documented and verified through a digital log, providing a transparent record of student participation.</li>
<li><strong>Active Student Participation:</strong> STG incorporates security questions to ensure students actively engage with the material, promoting a deeper understanding of the content.</li>
<li><strong>Thorough Instruction and Assessment:</strong> The online training includes a minimum reading time for screens with text, ensuring students spend adequate time comprehending the course material.</li>
</ul>

<h2>Online Security Training for Armed Class G Statewide License</h2>
<ul>
<li><strong>Firearm Online Training:</strong> Students complete up to 20 hours of classroom instruction through live online sessions.</li>
<li><strong>Qualified Instructors:</strong> Class "K" instructors, located in Florida, deliver the online instruction, ensuring expertise and adherence to state guidelines.</li>
<li><strong>Live Access for Monitoring:</strong> The online courses are accessible to department investigators for auditing, monitoring, and inspection.</li>
<li><strong>Comprehensive Record keeping:</strong> STG maintains thorough records of training sessions, instructor information, and compliance with security protocols.</li>
</ul>

<h2>Collaboration with Leading Security Schools</h2>
<p><strong>STG is a collaboration between the esteemed S2 Institute and Invictus Security</strong>, the renowned security schools in Florida. Their expertise and experience in security training allow STG to deliver the highest standard of online security training in Florida. With a strong reputation and track record in the industry, the partnership ensures that STG\'s curriculum is designed to meet the evolving needs of security professionals.</p>

<h2>Comprehensive Approach to Firearms Training</h2>
<p>One of the key advantages of choosing STG for online virtual security guard training is its comprehensive approach to firearms training. While other programs may focus solely on online instruction, STG goes above and beyond by offering the opportunity for students to register for the essential 8 hours of in-person range training.</p>
<p>This hands-on training covers critical aspects such as safe handling and storing firearms, providing students with practical skills under the guidance of qualified instructors. By incorporating in-person range training into the program, STG ensures students receive a well-rounded and complete firearms training experience.</p>

<h2>Convenience and Statewide Accessibility</h2>
<p>STG\'s online platform provides convenience and accessibility to students throughout the state of Florida. By offering online instruction and the flexibility to complete courses remotely, STG eliminates the need for individuals to travel long distances or disrupt their daily schedules.</p>
<p>STG\'s network of affiliate Class "K" instructor partners across different locations further enhances convenience. Students can find suitable training facilities nearby, reducing travel time and ensuring a seamless learning experience.</p>

<h2>Focus on Quality Education and Career Advancement</h2>
<p>STG aims to equip students with the knowledge, skills, and confidence necessary to excel as security officers. Through its comprehensive curriculum, experienced instructors, and state-of-the-art online platform, STG ensures that students receive a high-quality education that prepares them for the challenges and responsibilities of the profession.</p>

<h2>Conclusion</h2>
<p>Security Training Group (STG) is the premier choice for individuals seeking online security training in Florida. With a curriculum that adheres to state regulations, incorporates advanced security measures, and offers comprehensive firearms training classes online, STG ensures that students receive a well-rounded education that prepares them for successful careers in the security industry.</p>',
            'meta_description' => 'Discover why Security Training Group (STG) is Florida\'s premier online security guard training provider. State-compliant courses, advanced security measures, comprehensive firearms training, and expert instruction.',
            'meta_keywords' => 'Security Training Group, STG, online security training, Florida security guard, Class D license, Class G license, firearms training, security education',
            'author' => 'Security Training Expert',
            'category' => 'Training Programs',
            'tags' => json_encode(['Online Training', 'Security Guard', 'Florida', 'Firearms', 'Professional Development']),
            'read_time' => 12,
            'is_published' => true,
            'is_featured' => true,
            'published_at' => now(),
        ]);
    }
}
