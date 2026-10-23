document.addEventListener("DOMContentLoaded", () => {
    const grid = document.getElementById("eventGrid");
    const featured = document.getElementById("featuredSlides");
    const previous = document.getElementById("previousSlide");
    const next = document.getElementById("nextSlide");
    const counter = document.getElementById("slideCounter");
    const modal = document.getElementById("eventModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalContent = document.getElementById("modalContent");
    const modalClose = document.getElementById("modalClose");

    let slides = [];
    let currentSlide = 0;

    const safeText = (value) => value == null ? "" : String(value);

    function formatDate(value) {
        if (!value) return "Date to be announced";
        const parsed = new Date(String(value).replace(" ", "T"));
        if (Number.isNaN(parsed.getTime())) return safeText(value);
        return parsed.toLocaleString(undefined, {
            year: "numeric", month: "short", day: "numeric",
            hour: "2-digit", minute: "2-digit"
        });
    }

    function showSlide(index) {
        if (!slides.length) {
            if (counter) counter.textContent = "0 / 0";
            if (previous) previous.disabled = true;
            if (next) next.disabled = true;
            return;
        }
        currentSlide = (index + slides.length) % slides.length;
        slides.forEach((slide, i) => {
            slide.classList.toggle("active-slide", i === currentSlide);
        });
        if (counter) counter.textContent = `${currentSlide + 1} / ${slides.length}`;
        if (previous) previous.disabled = slides.length < 2;
        if (next) next.disabled = slides.length < 2;
    }

    function openModal(event) {
        if (!modal) return;
        modalTitle.textContent = safeText(event.title);
        const date = formatDate(event.event_date);
        const venue = safeText(event.venue);
        const description = safeText(event.description);
        modalContent.textContent = `${description}\n\nDate: ${date}\nVenue: ${venue}`;
        modal.classList.add("modal-open");
        document.body.classList.add("modal-open");
        modal.setAttribute("aria-hidden", "false");
        if (modalClose) modalClose.focus();
    }

    function renderEvents(events) {
        if (featured) {
            featured.replaceChildren();
            slides = [];
            events.slice(0, 3).forEach((event, index) => {
                const article = document.createElement("article");
                article.className = "slide";
                if (event.poster_path) {
                    const poster = document.createElement("img");
                    poster.className = "slide-poster";
                    poster.src = safeText(event.poster_path);
                    poster.alt = `Poster for ${safeText(event.title)}`;
                    poster.loading = "lazy";
                    article.appendChild(poster);
                }
                const heading = document.createElement("h2");
                heading.textContent = safeText(event.title);
                const description = document.createElement("p");
                description.textContent = safeText(event.description) || "Event details will be announced soon.";
                const date = document.createElement("p");
                date.className = "event-meta";
                date.textContent = `${formatDate(event.event_date)} · ${safeText(event.venue)}`;
                article.append(heading, description, date);
                featured.appendChild(article);
                slides.push(article);
            });
            if (!events.length) {
                featured.textContent = "No events are currently available.";
            }
            showSlide(0);
        }

        if (!grid) return;
        grid.replaceChildren();
        if (!events.length) {
            const empty = document.createElement("p");
            empty.textContent = "No events are currently available.";
            grid.appendChild(empty);
            return;
        }

        events.forEach((event) => {
            const card = document.createElement("article");
            card.className = "card event-card";

            if (event.poster_path) {
                const poster = document.createElement("img");
                poster.className = "event-poster";
                poster.src = safeText(event.poster_path);
                poster.alt = `Poster for ${safeText(event.title)}`;
                poster.loading = "lazy";
                card.appendChild(poster);
            }
            const heading = document.createElement("h2");
            heading.textContent = safeText(event.title);

            const description = document.createElement("p");
            description.textContent = safeText(event.description) || "Event details will be announced soon.";

            const date = document.createElement("p");
            date.className = "event-meta";
            date.textContent = `Date: ${formatDate(event.event_date)}`;

            const venue = document.createElement("p");
            venue.className = "event-meta";
            venue.textContent = `Venue: ${safeText(event.venue)}`;

            const button = document.createElement("button");
            button.className = "button open-modal";
            button.type = "button";
            button.textContent = "View Details";
            button.addEventListener("click", () => openModal(event));

            card.append(heading, description, date, venue, button);
            grid.appendChild(card);
        });
    }

    if (next) next.addEventListener("click", () => showSlide(currentSlide + 1));
    if (previous) previous.addEventListener("click", () => showSlide(currentSlide - 1));
    if (modalClose && modal) {
        modalClose.addEventListener("click", () => {
            modal.classList.remove("modal-open");
            document.body.classList.remove("modal-open");
            modal.setAttribute("aria-hidden", "true");
        });
        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.classList.remove("modal-open");
            document.body.classList.remove("modal-open");
                modal.setAttribute("aria-hidden", "true");
            }
        });
        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && modal.classList.contains("modal-open")) {
                modal.classList.remove("modal-open");
            document.body.classList.remove("modal-open");
                modal.setAttribute("aria-hidden", "true");
            }
        });
    }

    fetch("api/public-events.php", { headers: { "Accept": "application/json" } })
        .then((response) => {
            if (!response.ok) throw new Error("The events endpoint returned an error.");
            return response.json();
        })
        .then((payload) => {
            if (!payload.success || !Array.isArray(payload.data)) {
                throw new Error("The events response was not valid.");
            }
            renderEvents(payload.data);
        })
        .catch((error) => {
            console.error("Unable to load StudentHub events:", error);
            if (grid) grid.textContent = "Events could not be loaded. Check that Apache, MySQL, and the studenthub_db database are running.";
            if (featured) featured.textContent = "Featured events are unavailable until the database connection is restored.";
            if (counter) counter.textContent = "—";
        });
});
