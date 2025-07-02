import { createContext } from "react";
import { ValidatedInstructorShape } from "../../Config/types";

export const ValidatedInstructorContext =
    createContext<ValidatedInstructorShape | null>(null);
