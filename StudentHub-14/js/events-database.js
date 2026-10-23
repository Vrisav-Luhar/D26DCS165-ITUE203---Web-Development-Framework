document.addEventListener("DOMContentLoaded", () => {
  const grid = document.getElementById("databaseEventGrid");
  const featured = document.getElementById("featuredEvent");
  const previous = document.getElementById("previousSlide");
  const next = document.getElementById("nextSlide");
  const counter = document.getElementById("slideCounter");
  const status = document.getElementById("eventLoadStatus");
  const modal = document.getElementById("eventModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalContent = document.getElementById("modalContent");
  const modalClose = document.getElementById("modalClose");
  let events = [];
  let activeIndex = 0;

  const escapeText = (value) => String(value ?? "");
  const formatDate = (value) => {
    if (!value) return "Date to be announced";
    const date = new Date(String(value).replace(" ", "T"));
    if (Number.isNaN(date.getTime())) return String(value);
    return date.toLocaleString(undefined, {
      year: "numeric", month: "short", day: "numeric",
      hour: "2-digit", minute: "2-digit"
    });
  };

  function renderFeatured() {
    if (!events.length) {
      featured.innerHTML = "<h2>No events available</h2><p>There are currently no events in the database.</p>";
      counter.textContent = "0 / 0";
      previous.disabled = true;
      next.disabled = true;
      return;
    }
    const event = events[activeIndex];
    featured.replaceChildren();
    const title = document.createElement("h2");
    title.textContent = event.title;
    const description = document.createElement("p");
    description.textContent = event.description || "More details will be announced soon.";
    const meta = document.createElement("p");
    meta.textContent = `${formatDate(event.event_date)} · ${event.venue || "Venue to be announced"}`;
    featured.append(title, description, meta);
    counter.textContent = `${activeIndex + 1} / ${events.length}`;
    previous.disabled = events.length < 2;
    next.disabled = events.length < 2;
  }

  function openEventModal(event) {
    modalTitle.textContent = event.title;
    modalContent.textContent = [
      event.description || "No description provided.",
      `Date and time: ${formatDate(event.event_date)}`,
      `Venue: ${event.venue || "To be announced"}`,
      `Capacity: ${event.capacity ?? "Not specified"}`
    ].join("\n\n");
    modal.classList.add("active");
    modal.setAttribute("aria-hidden", "false");
    if (modalClose) modalClose.focus();
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove("active");
    modal.setAttribute("aria-hidden", "true");
  }

  function renderCards() {
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
      const title = document.createElement("h2");
      title.textContent = event.title;
      const description = document.createElement("p");
      description.textContent = event.description || "More details will be announced soon.";
      const details = document.createElement("p");
      details.className = "event-meta";
      details.textContent = `${formatDate(event.event_date)} · ${event.venue || "Venue to be announced"}`;
      const button = document.createElement("button");
      button.className = "button";
      button.type = "button";
      button.textContent = "View Details";
      button.addEventListener("click", () => openEventModal(event));
      card.append(title, description, details, button);
      grid.appendChild(card);
    });
  }

  previous?.addEventListener("click", () => {
    if (!events.length) return;
    activeIndex = (activeIndex - 1 + events.length) % events.length;
    renderFeatured();
  });
  next?.addEventListener("click", () => {
    if (!events.length) return;
    activeIndex = (activeIndex + 1) % events.length;
    renderFeatured();
  });
  modalClose?.addEventListener("click", closeModal);
  modal?.addEventListener("click", (event) => {
    if (event.target === modal) closeModal();
  });
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeModal();
  });

  fetch("api/public-events.php", { headers: { "Accept": "application/json" }, cache: "no-store" })
    .then((response) => {
      if (!response.ok) throw new Error("Server returned " + response.status);
      return response.json();
    })
    .then((payload) => {
      if (!payload.success || !Array.isArray(payload.data)) {
        throw new Error(payload.message || "Unexpected response from events API.");
      }
      events = payload.data;
      status.textContent = `${events.length} event(s) loaded from the database.`;
      renderFeatured();
      renderCards();
    })
    .catch((error) => {
      console.error("Unable to load StudentHub events:", error);
      grid.replaceChildren();
      const message = document.createElement("p");
      message.textContent = "Events could not be loaded. Check that Apache/MySQL are running and the database connection is configured.";
      grid.appendChild(message);
      featured.innerHTML = "<h2>Events unavailable</h2><p>Please try again after checking the database connection.</p>";
      counter.textContent = "—";
      status.textContent = "Database event feed unavailable.";
    });
});
