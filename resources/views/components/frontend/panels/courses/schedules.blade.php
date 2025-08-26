{{-- Course Schedules Panel (Improved) --}}
<div class="frost-secondary-bg py-5" id="schedules">
  <div class="container">
    <div class="row mb-4">
      <div class="col-12 text-center">
        <h2 class="text-white m-0">Course Schedules</h2>
        <p class="text-white-50 m-0">Find a date and book your class</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-3 col-md-4">
        <aside class="schedule-sidebar" aria-label="Course filters">
          <h5 class="text-white mb-3">Course Types</h5>
          <div class="course-filter-buttons d-grid gap-2">
            <button class="btn btn-outline-light btn-sm active" data-course="all">
              All Courses <span class="badge bg-light text-dark ms-1" id="count-all">0</span>
            </button>
            <button class="btn btn-outline-light btn-sm" data-course="d40">
              Class D (Armed) — D40 <span class="badge bg-primary ms-1" id="count-d40">0</span>
            </button>
            <button class="btn btn-outline-light btn-sm" data-course="g28">
              Class G (Unarmed) — G28 <span class="badge bg-purple ms-1" id="count-g28">0</span>
            </button>
          </div>

          <hr class="border-secondary my-4">

          <div class="legend small text-white-50">
            <div class="d-flex align-items-center mb-2">
              <span class="legend-dot me-2 bg-primary"></span> D40 (Armed)
            </div>
            <div class="d-flex align-items-center">
              <span class="legend-dot me-2 bg-purple"></span> G28 (Unarmed)
            </div>
          </div>
        </aside>
      </div>

      <div class="col-lg-9 col-md-8">
        <section class="schedule-calendar" aria-label="Calendar">
          <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
              <h4 class="text-white mb-0" id="currentMonth">August 2025</h4>
              <span class="badge bg-secondary text-uppercase small" id="viewLabel">Month</span>
            </div>
            <div class="calendar-nav">
              <button class="btn btn-outline-light btn-sm me-2" id="prevMonth" aria-label="Previous month">
                <i class="fas fa-chevron-left"></i>
              </button>
              <button class="btn btn-primary btn-sm me-2" id="todayBtn">Today</button>
              <button class="btn btn-outline-light btn-sm" id="nextMonth" aria-label="Next month">
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
          </div>

          <div class="calendar-grid" id="calendarGrid" role="grid" aria-readonly="true"></div>
        </section>
      </div>
    </div>
  </div>
</div>

<!-- Day modal -->
<div class="modal fade" id="dayModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header border-secondary">
        <h5 class="modal-title" id="dayModalLabel">Schedule</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="dayModalBody"></div>
    </div>
  </div>
</div>

<style>
/* Schedule Section Background */
#schedules {
  background-image:
    linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)),
    url('{{ asset('resources/assets/images/photo-calendar-page.webp') }}');
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  position: relative;
}

:root{
  --cal-bg:#003f52;
  --cal-cell:#0f3a46;
  --cal-cell-alt:#0b2f38;
  --cal-border:#124a58;
  --cal-header:#0b2a33;
  --cal-today:#fff3cd;
  --purple:#7c3aed;
}
.calendar-grid{
  background:var(--cal-bg)!important;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 10px 30px rgba(0,0,0,.25);
  display:grid;
  grid-template-rows:auto 1fr;
}
.calendar-head-row{
  display:grid;
  grid-template-columns:repeat(7,1fr);
  position:sticky; top:0; z-index:1;
  background:var(--cal-header); color:#fff; font-weight:600;
  border-bottom:1px solid var(--cal-border);
}
.calendar-head-row > div{ padding:12px; text-align:center; border-right:1px solid var(--cal-border); }
.calendar-head-row > div:last-child{ border-right:0; }

