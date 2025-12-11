<div class="blog-content-wrapper p-4 p-lg-5 rounded-4 shadow-lg frost-surface" style="background: var(--frost-secondary-bg, #0f172a);">
  <style>
    /* ===== Frost Theme Upgrades (accessible, high-contrast) ===== */
    .frost-surface{
      background:
        radial-gradient(1200px 600px at 10% -10%, rgba(255,255,255,.06), transparent 60%),
        radial-gradient(900px 500px at 110% 10%, rgba(59,130,246,.08), transparent 55%),
        var(--frost-secondary-bg, #0f172a);
      color: #e5e7eb;
    }
    .frost-heading{
      color: #e2e8f0;
      letter-spacing:.3px;
    }
    .frost-subtle{ color:#cbd5e1 }
    .frost-accent{ color: var(--frost-primary, #60a5fa) }
    .frost-chip{
      display:inline-flex; align-items:center; gap:.5rem;
      padding:.35rem .6rem; border-radius:999px;
      background: rgba(148,163,184,.12); color:#e5e7eb; font-weight:600; font-size:.85rem;
      border:1px solid rgba(148,163,184,.25)
    }
    .frost-card{
      background: rgba(15,23,42,.65);
      border:1px solid rgba(148,163,184,.2);
      border-radius: .9rem; padding:1.25rem 1.25rem;
      box-shadow: 0 8px 24px rgba(2,6,23,.35);
    }
    .frost-card + .frost-card{ margin-top:1rem }
    .frost-divider{ border-color: rgba(148,163,184,.25) !important }
    .frost-link{ color: var(--frost-primary, #60a5fa); text-decoration:none }
    .frost-link:hover{ text-decoration:underline }
    .toc{
      position: sticky; top: 1rem;
      background: rgba(2,6,23,.55);
      border:1px solid rgba(148,163,184,.25);
      border-radius:.75rem; padding:1rem;
    }
    .toc a{ display:block; padding:.4rem .5rem; border-radius:.5rem; color:#cbd5e1; text-decoration:none; }
    .toc a:hover{ background: rgba(59,130,246,.15); color:#e5e7eb }
    .section-anchor{ scroll-margin-top: 90px }
    .check-grid{ display:grid; grid-template-columns: repeat(1,minmax(0,1fr)); gap:.75rem }
    @media (min-width:768px){ .check-grid{ grid-template-columns: repeat(2,minmax(0,1fr)); } }
    .check-item{
      display:flex; gap:.75rem; align-items:flex-start;
      padding:.75rem .9rem; border-radius:.7rem;
      background: rgba(59,130,246,.08); border:1px solid rgba(59,130,246,.25)
    }
    .check-item .icon{ width:1.35rem; display:flex; justify-content:center; margin-top:.05rem; color:#22c55e }
    .kbd{
      border:1px solid rgba(148,163,184,.3); border-bottom-width:2px; padding:.1rem .45rem; border-radius:.4rem;
      background: rgba(148,163,184,.12); font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size:.85rem;
      color:#e5e7eb
    }
    .muted{ color:#94a3b8 }
    .badge-frost{
      background: #d9e631; color:#0b1220; font-weight:700; border:0
    }
    .cta{
      background: linear-gradient(180deg, rgba(59,130,246,.15), rgba(59,130,246,.05));
      border:1px solid rgba(148,163,184,.3);
      border-radius:1rem;
    }
    /* Ensure headings are legible over dark bg */
    .ensuring-compliance h1{
      background: linear-gradient(90deg, rgba(59,130,246,.18), transparent);
      color:#e2e8f0; border-radius:.6rem; padding: .75rem 1rem; border:1px solid rgba(148,163,184,.25)
    }
    .ensuring-compliance h2{
      color:#e5e7eb; font-size:1.25rem; margin-top:1.25rem; padding-top:.25rem;
      border-bottom:1px dashed rgba(148,163,184,.35); padding-bottom:.35rem
    }
    .ensuring-compliance p{ color:#dbeafe }
    .ensuring-compliance li{ color:#cbd5e1 }
    .article-footer .badge{ border-radius:.6rem; padding:.5rem .6rem }
  </style>

  <div class="ensuring-compliance">

    <div class="row g-4">
      <!-- TOC -->
      <aside class="col-lg-4">
        <nav class="toc">
          <div class="fw-bold frost-subtle mb-2">On this page</div>
          <a href="#s1">1) Initial Firearms Qualification</a>
          <a href="#s2">2) Annual Reporting Requirement</a>
          <a href="#s3">3) Firearms Instruction</a>
          <a href="#s4">4) Instructor Recordkeeping</a>
          <a href="#s5">5) Online Classroom Standards</a>
          <a href="#s6">6) Instructor Requirements (Online)</a>
          <a href="#s7">7) LMS Compliance Features</a>
          <a href="#conclusion">Conclusion</a>
        </nav>
      </aside>

      <!-- Content -->
      <main class="col-lg-8">
        <div class="frost-card">
          <p class="mb-0">
            With the increasing demand for online security training programs, it is essential to adhere to strict guidelines
            and regulations set forth by governing bodies. One such set of regulations is outlined in the <span class="kbd">5N-1.132</span> Firearms Training rules.
            This article shows how the STG platform aligns with these requirements—covering initial qualification, annual requalification,
            instruction standards, instructor records, and online-training protocols.
          </p>
        </div>

        <!-- Section Cards -->
        <section id="s1" class="section-anchor frost-card">
          <h2 class="mb-2"><i class="fas fa-graduation-cap frost-accent me-2"></i>1. Initial Firearms Qualification</h2>
          <div class="check-grid">
            <div class="check-item"><span class="icon"><i class="fas fa-check-circle"></i></span><div>28-hour range + classroom satisfied via live online instruction by Class “K” instructors.</div></div>
            <div class="check-item"><span class="icon"><i class="fas fa-check-circle"></i></span><div>Identity, attendance, and completion verified and logged.</div></div>
            <div class="check-item"><span class="icon"><i class="fas fa-check-circle"></i></span><div>Integrated scheduling for required 8 hours of in-person range training with affiliate partners.</div></div>
          </div>
        </section>

        <section id="s2" class="section-anchor frost-card">
          <h2 class="mb-2"><i class="fas fa-file-upload frost-accent me-2"></i>2. Annual Firearms Reporting Requirement</h2>
          <p class="mb-2">Licensees submit the Certificate of Firearms Proficiency (Statewide Firearms License) digitally; Class “K” instructors can file directly to the state.</p>
          <div class="muted">Deadline adherence prompts + receipt logs reduce reporting delays.</div>
        </section>

        <section id="s3" class="section-anchor frost-card">
          <h2 class="mb-2"><i class="fas fa-book frost-accent me-2"></i>3. Firearms Instruction</h2>
          <div class="check-grid">
            <div class="check-item"><span class="icon"><i class="fas fa-check-circle"></i></span><div>Student Handbook & Study Guide accessible online (initial + annual requal scope).</div></div>
            <div class="check-item"><span class="icon"><i class="fas fa-check-circle"></i></span><div>Instructor’s Guide supports Class “K” delivery and compliance.</div></div>
          </div>
        </section>

        <section id="s4" class="section-anchor frost-card">
          <h2 class="mb-2"><i class="fas fa-clipboard-list frost-accent me-2"></i>4. Instructor Recordkeeping</h2>
          <p class="mb-2">Immutable records include instructor name, license, session metadata, and security footprints.</p>
          <div class="muted">Investigator-ready retrieval with immediate access.</div>
        </section>

        <section id="s5" class="section-anchor frost-card">
          <h2 class="mb-2"><i class="fas fa-desktop frost-accent me-2"></i>5. Online Firearm Classroom Training</h2>
          <div class="check-grid">
            <div class="check-item"><span class="icon"><i class="fas fa-check-circle"></i></span><div>Live transmission format; one device per student enforced.</div></div>
            <div class="check-item"><span class="icon"><i class="fas fa-check-circle"></i></span><div>TLS/SSL security; session hardening & anti-spoofing.</div></div>
            <div class="check-item"><span class="icon"><i class="fas fa-check-circle"></i></span><div>Photo ID verification (state/federal) + digital attendance logs.</div></div>
          </div>
        </section>

        <section id="s6" class="section-anchor frost-card">
          <h2 class="mb-2"><i class="fas fa-user-tie frost-accent me-2"></i>6. Instructor Requirements for Online Courses</h2>
          <p class="mb-2">Comprehensive logs for each session, instructor credentials, and security compliance details maintained in-platform.</p>
          <div class="muted">One-click export for investigator audits.</div>
        </section>

        <section id="s7" class="section-anchor frost-card">
          <h2 class="mb-3"><i class="fas fa-cogs frost-accent me-2"></i>7. LMS Compliance Features (Highlights)</h2>
          <ul class="mb-3">
            <li><strong>(a)</strong> Real-time audio/video for Class “K” instructors and students (true live format).</li>
            <!-- Add remaining items here as needed -->
          </ul>
          <div class="alert alert-dark border frost-divider mb-0" role="alert" style="background: rgba(2,6,23,.35); color:#e5e7eb;">
            Tip: Use the <span class="kbd">Compliance → Reports</span> panel to export quarterly summaries with instructor signatures.
          </div>
        </section>

        <section id="conclusion" class="section-anchor frost-card">
          <h2 class="mb-2"><i class="fas fa-flag-checkered frost-accent me-2"></i>Conclusion</h2>
          <p class="mb-0">STG delivers end-to-end 5N-1.132 compliance: live instruction, verified identity/attendance, robust records, and hardened online delivery. Result: faster filings, cleaner audits, safer training.</p>
        </section>

        <!-- CTA -->
        <div class="cta p-4 p-md-5 mt-4 text-center">
          <h4 class="frost-heading">Ready to Start Your Security Career?</h4>
          <p class="mb-3 frost-subtle">Get the training and certification you need to become a professional security officer in Florida.</p>
          <a href="{{ url('/courses') }}" class="btn btn-frost-primary btn-lg me-2">
            <i class="fas fa-graduation-cap me-2"></i>View Training Programs
          </a>
          <a href="{{ url('/contact') }}" class="btn btn-outline-light btn-lg">
            <i class="fas fa-phone me-2"></i>Get More Info
          </a>
        </div>

        <!-- Footer -->
        <div class="article-footer mt-5 pt-4 border-top frost-divider">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h6 class="frost-accent">Tags:</h6>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-frost-accent text-dark">Security Compliance</span>
                <span class="badge bg-frost-accent text-dark">Online Training</span>
                <span class="badge bg-frost-accent text-dark">Firearms License</span>
              </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
              <h6 class="frost-accent">Share:</h6>
              <div class="d-flex gap-2 justify-content-md-end">
                <a href="#" class="btn btn-sm btn-outline-light"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="btn btn-sm btn-outline-light"><i class="fab fa-twitter"></i></a>
                <a href="#" class="btn btn-sm btn-outline-light"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="btn btn-sm btn-outline-light"><i class="fas fa-share-alt"></i></a>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</div>
