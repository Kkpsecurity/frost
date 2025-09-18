# Student Classroom UI Design - Implementation Plan

## Project Overview
**Goal:** Modernize the existing student classroom interface using **Bootstrap 5** and **React-Bootstrap**. Keep the current layout structure but with modern styling, better responsive design, and cleaner UX.

### **Current Design Reference:**
- ✅ Left sidebar with lesson cards (green theme)
- ✅ Three tabs: Home | Videos | Documents  
- ✅ Course Details and Student Info cards
- ✅ Dark blue background theme
- ✅ Progress tracking and completion status

### **Modernization Goals:**
- 🎯 Keep familiar layout but with cleaner, more modern styling
- 🎯 Improve responsive behavior for mobile devices
- 🎯 Better card design with shadows and spacing
- 🎯 Smoother transitions and hover effects
- 🎯 Maintain existing color scheme but refined

## Implementation Steps to Achieve Goal

### Phase 1: Project Setup & Foundation
**Estimated Time:** 2-3 hours

#### Step 1.1: Environment Verification
- [ ] Verify Bootstrap 5 is installed and configured
- [ ] Verify React-Bootstrap is available in the project
- [ ] ✅ **FIXED:** Moved all React components to proper `React/` folder structure
- [ ] ✅ **FIXED:** Removed duplicate components from wrong locations
- [ ] Identify target file location for student classroom components

#### Step 1.2: Component Architecture Planning
- [ ] ✅ **CLEAN STRUCTURE:** All React components now properly organized in `React/` folders
- [ ] Create new classroom components in correct structure:
  ```
  resources/js/React/Student/Components/
  ├── Classroom/                         (NEW folder for classroom UI)
  │   ├── StudentClassroomShell.tsx      (Main layout wrapper)
  │   ├── StudentClassroomSidebar.tsx    (Lesson sidebar - NEW, different from existing)
  │   ├── ClassroomTitleBar.tsx          (Header with title + tabs)
  │   ├── DashboardTab.tsx               (Dashboard content)
  │   ├── VideoRoomTab.tsx               (Video room content)
  │   └── DocumentsTab.tsx               (Documents list)
  ```
  
  **✅ FIXED:** 
  - Removed `resources/js/Components/` (wrong location)
  - Removed `resources/js/upload-modal-manager.tsx` (wrong location)
  - All React components now in proper `React/` structure

### Phase 2: Core Layout Implementation
**Estimated Time:** 4-5 hours

#### Step 2.1: Main Shell Component (StudentClassroomShell.tsx)
- [ ] Create responsive grid layout with full-height structure
- [ ] Implement sidebar + content area split  
- [ ] Add responsive breakpoints (≥992px desktop, <992px mobile)
- [ ] Set up state management for sidebar toggle
- [ ] Add min-vh-100 for full viewport height

#### Step 2.2: Sidebar Implementation (StudentClassroomSidebar.tsx)
- [ ] Create lesson sidebar matching current design but modernized:
  - [ ] **Green lesson cards** with rounded corners and subtle shadows
  - [ ] **Lesson titles** (e.g., "Security Officer And Private Investigator Licensure")
  - [ ] **Credit minutes** display (60, 180, etc.)
  - [ ] **"View" buttons** with hover effects
  - [ ] **Progress indicators** and completion status
- [ ] Responsive behavior: 300px desktop, collapsible on mobile
- [ ] Smooth scroll for long lesson lists
- [ ] **Keep existing color scheme:** Green cards, dark backgrounds

#### Step 2.3: Sidebar Responsive States
- [ ] **Desktop Expanded (300px):** Full lesson names and content
- [ ] **Desktop Collapsed (60px):** Lesson initials with tooltips
- [ ] **Mobile:** Offcanvas overlay with hamburger toggle

### Phase 3: Header & Navigation
**Estimated Time:** 2-3 hours

#### Step 3.1: Title Bar Component (ClassroomTitleBar.tsx)
- [ ] Create header matching current design:
  - [ ] **Course title** (e.g., "FLORIDA CLASS 'G' 28 HOUR")
  - [ ] **"Take Exam" button** (green, similar to current)
  - [ ] **User menu icons** on the right
  - [ ] **Course image/icon** (purple square placeholder)
- [ ] Apply dark navy background to match current theme
- [ ] Make responsive for mobile devices

#### Step 3.2: Tab Navigation Integration  
- [ ] Implement three tabs exactly like current: **Home | Videos | Documents**
- [ ] Use light purple/lavender background for active tab (matches current)
- [ ] Ensure tabs are clearly visible against dark background
- [ ] Smooth tab switching without layout shift

