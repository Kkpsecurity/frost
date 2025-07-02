import { createContext } from 'react';
import { CourseMeetingShape } from '../../Config/types';

export const AdminClassContext = createContext<CourseMeetingShape | null>(null);
