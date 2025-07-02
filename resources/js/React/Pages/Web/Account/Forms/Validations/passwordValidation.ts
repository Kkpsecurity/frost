import * as Yup from "yup";

const commonPasswords = [
    "123456",
    "password",
    "12345678",
    "qwerty",
    "12345",
    "123456789",
    "letmein",
    "1234567",
    "football",
    "iloveyou",
    "admin",
    "welcome",
    "monkey",
    "login",
    "abc123",
    "starwars",
    "123123",
    "dragon",
    "passw0rd",
    "master",
    "hello",
    "freedom",
    "whatever",
    "qazwsx",
    "trustno1",
];

export const validatePassword = Yup.object().shape({
    old_password: Yup.string().required("Current password is required"),
    password: Yup.string()
        .required("New password is required")
        .oneOf([Yup.ref("password_confirmation"), null], "Passwords must match")
        .test(
            "common-password",
            "Please choose a stronger password",
            (value) => {
                return !commonPasswords.includes(value);
            }
        ),
    password_confirmation: Yup.string().required(
        "Confirm password is required"
    ),
});