### Phase 4: Tab Content Implementation
**Estimated Time:** 3-4 hours

#### Step 4.1: Home Tab (DashboardTab.tsx) - **Default Active Tab**
- [ ] **Left Column: Course Details Card**
  - [ ] Title, Purchased Date, Start Date, Expires Date, Completed Date
  - [ ] Match current layout but with modern card styling
  - [ ] Orange headers like current design
- [ ] **Right Column: Student Info Card**
  - [ ] Name, Email, Initials, DOB, Suffix, Phone
  - [ ] Clean form-like layout
- [ ] **Bottom Section: Student Lessons Completed**
  - [ ] "All lessons" with "2 out of 14" progress indicator
  - [ ] Progress bar or percentage display
- [ ] **Responsive:** Stack cards vertically on mobile

#### Step 4.2: Videos Tab (VideoRoomTab.tsx)
- [ ] **Video library/playlist interface**
- [ ] **Grid of video thumbnails** with play buttons
- [ ] **Video titles and durations**
- [ ] **Progress indicators** for watched videos
- [ ] **Search/filter functionality** (UI only)
- [ ] Responsive grid layout

#### Step 4.3: Documents Tab (DocumentsTab.tsx)
- [ ] **Document library interface**
- [ ] **List of course materials** (PDFs, guides, etc.)
- [ ] **File icons and names**
- [ ] **Download/View buttons**
- [ ] **File size and type information**
- [ ] **Search functionality** (UI only)
- [ ] Clean table or card-based layout

### Phase 5: Styling & UX Polish
**Estimated Time:** 2-3 hours

#### Step 5.1: Visual Theme Implementation - **Match Current Design**
- [ ] **Dark navy/blue background** for main content area (like current)
- [ ] **Green lesson cards** in sidebar (match current green theme)
- [ ] **Light purple/lavender accents** for active states
- [ ] **Orange section headers** (like "Course Details")
- [ ] **White text** on dark backgrounds for readability
- [ ] **Subtle shadows and rounded corners** for modern feel
- [ ] **Maintain existing color palette** but with refined styling

#### Step 5.2: Interactive States
- [ ] Add hover states for all interactive elements
- [ ] Implement cursor-pointer for clickable items
- [ ] Add tooltips for collapsed sidebar items
- [ ] Style active/selected states with proper contrast

#### Step 5.3: Responsive Testing
- [ ] Test on sm breakpoint (576px+)
- [ ] Test on md breakpoint (768px+)
- [ ] Test on lg breakpoint (992px+)
- [ ] Test on xl breakpoint (1200px+)
- [ ] Ensure no horizontal scrollbars at any breakpoint

### Phase 6: Accessibility & Final Polish
**Estimated Time:** 1-2 hours

#### Step 6.1: Accessibility Implementation
- [ ] Add keyboard focus support for all interactive elements
- [ ] Implement ARIA attributes for collapsible sidebar
- [ ] Ensure visible focus rings on all controls
- [ ] Test tab navigation flow

#### Step 6.2: Code Quality & Documentation
- [ ] Add TypeScript interfaces for props (if using TypeScript)
- [ ] Add component documentation comments
- [ ] Clean up unused imports and code
- [ ] Verify all Bootstrap utility classes are properly used

### Phase 7: Integration & Testing
**Estimated Time:** 1-2 hours

#### Step 7.1: Component Integration
- [ ] Import and use StudentClassroomShell in appropriate parent component
- [ ] Add to student.ts or appropriate entry point in `resources/js/React/Student/`
- [ ] Test sidebar state persistence (React state only)
- [ ] Verify all placeholder content displays correctly
- [ ] Test mobile offcanvas functionality

#### Step 7.2: Final Validation
- [ ] Verify no network calls are made
- [ ] Confirm all data is placeholder/local only
- [ ] Test responsive behavior across all breakpoints
- [ ] Validate against acceptance criteria

## Design Reference - Current Interface

**Key Elements to Modernize:**
- ✅ **Sidebar:** Green lesson cards with credit minutes and "View" buttons
- ✅ **Header:** Course title, Take Exam button, user menu
- ✅ **Tabs:** Home | Videos | Documents (keep exact same structure)
- ✅ **Content:** Course Details + Student Info cards layout
- ✅ **Colors:** Dark navy background, green accents, orange headers
- ✅ **Progress:** "2 out of 14" lesson completion tracking

## Acceptance Criteria Checklist

