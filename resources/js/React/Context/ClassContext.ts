import { createContext } from 'react';
import { ClassDataShape } from '../Config/types';

export const ClassContext = createContext<ClassDataShape | null>(null);
