import * as yup from "yup";

export const studentValidation = yup.object().shape({
    course_date_id: yup.number().required(),
    license_type: yup.string().required(),
    type: yup.string().required(),
    message: yup.string().min(5),
});