- [ ] **Visual Match:** Interface resembles current design but with modern styling
- [ ] **Color Scheme:** Dark navy background, green lesson cards, orange headers maintained
- [ ] **Layout:** Left sidebar + main content area with proper responsive behavior
- [ ] **Tabs:** Home | Videos | Documents tabs function smoothly
- [ ] **Cards:** Course Details and Student Info cards with clean modern styling
- [ ] **Responsive:** Works on mobile with collapsible sidebar
- [ ] **Progress:** Lesson completion tracking clearly displayed
- [ ] **No data calls:** All content is placeholder/static for UI-only version

## Original Design Requirementsa tighter, no-nonsense prompt you can hand to a dev:

---

# Student Classroom UI (UI-only) — Bootstrap + React-Bootstrap

**Goal:** Build a stable, responsive UI shell using **Bootstrap 5** and **React-Bootstrap** *only*. **No data wiring, no API calls.** Deliver a working layout with a left sidebar, a top title bar, and three tabs: **Dashboard**, **Video Room**, **Documents**.

## Tech & Constraints

* **Libraries:** Bootstrap 5, React-Bootstrap (Tabs, Nav, Offcanvas/Collapse, Navbar, Container/Row/Col, Card).
* **Scope:** UI/markup/state only. Use placeholder content where needed.
* **Styling:** Prefer Bootstrap utility classes; minimal custom CSS (variables for widths/z-index).
* **A11y:** Keyboard focus, ARIA for collapsible sidebar, visible focus rings.

## Layout

* **App grid:** Full-height layout. Fixed left **Sidebar** + right **Content**.

  * Desktop (≥992px): Sidebar **300px** wide.
  * Collapsed state: **60px** wide.
  * Mobile (<992px): Sidebar becomes offcanvas; toggle via button in title bar.
* **Title Bar (inside Content):**

  * Left: Page Title (“Student Classroom”).
  * Right: Actions (placeholder buttons), Sidebar toggle (hamburger on mobile).
  * Below title: **Tabs** (React-Bootstrap `<Tabs>`): `Dashboard | Video Room | Documents`.
* **Content Area:** Padding using `p-3` (md+), `p-2` (sm). Each tab shows placeholder components (Cards, ListGroup).

## Sidebar

* **Sections:** Lesson list (static placeholders), Quick actions, Help.
* **Collapsed behavior (desktop):**

  * Width: **60px**.
  * Show only a circle/rounded box with the **first letter of the Lesson** as a monogram (e.g., “A”, “B”) using a consistent badge/avatar style.
  * Tooltips on hover to reveal full lesson names.
* **Expand/Collapse:**

  * Desktop: Toggle button pinned at bottom of sidebar.
  * Persist state locally (React state only; no storage).
* **Active item:** Highlight with `active` styles; maintain contrast.

## Tabs Content (placeholders)

* **Dashboard:** 2-column (md+) card grid: “Upcoming Sessions”, “Progress Summary”, “Announcements”. On small screens, stack.
* **Video Room:** Static layout with a large video container (responsive 16:9 ratio using `.ratio .ratio-16x9`) and a right panel placeholder for chat/participants (hidden on \<lg with a toggle).
* **Documents:** ListGroup of files with icons, filename, and “Open” button (no action).

## Components (suggested file names)

* `Layout/StudentShell.tsx` — wraps Sidebar + Content.
* `Sidebar/StudentSidebar.tsx` — handles expanded/collapsed/offcanvas states.
* `Header/TitleBar.tsx` — title + actions + mobile sidebar toggle.
* `Tabs/StudentTabs.tsx` — React-Bootstrap Tabs container.
* `Tabs/DashboardTab.tsx`, `Tabs/VideoRoomTab.tsx`, `Tabs/DocumentsTab.tsx`.

## Visual/UX Details

* Sidebar background `bg-light` with subtle border; content on `bg-white`.
* Use `position-sticky` for sidebar content (top: 0) to keep it visible.
* Add `shadow-sm` to Title Bar; tabs use `variant="tabs"` with consistent spacing.
* Provide hover states, `cursor-pointer`, and tooltips for collapsed items.
* Ensure min content height fills viewport: `min-vh-100`.

## Acceptance Criteria

* Sidebar expands to **300px**, collapses to **60px** on desktop; offcanvas on mobile.
* Collapsed sidebar shows **lesson initial** monograms with tooltips; no text overflow.
* Title Bar + Tabs render cleanly; switching tabs updates content without layout shift.
* No network calls; all data placeholders local.
* Responsive at sm/md/lg/xl with no horizontal scrollbars.

---

Pushback: don’t over-engineer. Ship the shell first with clean React-Bootstrap components, then iterate on visuals.
