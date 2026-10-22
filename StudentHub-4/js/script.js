/* =====================================================
   STUDENTHUB - PRACTICAL 4
   DOM Manipulation
   Event Handling
   localStorage
   ===================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function () {


    /* =========================================
       1. HAMBURGER MENU
       ========================================= */

    const menuToggle =
        document.getElementById("menuToggle");

    const siteNav =
        document.getElementById("siteNav");


    if (menuToggle && siteNav) {

        menuToggle.addEventListener(
            "click",
            function () {

                const isOpen =
                    siteNav.classList.toggle(
                        "nav-open"
                    );


                menuToggle.setAttribute(
                    "aria-expanded",
                    String(isOpen)
                );


                menuToggle.textContent =
                    isOpen
                        ? "✕"
                        : "☰";

            }
        );


        siteNav
            .querySelectorAll("a")
            .forEach(function (link) {

                link.addEventListener(
                    "click",
                    function () {

                        if (
                            window.innerWidth < 700
                        ) {

                            siteNav.classList.remove(
                                "nav-open"
                            );

                            menuToggle.setAttribute(
                                "aria-expanded",
                                "false"
                            );

                            menuToggle.textContent =
                                "☰";
                        }

                    }
                );

            });

    }



    /* =========================================
       2. LIGHT / DARK THEME
          + localStorage PERSISTENCE
       ========================================= */

    const themeToggle =
        document.getElementById(
            "themeToggle"
        );


    /*
       Read saved theme from browser
    */

    const savedTheme =
        localStorage.getItem(
            "studenthub-theme"
        );


    /*
       Restore dark theme
       when page loads
    */

    if (savedTheme === "dark") {

        document.body.classList.add(
            "dark-theme"
        );

    }


    /*
       Update theme button text
    */

    function updateThemeButton() {

        if (!themeToggle) {
            return;
        }


        if (
            document.body.classList.contains(
                "dark-theme"
            )
        ) {

            themeToggle.textContent =
                "☀️ Light Mode";

            themeToggle.setAttribute(
                "aria-label",
                "Switch to light mode"
            );

        }

        else {

            themeToggle.textContent =
                "🌙 Dark Mode";

            themeToggle.setAttribute(
                "aria-label",
                "Switch to dark mode"
            );

        }

    }


    updateThemeButton();


    /*
       Change theme when button
       is clicked
    */

    if (themeToggle) {

        themeToggle.addEventListener(
            "click",
            function () {

                document.body.classList.toggle(
                    "dark-theme"
                );


                const isDark =
                    document.body.classList.contains(
                        "dark-theme"
                    );


                /*
                   Save preference
                   in localStorage
                */

                localStorage.setItem(
                    "studenthub-theme",
                    isDark
                        ? "dark"
                        : "light"
                );


                updateThemeButton();

            }
        );

    }



    /* =========================================
       3. FAQ ACCORDION
       ========================================= */

    const faqQuestions =
        document.querySelectorAll(
            ".faq-question"
        );


    faqQuestions.forEach(
        function (question) {

            question.addEventListener(
                "click",
                function () {

                    const item =
                        question.closest(
                            ".faq-item"
                        );


                    const answer =
                        item.querySelector(
                            ".faq-answer"
                        );


                    const wasOpen =
                        item.classList.contains(
                            "faq-open"
                        );


                    /*
                       Close all FAQ items
                    */

                    document
                        .querySelectorAll(
                            ".faq-item"
                        )
                        .forEach(
                            function (faq) {

                                faq.classList.remove(
                                    "faq-open"
                                );


                                const button =
                                    faq.querySelector(
                                        ".faq-question"
                                    );


                                const answer =
                                    faq.querySelector(
                                        ".faq-answer"
                                    );


                                if (button) {

                                    button.setAttribute(
                                        "aria-expanded",
                                        "false"
                                    );

                                }


                                if (answer) {

                                    answer.hidden =
                                        true;

                                }

                            }
                        );


                    /*
                       Open selected FAQ
                    */

                    if (!wasOpen) {

                        item.classList.add(
                            "faq-open"
                        );


                        question.setAttribute(
                            "aria-expanded",
                            "true"
                        );


                        answer.hidden =
                            false;

                    }

                }
            );

        }
    );



    /* =========================================
       4. CONTENT SLIDER
       ========================================= */

    const slides =
        document.querySelectorAll(
            ".slide"
        );


    const previousSlide =
        document.getElementById(
            "previousSlide"
        );


    const nextSlide =
        document.getElementById(
            "nextSlide"
        );


    const slideCounter =
        document.getElementById(
            "slideCounter"
        );


    let currentSlide = 0;


    function showSlide(index) {

        if (!slides.length) {
            return;
        }


        /*
           Loop from last to first
           and first to last
        */

        currentSlide =
            (
                index + slides.length
            ) % slides.length;


        slides.forEach(
            function (slide, i) {

                slide.classList.toggle(
                    "active-slide",
                    i === currentSlide
                );


                slide.setAttribute(
                    "aria-hidden",
                    i === currentSlide
                        ? "false"
                        : "true"
                );

            }
        );


        if (slideCounter) {

            slideCounter.textContent =
                (
                    currentSlide + 1
                ) +
                " / " +
                slides.length;

        }

    }


    if (slides.length) {

        showSlide(0);


        if (previousSlide) {

            previousSlide.addEventListener(
                "click",
                function () {

                    showSlide(
                        currentSlide - 1
                    );

                }
            );

        }


        if (nextSlide) {

            nextSlide.addEventListener(
                "click",
                function () {

                    showSlide(
                        currentSlide + 1
                    );

                }
            );

        }

    }



    /* =========================================
       5. NOTIFICATION BANNER
       ========================================= */

    const notificationBanner =
        document.getElementById(
            "notificationBanner"
        );


    const closeNotification =
        document.getElementById(
            "closeNotification"
        );


    if (
        notificationBanner &&
        closeNotification
    ) {

        closeNotification.addEventListener(
            "click",
            function () {

                notificationBanner.classList.add(
                    "notification-hidden"
                );

            }
        );

    }



    /* =========================================
       6. MODAL POPUP
       ========================================= */

    const modal =
        document.getElementById(
            "eventModal"
        );


    const modalTitle =
        document.getElementById(
            "modalTitle"
        );


    const modalContent =
        document.getElementById(
            "modalContent"
        );


    const modalClose =
        document.getElementById(
            "modalClose"
        );


    /*
       Close modal function
    */

    function closeModal() {

        if (!modal) {
            return;
        }


        modal.classList.remove(
            "modal-open"
        );


        modal.setAttribute(
            "aria-hidden",
            "true"
        );


        document.body.classList.remove(
            "modal-open"
        );

    }


    /*
       Open modal buttons
    */

    const modalButtons =
        document.querySelectorAll(
            ".open-modal"
        );


    modalButtons.forEach(
        function (button) {

            button.addEventListener(
                "click",
                function () {

                    if (!modal) {
                        return;
                    }


                    if (modalTitle) {

                        modalTitle.textContent =
                            button.dataset.title ||
                            "StudentHub Event";

                    }


                    if (modalContent) {

                        modalContent.textContent =
                            button.dataset.content ||
                            "More information will be available soon.";

                    }


                    modal.classList.add(
                        "modal-open"
                    );


                    modal.setAttribute(
                        "aria-hidden",
                        "false"
                    );


                    document.body.classList.add(
                        "modal-open"
                    );


                    if (modalClose) {

                        modalClose.focus();

                    }

                }
            );

        }
    );


    /*
       Close button
    */

    if (modalClose) {

        modalClose.addEventListener(
            "click",
            closeModal
        );

    }


    /*
       Close when clicking
       outside modal
    */

    if (modal) {

        modal.addEventListener(
            "click",
            function (event) {

                if (
                    event.target === modal
                ) {

                    closeModal();

                }

            }
        );

    }


    /*
       Close modal with Escape
    */

    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape" &&
                modal &&
                modal.classList.contains(
                    "modal-open"
                )
            ) {

                closeModal();

            }

        }
    );

});