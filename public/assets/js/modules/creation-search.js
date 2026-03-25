export function initCreationSearch() {
    const input = document.querySelector("#creation-search-input");
    const resultsBox = document.querySelector("#creation-search-results");

    if (!input || !resultsBox) {
        return;
    }

    const searchWrapper = input.closest(".position-relative");

    let items = [];
    let activeIndex = -1;
    let debounceTimer = null;

    const closeResults = () => {
        resultsBox.innerHTML = "";
        resultsBox.classList.add("d-none");
        input.setAttribute("aria-expanded", "false");
        items = [];
        activeIndex = -1;
    };

    const openResults = () => {
        resultsBox.classList.remove("d-none");
        input.setAttribute("aria-expanded", "true");
    };

    const setActiveItem = (newIndex) => {
        const buttons = resultsBox.querySelectorAll("[data-autocomplete-item]");

        buttons.forEach((button, index) => {
            button.classList.toggle("active", index === newIndex);
        });

        activeIndex = newIndex;
    };

    const goToItem = (index) => {
        if (index < 0 || index >= items.length) {
            return;
        }

        window.location.href = items[index].url;
    };

    const renderResults = (data) => {
        resultsBox.innerHTML = "";
        items = data;
        activeIndex = -1;

        if (!items.length) {
            closeResults();
            return;
        }

        const fragment = document.createDocumentFragment();

        items.forEach((item, index) => {
            const button = document.createElement("button");
            button.type = "button";
            button.className = "list-group-item list-group-item-action";
            button.textContent = item.title;
            button.setAttribute("role", "option");
            button.setAttribute("data-autocomplete-item", "true");
            button.setAttribute("data-index", String(index));

            button.addEventListener("click", () => {
                window.location.href = item.url;
            });

            button.addEventListener("mouseenter", () => {
                setActiveItem(index);
            });

            fragment.appendChild(button);
        });

        resultsBox.appendChild(fragment);
        openResults();
    };

    const fetchResults = async (term) => {
        try {
            const response = await fetch(
                `/api/creations/search?q=${encodeURIComponent(term)}`,
                {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                },
            );

            if (!response.ok) {
                closeResults();
                return;
            }

            const json = await response.json();
            renderResults(json.items ?? []);
        } catch (error) {
            closeResults();
            console.error("Erreur lors de la recherche des créations :", error);
        }
    };

    input.addEventListener("input", () => {
        const term = input.value.trim();

        clearTimeout(debounceTimer);

        if (term.length < 2) {
            closeResults();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchResults(term);
        }, 250);
    });

    input.addEventListener("keydown", (event) => {
        if (resultsBox.classList.contains("d-none") || !items.length) {
            return;
        }

        if (event.key === "ArrowDown") {
            event.preventDefault();
            const nextIndex = activeIndex < items.length - 1 ? activeIndex + 1 : 0;
            setActiveItem(nextIndex);
            return;
        }

        if (event.key === "ArrowUp") {
            event.preventDefault();
            const prevIndex = activeIndex > 0 ? activeIndex - 1 : items.length - 1;
            setActiveItem(prevIndex);
            return;
        }

        if (event.key === "Enter") {
            event.preventDefault();

            if (activeIndex >= 0) {
                goToItem(activeIndex);
            } else if (items.length > 0) {
                goToItem(0);
            }

            return;
        }

        if (event.key === "Escape") {
            closeResults();
        }
    });

    document.addEventListener("click", (event) => {
        if (!searchWrapper || !searchWrapper.contains(event.target)) {
            closeResults();
        }
    });
}