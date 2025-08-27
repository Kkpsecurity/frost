# Classroom React Structure (review)

This document summarizes the current React setup backing the `/classroom` area and the three logical sections we will build: Student, Instructor, and Support. It maps files, explains the route-based mounting strategy, lists important DOM container IDs, tooling, and recommended next steps.

## Checklist (requirements)
- Review the React entrypoints and route-based loader logic for classroom pages
- Document Student, Support, and Instructor React code locations and responsibilities
- Explain how components are mounted (DOM container ids, dynamic imports)
- Include how to run the dev tooling (vite/npm) and where the build is wired
- Note missing/placeholder files and recommended low-risk next steps

## High-level summary
- The project uses Vite + React + TypeScript. React parts live under `resources/js/React` and are loaded by small route-aware entry scripts at `resources/js/student.ts`, `resources/js/support.ts`, and `resources/js/instructor.ts`.
- The classroom surface is split into three sections we will implement and maintain:
  - Student: components and data layer for the student portal
  - Instructor: instructor dashboard/widgets (some placeholders exist)
  - Support: support staff dashboard and ticketing UI

## Tooling / Build
- `package.json` scripts: `npm run dev` (starts Vite), `npm run build` (vite build), `npm run type-check` (tsc)
- `vite.config.js` uses the `laravel-vite-plugin` and includes the entry scripts `resources/js/student.ts`, `resources/js/support.ts`, and `resources/js/instructor.ts` in the `input` array. React TSX files are picked up by the React Vite plugin.

## Route detection and dynamic loading
## Route detection and dynamic loading
- Route helper: `resources/js/React/utils/routeUtils.ts` provides predicates (for example: `isClassroomRoute`, `isAdminFrostSupport`, `isAdminInstructors`) used by the entry scripts.
- Entry scripts use these checkers to conditionally import or reference the React entry modules:
  - `resources/js/student.ts` — student route loader; imports or mounts the Student app when student predicates match.
  - `resources/js/support.ts` — support route loader; mounts Support app when support predicates match.
  - `resources/js/instructor.ts` — instructor route loader; mounts Instructor app when instructor predicates match.
  - This strategy keeps role-specific bundles small by only executing/mounting the relevant React code on matching pages.
- Rationale: this reduces bundle size by only loading user-role-specific JS on matching pages.

## Mounting details and DOM containers
## Mounting details and DOM containers
- Student mount (`resources/js/React/Student/app.tsx`): looks for DOM element with id `student-dashboard-container`. If found, creates React root via `createRoot(container)` and renders the student app.
- Support mount (`resources/js/React/Support/app.tsx`): looks for `support-dashboard-container` and mounts the Support app.
- Instructor mount (`resources/js/React/Instructor/app.tsx`): the repository contains `resources/js/React/Instructor/app.tsx` which mounts to `instructor-dashboard-container` (the module contains mounting logic with a delayed retry if the container is not yet present).

## Notable files and responsibilities
- `resources/js/student.ts` — student route loader; imports core bootstrap then conditionally loads the Student React app.
- `resources/js/support.ts` — support route loader.
- `resources/js/instructor.ts` — instructor route loader.
- `resources/js/React/utils/routeUtils.ts` — route checks used throughout the entry scripts.
## Notable files and responsibilities
- `resources/js/student.ts` — student route loader; imports core bootstrap then conditionally loads the Student React app.
- `resources/js/support.ts` — support route loader.
- `resources/js/instructor.ts` — instructor route loader.
- `resources/js/React/utils/routeUtils.ts` — route checks used throughout the entry scripts.

- Student React folder (`resources/js/React/Student/`):
  - `app.tsx` — StudentEntry, QueryClient setup and DOM mounting.
  - `StudentDataLayer.tsx` — student UI scaffolding and data queries.
  - `StudentDashboard.tsx` — presentational components.
  - `ErrorBoundry/StudentErrorBoundry.tsx` — error boundary wrapper.

- Support React folder (`resources/js/React/Support/`):
  - `app.tsx` — SupportEntry, QueryClient setup and DOM mounting.
  - `SupportDataLayer.tsx` — support UI and data layer.
  - `ErrorBoundry/SupportErrorBoundry.tsx` — error boundary wrapper for Support.

- Instructor React folder (`resources/js/React/Instructor/`):
  - `app.tsx` — present and mounts to `instructor-dashboard-container`.
  - `InstructorDataLayer.tsx` — main instructor UI/data layer (exists in repo).
  - Markdown/design docs exist (`CLASSROOM_DASHBOARD.md`, `CLASSROOM_SCHEDULE_DISTRIBUTION.md`). Some widget files may require small placeholders to compile cleanly.

## Observations / missing pieces
## Observations / missing pieces
- The Instructor app entry exists at `resources/js/React/Instructor/app.tsx` and contains mounting logic. Many instructor design docs are present.
- Some instructor widget files referenced from the data layer may be empty or missing; adding small placeholders will prevent compile-time issues and make it easier to iterate.
- If `https://frost.test/classroom` doesn't return the expected blade view, check backend route files under `routes/` (there are older or archived routes in `routes_old/` that can be referenced).

## How to run locally (dev)
- From project root (Windows PowerShell), install and run dev server:

```powershell
npm install
npm run dev
```

- Open the site in the browser (for example `https://frost.test/classroom`) after making sure your local valet/hosts and Laravel routes are configured. Entry scripts will log mount attempts to the browser console.

## Recommended next steps (low risk prioritized)
## Recommended next steps (low risk prioritized)
1. Add small placeholder components for any empty/missing instructor widgets to avoid build/runtime errors.
2. If the classroom page is not served, restore or adapt the backend route file from `routes_old/` into `routes/` so Laravel renders the blade view containing the React container nodes.
3. Add or update a short README near `resources/js/React` outlining the container IDs (`student-dashboard-container`, `support-dashboard-container`, `instructor-dashboard-container`) and where blade templates must include them.
4. Optionally add a tiny smoke-test page or Cypress test that loads `/classroom` and asserts the correct container element exists (fast validation for future changes).

## Edge cases considered
- DOM container not present: entries already include delayed mounting retry (setTimeout 1000ms) and immediate mount when DOM already loaded.
- Route check mismatch: the `routeUtils` functions assume client-side path segments; server-side paths and trailing slashes could change detection — ensure blade templates and server routes produce the expected pathname.
- HTTP error (4xx) handling: query client retry logic is already conservative for 4xx errors.

## Final status
- Requirement: review and document structure — Done (this file).
- Next actions suggested: add small placeholders for missing widgets, verify backend classroom route, and add a short README for mount container IDs.

----

If you want, I can now:
- scaffold a minimal `resources/js/React/Instructor/app.tsx` and a placeholder widget (student list / schedule) so Instructor side compiles; or
- copy/adapt `routes_old/frontend/frost_classroom_routes.php` into `routes/frontend/classroom.routes.php` to restore the backend route referenced in logs.

Tell me which follow-up you'd like and I'll implement it next.
