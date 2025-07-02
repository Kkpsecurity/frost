import * as yup from "yup";

export const validateProfile = yup.object().shape({
    user_id: yup.number().required("Your User ID is Required"),
    fname: yup.string().required("Your First Name is Required"),
    lname: yup.string().required("Your Last Name is Required"),
    email: yup.string().email().required("Your Email is Required"),
});
