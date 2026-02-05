# AI Course Assistant Knowledge Base

This directory contains the structured knowledge that powers the Frost AI Course Assistant. The AI uses these files to provide accurate, course-specific answers to student and instructor questions during live classroom sessions.

## Directory Structure

```
knowledge/
├── README.md (this file)
├── courses/
│   ├── class-d-unarmed/
│   │   ├── overview.md
│   │   ├── legal-authority.md (future)
│   │   ├── patrol-procedures.md (future)
│   │   └── ...
│   ├── class-g-firearms/
│   │   ├── overview.md
│   │   ├── firearm-safety.md (future)
│   │   └── ...
│   └── (additional courses as needed)
├── policies/
│   ├── attendance-policy.md (future)
│   ├── code-of-conduct.md (future)
│   └── certification-requirements.md (future)
├── glossary/
│   └── security-terms.md
└── system-prompts/
    ├── base-instructions.txt
    ├── safety-guidelines.txt
    └── response-templates.txt (future)
```

## File Formats

- **Markdown (.md)**: Course content, policies, glossaries
    - Easy to read and edit
    - Version control friendly
    - Supports formatting (headings, lists, bold, etc.)

- **Plain Text (.txt)**: System prompts and instructions
    - Loaded directly into AI context
    - No formatting needed

## How the AI Uses This Knowledge

1. **System Prompts** are loaded FIRST to establish behavior rules and safety guidelines
2. **Course-Specific Content** is loaded based on the active course (from database)
3. **Glossary** is available for terminology lookups
4. **Policies** supplement course content with institutional rules

The AI combines this static knowledge with dynamic context from the database:

- Current course and lesson being taught
- Student progress and enrollment data
- Recent chat history for conversation context

## Content Guidelines

### When Adding New Content

- **Be Accurate**: Information must reflect actual curriculum and legal standards
- **Cite Sources**: If referencing laws, regulations, or standards, note the source
- **Stay Current**: Review and update content as curriculum changes
- **Safety First**: Always emphasize safety protocols, especially for firearms topics
- **Avoid Legal Advice**: Provide educational information, not legal counsel

### Markdown Best Practices

- Use clear headings (`#`, `##`, `###`) to organize topics
- Use bullet points or numbered lists for easy scanning
- **Bold** key terms and concepts
- Include `**WARNING**` or `**IMPORTANT**` callouts when needed
- Keep paragraphs concise (3-5 sentences)

### Example Structure for Course Content

```markdown
# Topic Title

## Overview

Brief introduction to the topic (2-3 sentences).

## Key Concepts

- **Concept 1**: Definition and explanation
- **Concept 2**: Definition and explanation

## Practical Application

How students will use this knowledge in the field.

## Common Questions

- **Q**: Question?
- **A**: Answer with context.

## Safety Notes (if applicable)

⚠️ Important safety reminders.

## Related Topics

Links to other lessons or glossary terms.
```

## Content Status

### ✅ Complete

- System prompts (base instructions, safety guidelines)
- Class D overview
- Class G overview
- Security terms glossary

### 🔲 Planned (Future)

- Detailed lesson files for each course unit
- Policy documents (attendance, conduct, certification)
- Response templates for common questions
- Scenario-based examples and case studies
- Additional courses (if/when added to platform)

## Maintenance

**Who Updates This Content?**

- **Instructors**: Can propose content updates based on curriculum changes
- **Course Admins**: Approve and implement content changes
- **Developers**: Maintain file structure and integration with AI service

**Review Schedule**:

- **Quarterly**: Review for accuracy and completeness
- **As Needed**: Update when curriculum, laws, or policies change
- **After Feedback**: Incorporate instructor/student suggestions

## Testing New Content

Before deploying new knowledge files to production:

1. Add the file to this directory
2. Restart the AI service to reload knowledge
3. Test with sample questions in a development environment
4. Have an instructor review AI responses for accuracy
5. Deploy to production after approval

## Technical Notes

- Knowledge files are loaded into AI context at startup
- File encoding: UTF-8
- Maximum recommended file size: 50KB per file (for efficient processing)
- Total knowledge base should stay under 500KB for optimal performance

## Questions?

If you're unsure about adding or editing content, contact:

- Course Director for curriculum accuracy
- Legal team for legal/liability content
- Development team for technical implementation

---

**Last Updated**: February 4, 2026  
**Version**: 1.0  
**Status**: Initial foundation established
