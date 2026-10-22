/* =========================================================
   STUDENTHUB - MAIN JAVASCRIPT
   Practical 4 + Practical 5
   ========================================================= */
/* 
   DOM
 */
document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       PRACTICAL 4
       HAMBURGER MENU
       ===================================================== */

    const hamburger = document.getElementById("hamburger");
    const mainNav = document.getElementById("mainNav");

    if (hamburger && mainNav) {

        hamburger.addEventListener("click", function () {

            mainNav.classList.toggle("active");

            const isOpen = mainNav.classList.contains("active");

            hamburger.setAttribute(
                "aria-expanded",
                isOpen
            );

        });

    }


    /* =====================================================
       PRACTICAL 4
       LIGHT / DARK THEME
       ===================================================== */

    const themeToggle = document.getElementById("themeToggle");

    // Restore previously selected theme
    const savedTheme = localStorage.getItem("studenthub-theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-theme");

        if (themeToggle) {
            themeToggle.textContent = "☀️";
        }
    }


    if (themeToggle) {

        themeToggle.addEventListener("click", function () {

            document.body.classList.toggle("dark-theme");

            const darkMode =
                document.body.classList.contains("dark-theme");

            if (darkMode) {

                localStorage.setItem(
                    "studenthub-theme",
                    "dark"
                );

                themeToggle.textContent = "☀️";

            } else {

                localStorage.setItem(
                    "studenthub-theme",
                    "light"
                );

                themeToggle.textContent = "🌙";
            }

        });

    }


    /* =====================================================
       PRACTICAL 4
       FAQ COLLAPSIBLE
       ===================================================== */

    const faqQuestions =
        document.querySelectorAll(".faq-question");

    faqQuestions.forEach(function (question) {

        question.addEventListener("click", function () {

            const answer =
                question.nextElementSibling;

            if (answer) {

                answer.classList.toggle("active");

                question.classList.toggle("active");

            }

        });

    });


    /* =====================================================
       PRACTICAL 4
       MODAL POPUP
       ===================================================== */

    const modal =
        document.getElementById("eventModal");

    const modalOpenButtons =
        document.querySelectorAll(".open-modal");

    const modalClose =
        document.querySelector(".modal-close");


    modalOpenButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            if (modal) {
                modal.classList.add("active");
            }

        });

    });


    if (modalClose) {

        modalClose.addEventListener("click", function () {

            if (modal) {
                modal.classList.remove("active");
            }

        });

    }


    if (modal) {

        modal.addEventListener("click", function (event) {

            if (event.target === modal) {

                modal.classList.remove("active");

            }

        });

    }


    /* =====================================================
       PRACTICAL 4
       NOTIFICATION BANNER
       ===================================================== */

    const notificationClose =
        document.getElementById("notificationClose");

    const notificationBanner =
        document.getElementById("notificationBanner");


    if (notificationClose && notificationBanner) {

        notificationClose.addEventListener(
            "click",
            function () {

                notificationBanner.style.display =
                    "none";

            }
        );

    }


    /* =====================================================
       PRACTICAL 4
       CONTENT SLIDER
       ===================================================== */

    const slides =
        document.querySelectorAll(".slide");

    const nextButton =
        document.getElementById("nextSlide");

    const previousButton =
        document.getElementById("prevSlide");

    let currentSlide = 0;


    function showSlide(index) {

        if (slides.length === 0) {
            return;
        }

        slides.forEach(function (slide) {

            slide.classList.remove("active");

        });

        slides[index].classList.add("active");

    }


    if (slides.length > 0) {

        showSlide(currentSlide);


        if (nextButton) {

            nextButton.addEventListener(
                "click",
                function () {

                    currentSlide++;

                    if (currentSlide >= slides.length) {
                        currentSlide = 0;
                    }

                    showSlide(currentSlide);

                }
            );

        }


        if (previousButton) {

            previousButton.addEventListener(
                "click",
                function () {

                    currentSlide--;

                    if (currentSlide < 0) {
                        currentSlide = slides.length - 1;
                    }

                    showSlide(currentSlide);

                }
            );

        }

    }


    /* =====================================================
       PRACTICAL 5
       REGISTRATION FORM VALIDATION
       ===================================================== */

    const registrationForm =
        document.getElementById("registrationForm");


    // Only execute validation code on register.html
    if (registrationForm) {

        initializeRegistrationValidation();

    }

});


