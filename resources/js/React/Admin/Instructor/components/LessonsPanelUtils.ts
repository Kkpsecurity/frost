export const getLessonIcon = (lessonType: string): string => {
    switch (lessonType) {
        case "video":
            return "fa-video";
        case "reading":
            return "fa-book";
        case "quiz":
            return "fa-clipboard-question";
        case "assignment":
            return "fa-file-pen";
        default:
            return "fa-book-open";
    }
};

export const formatDuration = (minutes: number): string => {
    if (minutes < 60) return `${minutes}m`;
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
};

export const lessonPanelStyles = `
  .lessons-list {
    padding: 0;
  }

  .lesson-item {
    display: flex;
    gap: 12px;
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: background 0.2s;
  }

  .lesson-item:hover {
    background: rgba(255, 255, 255, 0.05);
  }

  .lesson-item.completed {
    opacity: 0.7;
  }

  .lesson-item.active {
    background: rgba(0, 123, 255, 0.1);
    border-left: 3px solid #007bff;
  }

  .lesson-item.active .lesson-number {
    background: #007bff;
    color: white;
  }

  .lesson-number {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    font-weight: 600;
    font-size: 14px;
    color: white;
  }

  .lesson-content {
    flex: 1;
    min-width: 0;
  }

  .lesson-header {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
  }

  .lesson-title {
    font-size: 14px;
    font-weight: 500;
    color: white;
    line-height: 1.4;
  }

  .lesson-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 8px;
  }

  .lesson-duration {
    display: flex;
    align-items: center;
  }

  .lesson-status {
    display: flex;
    align-items: center;
  }

  .btn-start-lesson {
    width: 100%;
    font-size: 12px;
    padding: 6px 12px;
  }

  .lesson-count-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
  }
`;