.calendar-body{ display:grid; grid-template-columns:repeat(7,1fr); }
.calendar-day{
  min-height:130px; padding:8px; border-right:1px solid var(--cal-border);
  border-bottom:1px solid var(--cal-border); background:var(--cal-cell); position:relative;
}
.calendar-day:nth-child(7n), .calendar-head-row > div:nth-child(7n){ border-right:0; }
.calendar-day.weekend{ background:var(--cal-cell-alt); }
.calendar-day.other-month{ opacity:.45; }
.calendar-day:focus{ outline:3px solid var(--accent-theme, #22d3ee); outline-offset:-3px; }
.calendar-day.today{ background:var(--cal-today); color:#111; }

.day-number{ font-weight:700; font-size:.9rem; opacity:.9; color:#fff; }
.calendar-day.today .day-number{ color:#111; }
.event-stack{ margin-top:4px; display:flex; flex-direction:column; gap:4px; }
.course-event{
  border-radius:6px; padding:4px 6px; font-size:.78rem; line-height:1.1; font-weight:700;
  color:#fff; text-decoration:none; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.course-event:hover{ opacity:.9; text-decoration:none; }
.course-event.d40{ background:#2563eb; }
.course-event.g28{ background:var(--purple); }

.more-link{ display:inline-block; font-size:.75rem; font-weight:700; opacity:.9; cursor:pointer; }
.more-link:hover{ text-decoration:underline; }

.schedule-sidebar{ background:var(--primary-700); padding:16px; border-radius:12px; }
.schedule-sidebar h5{ font-size:1rem; margin-bottom:12px; }
.schedule-sidebar .btn{ font-size:0.8rem; padding:6px 10px; }
.schedule-sidebar .legend{ font-size:0.75rem; }
.legend-dot{ width:10px; height:10px; border-radius:50%; display:inline-block; }
.bg-purple{ background:var(--purple)!important; }
</style>

<script>
// ---- AJAX Course Schedule Loading ----
let courseSchedules = [];
let isLoading = false;

const loadCourseSchedules = async (courseFilter = null) => {
    if (isLoading) return;

    isLoading = true;

    // Show loading state
    const calendarBody = document.querySelector('.calendar-body');
    if (calendarBody) {
        calendarBody.style.opacity = '0.6';
    }

    try {
        const url = new URL('{{ route("courses.schedule.data") }}');
        if (courseFilter && courseFilter !== 'all') {
            url.searchParams.append('course_filter', courseFilter);
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success && data.events) {
            console.log('Loaded course schedules:', data.events.length, 'events');

            // Transform the events to match the existing format
            courseSchedules = data.events.map(event => {
                // Parse the start date to get just the date part
                const startDate = new Date(event.start);
                const dateStr = startDate.toISOString().split('T')[0];

                return {
                    title: event.title,
                    type: event.course_title || 'Course',
                    date: dateStr,
                    course: event.course_type?.toLowerCase() || (
                        event.title.toLowerCase().includes('armed') ||
                        event.title.toLowerCase().includes('d40') ? 'd40' : 'g28'
                    ),
                    url: event.url || '#'
                };
            });

            console.log('Transformed schedules:', courseSchedules);

            // Regenerate calendar with new data
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        } else {
            console.error('Invalid response format:', data);

            // If no events, still show calendar but empty
            courseSchedules = [];
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        }    } catch (error) {
        console.error('Failed to load course schedules:', error);

        // Show error message to user
        const calendarBody = document.querySelector('.calendar-body');
        if (calendarBody) {
            calendarBody.innerHTML = `
                <div class="text-center p-4">
                    <div class="text-warning mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="text-white-50">
                        Unable to load course schedules. Please try again later.
                    </div>
                    <button class="btn btn-outline-light btn-sm mt-2" onclick="location.reload()">
                        <i class="fas fa-refresh me-1"></i> Retry
                    </button>
                </div>
            `;
        }
    } finally {
        isLoading = false;

        // Remove loading state
        if (calendarBody) {
            calendarBody.style.opacity = '1';
        }
    }
};

const getCourseSchedule = () => {
    // Return current schedules (for backward compatibility)
    return courseSchedules;
};

// ---- state ---------------------------------------------------
let currentDate = new Date(); // Current date instead of hardcoded
let activeFilter = 'all';

// ---- helpers -------------------------------------------------
const fmtISO = d => new Date(d.getTime() - d.getTimezoneOffset()*60000).toISOString().split('T')[0];

function countByCourse(month, year){
  const start = new Date(year, month, 1);
  const end = new Date(year, month+1, 0);
  const inMonth = e => {
    const d = new Date(e.date);
    return d >= start && d <= end;
  };
  const m = courseSchedules.filter(inMonth);
  return {
    all: m.length,
    d40: m.filter(x=>x.course==='d40').length,
    g28: m.filter(x=>x.course==='g28').length
  };
}

// ---- render --------------------------------------------------
function generateCalendar(year, month){
  const first = new Date(year, month, 1);
  const last  = new Date(year, month+1, 0);

  // header month
  document.getElementById('currentMonth').textContent =
    first.toLocaleDateString('en-US',{month:'long',year:'numeric'});

  // counts
  const counts = countByCourse(month, year);
  document.getElementById('count-all').textContent = counts.all;
  document.getElementById('count-d40').textContent = counts.d40;
  document.getElementById('count-g28').textContent = counts.g28;

  const grid = document.getElementById('calendarGrid');
  grid.innerHTML = '';

  // sticky weekday header
  const head = document.createElement('div');
  head.className = 'calendar-head-row';
  ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d=>{
    const el = document.createElement('div'); el.textContent = d; head.appendChild(el);
  });
  grid.appendChild(head);

  // range to render (sun..sat)
  const start = new Date(first);
  start.setDate(1 - start.getDay());
  const end = new Date(last);
  end.setDate(end.getDate() + (6 - end.getDay()));

  const body = document.createElement('div');
  body.className = 'calendar-body';
  grid.appendChild(body);

  const today = fmtISO(new Date());

  for(let d = new Date(start); d <= end; d.setDate(d.getDate()+1)){
    const dayISO = fmtISO(d);
    const cell = document.createElement('div');
    cell.className = 'calendar-day';
    cell.setAttribute('role','gridcell');
    cell.setAttribute('tabindex','0');

    if(d.getDay() === 0 || d.getDay() === 6) cell.classList.add('weekend');
    if(d.getMonth() !== month) cell.classList.add('other-month');
    if(dayISO === today) cell.classList.add('today');

    // number
    const num = document.createElement('div');
    num.className = 'day-number';
    num.textContent = d.getDate();
    cell.appendChild(num);

    // events
    const list = document.createElement('div');
    list.className = 'event-stack';
    const events = courseSchedules.filter(e=>{
      if(e.date !== dayISO) return false;
      return (activeFilter==='all' || e.course===activeFilter);
    });

    // show max 3
    const MAX = 3;
    events.slice(0,MAX).forEach(e=>{
      const a = document.createElement('a');
      a.href = '#';
      a.className = `course-event ${e.course}`;
      a.textContent = e.title;
      a.title = e.type + ' — ' + e.date;
      list.appendChild(a);
    });

    if(events.length > MAX){
      const more = document.createElement('span');
      more.className = 'more-link text-light';
      more.textContent = `+${events.length-MAX} more`;
      more.addEventListener('click',()=>openDayModal(dayISO, events));
      list.appendChild(more);
    }

    cell.appendChild(list);
    body.appendChild(cell);
  }
}

function openDayModal(iso, events){
  const label = new Date(iso+'T00:00:00').toLocaleDateString('en-US',{weekday:'long', month:'long', day:'numeric', year:'numeric'});
  document.getElementById('dayModalLabel').textContent = label;
  const body = document.getElementById('dayModalBody');
  body.innerHTML = events.map(e=>`
    <div class="d-flex align-items-start gap-2 mb-2">
      <span class="legend-dot mt-1 ${e.course==='d40'?'bg-primary':'bg-purple'}"></span>
      <div>
        <div class="fw-bold">${e.title}</div>
        <div class="text-white-50 small">${e.type}</div>
      </div>
    </div>
  `).join('');
  if (window.bootstrap){
    new bootstrap.Modal(document.getElementById('dayModal')).show();
  } else {
    // fallback: simple alert
    alert(label + '\n\n' + events.map(e=>`${e.title} — ${e.type}`).join('\n'));
  }
}

// ---- init ----------------------------------------------------
document.addEventListener('DOMContentLoaded', async ()=>{
  // Initial load of course schedules
  await loadCourseSchedules();

  // Generate initial calendar
  generateCalendar(currentDate.getFullYear(), currentDate.getMonth());

  document.getElementById('prevMonth').addEventListener('click', ()=>{
    currentDate.setMonth(currentDate.getMonth()-1);
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
  });

  document.getElementById('nextMonth').addEventListener('click', ()=>{
    currentDate.setMonth(currentDate.getMonth()+1);
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
  });

  document.getElementById('todayBtn').addEventListener('click', ()=>{
    currentDate = new Date();
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
  });

  document.querySelectorAll('[data-course]').forEach(btn=>{
    btn.addEventListener('click', async function(){
      document.querySelectorAll('[data-course]').forEach(b=>b.classList.remove('active'));
      this.classList.add('active');
      activeFilter = this.dataset.course;

      // Load filtered data from server
      await loadCourseSchedules(activeFilter === 'all' ? null : activeFilter.toUpperCase());
    });
  });
});
</script>