/* =========================================================
   PRACTICAL 5
   REGISTRATION VALIDATION FUNCTION
   ========================================================= */

function initializeRegistrationValidation() {

    const form =
        document.getElementById("registrationForm");

    const fullName =
        document.getElementById("fullName");

    const email =
        document.getElementById("email");

    const mobile =
        document.getElementById("mobile");

    const course =
        document.getElementById("course");

    const year =
        document.getElementById("year");

    const password =
        document.getElementById("password");

    const confirmPassword =
        document.getElementById("confirmPassword");

    const terms =
        document.getElementById("terms");

    const successMessage =
        document.getElementById("registrationSuccess");


    /* =====================================================
       REGULAR EXPRESSIONS
       ===================================================== */

    // Name: letters, spaces, apostrophe, hyphen and dot
    const nameRegex =
        /^[A-Za-z][A-Za-z\s.'-]{2,49}$/;


    // Basic email format
    const emailRegex =
        /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;


    // Indian 10-digit mobile number
    const mobileRegex =
        /^[6-9]\d{9}$/;


    // Password:
    // Minimum 8 characters
    // At least one uppercase
    // At least one lowercase
    // At least one number
    // At least one special character
    const strongPasswordRegex =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;


    /* =====================================================
       ERROR MESSAGE HELPER
       ===================================================== */

    function setError(input, errorId, message) {

        const errorElement =
            document.getElementById(errorId);

        if (errorElement) {
            errorElement.textContent = message;
        }

        if (input) {

            input.classList.add("input-error");

            input.setAttribute(
                "aria-invalid",
                "true"
            );

        }

    }


    /* =====================================================
       CLEAR ERROR HELPER
       ===================================================== */

    function clearError(input, errorId) {

        const errorElement =
            document.getElementById(errorId);

        if (errorElement) {
            errorElement.textContent = "";
        }

        if (input) {

            input.classList.remove("input-error");
            input.classList.add("input-valid");

            input.setAttribute(
                "aria-invalid",
                "false"
            );

        }

    }


    /* =====================================================
       RESET FIELD STATE
       ===================================================== */

    function resetFieldState(input, errorId) {

        const errorElement =
            document.getElementById(errorId);

        if (errorElement) {
            errorElement.textContent = "";
        }

        if (input) {

            input.classList.remove(
                "input-error",
                "input-valid"
            );

            input.setAttribute(
                "aria-invalid",
                "false"
            );

        }

    }


    /* =====================================================
       NAME VALIDATION
       ===================================================== */

    function validateName() {

        const value =
            fullName.value.trim();


        if (value === "") {

            setError(
                fullName,
                "fullNameError",
                "Full name is required."
            );

            return false;

        }


        if (!nameRegex.test(value)) {

            setError(
                fullName,
                "fullNameError",
                "Enter a valid name using letters, spaces, hyphens or apostrophes."
            );

            return false;

        }


        clearError(
            fullName,
            "fullNameError"
        );

        return true;

    }


    /* =====================================================
       EMAIL VALIDATION
       ===================================================== */

    function validateEmail() {

        const value =
            email.value.trim();


        if (value === "") {

            setError(
                email,
                "emailError",
                "Email address is required."
            );

            return false;

        }


        if (!emailRegex.test(value)) {

            setError(
                email,
                "emailError",
                "Enter a valid email address."
            );

            return false;

        }


        clearError(
            email,
            "emailError"
        );

        return true;

    }


    /* =====================================================
       MOBILE VALIDATION
       ===================================================== */

    function validateMobile() {

        const value =
            mobile.value.trim();


        if (value === "") {

            setError(
                mobile,
                "mobileError",
                "Mobile number is required."
            );

            return false;

        }


        if (!mobileRegex.test(value)) {

            setError(
                mobile,
                "mobileError",
                "Enter a valid 10-digit Indian mobile number."
            );

            return false;

        }


        clearError(
            mobile,
            "mobileError"
        );

        return true;

    }


    /* =====================================================
       COURSE VALIDATION
       ===================================================== */

    function validateCourse() {

        if (course.value === "") {

            setError(
                course,
                "courseError",
                "Please select your course."
            );

            return false;

        }


        clearError(
            course,
            "courseError"
        );

        return true;

    }


    /* =====================================================
       YEAR VALIDATION
       ===================================================== */

    function validateYear() {

        if (year.value === "") {

            setError(
                year,
                "yearError",
                "Please select your year."
            );

            return false;

        }


        clearError(
            year,
            "yearError"
        );

        return true;

    }


    /* =====================================================
       GENDER VALIDATION
       ===================================================== */

    function validateGender() {

        const selectedGender =
            document.querySelector(
                'input[name="gender"]:checked'
            );


        const genderError =
            document.getElementById(
                "genderError"
            );


        if (!selectedGender) {

            genderError.textContent =
                "Please select your gender.";

            return false;

        }


        genderError.textContent = "";

        return true;

    }


    /* =====================================================
       PASSWORD STRENGTH
       ===================================================== */

    function calculatePasswordStrength() {

        const value =
            password.value;

        const strengthText =
            document.getElementById(
                "passwordStrength"
            );

        const strengthFill =
            document.getElementById(
                "strengthFill"
            );


        if (!value) {

            strengthText.textContent =
                "Password strength: Not entered";

            strengthFill.style.width =
                "0%";

            strengthFill.className =
                "strength-fill";

            return "none";

        }


        let score = 0;


        // Length
        if (value.length >= 8) {
            score++;
        }


        // Lowercase
        if (/[a-z]/.test(value)) {
            score++;
        }


        // Uppercase
        if (/[A-Z]/.test(value)) {
            score++;
        }


        // Number
        if (/\d/.test(value)) {
            score++;
        }


        // Special character
        if (/[^A-Za-z\d]/.test(value)) {
            score++;
        }


        strengthFill.className =
            "strength-fill";


        if (score <= 2) {

            strengthText.textContent =
                "Password strength: Weak";

            strengthFill.style.width =
                "33%";

            strengthFill.classList.add(
                "strength-weak"
            );

            return "weak";

        }


        if (score <= 4) {

            strengthText.textContent =
                "Password strength: Medium";

            strengthFill.style.width =
                "66%";

            strengthFill.classList.add(
                "strength-medium"
            );

            return "medium";

        }


        strengthText.textContent =
            "Password strength: Strong";

        strengthFill.style.width =
            "100%";

        strengthFill.classList.add(
            "strength-strong"
        );

        return "strong";

    }


    /* =====================================================
       PASSWORD VALIDATION
       ===================================================== */

    function validatePassword() {

        const value =
            password.value;


        if (value === "") {

            setError(
                password,
                "passwordError",
                "Password is required."
            );

            return false;

        }


        if (value.length < 8) {

            setError(
                password,
                "passwordError",
                "Password must contain at least 8 characters."
            );

            return false;

        }


        if (!strongPasswordRegex.test(value)) {

            setError(
                password,
                "passwordError",
                "Password must contain uppercase, lowercase, number and special character."
            );

            return false;

        }


        clearError(
            password,
            "passwordError"
        );

        return true;

    }


    /* =====================================================
       CONFIRM PASSWORD VALIDATION
       ===================================================== */

    function validateConfirmPassword() {

        const value =
            confirmPassword.value;


        if (value === "") {

            setError(
                confirmPassword,
                "confirmPasswordError",
                "Please confirm your password."
            );

            return false;

        }


        if (value !== password.value) {

            setError(
                confirmPassword,
                "confirmPasswordError",
                "Passwords do not match."
            );

            return false;

        }


        clearError(
            confirmPassword,
            "confirmPasswordError"
        );

        return true;

    }


    /* =====================================================
       TERMS VALIDATION
       ===================================================== */

    function validateTerms() {

        const error =
            document.getElementById(
                "termsError"
            );


        if (!terms.checked) {

            error.textContent =
                "You must accept the Terms & Conditions.";

            return false;

        }


        error.textContent = "";

        return true;

    }


    /* =====================================================
       REAL-TIME VALIDATION
       PRACTICAL 5 INTERMEDIATE EXTENSION
       ===================================================== */

    fullName.addEventListener(
        "input",
        validateName
    );


    email.addEventListener(
        "input",
        validateEmail
    );


    mobile.addEventListener(
        "input",
        function () {

            // Allow only numbers
            mobile.value =
                mobile.value.replace(
                    /\D/g,
                    ""
                );

            validateMobile();

        }
    );


    course.addEventListener(
        "change",
        validateCourse
    );


    year.addEventListener(
        "change",
        validateYear
    );


    password.addEventListener(
        "input",
        function () {

            calculatePasswordStrength();
            validatePassword();

            // Recheck confirmation if user changes password
            if (confirmPassword.value !== "") {
                validateConfirmPassword();
            }

        }
    );


    confirmPassword.addEventListener(
        "input",
        validateConfirmPassword
    );


    const genderInputs =
        document.querySelectorAll(
            'input[name="gender"]'
        );


    genderInputs.forEach(function (radio) {

        radio.addEventListener(
            "change",
            validateGender
        );

    });


    terms.addEventListener(
        "change",
        validateTerms
    );


    /* =====================================================
       FORM SUBMISSION
       ===================================================== */

    form.addEventListener(
        "submit",
        function (event) {

            event.preventDefault();


            const isNameValid =
                validateName();

            const isEmailValid =
                validateEmail();

            const isMobileValid =
                validateMobile();

            const isCourseValid =
                validateCourse();

            const isYearValid =
                validateYear();

            const isGenderValid =
                validateGender();

            const isPasswordValid =
                validatePassword();

            const isConfirmPasswordValid =
                validateConfirmPassword();

            const isTermsValid =
                validateTerms();


            const formIsValid =
                isNameValid &&
                isEmailValid &&
                isMobileValid &&
                isCourseValid &&
                isYearValid &&
                isGenderValid &&
                isPasswordValid &&
                isConfirmPasswordValid &&
                isTermsValid;


            if (!formIsValid) {

                successMessage.hidden =
                    true;

                // Find first invalid field
                const firstInvalid =
                    form.querySelector(
                        ".input-error"
                    );


                if (firstInvalid) {

                    firstInvalid.focus();

                }


                return;

            }


            /* =============================================
               SUCCESS
               ============================================= */

            successMessage.textContent =
                "Registration form is valid. Backend registration will be connected in a later practical.";

            successMessage.hidden =
                false;


            // Scroll to success message
            successMessage.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });


            console.log(
                "Student registration form passed frontend validation."
            );

        }
    );


    /* =====================================================
       RESET FORM
       ===================================================== */

    const resetButton =
        document.getElementById(
            "resetRegistration"
        );


    if (resetButton) {

        resetButton.addEventListener(
            "click",
            function () {

                setTimeout(function () {

                    resetFieldState(
                        fullName,
                        "fullNameError"
                    );

                    resetFieldState(
                        email,
                        "emailError"
                    );

                    resetFieldState(
                        mobile,
                        "mobileError"
                    );

                    resetFieldState(
                        course,
                        "courseError"
                    );

                    resetFieldState(
                        year,
                        "yearError"
                    );

                    resetFieldState(
                        password,
                        "passwordError"
                    );

                    resetFieldState(
                        confirmPassword,
                        "confirmPasswordError"
                    );


                    document.getElementById(
                        "genderError"
                    ).textContent = "";


                    document.getElementById(
                        "termsError"
                    ).textContent = "";


                    document.getElementById(
                        "passwordStrength"
                    ).textContent =
                        "Password strength: Not entered";


                    document.getElementById(
                        "strengthFill"
                    ).style.width = "0%";


                    document.getElementById(
                        "strengthFill"
                    ).className =
                        "strength-fill";


                    successMessage.hidden =
                        true;

                }, 0);

            }
        );

    }

}