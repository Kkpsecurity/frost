# Documentation Organization Plan

**Date:** September 23, 2025  
**Purpose:** Reorganize docs and tasks for better project management

## Current Issues
- Tasks scattered across multiple folders
- Mixed active/completed items
- Inconsistent naming conventions
- Hard to find current work items

## Proposed New Structure

```
docs/
├── README.md                          # Main documentation index
├── PROJECT_STATUS.md                  # High-level project status
│
├── current/                           # Active work items
│   ├── tasks/                         # Current tasks in progress
│   ├── issues/                        # Current bugs/problems
│   └── planning/                      # Upcoming work planning
│
├── completed/                         # Finished work
│   ├── tasks/                         # Completed tasks
│   ├── projects/                      # Major completed projects
│   └── fixes/                         # Resolved issues
│
├── reference/                         # Documentation resources
│   ├── architecture/                  # System architecture docs
│   ├── database/                      # Database schemas and models
│   ├── frontend/                      # React/TypeScript documentation
│   ├── backend/                       # Laravel/PHP documentation
│   └── deployment/                    # Setup and deployment guides
│
├── archive/                           # Old/obsolete documentation
│   └── deprecated/                    # No longer relevant items
│
└── templates/                         # Task and documentation templates
    ├── task-template.md
    ├── project-template.md
    └── issue-template.md
```

## Reorganization Tasks

### Phase 1: Create New Structure
1. Create new directory structure
2. Move current active tasks to `current/tasks/`
3. Move completed items to `completed/`
4. Create task templates

### Phase 2: Categorize Existing Files
1. Review each file and categorize
2. Update file names for consistency
3. Add proper headers and metadata
4. Create index files for navigation

### Phase 3: Clean Up
1. Archive obsolete files
2. Consolidate duplicate information
3. Update cross-references
4. Create main documentation index

## File Naming Conventions

### Tasks
- Format: `TASK-YYYY-MM-DD-short-description.md`
- Status: Include status in filename or header
- Priority: Use priority labels

### Projects
- Format: `PROJECT-name-description.md`
- Include completion date for finished projects

### Issues
- Format: `ISSUE-YYYY-MM-DD-problem-description.md`
- Include resolution in completed issues

## Implementation Plan

Would you like me to:
1. **Create the new directory structure**
2. **Move and reorganize existing files**
3. **Create templates for future tasks**
4. **Update cross-references and links**

Let me know which approach you prefer and I'll start the reorganization.
