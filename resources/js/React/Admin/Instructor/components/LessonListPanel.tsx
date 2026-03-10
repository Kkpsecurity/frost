import React from 'react'
import type { Lesson } from './LessonsPanelTypes';
import LessonItem from './LessonItem';

const LessonListPanel = ({
    lessons,
    isLessonCompleted,
    isLessonActive,
    isLessonEnabled,
    actionLoading,
    isZoomReady,
    postLessonAction,
}: {
    lessons: Lesson[];
    isLessonCompleted: (lessonId: number) => boolean;
    isLessonActive: (lessonId: number) => boolean;
    isLessonEnabled: (lesson: Lesson, index: number) => boolean;
    actionLoading: boolean;
    isZoomReady: boolean;
    postLessonAction: (url: string, lessonId: number) => Promise<void>;
}) => {
    return (
        <div className="lessons-list">
            {lessons.map((lesson, index) => {
                const completed = isLessonCompleted(lesson.id);
                const active = isLessonActive(lesson.id);
                const enabled = isLessonEnabled(lesson, index);

                return (
                    <LessonItem
                        key={lesson.id}
                        lesson={lesson}
                        index={index}
                        completed={completed}
                        active={active}
                        enabled={enabled}
                        actionLoading={actionLoading}
                        isZoomReady={isZoomReady}
                        onAction={postLessonAction}
                    />
                );
            })}
        </div>
    )
}

export default LessonListPanel
