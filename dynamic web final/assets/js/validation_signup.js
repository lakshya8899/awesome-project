document.addEventListener("DOMContentLoaded", () => {
    // Get form fields
    const firstName = document.querySelector("input[name='first_name']");
    const lastName = document.querySelector("input[name='last_name']");
    const email = document.querySelector("input[name='email']");
    const password = document.querySelector("input[name='password']");
    const confirmPassword = document.querySelector("input[name='confirm_password']");
    const form = document.getElementById("signupForm");

    // Validation functions
    const validateFirstName = () => {
        const error = firstName.nextElementSibling;
        if (!/^[a-zA-Z]+$/.test(firstName.value.trim()) || firstName.value.trim().length > 20) {
            error.textContent = "First name must be alphabetic and no more than 20 characters.";
            error.style.display = "block";
            return false;
        } else {
            error.style.display = "none";
            return true;
        }
    };

    const validateLastName = () => {
        const error = lastName.nextElementSibling;
        if (!/^[a-zA-Z]+$/.test(lastName.value.trim()) || lastName.value.trim().length > 20) {
            error.textContent = "Last name must be alphabetic and no more than 20 characters.";
            error.style.display = "block";
            return false;
        } else {
            error.style.display = "none";
            return true;
        }
    };

    const validateEmail = () => {
        const error = email.nextElementSibling;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value.trim())) {
            error.textContent = "Please enter a valid email address.";
            error.style.display = "block";
            return false;
        } else {
            error.style.display = "none";
            return true;
        }
    };

    const validatePassword = () => {
        const error = password.nextElementSibling;
        if (
            password.value.length < 8 ||
            !/[A-Z]/.test(password.value) ||
            !/[0-9]/.test(password.value) ||
            !/[\W]/.test(password.value)
        ) {
            error.textContent =
                "Password must be at least 8 characters long, with an uppercase letter, a number, and a special character.";
            error.style.display = "block";
            return false;
        } else {
            error.style.display = "none";
            return true;
        }
    };

    const validateConfirmPassword = () => {
        const error = confirmPassword.nextElementSibling;
        if (password.value !== confirmPassword.value) {
            error.textContent = "Passwords do not match.";
            error.style.display = "block";
            return false;
        } else {
            error.style.display = "none";
            return true;
        }
    };

    // Attach real-time validation listeners
    firstName.addEventListener("input", validateFirstName);
    lastName.addEventListener("input", validateLastName);
    email.addEventListener("input", validateEmail);
    password.addEventListener("input", validatePassword);
    confirmPassword.addEventListener("input", validateConfirmPassword);

    // Final form validation on submit
    form.addEventListener("submit", (event) => {
        const isValid =
            validateFirstName() &&
            validateLastName() &&
            validateEmail() &&
            validatePassword() &&
            validateConfirmPassword();

        if (!isValid) {
            // Display a general error message
            const generalError = document.querySelector("#general-error");
            generalError.textContent = "Please fix the errors above before submitting.";
            generalError.style.display = "block";
            event.preventDefault(); // Prevent form submission if invalid
        }
    });
});
